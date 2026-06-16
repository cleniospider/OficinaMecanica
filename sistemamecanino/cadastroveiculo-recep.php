<?php 
require_once('conexao/conexao.php');

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Veículos Cadastrados (Recepcionista)</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/ordens.css">
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
                <span class="role-text" style="color: #3399ff;">RECEPCIONISTA</span>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="recep.php">Painel de Gestão</a></li>
            <li><a href="cadastrocliente-recep.php">Cadastro Cliente</a></li>
            <li><a href="cadastroveiculo-recep.php" class="active">Cadastro Veículo</a></li>
            <li><a href="ordens-recep.php">Ordens de Serviços</a></li> 
            <li><a href="historico-veiculos-recep.php">Histórico de Veículos</a></li>
            <li><a href="minha-conta-recep.php">Minha conta</a></li> 
            <li><a href="index.php" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="orders-container">
            <div class="orders-header">
                <h2>Veículos Cadastrados - Recepção</h2>
                <div class="search-box">
                    <input type="text" id="searchVeiculo" placeholder="Pesquisar por placa ou modelo...">
                </div>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>MARCA</th>
                            <th>MODELO</th>
                            <th>PLACA</th>
                            <th>ANO</th>
                            <th>COR</th>
                            <th>PROPRIETÁRIO</th>
                            <th>AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-label="MARCA">HONDA</td>
                            <td data-label="MODELO"><strong>CBR 600RR</strong></td>
                            <td data-label="PLACA" class="placa-texto">ABC-1234</td>
                            <td data-label="ANO">2020</td>
                            <td data-label="COR">Vermelho</td>
                            <td data-label="PROPRIETÁRIO">Marcos Silva</td>
                            <td data-label="AÇÕES">
                                <div class="acoes-flex">
                                    <a href="editar-veiculo-recep.php" class="btn-editar">EDITAR</a>
                                    <a href="excluir-veiculo-recep.php" class="btn-excluir">EXCLUIR</a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td data-label="MARCA">TOYOTA</td>
                            <td data-label="MODELO"><strong>COROLLA</strong></td>
                            <td data-label="PLACA" class="placa-texto">DEF-5678</td>
                            <td data-label="ANO">2019</td>
                            <td data-label="COR">Prata</td>
                            <td data-label="PROPRIETÁRIO">José Costa</td>
                            <td data-label="AÇÕES">
                                <div class="acoes-flex">
                                    <a href="editar-veiculo-recep.php" class="btn-editar">EDITAR</a>
                                    <a href="excluir-veiculo-recep.php" class="btn-excluir">EXCLUIR</a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="area-botao-novo">
                <a href="novo-veiculo-recep.php" class="btn-nova-ordem btn-espacado">+ NOVO VEÍCULO</a>
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

        if(btnMobile) {
            btnMobile.addEventListener('click', () => {
                sidebar.classList.toggle('open');
            });
        }

        const links = document.querySelectorAll('.nav-links a');
        links.forEach(link => {
            link.addEventListener('click', () => {
                sidebar.classList.remove('open');
            });
        });

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