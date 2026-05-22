<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Relatórios</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/relatorios.css">
</head>
<body class="dark-theme">

    <header class="top-header">
        <button class="hamburger-btn" id="btn-menu">
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
            <li><a href="financeiro.php">Financeiro</a></li>
            <li><a href="relatorios.php" class="active">Relatórios</a></li>
            <li><a href="minha-conta.php">Minha conta</a></li>
            <li><a href="index.php" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="relatorios-container">
            <h2 class="titulo-sessao-relatorio">RELATÓRIOS GERENCIAIS</h2>

            <div class="card-grafico">
                <h3>FATURAMENTO MENSAL</h3>
                <canvas id="chartFaturamento"></canvas>
            </div>

            <hr class="divisor">

            <div class="card-grafico">
                <h3>PRODUTIVIDADE POR MECÂNICO</h3>
                <canvas id="chartProdutividade"></canvas>
            </div>

            <hr class="divisor">

            <div class="card-grafico">
                <h3>ESTOQUE DE PEÇAS</h3>
                <div class="pizza-wrapper">
                    <canvas id="chartEstoque"></canvas>
                </div>
            </div>

            <hr class="divisor">

            <div class="card-grafico">
                <h3>TEMPO MÉDIO DE ENTREGA</h3>
                <div class="gauge-container">
                    <canvas id="chartGauge"></canvas>
                    <div class="gauge-value">3.5 <small>DIAS</small></div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Menu Hambúrguer Responsivo
        const btnMenu = document.getElementById('btn-menu');
        const sidebar = document.getElementById('sidebar');

        btnMenu.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        // Gráficos (Chart.js)
        const ctxFaturamento = document.getElementById('chartFaturamento');
        new Chart(ctxFaturamento, {
            type: 'line',
            data: {
                labels: ['Janeiro', 'Fevereiro', 'Março', 'Abril'],
                datasets: [{
                    data: [4500, 4200, 3100, 5800],
                    backgroundColor: 'rgba(0, 204, 68, 0.2)',
                    borderColor: '#00cc44',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#00cc44'
                }]
            },
            options: { plugins: { legend: { display: false } } }
        });

        const ctxProdutividade = document.getElementById('chartProdutividade');
        new Chart(ctxProdutividade, {
            type: 'bar',
            data: {
                labels: ['Pedro', 'Matheus', 'Arthur'],
                datasets: [{
                    data: [15, 22, 18],
                    backgroundColor: '#3a4dc0'
                }]
            },
            options: { plugins: { legend: { display: false } } }
        });

        const ctxEstoque = document.getElementById('chartEstoque');
        new Chart(ctxEstoque, {
            type: 'pie',
            data: {
                labels: ['Disponível', 'Em uso', 'Crítico'],
                datasets: [{
                    data: [70, 20, 10],
                    backgroundColor: ['#00cc44', '#3a4dc0', '#ff4444'],
                    borderWidth: 0
                }]
            },
            options: { plugins: { legend: { position: 'bottom', labels: { color: '#888' } } } }
        });

        const ctxGauge = document.getElementById('chartGauge');
        new Chart(ctxGauge, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [70, 30],
                    backgroundColor: ['#00cc44', '#222'],
                    circumference: 180,
                    rotation: 270,
                    borderWidth: 0
                }]
            }
        });
    </script>
</body>
</html>
