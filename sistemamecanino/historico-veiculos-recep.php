<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Histórico (Recepcionista)</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/historico-veiculo.css">
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
        <div class="orders-container">
            <div class="orders-header">
                <h2 class="titulo-pagina">Histórico de Atendimentos - Recepção</h2>
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Pesquisar por cliente ou OS...">
                </div>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Nº OS</th>
                            <th>PROPRIETÁRIO</th>
                            <th>DATA</th>
                            <th>STATUS</th>
                            <th>AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-label="Nº OS">#1025</td>
                            <td data-label="PROPRIETÁRIO">Marcos Silva</td>
                            <td data-label="DATA">14/05/2026</td>
                            <td data-label="STATUS">
                                <span class="status-dot dot-finalizado"></span> Finalizado
                            </td>
                            <td data-label="AÇÕES">
                                <div class="acoes-flex">
                                    <a href="detalhes-historico-recep.php" class="btn-editar">ANALISAR</a>
                                    <a href="excluir-historico-recep.php" class="btn-excluir-vinho">EXCLUIR</a>
                                </div>
                            </td>
                        </tr>
                    
                        <tr>
                            <td data-label="Nº OS">#1024</td>
                            <td data-label="PROPRIETÁRIO">José Costa</td>
                            <td data-label="DATA">12/05/2026</td>
                            <td data-label="STATUS">
                                <span class="status-dot dot-finalizado"></span> Finalizado
                            </td>
                            <td data-label="AÇÕES">
                                <div class="acoes-flex">
                                    <a href="detalhes-historico-recep.php" class="btn-editar">ANALISAR</a>
                                    <a href="excluir-historico-recep.php" class="btn-excluir-vinho">EXCLUIR</a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
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

        const links = document.querySelectorAll('.nav-links a');
        links.forEach(link => {
            link.addEventListener('click', () => {
                sidebar.classList.remove('open');
            });
        });

        // Controle do Modal "Minha Conta"
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