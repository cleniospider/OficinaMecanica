<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Minha Conta (Mecânico)</title>
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
                <span class="role-text" style="color: #ffaa00;">MECÂNICO</span>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="mecan.php">Painel de Gestão</a></li>
            <li><a href="ordens-mecanico.php">Ordens de Serviços</a></li>
            <li><a href="estoque-critico-mecan.php">Estoque de Peças</a></li>
            <li><a href="historico-veiculos-mecan.php">Histórico de Veículos</a></li>
            <li><a href="minha-conta-mecan.php" class="active">Minha conta</a></li>
            <li><a href="index.php" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="account-container">
            <div class="account-header">
                <h1>Perfil do Usuário</h1>
            </div>

            <div class="profile-main-card">
                <img src="img/download.png" class="profile-avatar-large">
                <h2 style="color: #ffaa00;">MECÂNICO</h2>
                <span class="badge-admin" style="background-color: #ffaa00; color: #000;">Oficina</span>
            </div>

            <div class="data-table-section">
                <div class="data-row">
                    <div class="data-label">USUÁRIO</div>
                    <div class="data-value">Carlos Souza</div>
                </div>
                <div class="data-row">
                    <div class="data-label">E-MAIL PRINCIPAL</div>
                    <div class="data-value">carlos.mecanico@autorepair.com</div>
                </div>
                <div class="data-row">
                    <div class="data-label">CARGO</div>
                    <div class="data-value">MECÂNICO CHEFE</div>
                </div>
            </div>

            <a href="configuracoes-mecan.php" class="btn-show-modal" style="text-decoration: none; display: block; text-align: center;">
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

        // Fecha o menu ao clicar em links (importante para mobile)
        const links = document.querySelectorAll('.nav-links a');
        links.forEach(link => {
            link.addEventListener('click', () => {
                sidebar.classList.remove('open');
            });
        });
    </script>
</body>
</html>