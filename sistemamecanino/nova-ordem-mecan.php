<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Nova Ordem (Mecânico)</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/ordens.css">
    <link rel="stylesheet" href="css/nova-ordem.css">
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
                <span class="role-text" style="color: #ffaa00;">MECÂNICO</span>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="mecan.php">Painel de Gestão</a></li>
            <li><a href="ordens-mecanico.php" class="active">Ordens de Serviços</a></li>
            <li><a href="estoque-critico-mecan.php">Estoque de Peças</a></li>
            <li><a href="historico-veiculos.php">Histórico de Veículos</a></li>
            <li><a href="minha-conta-mecan.php">Minha conta</a></li>
            <li><a href="index.php" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="orders-container">
            <div class="os-header-detalhe">
                <h2>NOVA ORDEM <span class="text-red">DE SERVIÇO</span></h2>
            </div>
    
            <form action="ordens-mecanico.php" class="form-nova-ordem">
                
                <div class="form-header-info">
                    <div class="grupo-input">
                        <label class="label-padrao">Cliente:</label>
                        <input type="text" placeholder="Nome do Cliente" required>
                    </div>
                    <div class="grupo-input">
                        <label class="label-padrao">Veículo:</label>
                        <input type="text" placeholder="Ex: CBR 600RR" required>
                    </div>
                    <div class="grupo-input">
                        <label class="label-padrao">Placa:</label>
                        <input type="text" class="placa-field" placeholder="ABC-1234" maxlength="8" required style="text-transform: uppercase;">
                    </div>
                </div>
        
                <div class="form-corpo-ordem">
                    
                    <div class="grupo-input-dark">
                        <label>Problema:</label>
                        <textarea rows="3" placeholder="Descreva o problema constatado..."></textarea>
                    </div>

                    <div class="grupo-input-dark">
                        <label>Serviços:</label>
                        <textarea rows="3" placeholder="Serviços a realizar..."></textarea>
                    </div>
            
                    <div class="grupo-input-dark">
                        <label>Peças usadas:</label>
                        <textarea rows="2" placeholder="Lista de peças e componentes..."></textarea>
                    </div>

                    <div class="flex-row">
                        <div class="grupo-input-dark">
                            <label>Valor total:</label>
                            <input type="text" class="input-valor-dark money-mask" placeholder="R$ 0,00">
                        </div>
                        <div class="grupo-input-dark">
                            <label>Status:</label>
                            <select class="select-dark">
                                <option value="ativo" selected>Ativo</option>
                                <option value="finalizado">Finalizado</option>
                                <option value="parado">Parado</option>
                            </select>
                        </div>
                    </div>
            
                    <div class="botoes-acao">
                        <button type="submit" class="btn-os btn-salvar-red">SALVAR ALTERAÇÕES</button>
                        <a href="ordens-mecanico.php" class="btn-os btn-voltar-dark">CANCELAR</a>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script>
        // --- SCRIPT DO MENU LATERAL (ORIGINAL) ---
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

        // --- MÁSCARA MONETÁRIA (VALOR TOTAL) ---
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

        // --- MÁSCARA DE PLACA DE VEÍCULO (PADRÃO ANTIGO E MERCOSUL) ---
        const inputPlaca = document.querySelector('.placa-field');

        if (inputPlaca) {
            inputPlaca.addEventListener('input', (e) => {
                let value = e.target.value;
                
                // Remove caracteres especiais
                value = value.replace(/[^a-zA-Z0-9]/g, '');
                
                // Insere o hífen na quarta posição
                if (value.length > 3) {
                    value = value.substring(0, 3) + '-' + value.substring(3, 7);
                }
                
                e.target.value = value.toUpperCase();
            });
        }
    </script>
</body>
</html>