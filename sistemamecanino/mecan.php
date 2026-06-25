<?php 
require_once('conexao/conexao.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil'] !== 'Mecanico') {
    header("Location: index.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Mecânico</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

    <header class="top-header">
        <button class="hamburger-btn">
            <span></span>
            <span></span>
            <span></span>
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
            <li><a href="mecan.php" class="active">Painel de Gestão</a></li>
            <li><a href="ordens.php">Ordens de Serviços</a></li>
            <li><a href="servicos.php">Serviços</a></li>
            <li><a href="estoque-critico-mecan.php">Estoque de Peças</a></li>
            <li><a href="historico-veiculos-mecan.php">Histórico de Veículos</a></li>
            <li><a href="minha-conta-mecan.php">Minha conta</a></li>
            <li><a href="index.php?logout=1" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content" id="main-content">
        <div class="banner-area">
            <img src="img/CARRO.jpg" alt="Carro Banner" class="banner-image">
        </div>

        <div class="header-titles">
            <h1>Painel de Gestão</h1>
            <p>Controle total da sua oficina.</p>
        </div>

        <div class="dashboard-grid">
            <a href="ordens.php" class="grid-card">ORDENS DE SERVIÇOS</a>
            <a href="servicos.php" class="grid-card">SERVIÇOS</a>
            <a href="estoque-critico-mecan.php" class="grid-card">ESTOQUE DE PEÇAS</a>
            <a href="historico-veiculos-mecan.php" class="grid-card">HISTÓRICO DE VEÍCULOS</a>
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
    </script>
</body>
</html>