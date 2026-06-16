<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Excluir Ordem (Mecânico)</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/excluir-ordem.css"> 
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
            <li><a href="mecan.html">Painel de Gestão</a></li>
            <li><a href="ordens-mecanico.php" class="active">Ordens de Serviços</a></li>
            <li><a href="estoque-critico-mecan.php">Estoque de Peças</a></li>
            <li><a href="historico-veiculos-mecan.php">Histórico de Veículos</a></li>
            <li><a href="minha-conta-mecan.php">Minha conta</a></li>
            <li><a href="index.php" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="ordem-exclusao-container">
            <h2 class="titulo-sessao">EXCLUIR ORDEM DE SERVIÇO</h2>

            <div class="caixa-exclusao">
                <div class="icone-alerta">⚠️</div>
                <h3>Tem certeza que deseja excluir esta OS?</h3>
                <p class="aviso-texto">Esta ação não poderá ser desfeita e os dados do serviço serão perdidos.</p>
                
                <form action="ordens-mecanico.php" class="form-exclusao">
                    <div class="botoes-acao-excluir">
                        <a href="ordens-mecanico.php" class="btn-cancelar-exclusao">CANCELAR</a>
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