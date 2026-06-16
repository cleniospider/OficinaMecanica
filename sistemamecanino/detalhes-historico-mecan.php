<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Detalhes (Mecânico)</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/detalhes-historico.css">
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
            <li><a href="ordens-mecanico.php">Ordens de Serviços</a></li>
            <li><a href="estoque-critico-mecan.php">Estoque de Peças</a></li>
            <li><a href="historico-veiculos-mecan.php" class="active">Histórico de Veículos</a></li>
            <li><a href="minha-conta-mecan.php">Minha conta</a></li>
            <li><a href="index.php" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="container-detalhes">
            
            <div class="header-topo">
                <h2 class="titulo-pagina">Análise de Atendimento</h2>
            </div>

            <div class="cartao-analise">
                <div class="info-secao">
                    <label>CLIENTE</label>
                    <span>Marcos Silva</span>
                </div>
                
                <div class="info-secao">
                    <label>VEÍCULO</label>
                    <span>CBR 600RR</span>
                </div>
                
                <div class="info-secao">
                    <label>PLACA</label>
                    <span class="placa-estilo">ABC-1234</span>
                </div>
                
                <div class="info-secao">
                    <label>PROBLEMA RELATADO</label>
                    <p>Vazamento de óleo na suspensão dianteira (bengalas).</p>
                </div>

                <div class="info-secao">
                    <label>SERVIÇOS REALIZADOS</label>
                    <p>Troca de retentores e fluido de suspensão.</p>
                </div>

                <div class="info-secao">
                    <label>PEÇAS UTILIZADAS</label>
                    <p>Retentores originais Honda, Óleo Motul Fork Oil 10W.</p>
                </div>

                <div class="info-secao">
                    <label>VALOR TOTAL</label>
                    <span class="valor-final">R$ 450,00</span>
                </div>

                <div class="info-secao">
                    <label>STATUS</label>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="dot-finalizado"></span> Finalizado em 14/05/2026
                    </div>
                </div>

                <div class="area-botao-voltar">
                    <a href="historico-veiculos-mecan.php" class="btn-voltar-simples">VOLTAR</a>
                </div>
            </div>
        </div>
    </main>

    <script>
        const btnMobile = document.querySelector('.hamburger-btn');
        const sidebar = document.querySelector('#sidebar');
        if(btnMobile && sidebar) {
            btnMobile.addEventListener('click', () => sidebar.classList.toggle('open'));
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