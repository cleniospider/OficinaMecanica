<?php 
require_once('conexao/conexao.php');

// Proteção de sessão
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$erro = "";
$sucesso = "";

// Buscar veículos para o dropdown
try {
    $stmt_veic = $pdo->query("SELECT id, placa, `marca/modelo` as modelo, cliente, clientes_cpf FROM veiculo ORDER BY `marca/modelo` ASC");
    $veiculos = $stmt_veic->fetchAll();

    // REMOVIDO: Busca de mecânicos do banco

    // Buscar pecas
    $stmt_pecas = $pdo->query("SELECT id, nome, preco_venda, estoque_atual FROM pecas WHERE estoque_atual > 0 ORDER BY nome ASC");
    $lista_pecas = $stmt_pecas->fetchAll();

    // REMOVIDO: Busca de serviços pré-definidos do banco
} catch (PDOException $e) {
    $erro = "Erro ao carregar dados do banco: " . $e->getMessage();
}

// Processar POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['veiculo_id'])) {
    $veiculo_id = filter_var($_POST['veiculo_id'], FILTER_VALIDATE_INT);
    // MODIFICADO: Mecânico agora é opcional ou nulo (removido a validação obrigatória)
    $mecanico_id = 1; 
    $problema = trim($_POST['problema']);
    $status = $_POST['status'];

    $pecas_selecionadas = $_POST['pecas_ids'] ?? [];
    
    // MODIFICADO: Captura o texto digitado livremente no textarea de serviços
    $txt_servicos = trim($_POST['servicos_texto']); 

    // Limpar valor_total para formato float
    $valor_str = $_POST['valor_total'];
    $valor_clean = str_replace(['R$', ' ', '.', ','], ['', '', '', '.'], $valor_str);
    $valor_total = floatval($valor_clean);

    // MODIFICADO: Validação agora exige apenas o veículo
    if ($veiculo_id) {
        try {
            // Obter CPF do proprietário a partir do veículo selecionado
            $stmt_v = $pdo->prepare("SELECT clientes_cpf, `marca/modelo` FROM veiculo WHERE id = ?");
            $stmt_v->execute([$veiculo_id]);
            $veiculo = $stmt_v->fetch();

            if ($veiculo) {
                $clientes_cpf = $veiculo['clientes_cpf'];
                $veiculo_modelo = $veiculo['marca/modelo'];
                $data_entrada = date('Y-m-d H:i:s'); 

                $txt_pecas = "";

                if (!empty($pecas_selecionadas)) {
                    $in_p = str_repeat('?,', count($pecas_selecionadas) - 1) . '?';
                    $stmt_p = $pdo->prepare("SELECT nome FROM pecas WHERE id IN ($in_p)");
                    $stmt_p->execute($pecas_selecionadas);
                    $txt_pecas = implode(', ', $stmt_p->fetchAll(PDO::FETCH_COLUMN));
                }

                // Iniciar transação para garantir que OS e Financeiro sejam criados juntos
                $pdo->beginTransaction();

                // Inserir OS (mecanico_id passará como NULL se sua tabela permitir)
                $stmt_os = $pdo->prepare("
                    INSERT INTO OS (veiculo_id, mecanico_id, data_entrada, veiculo_id1, clientes_cpf, problema, servicos, pecas_usadas, valor_total, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt_os->execute([$veiculo_id, $mecanico_id, $data_entrada, $veiculo_id, $clientes_cpf, $problema, $txt_servicos, $txt_pecas, $valor_total, $status]);
                $os_id = $pdo->lastInsertId();

                // REMOVIDO: Vínculo da tabela pivot servicos_has_OS (já que o serviço virou texto livre)

                foreach ($pecas_selecionadas as $p_id) {
                    $stmt_p_os = $pdo->prepare("INSERT INTO pecas_na_OS (pecas_id, OS_id) VALUES (?, ?)");
                    $stmt_p_os->execute([$p_id, $os_id]);
                    
                    // Se for finalizado, dá baixa no estoque
                    if ($status === 'finalizado') {
                        $pdo->prepare("UPDATE pecas SET estoque_atual = estoque_atual - 1 WHERE id = ? AND estoque_atual > 0")->execute([$p_id]);
                    }
                }

                // Inserir registro financeiro
                $fin_desc = "OS #$os_id - " . $veiculo_modelo;
                $fin_tipo = '1: Receita'; // OS é receita
                $fin_status = ($status === 'finalizado') ? 'PAGO' : 'Aguardando';

                $stmt_fin = $pdo->prepare("
                    INSERT INTO Financeiro (descricao, valor, tipo, status, OS_id)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt_fin->execute([$fin_desc, $valor_total, $fin_tipo, $fin_status, $os_id]);

                $pdo->commit();
                header("Location: ordens.php");
                exit;
            } else {
                $erro = "Veículo selecionado inválido!";
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $erro = "Erro ao salvar Ordem de Serviço: " . $e->getMessage();
        }
    } else {
        $erro = "Selecione um veículo válido!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Nova Ordem</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/ordens.css">
    <link rel="stylesheet" href="css/nova-ordem.css">
    <style>
        .select-dark, .textarea-dark {
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
        .select-dark:focus, .textarea-dark:focus {
            border-color: #ff0000;
        }
        .textarea-dark {
            resize: vertical;
            font-family: inherit;
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
                <span class="role-text">ADMINISTRADOR</span>
            </div>
        </div>

        <ul class="nav-links">
            <li><a href="admin.php" >Painel de Gestão</a></li>
            <li><a href="cadastrocliente.php" >Cadastro Cliente</a></li>
            <li><a href="cadastroveiculo.php" >Cadastro Veículo</a></li>
            <li><a href="ordens.php" class="active">Ordens de Serviços</a></li>
            <li><a href="estoque-critico.php">Estoque de Peças</a></li>
            <li><a href="historico-veiculos.php">Histórico de Veículos</a></li>
            <li><a href="financeiro.php">Financeiro</a></li>
            <li><a href="relatorios.php">Relatórios</a></li>
            <li><a href="minha-conta.php">Minha Conta</a></li>
            <li><a href="index.php?logout=1" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="orders-container">
            <div class="os-header-detalhe">
                <h2>NOVA ORDEM <span class="text-red">DE SERVIÇO</span></h2>
            </div>
    
            <?php if (!empty($erro)): ?>
                <div class="alert-error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <form method="POST" class="form-nova-ordem">
                
                <div class="form-header-info" style="display: grid; grid-template-columns: 1fr; gap: 20px;">
                    <div class="grupo-input">
                        <label class="label-padrao">Veículo / Proprietário:</label>
                        <select name="veiculo_id" class="select-dark" required>
                            <option value="">Selecione o Veículo</option>
                            <?php foreach ($veiculos as $v): ?>
                                <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['modelo']) ?> (<?= htmlspecialchars($v['placa']) ?>) - <?= htmlspecialchars($v['cliente']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
        
                <div class="form-corpo-ordem" style="margin-top: 20px;">
                    
                    <div class="grupo-input-dark">
                        <label>Problema Constatado:</label>
                        <textarea name="problema" class="textarea-dark" rows="3" placeholder="Descreva o problema constatado..." required></textarea>
                    </div>

                    <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="grupo-input-dark">
                            <label>Serviços a realizar (Descreva livremente):</label>
                            <textarea name="servicos_texto" class="textarea-dark" rows="6" placeholder="Digite os serviços realizados aqui..." required></textarea>
                        </div>

                        <div class="grupo-input-dark">
                            <label>Peças a utilizar:</label>
                            <div class="checkbox-group" style="height: 148px;">
                                <?php if (empty($lista_pecas)): ?>
                                    <span style="color: #666; font-size: 13px;">Nenhuma peça com estoque disponível.</span>
                                <?php endif; ?>
                                <?php foreach ($lista_pecas as $p): ?>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="pecas_ids[]" value="<?= $p['id'] ?>" data-preco="<?= $p['preco_venda'] ?>" class="calc-item">
                                        <?= htmlspecialchars($p['nome']) ?> (Estoque: <?= $p['estoque_atual'] ?>)
                                        <span class="item-preco">+ R$ <?= number_format($p['preco_venda'], 2, ',', '.') ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="flex-row" style="display: flex; gap: 20px; margin-top: 15px;">
                        <div class="grupo-input-dark" style="flex: 1;">
                            <label>Valor total da OS (R$):</label>
                            <input type="text" name="valor_total" id="valor_total" class="input-valor-dark" value="R$ 0,00" required style="background-color: #121212; border-color: #333; color: #2ecc71; font-weight: bold; width: 100%; padding: 12px; border-radius: 8px;">
                        </div>
                        <div class="grupo-input-dark" style="flex: 1;">
                            <label>Status da OS:</label>
                            <select name="status" class="select-dark">
                                <option value="ativo" selected>Em andamento (Ativo)</option>
                                <option value="parado">Aguardando Aprovação (Parado)</option>
                                <option value="finalizado">Finalizado / Entregue</option>
                            </select>
                            <small style="color: #888; font-size: 12px; margin-top: 5px; display: block;">Marcar como 'Finalizado' dará baixa no estoque das peças selecionadas.</small>
                        </div>
                    </div>
            
                    <div class="botoes-acao" style="margin-top: 25px;">
                        <button type="submit" class="btn-os btn-salvar-red">SALVAR ORDEM</button>
                        <a href="ordens.php" class="btn-os btn-voltar-dark">CANCELAR</a>
                    </div>
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

        // MODIFICADO: Cálculo automático agora soma as peças, mas permite que o usuário adicione o valor manualmente na caixa de texto.
        const checkboxes = document.querySelectorAll('.calc-item');
        const valorInput = document.getElementById('valor_total');

        function calcularTotal() {
            let total = 0;
            checkboxes.forEach(cb => {
                if (cb.checked) {
                    total += parseFloat(cb.getAttribute('data-preco'));
                }
            });
            let valorBR = total.toFixed(2).replace('.', ',');
            valorBR = valorBR.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
            valorInput.value = "R$ " + valorBR;
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', calcularTotal);
        });

        // Formatação simples enquanto o usuário digita o preço manualmente
        valorInput.addEventListener('focus', function() {
            if(this.value === 'R$ 0,00') this.value = '';
        });
    </script>
</body>
</html>