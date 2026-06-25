<?php 
require_once('conexao/conexao.php');

// Proteção de sessão
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

try {
    // Recepcionista vê todos os históricos finalizados
    $stmt = $pdo->query("
        SELECT o.id, o.data_entrada, o.status, c.`nome completo` AS cliente_nome 
        FROM OS o
        JOIN veiculo v ON o.veiculo_id1 = v.id
        JOIN clientes c ON o.clientes_cpf = c.cpf
        WHERE o.status = 'finalizado'
        ORDER BY o.data_entrada DESC
    ");
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
    <title>Auto Repair - Histórico (Recepcionista)</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/historico-veiculo.css">
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
                <span class="role-text" style="color: #3399ff;">RECEPCIONISTA</span>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="recep.php">Painel de Gestão</a></li>
            <li><a href="cadastrocliente-recep.php">Cadastro Cliente</a></li>
            <li><a href="cadastroveiculo-recep.php">Cadastro Veículo</a></li>
            <li><a href="ordens.php">Ordens de Serviços</a></li>
            <li><a href="servicos.php">Serviços</a></li>
            <li><a href="historico-veiculos-recep.php" class="active">Histórico de Veículos</a></li>
            <li><a href="minha-conta.php">Minha conta</a></li>
            <li><a href="index.php?logout=1" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="orders-container">
            <div class="orders-header">
                <h2 class="titulo-pagina">Histórico de Atendimentos - Recepção</h2>
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
                                        <a href="detalhes-historico-recep.php?id=<?= $h['id'] ?>" class="btn-editar">ANALISAR</a>
                                        <a href="excluir-historico-recep.php?id=<?= $h['id'] ?>" class="btn-excluir-vinho">EXCLUIR</a>
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

        const searchInput = document.getElementById('searchInput');
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
    </script>
</body>
</html>