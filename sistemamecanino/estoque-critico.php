<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Estoque</title>
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
            <li><a href="admin.html">Painel de Gestão</a></li>
            <li><a href="cadastrocliente.html">Cadastro Cliente</a></li>
            <li><a href="cadastroveiculo.html">Cadastro Veículo</a></li>
            <li><a href="ordens.html">Ordens de Serviços</a></li>
            <li><a href="estoque-critico.html" class="active">Estoque de Peças</a></li>
            <li><a href="historico-veiculos.html">Histórico de Veículos</a></li>
            <li><a href="financeiro.html">Financeiro</a></li>
            <li><a href="relatorios.html">Relatórios</a></li>
            <li><a href="minha-conta.html">Minha conta</a></li>
            <li><a href="index.html" class="logout-link">Sair</a></li>
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
                        <a href="estoque-critico.php" class="btn-f b-critico ativo">CRÍTICO</a>
                        <a href="estoque-baixo.php" class="btn-f b-baixo">BAIXO</a>
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
                            <div class="peca-img-caixa"><img src="img/pastilha.jpg"></div>
                            <div class="peca-texto">
                                <h4>Pastilha de Freio</h4>
                                <p>R$ 89,00</p>
                            </div>
                        </div>
                        <div class="peca-qtd-direita">1 un.</div>
                    </div>

                    <div class="peca-card">
                        <div class="peca-info-esquerda">
                            <div class="peca-img-caixa"><img src="img/oleo.webp"></div>
                            <div class="peca-texto">
                                <h4>Óleo Mobil 4T 20W50</h4>
                                <p>R$ 45,00</p>
                            </div>
                        </div>
                        <div class="peca-qtd-direita">2 un.</div>
                    </div>

                    <div class="peca-card">
                        <div class="peca-info-esquerda">
                            <div class="peca-img-caixa"><img src="img/juntashomocinetica.png"></div>
                            <div class="peca-texto">
                                <h4>Juntas homocinéticas</h4>
                                <p>R$ 145,00</p>
                            </div>
                        </div>
                        <div class="peca-qtd-direita">0 un.</div>
                    </div>

                    <div class="peca-card">
                        <div class="peca-info-esquerda">
                            <div class="peca-img-caixa"><img src="img/sapata.webp"></div>
                            <div class="peca-texto">
                                <h4>Jg. de sapata freio traseiro MG-613</h4>
                                <p>R$ 110,00</p>
                            </div>
                        </div>
                        <div class="peca-qtd-direita">1 un.</div>
                    </div>

                    <div class="peca-card">
                        <div class="peca-info-esquerda">
                            <div class="peca-img-caixa"><img src="img/kitrolas.webp"></div>
                            <div class="peca-texto">
                                <h4>Kit rolamento rola dianteira cg 125</h4>
                                <p>R$ 75,00</p>
                            </div>
                        </div>
                        <div class="peca-qtd-direita">1 un.</div>
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
