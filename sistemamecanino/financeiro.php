<?php 
require_once('conexao/conexao.php');

?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Financeiro</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/financeiro.css">
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
            <li><a href="historico-veiculos.php">Histórico de Veículos</a></li>
            <li><a href="financeiro.php" class="active">Financeiro</a></li>
            <li><a href="relatorios.php">Relatórios</a></li>
            <li><a href="minha-conta.php">Minha conta</a></li>
            <li><a href="index.php" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="financeiro-container">
            <h2 class="titulo-sessao-esquerda">FINANCEIRO</h2>

            <div class="cards-resumo">
                <div class="card-fin">
                    <span class="label-fin">SALDO ATUAL</span>
                    <span class="valor-fin saldo">R$ 6.130,00</span>
                </div>
                <div class="card-fin">
                    <span class="label-fin">A RECEBER</span>
                    <span class="valor-fin receber">R$ 6.980,00</span>
                </div>
                <div class="card-fin">
                    <span class="label-fin">A PAGAR</span>
                    <span class="valor-fin pagar">R$ 1.150,00</span>
                </div>
            </div>

            <div class="tabela-container">
                <table>
                    <thead>
                        <tr>
                            <th>DATA</th>
                            <th>CLIENTE</th>
                            <th>DESCRIÇÃO</th>
                            <th>VALOR</th>
                            <th>STATUS</th>
                            <th style="text-align: center;">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-label="DATA">30/03</td>
                            <td data-label="CLIENTE">Marcos Silva</td>
                            <td data-label="DESCRIÇÃO">Troca de Óleo (Gol G8)</td>
                            <td data-label="VALOR">R$ 350,00</td>
                            <td data-label="STATUS"><span class="status-pago">PAGO</span></td>
                            <td data-label="AÇÕES" class="td-acoes">
                                <a href="excluir-financeiro.php" class="btn-excluir-vinho">EXCLUIR</a>
                            </td>
                        </tr>
                        <tr>
                            <td data-label="DATA">30/03</td>
                            <td data-label="CLIENTE">Ana Souza</td>
                            <td data-label="DESCRIÇÃO">Filtros e Velas</td>
                            <td data-label="VALOR">R$ 480,00</td>
                            <td data-label="STATUS"><span class="status-aguard">AGUARD.</span></td>
                            <td data-label="AÇÕES" class="td-acoes">
                                <a href="excluir-financeiro.php" class="btn-excluir-vinho">EXCLUIR</a>
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
