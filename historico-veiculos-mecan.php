<?php 
session_start(); // Inicializa a sessão para ler o perfil do usuário logado
require_once('conexao/conexao.php');

// Proteção de sessão — garante que apenas mecânicos ou administradores acessem
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_perfil'], ['Admin', 'Mecanico'])) {
    header("Location: index.php");
    exit;
}

try {
    // Buscar ordens finalizadas — mecânico vê as dele OU se o ID do mecânico for igual ao logado
    $stmt = $pdo->prepare("
        SELECT o.id, o.data_entrada, o.status, c.`nome completo` AS cliente_nome 
        FROM OS o
        JOIN veiculo v ON o.veiculo_id1 = v.id
        JOIN clientes c ON o.clientes_cpf = c.cpf
        WHERE o.status = 'finalizado' 
          AND (o.mecanico_id = ? OR o.mecanico_id IS NULL OR o.mecanico_id = 0)
        ORDER BY o.data_entrada DESC
    ");
    $stmt->execute([$_SESSION['usuario_id']]);
    $historicos = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao buscar histórico de veículos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Histórico (Mecânico)</title>
    <link class="target" rel="stylesheet" href="css/admin.css">
    <link class="target" rel="stylesheet" href="css/historico-veiculo.css">
    <style>.dot-finalizado { background-color: #2ecc71; }</style>
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
            <li><a href="ordens-mecanico.php">Ordens de Serviços</a></li>
            <li><a href="estoque-critico-mecan.php">Estoque de Peças</a></li>
            <li><a href="historico-veiculos-mecan.php" class="active">Histórico de Veículos</a></li>
            <li><a href="minha-conta-mecan.php">Minha Conta</a></li>
            <li><a href="index.php?logout=1" class="logout-link">Sair</a></li>
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
                    <tbody id="tableBody">
                        <?php if (empty($historicos)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: #aaa; padding: 20px;">Nenhum atendimento finalizado registrado no histórico.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($historicos as $h): ?>
                            <tr>
                                <td data-label="Nº OS">#<?= htmlspecialchars($h['id']) ?></td>
                                <td data-label="PROPRIETÁRIO"><strong><?= htmlspecialchars($h['cliente_nome']) ?></strong></td>
                                <td data-label="DATA"><?= date('d/m/Y', strtotime($h['data_entrada'])) ?></td>
                                <td data-label="STATUS">
                                    <span class="status-dot dot-finalizado"></span> Finalizado
                                </td>
                                <td data-label="AÇÕES">
                                    <div class="acoes-flex">
                                        <a href="detalhes-historico-mecan.php?id=<?= $h['id'] ?>" class="btn-editar">ANALISAR</a>
                                        <a href="excluir-historico-mecan.php?id=<?= $h['id'] ?>" class="btn-excluir-vinho">EXCLUIR</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
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

        const links = document.querySelectorAll('.nav-links a');
        links.forEach(link => {
            link.addEventListener('click', () => sidebar.classList.remove('open'));
        });

        // Filtro em tempo real
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const filter = this.value.toLowerCase();
                const rows = document.querySelectorAll('#tableBody tr');
                rows.forEach(row => {
                    const idCell = row.querySelector('td[data-label="Nº OS"]');
                    const propCell = row.querySelector('td[data-label="PROPRIETÁRIO"]');
                    if (idCell && propCell) {
                        const visible = idCell.textContent.toLowerCase().includes(filter) || propCell.textContent.toLowerCase().includes(filter);
                        row.style.display = visible ? "" : "none";
                    }
                });
            });
        }
    </script>
</body>
</html>