<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Minha Conta (Recepção)</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/minha-conta.css">
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
                <span class="role-text" style="color: #3399ff;">RECEPCIONISTA</span>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="recep.php">Painel de Gestão</a></li>
            <li><a href="cadastrocliente-recep.php">Cadastro Cliente</a></li>
            <li><a href="cadastroveiculo-recep.php">Cadastro Veículo</a></li>
            <li><a href="ordens-recep.php">Ordens de Serviços</a></li> 
            <li><a href="historico-veiculos-recep.php">Histórico de Veículos</a></li>
            <li><a href="minha-conta-recep.php" class="active">Minha conta</a></li> 
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
                <h2>RECEPCIONISTA</h2>
                <span class="badge-admin" style="background-color: #3399ff;">Atendimento</span>
            </div>

            <div class="data-table-section">
                <div class="data-row">
                    <div class="data-label">USUÁRIO</div>
                    <div class="data-value">Mariana Souza</div>
                </div>
                <div class="data-row">
                    <div class="data-label">E-MAIL PRINCIPAL</div>
                    <div class="data-value">mariana@autorepair.com</div>
                </div>
                <div class="data-row">
                    <div class="data-label">CARGO</div>
                    <div class="data-value">RECEPÇÃO</div>
                </div>
            </div>

            <a href="configuracoes-recep.php" class="btn-show-modal" style="text-decoration: none; display: block; text-align: center;">
                ⚙️ CONFIGURAÇÕES DA CONTA
            </a>
        </div>
    </main>

    <script>
        // Lógica do Menu Lateral Responsivo
        const btnMobile = document.querySelector('.hamburger-btn');
        const sidebar = document.querySelector('#sidebar');
        
        if (btnMobile) {
            btnMobile.addEventListener('click', () => sidebar.classList.toggle('open'));
        }

        // Fecha o menu lateral ao clicar em um link
        const links = document.querySelectorAll('.nav-links a');
        links.forEach(link => {
            link.addEventListener('click', () => {
                sidebar.classList.remove('open');
            });
        });
    </script>
</body>
</html>