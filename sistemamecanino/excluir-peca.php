<?php 
require_once('conexao/conexao.php');

// Proteção de sessão
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_perfil'], ['Admin', 'Mecanico'])) {
    header("Location: index.php");
    exit;
}

$erro = "";
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: estoque-critico.php");
    exit;
}

// Buscar dados da peça
$stmt = $pdo->prepare("SELECT * FROM pecas WHERE id = ?");
$stmt->execute([$id]);
$peca = $stmt->fetch();

if (!$peca) {
    header("Location: estoque-critico.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Excluir a peça
        // (Nota: em um sistema real, talvez devêssemos verificar se a peça já foi usada em alguma OS
        // e não permitir a exclusão, ou apenas desativá-la. Para simplificar, faremos o DELETE).
        $stmt_check = $pdo->prepare("SELECT pecas_id FROM pecas_na_OS WHERE pecas_id = ? LIMIT 1");
        $stmt_check->execute([$id]);
        if ($stmt_check->fetch()) {
            $erro = "Não é possível excluir esta peça pois ela já foi vinculada a Ordens de Serviço.";
        } else {
            $stmt_del = $pdo->prepare("DELETE FROM pecas WHERE id = ?");
            $stmt_del->execute([$id]);
            header("Location: estoque-critico.php"); // volta para a tela de estoque
            exit;
        }
    } catch (PDOException $e) {
        $erro = "Erro ao excluir peça: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Excluir Peça</title>
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
            <li><a href="<?= $_SESSION['usuario_perfil'] === 'Admin' ? 'admin.php' : 'mecan.php' ?>">Painel de Gestão</a></li>
            <li><a href="cadastrocliente.php">Cadastro Cliente</a></li>
            <li><a href="cadastroveiculo.php">Cadastro Veículo</a></li>
            <li><a href="ordens.php">Ordens de Serviços</a></li>
            <li><a href="estoque-critico.php" class="active">Estoque de Peças</a></li>
            <li><a href="historico-veiculos.php">Histórico de Veículos</a></li>
            <li><a href="financeiro.php">Financeiro</a></li>
            <li><a href="relatorios.php">Relatórios</a></li>
            <li><a href="minha-conta.php">Minha conta</a></li>
            <li><a href="index.php?logout=1" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="cliente-exclusao-container">
            <h2 class="titulo-sessao">EXCLUIR PEÇA DO ESTOQUE</h2>

            <?php if (!empty($erro)): ?>
                <div class="alert-error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <div class="caixa-exclusao">
                <div class="icone-alerta">⚠️</div>
                <h3>Tem certeza que deseja excluir a peça "<?= htmlspecialchars($peca['nome']) ?>"?</h3>
                <p class="aviso-texto">Esta ação removerá a peça do catálogo do sistema. Você não poderá excluir caso ela já tenha sido vinculada a alguma Ordem de Serviço.</p>
                
                <form method="POST" class="form-exclusao">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id ?? $_GET['id']) ?>">
                    <div class="botoes-acao-excluir">
                        <a href="estoque-critico.php" class="btn-cancelar-exclusao">CANCELAR</a>
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
