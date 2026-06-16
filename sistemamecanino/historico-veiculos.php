<?php 
require_once('conexao/conexao.php');

?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Histórico</title>
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
                <span class="role-text">ADMINISTRADOR</span>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="admin.php">Painel de Gestão</a></li>
            <li><a href="cadastrocliente.php">Cadastro Cliente</a></li>
            <li><a href="cadastroveiculo.php">Cadastro Veículo</a></li>
            <li><a href="ordens.php">Ordens de Serviços</a></li>
            <li><a href="estoque-critico.php">Estoque de Peças</a></li>
            <li><a href="historico-veiculos.php" class="active">Histórico de Veículos</a></li>
            <li><a href="financeiro.php">Financeiro</a></li>
            <li><a href="relatorios.php">Relatórios</a></li>
            <li><a href="minha-conta.php">Minha conta</a></li>
            <li><a href="index.php" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="orders-container">
            <div class="orders-header">
                <h2 class="titulo-pagina">Histórico de Atendimentos</h2>
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
                                    <a href="detalhes-historico.php" class="btn-editar">ANALISAR</a>
                                    <a href="excluir-historico.php" class="btn-excluir-vinho">EXCLUIR</a>
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
                                    <a href="detalhes-historico.php" class="btn-editar">ANALISAR</a>
                                    <a href="excluir-historico.php" class="btn-excluir-vinho">EXCLUIR</a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
