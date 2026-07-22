<?php 
session_start();
require_once('conexao/conexao.php');

// Proteção de sessão: garante que apenas Mecânicos (ou Administradores) acessem
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_perfil'], ['Admin', 'Mecanico'])) {
    header("Location: index.php");
    exit;
}

// Buscar todas as Ordens de Serviço cadastradas com os nomes de colunas corretos (idêntico ao Admin)
try {
    $stmt = $pdo->query("
        SELECT o.*, v.placa, v.`marca/modelo` AS veiculo_modelo, c.`nome completo` AS cliente_nome 
        FROM OS o
        LEFT JOIN veiculo v ON o.veiculo_id1 = v.id
        LEFT JOIN clientes c ON o.clientes_cpf = c.cpf
        ORDER BY o.id DESC
    ");
    $ordens = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao carregar as ordens de serviço: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Ordens de Serviço (Mecânico)</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/ordens.css">
    <style>
        .dot-finalizado { background-color: #2ecc71; } /* Verde */
        .dot-ativo { background-color: #f1c40f; }      /* Amarelo */
        .dot-parado { background-color: #ff0000; }     /* Vermelho */

        .placa-badge {
            background: #eee;
            padding: 2px 5px;
            border-radius: 4px;
            font-family: monospace;
            font-weight: bold;
            color: #333;
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
                <span class="role-text" style="color: #ffaa00;">MECÂNICO</span>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="mecan.php">Painel de Gestão</a></li>
            <li><a href="ordens-mecanico.php" class="active">Ordens de Serviços</a></li>
            <li><a href="estoque-critico-mecan.php">Estoque de Peças</a></li>
            <li><a href="historico-veiculos-mecan.php">Histórico de Veículos</a></li>
            <li><a href="minha-conta-mecan.php">Minha Conta</a></li>
            <li><a href="index.php?logout=1" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="orders-container">
            <div class="orders-header">
                <h2>Controle de Ordens de Serviço (OS)</h2>
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Pesquisar veículo, cliente ou Nº OS...">
                </div>
            </div>

            <div class="table-responsive">
                <table id="ordersTable">
                    <thead>
                        <tr>
                            <th>Nº OS</th>
                            <th>STATUS</th>
                            <th>VEÍCULO</th>
                            <th>PLACA</th>
                            <th>PROPRIETÁRIO</th>
                            <th>PROBLEMA</th>
                            <th>SERVIÇOS</th>
                            <th>PEÇAS</th>
                            <th>VALOR (R$)</th>
                            <th>AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if (empty($ordens)): ?>
                            <tr>
                                <td colspan="10" style="text-align: center; color: #aaa; padding: 20px;">Nenhuma ordem de serviço registrada no momento.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ordens as $o): 
                                $statusDot = 'dot-ativo';
                                if (strcasecmp($o['status'] ?? '', 'finalizado') === 0) {
                                    $statusDot = 'dot-finalizado';
                                } elseif (strcasecmp($o['status'] ?? '', 'parado') === 0) {
                                    $statusDot = 'dot-parado';
                                }
                            ?>
                            <tr>
                                <td data-label="Nº OS">#<?= htmlspecialchars($o['id'] ?? '') ?></td>
                                <td data-label="STATUS">
                                    <span class="status-dot <?= $statusDot ?>" title="<?= htmlspecialchars(ucfirst($o['status'] ?? 'Ativo')) ?>"></span>
                                </td>
                                <td data-label="VEÍCULO"><strong><?= htmlspecialchars($o['veiculo_modelo'] ?? 'Não informado') ?></strong></td>
                                <td data-label="PLACA"><span class="placa-badge"><?= htmlspecialchars($o['placa'] ?? '---') ?></span></td>
                                <td data-label="PROPRIETÁRIO"><?= htmlspecialchars($o['cliente_nome'] ?? 'Não cadastrado') ?></td>
                                <td data-label="PROBLEMA"><small><?= htmlspecialchars($o['problema'] ?? 'Não informado') ?></small></td>
                                <td data-label="SERVIÇOS"><small><?= htmlspecialchars($o['servicos'] ?? 'Nenhum') ?></small></td>
                                <td data-label="PEÇAS"><small><?= htmlspecialchars($o['pecas_usadas'] ?? 'Nenhuma') ?></small></td>
                                <td data-label="VALOR (R$)">
                                    <strong style="color: #2ecc71;">R$ <?= number_format((float)($o['valor_total'] ?? 0), 2, ',', '.') ?></strong>
                                </td>
                                <td data-label="AÇÕES">
                                    <div class="acoes-flex">
                                        <a href="editar-ordem-mecan.php?id=<?= $o['id'] ?>" class="btn-editar">EDITAR</a>
                                        <a href="excluir-ordem-mecan.php?id=<?= $o['id'] ?>" class="btn-excluir">EXCLUIR</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="legend-area">
                <span class="dot dot-finalizado"></span> Finalizado
                <span class="dot dot-ativo"></span> Ativo
                <span class="dot dot-parado"></span> Parado
            </div>

            <div class="area-botao-novo">
                <a href="nova-ordem-mecan.php" class="btn-nova-ordem">+ ABRIR NOVA ORDEM DE SERVIÇO</a>
            </div>
        </div>
    </main>

    <div id="modal-conta" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Minha Conta</h2>
            <div class="conta-dados">
                <p><strong>Status:</strong> <span style="color: #00cc44;">Ativo </span></p>
            </div>
            <button class="btn-fechar-modal">Fechar</button>
        </div>
    </div>

    <script>
        const btnMobile = document.querySelector('.hamburger-btn');
        const sidebar = document.querySelector('#sidebar');

        if(btnMobile) {
            btnMobile.addEventListener('click', () => {
                sidebar.classList.toggle('open');
            });
        }

        const links = document.querySelectorAll('.nav-links a');
        links.forEach(link => {
            link.addEventListener('click', () => {
                sidebar.classList.remove('open');
            });
        });

        // Filtro em tempo real inteligente por Nº OS, Veículo, Proprietário ou Placa
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#tableBody tr');
            
            rows.forEach(row => {
                if (row.cells.length === 1) return; // ignora a linha de "Nenhuma ordem"

                const idCell = row.querySelector('td[data-label="Nº OS"]');
                const veicCell = row.querySelector('td[data-label="VEÍCULO"]');
                const propCell = row.querySelector('td[data-label="PROPRIETÁRIO"]');
                const placaCell = row.querySelector('td[data-label="PLACA"]');
                
                if (idCell && veicCell && propCell) {
                    const idText = idCell.textContent.toLowerCase();
                    const veicText = veicCell.textContent.toLowerCase();
                    const propText = propCell.textContent.toLowerCase();
                    const placaText = placaCell ? placaCell.textContent.toLowerCase() : '';
                    
                    if (idText.includes(filter) || veicText.includes(filter) || propText.includes(filter) || placaText.includes(filter)) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                }
            });
        });

        // Modal de Configurações da Conta
        let linkConta = null;
        links.forEach(link => {
            if(link.textContent.trim() === "Minha conta") {
                linkConta = link;
                link.style.cursor = "pointer";
            }
        });

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
    </script>
</body> 
</html>