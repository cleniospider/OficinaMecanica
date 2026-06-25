<?php 
require_once('conexao/conexao.php');

// Proteção de sessão
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$erro = "";
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: historico-veiculos-mecan.php");
    exit;
}

// Verificar se OS existe e está finalizada
try {
    $stmt = $pdo->prepare("SELECT id FROM OS WHERE id = ? AND status = 'finalizado'");
    $stmt->execute([$id]);
    $os = $stmt->fetch();

    if (!$os) {
        header("Location: historico-veiculos-mecan.php");
        exit;
    }
} catch (PDOException $e) {
    die("Erro ao buscar dados do banco: " . $e->getMessage());
}

// Processar POST de exclusão
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $pdo->beginTransaction();

        // 1. Excluir lançamentos no Financeiro
        $pdo->prepare("DELETE FROM Financeiro WHERE OS_id = ?")->execute([$id]);

        // 2. Excluir relacionamentos (peças e serviços)
        $pdo->prepare("DELETE FROM servicos_has_OS WHERE OS_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM pecas_na_OS WHERE OS_id = ?")->execute([$id]);

        // 3. Excluir a OS
        $pdo->prepare("DELETE FROM OS WHERE id = ?")->execute([$id]);

        $pdo->commit();
        header("Location: historico-veiculos-mecan.php");
        exit;
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $erro = "Erro ao excluir o registro de histórico: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Excluir Histórico (Mecânico)</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/excluir-historico.css">
    <style>
        .alert-error {
            background-color: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
            border: 1px solid #e74c3c;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
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
                <span class="role-text" style="color: #ffaa00;">MECÂNICO</span>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="mecan.php">Painel de Gestão</a></li>
            <li><a href="ordens.php">Ordens de Serviços</a></li>
            <li><a href="servicos.php">Serviços</a></li>
            <li><a href="estoque-critico-mecan.php">Estoque de Peças</a></li>
            <li><a href="historico-veiculos-mecan.php" class="active">Histórico de Veículos</a></li>
            <li><a href="minha-conta.php">Minha conta</a></li>
            <li><a href="index.php?logout=1" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="historico-container">
            <h2 class="titulo-sessao">EXCLUIR HISTÓRICO</h2>

            <?php if (!empty($erro)): ?>
                <div class="alert-error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <div class="caixa-exclusao">
                <div class="icone-alerta">⚠️</div>
                <h3>Tem certeza que deseja excluir o histórico da OS #<?= htmlspecialchars($id) ?>?</h3>
                <p class="aviso-texto">Esta ação removerá permanentemente os dados desta manutenção e lançamentos financeiros relacionados.</p>
                
                <form method="POST" class="form-exclusao">
                    <div class="botoes-acao-excluir">
                        <a href="historico-veiculos-mecan.php" class="btn-cancelar-exclusao">CANCELAR</a>
                        <button type="submit" class="btn-confirmar-exclusao">SIM, EXCLUIR</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        const btnMobile = document.querySelector('.hamburger-btn');
        const sidebar = document.querySelector('#sidebar');
        if(btnMobile && sidebar) {
            btnMobile.addEventListener('click', () => sidebar.classList.toggle('open'));
        }
    </script>
</body>
</html>