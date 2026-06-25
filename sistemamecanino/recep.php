<?php 
require_once('conexao/conexao.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil'] !== 'Recepcionista') {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Recepcionista</title>
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
                <span class="role-text" style="color: #3399ff;">RECEPCIONISTA</span>
            </div>
        </div>

        <ul class="nav-links">
            <li><a href="recep.php"class="active">Painel de Gestão</a></li>
            <li><a href="cadastrocliente-recep.php">Cadastro Cliente</a></li>
            <li><a href="cadastroveiculo-recep.php">Cadastro Veículo</a></li>
            <li><a href="ordens.php">Ordens de Serviços</a></li>
            <li><a href="servicos.php">Serviços</a></li>
            <li><a href="historico-veiculos-recep.php">Histórico de Veículos</a></li>
            <li><a href="minha-conta.php">Minha conta</a></li> 
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
            <a href="cadastrocliente-recep.php" class="grid-card">CADASTRO CLIENTE</a>
            <a href="cadastroveiculo-recep.php" class="grid-card">CADASTRO VEÍCULO</a>
            <a href="ordens.php" class="grid-card">ORDENS DE SERVIÇOS</a>
            <a href="servicos.php" class="grid-card">SERVIÇOS</a>
            <a href="historico-veiculos-recep.php" class="grid-card">HISTÓRICO DE VEÍCULOS</a>
        </div>
    </main>

    <div id="modal-conta" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Minha Conta</h2>
            <div class="conta-dados">
                <p><strong>Status:</strong> <span style="color: #00cc44;">Ativo ✔️</span></p>
            </div>
            <button class="btn-fechar-modal">Fechar</button>
        </div>
    </div>

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

        // Lógica do Modal de Conta (Igual ao que você usa nas outras páginas)
        const linkConta = document.querySelector('a[style*="cursor:pointer"]'); 
        const modal = document.querySelector('#modal-conta');
        const btnFechar = document.querySelector('.btn-fechar-modal');
        const btnX = document.querySelector('.close-btn');
    
        if(linkConta) {
            linkConta.addEventListener('click', () => {
                modal.style.display = 'flex';
            });
        }
    
        [btnFechar, btnX].forEach(btn => {
            if(btn) {
                btn.addEventListener('click', () => {
                    modal.style.display = 'none';
                });
            }
        });
    </script>
</body>
</html>