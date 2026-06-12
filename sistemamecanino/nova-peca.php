<?php 
require_once('conexao/conexao.php');

?>



<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Novo Cliente</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/novo-cliente.css">
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
            <li><a href="cadastrocliente.php" class="active">Cadastro Cliente</a></li>
            <li><a href="cadastroveiculo.php">Cadastro Veículo</a></li>
            <li><a href="ordens.php">Ordens de Serviços</a></li>
            <li><a href="estoque-critico.php">Estoque de Peças</a></li>
            <li><a href="historico-veiculos.php">Histórico de Veículos</a></li>
            <li><a href="financeiro.php">Financeiro</a></li>
            <li><a href="relatorios.php">Relatórios</a></li>
            <li><a href="minha-conta.php">Minha conta</a></li>
            <li><a href="index.php" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="container-form-dark">
            <h2 class="titulo-sessao">CADASTRAR NOVO CLIENTE </span></h2>
            
            <div class="card-dark">
                <form action="cadastrocliente.php" method="POST">
                    
                    <div class="form-row">
                        <div class="grupo-input-dark flex-3">
                            <label>NOME COMPLETO</label>
                            <input type="text" placeholder="Ex: João Silva" required>
                        </div>
                        <div class="grupo-input-dark flex-1">
                            <label>CPF / CNPJ</label>
                            <input type="text" id="cpf" name = "cpf" placeholder="000.000.000-00" maxlength="18" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="grupo-input-dark flex-1">
                            <label>TELEFONE / WHATSAPP</label>
                            <input type="text" id="telefone" name ="telefone" placeholder="(00) 00000-0000" maxlength="15" required>
                        </div>
                        <div class="grupo-input-dark flex-2">
                            <label>E-MAIL</label>
                            <input type="email" placeholder="cliente@email.com">
                        </div>
                    </div>

                    <div class="footer-acoes">
                        <button type="submit" class="btn-acao btn-salvar-os">SALVAR CADASTRO</button>
                        <a href="cadastrocliente.php" class="btn-acao btn-voltar-os">VOLTAR</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Mantive seus scripts de máscara aqui...
        const inputCpf = document.getElementById('cpf');
        const inputTelefone = document.getElementById('telefone');

        inputCpf.addEventListener('input', () => {
            let valor = inputCpf.value.replace(/\D/g, ''); 
            if (valor.length <= 11) {
                valor = valor.replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            } else {
                valor = valor.substring(0, 14).replace(/^(\d{2})(\d)/, '$1.$2').replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3').replace(/\.(\d{3})(\d)/, '.$1/$2').replace(/(\d{4})(\d)/, '$1-$2');
            }
            inputCpf.value = valor;
        });

        inputTelefone.addEventListener('input', () => {
            let valor = inputTelefone.value.replace(/\D/g, '');
            valor = valor.replace(/^(\d{2})(\d)/g, '($1) $2');
            if (valor.length > 13) valor = valor.replace(/(\d{5})(\d)/, '$1-$2');
            else valor = valor.replace(/(\d{4})(\d)/, '$1-$2');
            inputTelefone.value = valor;
        });
    </script>
    <script>
        const btnMobile = document.querySelector('.hamburger-btn');
        const sidebar = document.querySelector('#sidebar');

        // Abre e fecha o menu lateral
        btnMobile.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        // Fecha o menu ao clicar em um link (essencial para mobile)
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

        // Fecha o modal se clicar fora dele
        window.addEventListener('click', (e) => {
            if (e.target == modal) {
                modal.style.display = 'none';
            }
        });
    </script>
</body> </html>
</body>
</html>
