<?php 
require_once('conexao/conexao.php');

// Proteção de sessão
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

// Dados reais para os gráficos
try {
    // Faturamento mensal dos últimos 6 meses (receitas pagas)
    $stmt_fat = $pdo->query("
        SELECT MONTH(data) AS mes, YEAR(data) AS ano, SUM(valor) AS total
        FROM Financeiro
        WHERE tipo = '1: Receita' AND status = 'PAGO'
        AND data >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY YEAR(data), MONTH(data)
        ORDER BY ano ASC, mes ASC
    ");
    $faturamentos = $stmt_fat->fetchAll();

    // Produtividade por mecânico (OS finalizadas)
    $stmt_prod = $pdo->query("
        SELECT u.nome_completo, COUNT(o.id) AS total_os
        FROM usuarios u
        LEFT JOIN OS o ON o.mecanico_id = u.id AND o.status = 'finalizado'
        WHERE u.perfil = 'Mecanico'
        GROUP BY u.id, u.nome_completo
        ORDER BY total_os DESC
        LIMIT 5
    ");
    $produtividade = $stmt_prod->fetchAll();

    // Estoque de peças por categoria
    $stmt_est = $pdo->query("
        SELECT 
            SUM(CASE WHEN estoque_atual > estoque_minimo THEN 1 ELSE 0 END) AS ok,
            SUM(CASE WHEN estoque_atual > 2 AND estoque_atual <= estoque_minimo THEN 1 ELSE 0 END) AS baixo,
            SUM(CASE WHEN estoque_atual <= 2 THEN 1 ELSE 0 END) AS critico
        FROM pecas
    ");
    $estoque_stats = $stmt_est->fetch();

    // Tempo médio de entrega em dias (diferença entre data_entrada e data de finalização)
    $stmt_tempo = $pdo->query("
        SELECT AVG(DATEDIFF(NOW(), data_entrada)) AS media_dias
        FROM OS WHERE status = 'finalizado'
    ");
    $tempo_medio = floatval($stmt_tempo->fetchColumn() ?: 0);

    // Total de OS
    $stmt_total = $pdo->query("SELECT COUNT(*) FROM OS");
    $total_os = intval($stmt_total->fetchColumn());

    $stmt_finalizadas = $pdo->query("SELECT COUNT(*) FROM OS WHERE status = 'finalizado'");
    $os_finalizadas = intval($stmt_finalizadas->fetchColumn());

    $stmt_ativas = $pdo->query("SELECT COUNT(*) FROM OS WHERE status = 'ativo'");
    $os_ativas = intval($stmt_ativas->fetchColumn());

} catch (PDOException $e) {
    // fallback com zeros se tabela não existir
    $faturamentos = [];
    $produtividade = [];
    $estoque_stats = ['ok' => 0, 'baixo' => 0, 'critico' => 0];
    $tempo_medio = 0;
    $total_os = 0; $os_finalizadas = 0; $os_ativas = 0;
}

// Preparar dados para JS
$meses_nomes = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
$labels_fat = []; $data_fat = [];
foreach ($faturamentos as $f) {
    $labels_fat[] = $meses_nomes[$f['mes'] - 1] . '/' . substr($f['ano'], 2, 2);
    $data_fat[] = floatval($f['total']);
}
// Se não houver dados, preenche com zeros
if (empty($labels_fat)) {
    $labels_fat = ['Sem dados']; $data_fat = [0];
}

$labels_prod = []; $data_prod = [];
foreach ($produtividade as $p) {
    $labels_prod[] = explode(' ', $p['nome_completo'])[0]; // primeiro nome
    $data_prod[] = intval($p['total_os']);
}
if (empty($labels_prod)) {
    $labels_prod = ['Sem mecânicos']; $data_prod = [0];
}

$est_ok     = intval($estoque_stats['ok'] ?? 0);
$est_baixo  = intval($estoque_stats['baixo'] ?? 0);
$est_critico= intval($estoque_stats['critico'] ?? 0);
?>

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
                <span class="role-text"><?= htmlspecialchars(strtoupper($_SESSION['usuario_perfil'] ?? 'ADMINISTRADOR')) ?></span>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="<?= $_SESSION['usuario_perfil'] === 'Admin' ? 'admin.php' : ($_SESSION['usuario_perfil'] === 'Mecanico' ? 'mecan.php' : 'recep.php') ?>">Painel de Gestão</a></li>
            <?php if ($_SESSION['usuario_perfil'] === 'Admin'): ?>
                <li><a href="bd/lista.php">Gerenciar Usuários</a></li>
            <?php endif; ?>
            <li><a href="cadastrocliente.php">Cadastro Cliente</a></li>
            <li><a href="cadastroveiculo.php">Cadastro Veículo</a></li>
            <li><a href="ordens.php">Ordens de Serviços</a></li>
            <li><a href="servicos.php">Serviços</a></li>
            <li><a href="estoque-critico.php">Estoque de Peças</a></li>
            <li><a href="historico-veiculos.php">Histórico de Veículos</a></li>
            <li><a href="financeiro.php">Financeiro</a></li>
            <li><a href="relatorios.php" class="active">Relatórios</a></li>
            <li><a href="minha-conta.php">Minha conta</a></li>
            <li><a href="index.php?logout=1" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="relatorios-container">
            <h2 class="titulo-sessao-relatorio">RELATÓRIOS GERENCIAIS</h2>

            <!-- Cards de Resumo -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 30px;">
                <div style="background: #1a1a1a; border: 1px solid #333; border-radius: 10px; padding: 20px; text-align: center;">
                    <div style="font-size: 2rem; font-weight: bold; color: #2ecc71;"><?= $total_os ?></div>
                    <div style="color: #888; font-size: 0.85rem; margin-top: 5px;">TOTAL DE OS</div>
                </div>
                <div style="background: #1a1a1a; border: 1px solid #333; border-radius: 10px; padding: 20px; text-align: center;">
                    <div style="font-size: 2rem; font-weight: bold; color: #f1c40f;"><?= $os_ativas ?></div>
                    <div style="color: #888; font-size: 0.85rem; margin-top: 5px;">OS EM ANDAMENTO</div>
                </div>
                <div style="background: #1a1a1a; border: 1px solid #333; border-radius: 10px; padding: 20px; text-align: center;">
                    <div style="font-size: 2rem; font-weight: bold; color: #3498db;"><?= $os_finalizadas ?></div>
                    <div style="color: #888; font-size: 0.85rem; margin-top: 5px;">OS FINALIZADAS</div>
                </div>
            </div>

            <div class="card-grafico">
                <h3>FATURAMENTO MENSAL (Receitas Pagas)</h3>
                <canvas id="chartFaturamento"></canvas>
            </div>

            <hr class="divisor">

            <div class="card-grafico">
                <h3>PRODUTIVIDADE POR MECÂNICO (OS Finalizadas)</h3>
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
                    <div class="gauge-value"><?= number_format($tempo_medio, 1) ?> <small>DIAS</small></div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Menu Hambúrguer
        const btnMenu = document.getElementById('btn-menu');
        const sidebar = document.getElementById('sidebar');
        btnMenu.addEventListener('click', () => sidebar.classList.toggle('open'));

        const links = document.querySelectorAll('.nav-links a');
        links.forEach(link => link.addEventListener('click', () => sidebar.classList.remove('open')));

        // Dados vindos do PHP
        const labelsFat  = <?= json_encode($labels_fat) ?>;
        const dataFat    = <?= json_encode($data_fat) ?>;
        const labelsProd = <?= json_encode($labels_prod) ?>;
        const dataProd   = <?= json_encode($data_prod) ?>;
        const estOk      = <?= $est_ok ?>;
        const estBaixo   = <?= $est_baixo ?>;
        const estCritico = <?= $est_critico ?>;
        const tempoMedio = <?= $tempo_medio ?>;

        // Gráfico de Faturamento
        new Chart(document.getElementById('chartFaturamento'), {
            type: 'line',
            data: {
                labels: labelsFat,
                datasets: [{
                    data: dataFat,
                    backgroundColor: 'rgba(0, 204, 68, 0.2)',
                    borderColor: '#00cc44',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#00cc44'
                }]
            },
            options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });

        // Gráfico de Produtividade
        new Chart(document.getElementById('chartProdutividade'), {
            type: 'bar',
            data: {
                labels: labelsProd,
                datasets: [{
                    data: dataProd,
                    backgroundColor: '#3a4dc0'
                }]
            },
            options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
        });

        // Gráfico de Estoque (Pizza)
        new Chart(document.getElementById('chartEstoque'), {
            type: 'pie',
            data: {
                labels: ['Disponível (OK)', 'Estoque Baixo', 'Crítico'],
                datasets: [{
                    data: [estOk, estBaixo, estCritico],
                    backgroundColor: ['#00cc44', '#f1c40f', '#ff4444'],
                    borderWidth: 0
                }]
            },
            options: { plugins: { legend: { position: 'bottom', labels: { color: '#888' } } } }
        });

        // Gráfico de Tempo (Gauge)
        const maxDias = 14;
        const pct = Math.min(tempoMedio / maxDias, 1);
        new Chart(document.getElementById('chartGauge'), {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [pct, 1 - pct],
                    backgroundColor: [pct < 0.5 ? '#00cc44' : (pct < 0.75 ? '#f1c40f' : '#ff4444'), '#222'],
                    circumference: 180,
                    rotation: 270,
                    borderWidth: 0
                }]
            }
        });
    </script>
</body>
</html>
