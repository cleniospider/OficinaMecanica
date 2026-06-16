<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Gerenciar OS (Recepção)</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/ordens.css">
    <link rel="stylesheet" href="css/editar-ordem.css">
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
            <li><a href="ordens-recep.php" class="active">Ordens de Serviços</a></li> 
            <li><a href="historico-veiculos-recep.php">Histórico de Veículos</a></li>
            <li><a href="minha-conta-recep.php" class="link-minha-conta">Minha conta</a></li>  
            <li><a href="index.php" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="orders-container">
            <div class="os-header-detalhe">
                <h2>ORDEM DE SERVIÇO <span class="text-red">#1025</span> - RECEPÇÃO</h2>
            </div>

            <div class="form-header-info">
                <div class="grupo-input">
                    <label class="label-padrao">Cliente:</label>
                    <input type="text" value="Marcos Silva" required>
                </div>
                <div class="grupo-input">
                    <label class="label-padrao">Veículo:</label>
                    <input type="text" value="CBR 600RR" required>
                </div>
                <div class="grupo-input">
                    <label class="label-padrao">Placa:</label>
                    <input type="text" class="placa-field" value="ABC-1234" maxlength="8" required style="text-transform: uppercase;">
                </div>
            </div>

            <form class="form-gerenciar" action="ordens-recep.html">
                <div class="grupo-input-dark">
                    <label>Problema:</label>
                    <textarea rows="3">Vazamento de óleo na suspensão</textarea>
                </div>

                <div class="grupo-input-dark">
                    <label>Serviços:</label>
                    <textarea rows="3">Troca de retentores e fluido</textarea>
                </div>

                <div class="grupo-input-dark">
                    <label>Peças usadas:</label>
                    <textarea rows="2">Retentores Honda, Óleo Motul</textarea>
                </div>

                <div class="flex-row">
                    <div class="grupo-input-dark">
                        <label>Valor total:</label>
                        <input type="text" value="R$ 450,00" class="input-valor-dark money-mask">
                    </div>

                    <div class="grupo-input-dark">
                        <label>Status:</label>
                        <select class="select-dark">
                            <option value="ativo" selected>Ativo</option>
                            <option value="parado">Parado</option>
                            <option value="finalizado">Finalizado</option>
                        </select>
                    </div>
                </div>

                <div class="acoes-os-dark">
                    <button type="submit" class="btn-os btn-salvar-red">SALVAR ALTERAÇÕES</button>
                    <a href="ordens-recep.php" class="btn-os btn-voltar-dark">VOLTAR</a>
                </div>
            </form>
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
        // --- SCRIPT DO MENU LATERAL ---
        const btnMobile = document.querySelector('.hamburger-btn');
        const sidebar = document.querySelector('#sidebar');

        btnMobile.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        const links = document.querySelectorAll('.nav-links a');
        links.forEach(link => {
            link.addEventListener('click', () => {
                sidebar.classList.remove('open');
            });
        });

        // --- LÓGICA DO MODAL DE CONTA ---
        // CORRIGIDO: Agora seleciona pela classe do link
        const linkConta = document.querySelector('.link-minha-conta'); 
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

        // --- SCRIPT: MÁSCARA MONETÁRIA ---
        const inputValor = document.querySelector('.money-mask');

        if (inputValor) {
            inputValor.addEventListener('input', (e) => {
                let value = e.target.value;
                value = value.replace(/\D/g, '');

                if (!value) {
                    e.target.value = '';
                    return;
                }

                value = (parseInt(value) / 100).toFixed(2);

                let valorFormatado = parseFloat(value).toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                });

                e.target.value = valorFormatado;
            });
        }

        // --- SCRIPT: MÁSCARA DE PLACA DE VEÍCULO ---
        const inputPlaca = document.querySelector('.placa-field');

        if (inputPlaca) {
            inputPlaca.addEventListener('input', (e) => {
                let value = e.target.value;
                value = value.replace(/[^a-zA-Z0-9]/g, '');
                
                if (value.length > 3) {
                    value = value.substring(0, 3) + '-' + value.substring(3, 7);
                }
                
                e.target.value = value.toUpperCase();
            });
        }
    </script>
</body>
</html>