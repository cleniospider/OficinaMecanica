<?php 
require_once('conexao/conexao.php');

// Proteção de sessão
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

try {
    // 1. Calcular Saldo Atual (Receitas pagas - Despesas pagas)
    $stmt_saldo = $pdo->query("
        SELECT 
            SUM(CASE WHEN tipo = '1: Receita' AND status = 'PAGO' THEN valor ELSE 0 END) -
            SUM(CASE WHEN tipo = '2: Despesa' AND status = 'PAGO' THEN valor ELSE 0 END) AS saldo
        FROM Financeiro
    ");
    $saldo_atual = floatval($stmt_saldo->fetchColumn() ?: 0.0);

    // 2. Calcular A Receber (Receitas aguardando)
    $stmt_receber = $pdo->query("
        SELECT SUM(valor) FROM Financeiro WHERE tipo = '1: Receita' AND status = 'Aguardando'
    ");
    $a_receber = floatval($stmt_receber->fetchColumn() ?: 0.0);

    // 3. Calcular A Pagar (Despesas aguardando)
    $stmt_pagar = $pdo->query("
        SELECT SUM(valor) FROM Financeiro WHERE tipo = '2: Despesa' AND status = 'Aguardando'
    ");
    $a_pagar = floatval($stmt_pagar->fetchColumn() ?: 0.0);

    // 4. Buscar Lançamentos
    $stmt_trans = $pdo->query("
        SELECT f.*, c.`nome completo` AS cliente_nome 
        FROM Financeiro f
        LEFT JOIN OS o ON f.OS_id = o.id
        LEFT JOIN clientes c ON o.clientes_cpf = c.cpf
        ORDER BY f.data DESC
    ");
    $transacoes = $stmt_trans->fetchAll();
} catch (PDOException $e) {
    die("Erro ao carregar dados financeiros: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Financeiro</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/financeiro.css">
    <style>
        .status-cancelado {
            background-color: #7f8c8d;
            color: #fff;
            padding: 4px 12px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 0.7rem;
        }
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
            <li><a href="historico-veiculos.php">Histórico de Veículos</a></li>
            <li><a href="financeiro.php" class="active">Financeiro</a></li>
            <li><a href="relatorios.php">Relatórios</a></li>
            <li><a href="minha-conta.php">Minha conta</a></li>
            <li><a href="index.php?logout=1" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="financeiro-container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h2 class="titulo-sessao-esquerda" style="margin-bottom: 0;">FINANCEIRO</h2>
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Pesquisar cliente ou descrição..." style="background: #121212; border: 1px solid #333; padding: 8px 15px; border-radius: 8px; color: #fff; outline: none;">
                </div>
            </div>

            <div class="cards-resumo">
                <div class="card-fin">
                    <span class="label-fin">SALDO ATUAL</span>
                    <span class="valor-fin saldo">R$ <?= number_format($saldo_atual, 2, ',', '.') ?></span>
                </div>
                <div class="card-fin">
                    <span class="label-fin">A RECEBER</span>
                    <span class="valor-fin receber">R$ <?= number_format($a_receber, 2, ',', '.') ?></span>
                </div>
                <div class="card-fin">
                    <span class="label-fin">A PAGAR</span>
                    <span class="valor-fin pagar">R$ <?= number_format($a_pagar, 2, ',', '.') ?></span>
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
                    <tbody id="tableBody">
                        <?php if (empty($transacoes)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #aaa; padding: 20px;">Nenhum lançamento financeiro registrado.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($transacoes as $t): 
                                $statusClass = 'status-aguard';
                                if ($t['status'] === 'PAGO') $statusClass = 'status-pago';
                                elseif ($t['status'] === 'Cancelado') $statusClass = 'status-cancelado';

                                $prefixo = ($t['tipo'] === '1: Receita') ? '+' : '-';
                                $corValor = ($t['tipo'] === '1: Receita') ? '#30d158' : '#ff453a';
                            ?>
                            <tr>
                                <td data-label="DATA"><?= date('d/m', strtotime($t['data'])) ?></td>
                                <td data-label="CLIENTE"><strong><?= htmlspecialchars($t['cliente_nome'] ?: 'Geral / Oficina') ?></strong></td>
                                <td data-label="DESCRIÇÃO"><?= htmlspecialchars($t['descricao']) ?></td>
                                <td data-label="VALOR" style="color: <?= $corValor ?>; font-weight: bold;"><?= $prefixo ?> R$ <?= number_format($t['valor'], 2, ',', '.') ?></td>
                                <td data-label="STATUS"><span class="<?= $statusClass ?>"><?= htmlspecialchars($t['status']) ?></span></td>
                                <td data-label="AÇÕES" class="td-acoes">
                                    <a href="excluir-financeiro.php?id=<?= $t['id'] ?>&os_id=<?= $t['OS_id'] ?>" class="btn-excluir-vinho">EXCLUIR</a>
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

        // Filtro em tempo real
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#tableBody tr');
            
            rows.forEach(row => {
                const clientCell = row.querySelector('td[data-label="CLIENTE"]');
                const descCell = row.querySelector('td[data-label="DESCRIÇÃO"]');
                if (clientCell && descCell) {
                    const client = clientCell.textContent.toLowerCase();
                    const desc = descCell.textContent.toLowerCase();
                    if (client.includes(filter) || desc.includes(filter)) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                }
            });
        });
    </script>
</body>
</html>
