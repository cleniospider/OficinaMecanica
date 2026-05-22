<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Estoque Baixo</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/ordens.css">
    <link rel="stylesheet" href="css/estoque.css">
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
                <span class="role-text">ADMINISTRADOR</span>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="admin.php">Painel de Gestão</a></li>
            <li><a href="cadastrocliente.php">Cadastro Cliente</a></li>
            <li><a href="cadastroveiculo.php">Cadastro Veículo</a></li>
            <li><a href="ordens.php">Ordens de Serviços</a></li>
            <li><a href="estoque-critico.php" class="active">Estoque de Peças</a></li>
            <li><a href="historico-veiculos.php">Histórico de Veículos</a></li>
            <li><a href="financeiro.php">Financeiro</a></li>
            <li><a href="relatorios.php">Relatórios</a></li>
            <li><a href="minha-conta.php">Minha conta</a></li>
            <li><a href="index.php" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="orders-container">
            <div class="orders-header">
                <h2>Controle de Estoque</h2>
            </div>

            <div class="estoque-wrapper">
                <div class="estoque-controles">
                    <div class="filtros-grupo">
                        <a href="estoque-critico.php" class="btn-f b-critico">CRÍTICO</a>
                        <a href="estoque-baixo.php" class="btn-f b-baixo ativo">BAIXO</a>
                        <a href="estoque-ok.php" class="btn-f b-ok">OK</a>
                    </div>

                    <div class="busca-acoes">
                        <input type="text" class="input-busca" placeholder=" Pesquisar por nome...">
                        <a href="nova-peca.php" class="btn-nova-peca">+ NOVA PEÇA</a>
                    </div>
                </div>

                <div class="lista-pecas">
                    <div class="peca-card">
                        <div class="peca-info-esquerda">
                            <div class="peca-img-caixa"><img src="img/filtro.webp"></div>
                            <div class="peca-texto">
                                <h4>Filtro de Ar Condicionado (Cabine)</h4>
                                <p>R$ 50,00</p>
                            </div>
                        </div>
                        <div class="peca-qtd-direita">5 un.</div>
                    </div>

                    <div class="peca-card">
                        <div class="peca-info-esquerda">
                            <div class="peca-img-caixa"><img src="img/cilindro.jpeg"></div>
                            <div class="peca-texto">
                                <h4>Cilindro de roda traseiro</h4>
                                <p>R$ 95,00</p>
                            </div>
                        </div>
                        <div class="peca-qtd-direita">3 un.</div>
                    </div>

                    <div class="peca-card">
                        <div class="peca-info-esquerda">
                            <div class="peca-img-caixa"><img src="img/vela.webp"></div>
                            <div class="peca-texto">
                                <h4>Vela de Ignição</h4>
                                <p>R$ 50,00</p>
                            </div>
                        </div>
                        <div class="peca-qtd-direita">8 un.</div>
                    </div>

                    <div class="peca-card">
                        <div class="peca-info-esquerda">
                            <div class="peca-img-caixa"><img src="img/terminal.jpeg"></div>
                            <div class="peca-texto">
                                <h4>Terminal de Direção (Lado Direito)</h4>
                                <p>R$ 40,00</p>
                            </div>
                        </div>
                        <div class="peca-qtd-direita">4 un.</div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script>
        const btnMobile = document.querySelector('.hamburger-btn');
        const sidebar = document.querySelector('#sidebar');

        btnMobile.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

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
