<?php 
require_once('conexao/conexao.php');

// Proteção de sessão
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: historico-veiculos.php");
    exit;
}

// Buscar dados detalhados da OS finalizada
try {
    $stmt = $pdo->prepare("
        SELECT o.*, v.placa, v.`marca/modelo` AS veiculo_modelo, c.`nome completo` AS cliente_nome 
        FROM OS o
        JOIN veiculo v ON o.veiculo_id1 = v.id
        JOIN clientes c ON o.clientes_cpf = c.cpf
        WHERE o.id = ?
    ");
    $stmt->execute([$id]);
    $os = $stmt->fetch();

    if (!$os) {
        header("Location: historico-veiculos.php");
        exit;
    }
} catch (PDOException $e) {
    die("Erro ao carregar dados do histórico: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Detalhes</title>
    <link class="styles" rel="stylesheet" href="css/admin.css">
    <link class="styles" rel="stylesheet" href="css/detalhes-historico.css">
    <style>
        .dot-finalizado { background-color: #2ecc71; display: inline-block; width: 8px; height: 8px; border-radius: 50%; }
    </style>
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
            <li><a href="estoque-critico.php">Estoque de Peças</a></li>
            <li><a href="historico-veiculos.php" class="active">Histórico de Veículos</a></li>
            <li><a href="financeiro.php">Financeiro</a></li>
            <li><a href="relatorios.php">Relatórios</a></li>
            <li><a href="minha-conta.php">Minha conta</a></li>
            <li><a href="index.php?logout=1" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="container-detalhes">
            
            <div class="header-topo">
                <h2 class="titulo-pagina">Análise de Atendimento #<?= htmlspecialchars($os['id']) ?></h2>
            </div>

            <div class="cartao-analise">
                <div class="info-secao">
                    <label>CLIENTE</label>
                    <span><?= htmlspecialchars($os['cliente_nome']) ?></span>
                </div>
                
                <div class="info-secao">
                    <label>VEÍCULO</label>
                    <span><?= htmlspecialchars($os['veiculo_modelo']) ?></span>
                </div>
                
                <div class="info-secao">
                    <label>PLACA</label>
                    <span class="placa-estilo"><?= htmlspecialchars($os['placa']) ?></span>
                </div>
                
                <div class="info-secao">
                    <label>PROBLEMA RELATADO</label>
                    <p><?= htmlspecialchars($os['problema']) ?></p>
                </div>

                <div class="info-secao">
                    <label>SERVIÇOS REALIZADOS</label>
                    <p><?= htmlspecialchars($os['servicos'] ?: 'Nenhum serviço registrado') ?></p>
                </div>

                <div class="info-secao">
                    <label>PEÇAS UTILIZADAS</label>
                    <p><?= htmlspecialchars($os['pecas_usadas'] ?: 'Nenhuma peça registrada') ?></p>
                </div>

                <div class="info-secao">
                    <label>VALOR TOTAL</label>
                    <span class="valor-final">R$ <?= number_format($os['valor_total'], 2, ',', '.') ?></span>
                </div>

                <div class="info-secao">
                    <label>STATUS</label>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span class="dot-finalizado"></span> Finalizado em <?= date('d/m/Y H:i', strtotime($os['data_entrada'])) ?>
                    </div>
                </div>

                <div class="area-botao-voltar">
                    <a href="historico-veiculos.php" class="btn-voltar-simples">VOLTAR</a>
                </div>
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
