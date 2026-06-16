<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Detalhes (Recepção)</title>
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
                <span class="role-text" style="color: #3399ff;">RECEPCIONISTA</span>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="recep.php">Painel de Gestão</a></li>
            <li><a href="cadastrocliente-recep.php">Cadastro Cliente</a></li>
            <li><a href="cadastroveiculo-recep.php">Cadastro Veículo</a></li>
            <li><a href="ordens-recep.php">Ordens de Serviços</a></li> 
            <li><a href="historico-veiculos-recep.php" class="active">Histórico de Veículos</a></li>
            <li><a href="minha-conta-recep.php">Minha conta</a></li> 
            <li><a href="index.php" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="container-detalhes">
            
            <div class="header-topo">
                <h2 class="titulo-pagina">Análise de Atendimento - Recepção</h2>
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
                    <a href="historico-veiculos-recep.php" class="btn-voltar-simples">VOLTAR</a>
                </div>
            </div>
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
        
        if(btnMobile && sidebar) {
            btnMobile.addEventListener('click', () => sidebar.classList.toggle('open'));
        }

        // Fecha o menu ao clicar em um link
        const links = document.querySelectorAll('.nav-links a');
        links.forEach(link => {
            link.addEventListener('click', () => {
                sidebar.classList.remove('open');
            });
        });

        // Lógica do Modal de Conta
        const linkConta = document.querySelector('a[style*="cursor:pointer"]'); 
        const modal = document.querySelector('#modal-conta');
        const btnFechar = document.querySelector('.btn-fechar-modal');
        const btnX = document.querySelector('.close-btn');

        if(linkConta) {
            linkConta.addEventListener('click', (e) => {
                e.preventDefault();
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

        window.addEventListener('click', (e) => {
            if (e.target == modal) {
                modal.style.display = 'none';
            }
        });
    </script>
</body>
</html>