<?php 
require_once('conexao/conexao.php');

// Proteção de sessão
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$erro = "";
$sucesso = "";

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: ordens.php");
    exit;
}

// Buscar dados da OS selecionada
try {
    $stmt = $pdo->prepare("
        SELECT o.*, v.placa, v.`marca/modelo` AS veiculo_modelo, c.`nome completo` AS cliente_nome 
        FROM OS o
        JOIN veiculo v ON o.veiculo_id1 = v.id
        JOIN clientes c ON o.clientes_cpf = c.cpf
        WHERE o.id = ?
    ");
    $stmt->execute([$id]);
    $os = $stmt->fetch();

    if (!$os) {
        header("Location: ordens.php");
        exit;
    }

    // Buscar mecânicos
    $stmt_mec = $pdo->query("SELECT id, nome_completo FROM usuarios WHERE perfil = 'Mecanico' ORDER BY nome_completo ASC");
    $mecanicos = $stmt_mec->fetchAll();

    // Buscar pecas
    $stmt_pecas = $pdo->query("SELECT id, nome, preco_venda, estoque_atual FROM pecas ORDER BY nome ASC");
    $lista_pecas = $stmt_pecas->fetchAll();

    // Buscar servicos
    $stmt_serv = $pdo->query("SELECT idservicos, nome, preco FROM servicos ORDER BY nome ASC");
    $lista_servicos = $stmt_serv->fetchAll();

    // Buscar relacoes atuais
    $stmt_rel_s = $pdo->prepare("SELECT servicos_idservicos FROM servicos_has_OS WHERE OS_id = ?");
    $stmt_rel_s->execute([$id]);
    $servicos_atuais = $stmt_rel_s->fetchAll(PDO::FETCH_COLUMN);

    $stmt_rel_p = $pdo->prepare("SELECT pecas_id FROM pecas_na_OS WHERE OS_id = ?");
    $stmt_rel_p->execute([$id]);
    $pecas_atuais = $stmt_rel_p->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {
    die("Erro ao buscar dados da ordem de serviço: " . $e->getMessage());
}

// Processar POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mecanico_id = filter_var($_POST['mecanico_id'], FILTER_VALIDATE_INT);
    $problema = trim($_POST['problema']);
    $status = $_POST['status'];
    $status_anterior = $os['status'];

    $pecas_selecionadas = $_POST['pecas_ids'] ?? [];
    $servicos_selecionados = $_POST['servicos_ids'] ?? [];

    // Limpar valor_total para formato float
    $valor_str = $_POST['valor_total'];
    $valor_clean = str_replace(['R$', ' ', '.', ','], ['', '', '', '.'], $valor_str);
    $valor_total = floatval($valor_clean);

    if ($mecanico_id) {
        try {
            // Concatenar os nomes para salvar como texto
            $txt_servicos = "";
            $txt_pecas = "";

            if (!empty($servicos_selecionados)) {
                $in_s = str_repeat('?,', count($servicos_selecionados) - 1) . '?';
                $stmt_s = $pdo->prepare("SELECT nome FROM servicos WHERE idservicos IN ($in_s)");
                $stmt_s->execute($servicos_selecionados);
                $txt_servicos = implode(', ', $stmt_s->fetchAll(PDO::FETCH_COLUMN));
            }

            if (!empty($pecas_selecionadas)) {
                $in_p = str_repeat('?,', count($pecas_selecionadas) - 1) . '?';
                $stmt_p = $pdo->prepare("SELECT nome FROM pecas WHERE id IN ($in_p)");
                $stmt_p->execute($pecas_selecionadas);
                $txt_pecas = implode(', ', $stmt_p->fetchAll(PDO::FETCH_COLUMN));
            }

            $pdo->beginTransaction();

            // Atualizar OS
            $stmt_update = $pdo->prepare("
                UPDATE OS 
                SET mecanico_id = ?, problema = ?, servicos = ?, pecas_usadas = ?, valor_total = ?, status = ? 
                WHERE id = ?
            ");
            $stmt_update->execute([$mecanico_id, $problema, $txt_servicos, $txt_pecas, $valor_total, $status, $id]);

            // Atualizar relacionamentos Servicos
            $pdo->prepare("DELETE FROM servicos_has_OS WHERE OS_id = ?")->execute([$id]);
            foreach ($servicos_selecionados as $s_id) {
                $stmt_s_os = $pdo->prepare("INSERT INTO servicos_has_OS (servicos_idservicos, OS_id) VALUES (?, ?)");
                $stmt_s_os->execute([$s_id, $id]);
            }

            // Atualizar relacionamentos Pecas
            $pdo->prepare("DELETE FROM pecas_na_OS WHERE OS_id = ?")->execute([$id]);
            foreach ($pecas_selecionadas as $p_id) {
                $stmt_p_os = $pdo->prepare("INSERT INTO pecas_na_OS (pecas_id, OS_id) VALUES (?, ?)");
                $stmt_p_os->execute([$p_id, $id]);
                
                // Se mudou para finalizado e antes não era, baixa o estoque
                if ($status === 'finalizado' && $status_anterior !== 'finalizado') {
                    $pdo->prepare("UPDATE pecas SET estoque_atual = estoque_atual - 1 WHERE id = ? AND estoque_atual > 0")->execute([$p_id]);
                }
            }

            // Atualizar registro financeiro correspondente
            $fin_desc = "OS #$id - " . $os['veiculo_modelo'];
            $fin_status = ($status === 'finalizado') ? 'PAGO' : 'Aguardando';

            // Verificar se o lançamento existe no financeiro
            $stmt_check_fin = $pdo->prepare("SELECT id FROM Financeiro WHERE OS_id = ?");
            $stmt_check_fin->execute([$id]);
            $financeiro_rec = $stmt_check_fin->fetch();

            if ($financeiro_rec) {
                // Atualizar lançamento existente
                $stmt_up_fin = $pdo->prepare("
                    UPDATE Financeiro 
                    SET descricao = ?, valor = ?, status = ? 
                    WHERE OS_id = ?
                ");
                $stmt_up_fin->execute([$fin_desc, $valor_total, $fin_status, $id]);
            } else {
                // Criar novo lançamento se por acaso não existia
                $fin_tipo = '1: Receita';
                $stmt_in_fin = $pdo->prepare("
                    INSERT INTO Financeiro (descricao, valor, tipo, status, OS_id) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt_in_fin->execute([$fin_desc, $valor_total, $fin_tipo, $fin_status, $id]);
            }

            $pdo->commit();
            header("Location: ordens.php");
            exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $erro = "Erro ao atualizar a Ordem de Serviço: " . $e->getMessage();
        }
    } else {
        $erro = "Selecione um mecânico válido!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Gerenciar OS</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/ordens.css">
    <link rel="stylesheet" href="css/editar-ordem.css">
    <style>
        .select-dark {
            width: 100%;
            background-color: #121212;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 12px;
            color: #fff;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.3s;
        }
        .select-dark:focus {
            border-color: #ff0000;
        }
        .alert-error {
            background-color: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
            border: 1px solid #e74c3c;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            width: 100%;
        }
        .checkbox-group {
            background: #121212;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 15px;
            max-height: 150px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #ccc;
            font-size: 14px;
        }
        .checkbox-item input {
            cursor: pointer;
        }
        .item-preco {
            color: #2ecc71;
            font-weight: bold;
            margin-left: auto;
        }
    </style>
</head>
<body class="dark-theme">

    <header class="top-header">
        <button class="hamburger-btn">
            <span></span><span></span><span></span>
        </button>
        <div class="header-logo-text">AUTO REPAIR</div>
    </header>

    <aside class="sidebar" id="sidebar">
        <div class="profile-area">
            <img src="img/download.png" alt="Avatar" class="avatar"> 
            <div class="mobile-profile-text">
                AUTO REPAIR<br>
                <span class="role-text"><?= htmlspecialchars(strtoupper($_SESSION['usuario_perfil'] ?? 'ADMINISTRADOR')) ?></span>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="<?= $_SESSION['usuario_perfil'] === 'Admin' ? 'admin.php' : ($_SESSION['usuario_perfil'] === 'Mecanico' ? 'mecan.php' : 'recep.php') ?>">Painel de Gestão</a></li>
            <?php if ($_SESSION['usuario_perfil'] === 'Admin'): ?>
                <li><a href="bd/lista.php">Gerenciar Usuários</a></li>
            <?php endif; ?>
            <li><a href="cadastrocliente.php">Cadastro Cliente</a></li>
            <li><a href="cadastroveiculo.php">Cadastro Veículo</a></li>
            <li><a href="ordens.php" class="active">Ordens de Serviços</a></li>
            <li><a href="servicos.php">Serviços</a></li>
            <li><a href="estoque-critico.php">Estoque de Peças</a></li>
            <li><a href="historico-veiculos.php">Histórico de Veículos</a></li>
            <li><a href="financeiro.php">Financeiro</a></li>
            <li><a href="relatorios.php">Relatórios</a></li>
            <li><a href="minha-conta.php">Minha conta</a></li>
            <li><a href="index.php?logout=1" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="orders-container">
            <div class="os-header-detalhe">
                <h2>ORDEM DE SERVIÇO <span class="text-red">#<?= htmlspecialchars($os['id']) ?></span></h2>
            </div>

            <?php if (!empty($erro)): ?>
                <div class="alert-error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <form method="POST" class="form-gerenciar">
                <div class="form-header-info" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="grupo-input">
                        <label class="label-padrao">Cliente:</label>
                        <input type="text" value="<?= htmlspecialchars($os['cliente_nome']) ?>" readonly style="background-color: #222; border-color: #444; color: #888;">
                    </div>
                    <div class="grupo-input">
                        <label class="label-padrao">Veículo:</label>
                        <input type="text" value="<?= htmlspecialchars($os['veiculo_modelo']) ?>" readonly style="background-color: #222; border-color: #444; color: #888;">
                    </div>
                    <div class="grupo-input">
                        <label class="label-padrao">Placa:</label>
                        <input type="text" class="placa-field" value="<?= htmlspecialchars($os['placa']) ?>" readonly style="background-color: #222; border-color: #444; color: #888; text-transform: uppercase;">
                    </div>
                </div>

                <div class="grupo-input-dark">
                    <label>Mecânico Responsável:</label>
                    <select name="mecanico_id" class="select-dark" required>
                        <option value="">Selecione o Mecânico</option>
                        <?php foreach ($mecanicos as $m): ?>
                            <option value="<?= $m['id'] ?>" <?= ($os['mecanico_id'] == $m['id']) ? 'selected' : '' ?>><?= htmlspecialchars($m['nome_completo']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grupo-input-dark">
                    <label>Problema Constatado:</label>
                    <textarea name="problema" rows="3" required><?= htmlspecialchars($os['problema']) ?></textarea>
                </div>

                <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px;">
                    <div class="grupo-input-dark">
                        <label>Serviços realizados:</label>
                        <div class="checkbox-group">
                            <?php if (empty($lista_servicos)): ?>
                                <span style="color: #666; font-size: 13px;">Nenhum serviço cadastrado.</span>
                            <?php endif; ?>
                            <?php foreach ($lista_servicos as $s): ?>
                                <?php $checked = in_array($s['idservicos'], $servicos_atuais) ? 'checked' : ''; ?>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="servicos_ids[]" value="<?= $s['idservicos'] ?>" data-preco="<?= $s['preco'] ?>" class="calc-item" <?= $checked ?>>
                                    <?= htmlspecialchars($s['nome']) ?>
                                    <span class="item-preco">+ R$ <?= number_format($s['preco'], 2, ',', '.') ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="grupo-input-dark">
                        <label>Peças utilizadas:</label>
                        <div class="checkbox-group">
                            <?php if (empty($lista_pecas)): ?>
                                <span style="color: #666; font-size: 13px;">Nenhuma peça cadastrada.</span>
                            <?php endif; ?>
                            <?php foreach ($lista_pecas as $p): ?>
                                <?php $checked = in_array($p['id'], $pecas_atuais) ? 'checked' : ''; ?>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="pecas_ids[]" value="<?= $p['id'] ?>" data-preco="<?= $p['preco_venda'] ?>" class="calc-item" <?= $checked ?>>
                                    <?= htmlspecialchars($p['nome']) ?> (Estoque: <?= $p['estoque_atual'] ?>)
                                    <span class="item-preco">+ R$ <?= number_format($p['preco_venda'], 2, ',', '.') ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="flex-row" style="display: flex; gap: 20px; margin-top: 15px;">
                    <div class="grupo-input-dark" style="flex: 1;">
                        <label>Valor total calculado:</label>
                        <input type="text" name="valor_total" id="valor_total" value="R$ <?= number_format($os['valor_total'], 2, ',', '.') ?>" class="input-valor-dark" required readonly style="background-color: #222; border-color: #555; color: #2ecc71; font-weight: bold;">
                    </div>

                    <div class="grupo-input-dark" style="flex: 1;">
                        <label>Status da OS:</label>
                        <select name="status" class="select-dark">
                            <option value="ativo" <?= ($os['status'] === 'ativo') ? 'selected' : '' ?>>Em andamento (Ativo)</option>
                            <option value="parado" <?= ($os['status'] === 'parado') ? 'selected' : '' ?>>Aguardando Aprovação (Parado)</option>
                            <option value="finalizado" <?= ($os['status'] === 'finalizado') ? 'selected' : '' ?>>Finalizado / Entregue</option>
                        </select>
                        <small style="color: #888; font-size: 12px; margin-top: 5px; display: block;">Ao alterar para 'Finalizado', será dada baixa no estoque das peças selecionadas.</small>
                    </div>
                </div>

                <div class="acoes-os-dark" style="margin-top: 25px;">
                    <button type="submit" class="btn-os btn-salvar-red">SALVAR ALTERAÇÕES</button>
                    <a href="ordens.php" class="btn-os btn-voltar-dark">VOLTAR</a>
                </div>
            </form>
        </div>
    </main>

    <script>
        const btnMobile = document.querySelector('.hamburger-btn');
        const sidebar = document.querySelector('#sidebar');

        btnMobile.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        const links = document.querySelectorAll('.nav-links a');
        links.forEach(link => {
            link.addEventListener('click', () => {
                sidebar.classList.remove('open');
            });
        });

        // Cálculo Automático de Valor
        const checkboxes = document.querySelectorAll('.calc-item');
        const valorInput = document.getElementById('valor_total');

        function calcularTotal() {
            let total = 0;
            checkboxes.forEach(cb => {
                if (cb.checked) {
                    total += parseFloat(cb.getAttribute('data-preco'));
                }
            });
            // Formatar para Moeda BR
            let valorBR = total.toFixed(2).replace('.', ',');
            valorBR = valorBR.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
            valorInput.value = "R$ " + valorBR;
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', calcularTotal);
        });

        // Uncomment the line below if you want to force recalculation on page load
        // calcularTotal(); 
    </script>
</body>
</html>
