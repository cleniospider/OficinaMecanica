<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Editar Cliente</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/editar_cliente.css">
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
        <div class="container-os">
            
            <h2 class="titulo-os">EDITAR CADASTRO <span class="n-os">DE CLIENTE</span></h2>

            <div class="caixa-gerenciar">
                <form class="form-os" action="cadastrocliente.php" method="POST">
                    
                    <div class="campo-grupo">
                        <label>Nome Completo:</label>
                        <input type="text" value="Marcos Silva" name = "nome" required>
                    </div>

                    <div class="campo-grupo">
                        <label>CPF / CNPJ:</label>
                        <input type="text" id="cpf_cnpj" name = "cpf" value="123.456.789-00" required maxlength="18">
                    </div>

                    <div class="linha-dupla">
                        <div class="campo-grupo">
                            <label>Telefone / WhatsApp:</label>
                            <input type="text" id="telefone" name = "telefone" value="(11) 98888-7777" required maxlength="15">
                        </div>

                        <div class="campo-grupo">
                            <label>E-mail:</label>
                            <input type="email" value="marcos@email.com">
                        </div>
                    </div>

                    <div class="botoes-os">
                        <button type="submit" class="btn-finalizar-os">ATUALIZAR CADASTRO</button>
                        <a href="cadastrocliente.html" class="btn-voltar-os">CANCELAR</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Máscara de Telefone ( (11) 99999-9999 )
        const handlePhone = (event) => {
            let input = event.target;
            input.value = phoneMask(input.value);
        }

        const phoneMask = (value) => {
            if (!value) return "";
            value = value.replace(/\D/g, '');
            value = value.replace(/(\d{2})(\d)/, "($1) $2");
            value = value.replace(/(\d)(\d{4})$/, "$1-$2");
            return value;
        }

        // Máscara de CPF / CNPJ
        const handleCpfCnpj = (event) => {
            let input = event.target;
            let value = input.value.replace(/\D/g, '');
            
            if (value.length <= 11) {
                // CPF: 000.000.000-00
                value = value.replace(/(\d{3})(\d)/, "$1.$2");
                value = value.replace(/(\d{3})(\d)/, "$1.$2");
                value = value.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
            } else {
                // CNPJ: 00.000.000/0000-00
                value = value.replace(/^(\d{2})(\d)/, "$1.$2");
                value = value.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
                value = value.replace(/\.(\d{3})(\d)/, ".$1/$2");
                value = value.replace(/(\d{4})(\d)/, "$1-$2");
            }
            input.value = value;
        }

        // Aplicando os eventos nos inputs
        document.getElementById('telefone').addEventListener('keyup', handlePhone);
        document.getElementById('cpf_cnpj').addEventListener('keyup', handleCpfCnpj);
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
