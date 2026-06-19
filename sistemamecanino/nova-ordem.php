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

    // Buscar mecânicos
    $stmt_mec = $pdo->query("SELECT id, nome_completo FROM usuarios WHERE perfil = 'Mecanico' ORDER BY nome_completo ASC");
    $mecanicos = $stmt_mec->fetchAll();
} catch (PDOException $e) {
    $erro = "Erro ao carregar dados do banco: " . $e->getMessage();
}

// Processar POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['veiculo_id'])) {
    $veiculo_id = filter_var($_POST['veiculo_id'], FILTER_VALIDATE_INT);
    $mecanico_id = filter_var($_POST['mecanico_id'], FILTER_VALIDATE_INT);
    $problema = trim($_POST['problema']);
    $servicos = trim($_POST['servicos']);
    $pecas_usadas = trim($_POST['pecas_usadas']);
    $status = $_POST['status'];

    // Limpar valor_total para formato float
    $valor_str = $_POST['valor_total'];
    $valor_clean = str_replace(['R$', ' ', '.', ','], ['', '', '', '.'], $valor_str);
    $valor_total = floatval($valor_clean);

    if ($veiculo_id && $mecanico_id) {
        try {
            // Obter CPF do proprietário a partir do veículo selecionado
            $stmt_v = $pdo->prepare("SELECT clientes_cpf, `marca/modelo` FROM veiculo WHERE id = ?");
            $stmt_v->execute([$veiculo_id]);
            $veiculo = $stmt_v->fetch();

            if ($veiculo) {
                $clientes_cpf = $veiculo['clientes_cpf'];
                $veiculo_modelo = $veiculo['marca/modelo'];
                $data_entrada = date('Y-m-d H:i:s');

                // Iniciar transação para garantir que OS e Financeiro sejam criados juntos
                $pdo->beginTransaction();

                // Inserir OS
                // Nota: o schema pede veiculo_id, mecanico_id, data_entrada, veiculo_id1, clientes_cpf
                $stmt_os = $pdo->prepare("
                    INSERT INTO OS (veiculo_id, mecanico_id, data_entrada, veiculo_id1, clientes_cpf, problema, servicos, pecas_usadas, valor_total, status)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                // veiculo_id e veiculo_id1 recebem o mesmo ID de veiculo
                $stmt_os->execute([$veiculo_id, $mecanico_id, $data_entrada, $veiculo_id, $clientes_cpf, $problema, $servicos, $pecas_usadas, $valor_total, $status]);
                $os_id = $pdo->lastInsertId();

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
        $erro = "Selecione um veículo e um mecânico válidos!";
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
                <h2>NOVA ORDEM <span class="text-red">DE SERVIÇO</span></h2>
            </div>
    
            <?php if (!empty($erro)): ?>
                <div class="alert-error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <form method="POST" class="form-nova-ordem">
                
                <div class="form-header-info" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="grupo-input">
                        <label class="label-padrao">Veículo / Proprietário:</label>
                        <select name="veiculo_id" class="select-dark" required>
                            <option value="">Selecione o Veículo</option>
                            <?php foreach ($veiculos as $v): ?>
                                <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['modelo']) ?> (<?= htmlspecialchars($v['placa']) ?>) - <?= htmlspecialchars($v['cliente']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="grupo-input">
                        <label class="label-padrao">Mecânico Responsável:</label>
                        <select name="mecanico_id" class="select-dark" required>
                            <option value="">Selecione o Mecânico</option>
                            <?php foreach ($mecanicos as $m): ?>
                                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nome_completo']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
        
                <div class="form-corpo-ordem" style="margin-top: 20px;">
                    
                    <div class="grupo-input-dark">
                        <label>Problema:</label>
                        <textarea name="problema" rows="3" placeholder="Descreva o problema constatado..." required></textarea>
                    </div>

                    <div class="grupo-input-dark">
                        <label>Serviços:</label>
                        <textarea name="servicos" rows="3" placeholder="Serviços a realizar..."></textarea>
                    </div>
            
                    <div class="grupo-input-dark">
                        <label>Peças usadas:</label>
                        <textarea name="pecas_usadas" rows="2" placeholder="Lista de peças e componentes..."></textarea>
                    </div>

                    <div class="flex-row">
                        <div class="grupo-input-dark">
                            <label>Valor total:</label>
                            <input type="text" name="valor_total" id="valor_total" class="input-valor-dark" placeholder="R$ 0,00" required>
                        </div>
                        <div class="grupo-input-dark">
                            <label>Status:</label>
                            <select name="status" class="select-dark">
                                <option value="ativo" selected>Ativo</option>
                                <option value="finalizado">Finalizado</option>
                                <option value="parado">Parado</option>
                            </select>
                        </div>
                    </div>
            
                    <div class="botoes-acao">
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

        // Formatação simples do campo de valor
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
