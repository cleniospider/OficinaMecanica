<?php
require_once __DIR__ . '/../conexao/conexao.php';

// Proteção para apenas Administradores acessarem
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil'] !== 'Admin') {
    header("Location: ../index.php");
    exit;
}

$query = $pdo->query("SELECT * FROM usuarios ORDER BY nome_completo ASC");
$usuarios = $query->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Gerenciar Usuários</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/ordens.css">
    <style>
        .roles-badges {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-admin { background-color: rgba(231, 76, 60, 0.2); color: #e74c3c; border: 1px solid #e74c3c; }
        .badge-recep { background-color: rgba(52, 152, 219, 0.2); color: #3498db; border: 1px solid #3498db; }
        .badge-mecan { background-color: rgba(46, 204, 113, 0.2); color: #2ecc71; border: 1px solid #2ecc71; }
    </style>
</head>
<body>

    <header class="top-header">
        <button class="hamburger-btn">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <div class="header-logo-text">AUTO REPAIR</div>
    </header>

    <aside class="sidebar" id="sidebar">
        <div class="profile-area">
            <img src="../img/download.png" alt="Avatar" class="avatar"> 
            <div class="mobile-profile-text">
                AUTO REPAIR<br>
                <span class="role-text">ADMINISTRADOR</span>
            </div>
        </div>

        <ul class="nav-links">
            <li><a href="../admin.php">Painel de Gestão</a></li>
            <li><a href="lista.php" class="active">Gerenciar Usuários</a></li>
            <li><a href="../cadastrocliente.php">Cadastro Cliente</a></li>
            <li><a href="../cadastroveiculo.php">Cadastro Veículo</a></li>
            <li><a href="../ordens.php">Ordens de Serviços</a></li>
            <li><a href="../estoque-critico.php">Estoque de Peças</a></li>
            <li><a href="../historico-veiculos.php">Histórico de Veículos</a></li>
            <li><a href="../financeiro.php">Financeiro</a></li>
            <li><a href="../relatorios.php">Relatórios</a></li>
            <li><a href="../minha-conta.php">Minha Conta</a></li>
            <li><a href="../index.php?logout=1" class="logout-php">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content" id="main-content">
        <div class="orders-container">
            <div class="orders-header">
                <h2>Gerenciar Usuários (Profissionais)</h2>
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Pesquisar profissional ou cargo...">
                </div>
            </div>

            <div class="table-responsive">
                <table id="usersTable">
                    <thead>
                        <tr>
                            <th>NOME COMPLETO</th>
                            <th>EMAIL</th>
                            <th>CPF</th>
                            <th>CARGO</th>
                            <th>TELEFONE</th>
                            <th>AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php foreach ($usuarios as $u): 
                            $badgeClass = 'badge-mecan';
                            if ($u['perfil'] === 'Admin') $badgeClass = 'badge-admin';
                            elseif ($u['perfil'] === 'Recepcionista') $badgeClass = 'badge-recep';
                        ?>
                        <tr>
                            <td data-label="NOME COMPLETO"><strong><?= htmlspecialchars($u['nome_completo']) ?></strong></td>
                            <td data-label="EMAIL"><?= htmlspecialchars($u['email']) ?></td>
                            <td data-label="CPF"><?= htmlspecialchars($u['cpf']) ?></td>
                            <td data-label="CARGO">
                                <span class="roles-badges <?= $badgeClass ?>"><?= htmlspecialchars($u['perfil']) ?></span>
                            </td>
                            <td data-label="TELEFONE"><?= htmlspecialchars($u['telefone'] ?: 'Não informado') ?></td>
                            <td data-label="AÇÕES">
                                <div class="acoes-flex">
                                    <a href="editar.php?id=<?= $u['id'] ?>" class="btn-editar" style="background-color: #2ecc71; margin-right: 5px;">EDITAR</a>
                                    <a href="excluir.php?id=<?= $u['id'] ?>" class="btn-excluir" onclick="return confirm('Deseja excluir este profissional?')">EXCLUIR</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="area-botao-novo">
                <!-- Como o cadastro de profissional está na tela de login/index.php, mantemos botão ou link para orientar -->
                <a href="../index.php?logout=1" class="btn-nova-ordem">+ CADASTRAR NOVO PROFISSIONAL</a>
            </div>
        </div>
    </main>

    <script>
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

        // Filtro em tempo real
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#tableBody tr');
            
            rows.forEach(row => {
                const nameCell = row.querySelector('td[data-label="NOME COMPLETO"]');
                const roleCell = row.querySelector('td[data-label="CARGO"]');
                if (nameCell && roleCell) {
                    const name = nameCell.textContent.toLowerCase();
                    const role = roleCell.textContent.toLowerCase();
                    if (name.includes(filter) || role.includes(filter)) {
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
