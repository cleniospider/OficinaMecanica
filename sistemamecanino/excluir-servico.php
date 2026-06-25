<?php 
require_once('conexao/conexao.php');

// Proteção de sessão
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_perfil'], ['Admin', 'Mecanico', 'Recepcionista'])) {
    header("Location: index.php");
    exit;
}

$erro = "";
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: servicos.php");
    exit;
}

// Buscar dados do serviço
$stmt = $pdo->prepare("SELECT * FROM servicos WHERE idservicos = ?");
$stmt->execute([$id]);
$servico = $stmt->fetch();

if (!$servico) {
    header("Location: servicos.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $stmt_check = $pdo->prepare("SELECT servicos_idservicos FROM servicos_has_OS WHERE servicos_idservicos = ? LIMIT 1");
        $stmt_check->execute([$id]);
        if ($stmt_check->fetch()) {
            $erro = "Não é possível excluir este serviço pois ele já foi vinculado a Ordens de Serviço.";
        } else {
            $stmt_del = $pdo->prepare("DELETE FROM servicos WHERE idservicos = ?");
            $stmt_del->execute([$id]);
            header("Location: servicos.php");
            exit;
        }
    } catch (PDOException $e) {
        $erro = "Erro ao excluir serviço: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Excluir Serviço</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/excluir-cliente.css">
    <style>
        .alert-error {
            background-color: #e74c3c;
            color: white;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
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
                <span class="role-text"><?= htmlspecialchars(strtoupper($_SESSION['usuario_perfil'])) ?></span>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="<?= $_SESSION['usuario_perfil'] === 'Admin' ? 'admin.php' : ($_SESSION['usuario_perfil'] === 'Mecanico' ? 'mecan.php' : 'recep.php') ?>">Painel de Gestão</a></li>
            <li><a href="cadastrocliente.php">Cadastro Cliente</a></li>
            <li><a href="cadastroveiculo.php">Cadastro Veículo</a></li>
            <li><a href="ordens.php">Ordens de Serviços</a></li>
            <li><a href="estoque-critico.php">Estoque de Peças</a></li>
            <li><a href="servicos.php" class="active">Serviços</a></li>
            <li><a href="historico-veiculos.php">Histórico de Veículos</a></li>
            <li><a href="financeiro.php">Financeiro</a></li>
            <li><a href="relatorios.php">Relatórios</a></li>
            <li><a href="minha-conta.php">Minha conta</a></li>
            <li><a href="index.php?logout=1" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="cliente-exclusao-container">
            <h2 class="titulo-sessao">EXCLUIR SERVIÇO</h2>

            <?php if (!empty($erro)): ?>
                <div class="alert-error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <div class="caixa-exclusao">
                <div class="icone-alerta">⚠️</div>
                <h3>Tem certeza que deseja excluir o serviço "<?= htmlspecialchars($servico['nome']) ?>"?</h3>
                <p class="aviso-texto">Esta ação removerá o serviço do catálogo do sistema. Você não poderá excluir caso ele já tenha sido vinculado a alguma Ordem de Serviço.</p>
                
                <form method="POST" class="form-exclusao">
                    <div class="botoes-acao-excluir">
                        <a href="servicos.php" class="btn-cancelar-exclusao">CANCELAR</a>
                        <button type="submit" class="btn-confirmar-exclusao">SIM, EXCLUIR</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        const btnMobile = document.querySelector('.hamburger-btn');
        const sidebar = document.querySelector('#sidebar');

        if(btnMobile) {
            btnMobile.addEventListener('click', () => {
                sidebar.classList.toggle('open');
            });
        }

        const links = document.querySelectorAll('.nav-links a');
        links.forEach(link => {
            link.addEventListener('click', () => {
                sidebar.classList.remove('open');
            });
        });
    </script>
</body>
</html>
