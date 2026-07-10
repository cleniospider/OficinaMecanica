<?php 
require_once('conexao/conexao.php');

// Proteção de sessão
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

// Buscar dados atualizados do usuário no banco
$usuario = null;
try {
    $stmt = $pdo->prepare("SELECT id, nome_completo, email, perfil FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    $usuario = $stmt->fetch();
} catch (PDOException $e) {
    // fallback para sessão
}

$nome    = $usuario['nome_completo'] ?? $_SESSION['usuario_nome'] ?? 'Usuário';
$email   = $usuario['email']        ?? 'Não informado';
$perfil  = $usuario['perfil']       ?? $_SESSION['usuario_perfil'] ?? 'Usuário';

// URL do painel de acordo com o perfil
$painel = match($perfil) {
    'Admin'         => 'admin.php',
    'Mecanico'      => 'mecan.php',
    'Recepcionista' => 'recep.php',
    default         => 'index.php'
};

$nivel = match($perfil) {
    'Admin'         => 'Nível 1 — Administrador',
    'Mecanico'      => 'Nível 2 — Mecânico',
    'Recepcionista' => 'Nível 3 — Recepcionista',
    default         => 'Nível Indefinido'
};
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Minha Conta</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/minha-conta.css">
</head>
<body>

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
                <span class="role-text"><?= htmlspecialchars(strtoupper($perfil)) ?></span>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="<?= $painel ?>">Painel de Gestão</a></li>
            <?php if ($perfil === 'Admin'): ?>
                <li><a href="bd/lista.php">Gerenciar Usuários</a></li>
            <?php endif; ?>
            <?php if ($perfil !== 'Mecanico'): ?>
                <li><a href="<?= $perfil === 'Recepcionista' ? 'cadastrocliente-recep.php' : 'cadastrocliente.php' ?>">Cadastro Cliente</a></li>
                <li><a href="<?= $perfil === 'Recepcionista' ? 'cadastroveiculo-recep.php' : 'cadastroveiculo.php' ?>">Cadastro Veículo</a></li>
            <?php endif; ?>
            <li><a href="ordens.php">Ordens de Serviços</a></li>
            <li><a href="servicos.php">Serviços</a></li>
            <?php if ($perfil !== 'Recepcionista'): ?>
                <li><a href="estoque-critico.php">Estoque de Peças</a></li>
            <?php endif; ?>
            <li><a href="historico-veiculos.php">Histórico de Veículos</a></li>
            <?php if ($perfil === 'Admin'): ?>
                <li><a href="financeiro.php">Financeiro</a></li>
                <li><a href="relatorios.php">Relatórios</a></li>
            <?php endif; ?>
            <li><a href="minha-conta.php" class="active">Minha Conta</a></li>
            <li><a href="index.php?logout=1" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="account-container">
            <div class="account-header">
                <h1>Perfil do Usuário</h1>
            </div>

            <div class="profile-main-card">
                <img src="img/download.png" class="profile-avatar-large" alt="Avatar">
                <h2><?= htmlspecialchars($nome) ?></h2>
                <span class="badge-admin"><?= htmlspecialchars($nivel) ?></span>
            </div>

            <div class="data-table-section">
                <div class="data-row">
                    <div class="data-label">USUÁRIO</div>
                    <div class="data-value"><?= htmlspecialchars($nome) ?></div>
                </div>
                <div class="data-row">
                    <div class="data-label">E-MAIL</div>
                    <div class="data-value"><?= htmlspecialchars($email) ?></div>
                </div>
                <div class="data-row">
                    <div class="data-label">CARGO</div>
                    <div class="data-value"><?= htmlspecialchars(strtoupper($perfil)) ?></div>
                </div>
                <div class="data-row">
                    <div class="data-label">STATUS</div>
                    <div class="data-value" style="color: #2ecc71; font-weight: bold;">✔ Ativo</div>
                </div>
            </div>

            <a href="<?= $perfil === 'Admin' ? 'configuracoes.php' : ($perfil === 'Mecanico' ? 'configuracoes-mecan.php' : 'configuracoes-recep.php') ?>" class="btn-show-modal" style="text-decoration: none; display: block; text-align: center;">
                ⚙️ CONFIGURAÇÕES DA CONTA
            </a>
        </div>
    </main>

    <script>
        const btnMobile = document.querySelector('.hamburger-btn');
        const sidebar = document.querySelector('#sidebar');
        
        if (btnMobile) {
            btnMobile.addEventListener('click', () => sidebar.classList.toggle('open'));
        }

        const links = document.querySelectorAll('.nav-links a');
        links.forEach(link => {
            link.addEventListener('click', () => sidebar.classList.remove('open'));
        });
    </script>
</body>
</html>
