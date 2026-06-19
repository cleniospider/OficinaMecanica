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
} catch (PDOException $e) {
    die("Erro ao buscar dados da ordem de serviço: " . $e->getMessage());
}

// Processar POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mecanico_id = filter_var($_POST['mecanico_id'], FILTER_VALIDATE_INT);
    $problema = trim($_POST['problema']);
    $servicos = trim($_POST['servicos']);
    $pecas_usadas = trim($_POST['pecas_usadas']);
    $status = $_POST['status'];

    // Limpar valor_total para formato float
    $valor_str = $_POST['valor_total'];
    $valor_clean = str_replace(['R$', ' ', '.', ','], ['', '', '', '.'], $valor_str);
    $valor_total = floatval($valor_clean);

    if ($mecanico_id) {
        try {
            $pdo->beginTransaction();

            // Atualizar OS
            $stmt_update = $pdo->prepare("
                UPDATE OS 
                SET mecanico_id = ?, problema = ?, servicos = ?, pecas_usadas = ?, valor_total = ?, status = ? 
                WHERE id = ?
            ");
            $stmt_update->execute([$mecanico_id, $problema, $servicos, $pecas_usadas, $valor_total, $status, $id]);

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
                    <label>Problema:</label>
                    <textarea name="problema" rows="3" required><?= htmlspecialchars($os['problema']) ?></textarea>
                </div>

                <div class="grupo-input-dark">
                    <label>Serviços:</label>
                    <textarea name="servicos" rows="3"><?= htmlspecialchars($os['servicos']) ?></textarea>
                </div>

                <div class="grupo-input-dark">
                    <label>Peças usadas:</label>
                    <textarea name="pecas_usadas" rows="2"><?= htmlspecialchars($os['pecas_usadas']) ?></textarea>
                </div>

                <div class="flex-row">
                    <div class="grupo-input-dark">
                        <label>Valor total:</label>
                        <input type="text" name="valor_total" id="valor_total" value="R$ <?= number_format($os['valor_total'], 2, ',', '.') ?>" class="input-valor-dark" required>
                    </div>

                    <div class="grupo-input-dark">
                        <label>Status:</label>
                        <select name="status" class="select-dark">
                            <option value="ativo" <?= ($os['status'] === 'ativo') ? 'selected' : '' ?>>Ativo</option>
                            <option value="parado" <?= ($os['status'] === 'parado') ? 'selected' : '' ?>>Parado</option>
                            <option value="finalizado" <?= ($os['status'] === 'finalizado') ? 'selected' : '' ?>>Finalizado</option>
                        </select>
                    </div>
                </div>

                <div class="acoes-os-dark">
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

        // Formatação do campo de valor
        const valorInput = document.getElementById('valor_total');
        valorInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, "");
            value = (value / 100).toFixed(2) + "";
            value = value.replace(".", ",");
            value = value.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
            e.target.value = "R$ " + value;
        });
    </script>
</body>
</html>
