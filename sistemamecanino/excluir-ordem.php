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
    header("Location: ordens.php");
    exit;
}

// Verificar se OS existe
try {
    $stmt = $pdo->prepare("SELECT id FROM OS WHERE id = ?");
    $stmt->execute([$id]);
    $os = $stmt->fetch();

    if (!$os) {
        header("Location: ordens.php");
        exit;
    }
} catch (PDOException $e) {
    die("Erro ao buscar dados do banco: " . $e->getMessage());
}

// Processar POST de exclusão
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $pdo->beginTransaction();

        // 1. Excluir lançamentos relacionados no Financeiro primeiro
        $stmt_del_fin = $pdo->prepare("DELETE FROM Financeiro WHERE OS_id = ?");
        $stmt_del_fin->execute([$id]);

        // 2. Excluir relacionamentos (peças e serviços)
        $pdo->prepare("DELETE FROM estoque_pecas_has_OS WHERE OS_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM estoque_pecas_has_OS1 WHERE OS_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM servicos_has_OS WHERE OS_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM pecas_na_OS WHERE OS_id = ?")->execute([$id]);

        // 3. Excluir a OS
        $stmt_del_os = $pdo->prepare("DELETE FROM OS WHERE id = ?");
        $stmt_del_os->execute([$id]);

        $pdo->commit();
        header("Location: ordens.php");
        exit;
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $erro = "Erro ao excluir a Ordem de Serviço: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Excluir Ordem</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/excluir-ordem.css"> 
    <style>
        .alert-error {
            background-color: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
            border: 1px solid #e74c3c;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            width: 100%;
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
        <div class="ordem-exclusao-container">
            <h2 class="titulo-sessao">EXCLUIR ORDEM DE SERVIÇO</h2>

            <?php if (!empty($erro)): ?>
                <div class="alert-error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <div class="caixa-exclusao">
                <div class="icone-alerta">⚠️</div>
                <h3>Tem certeza que deseja excluir a OS #<?= htmlspecialchars($id) ?>?</h3>
                <p class="aviso-texto">Esta ação não poderá ser desfeita e os dados do serviço e lançamentos financeiros associados serão perdidos.</p>
                
                <form method="POST" class="form-exclusao">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id ?? $_GET['id']) ?>">
                    <div class="botoes-acao-excluir">
                        <a href="ordens.php" class="btn-cancelar-exclusao">CANCELAR</a>
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
