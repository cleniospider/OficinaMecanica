<?php 
require_once('conexao/conexao.php');

// Proteção de sessão
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_perfil'], ['Admin', 'Recepcionista'])) {
    header("Location: index.php");
    exit;
}

$erro = "";
$sucesso = "";

// 1. Processar Cadastro via POST (vindo de novo-cliente.php)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nome_completo'])) {
    $nome = trim($_POST['nome_completo']);
    $cpf = preg_replace('/\D/', '', $_POST['cpf']); // Remove formatação de CPF
    $telefone = trim($_POST['telefone']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

    if (!empty($nome) && !empty($cpf) && !empty($telefone)) {
        try {
            // Verificar se o CPF já está cadastrado
            $stmt_check = $pdo->prepare("SELECT cpf FROM clientes WHERE cpf = ?");
            $stmt_check->execute([$cpf]);
            if ($stmt_check->fetch()) {
                $erro = "Erro: Já existe um cliente cadastrado com este CPF!";
            } else {
                // Inserir no banco
                $stmt = $pdo->prepare("INSERT INTO clientes (`nome completo`, cpf, telefone, email) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nome, $cpf, $telefone, $email]);
                $sucesso = "Cliente cadastrado com sucesso!";
            }
        } catch (PDOException $e) {
            $erro = "Erro ao cadastrar cliente: " . $e->getMessage();
        }
    } else {
        $erro = "Preencha todos os campos obrigatórios (Nome, CPF e Telefone)!";
    }
}

// 2. Processar Exclusão via GET
if (isset($_GET['excluir'])) {
    $cpf_excluir = preg_replace('/\D/', '', $_GET['excluir']);
    try {
        // Verificar se o cliente possui veículos cadastrados para evitar erros de chave estrangeira
        $stmt_check_veic = $pdo->prepare("SELECT id FROM veiculo WHERE clientes_cpf = ?");
        $stmt_check_veic->execute([$cpf_excluir]);
        if ($stmt_check_veic->fetch()) {
            $erro = "Erro: Não é possível excluir o cliente porque ele possui veículos vinculados!";
        } else {
            $stmt = $pdo->prepare("DELETE FROM clientes WHERE cpf = ?");
            $stmt->execute([$cpf_excluir]);
            $sucesso = "Cliente excluído com sucesso!";
        }
    } catch (PDOException $e) {
        $erro = "Erro ao excluir cliente: " . $e->getMessage();
    }
}

// 3. Buscar todos os clientes
$stmt_clientes = $pdo->query("SELECT * FROM clientes ORDER BY `nome completo` ASC");
$clientes = $stmt_clientes->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Cadastro de Clientes</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/ordens.css">
    <style>
        .alert-error {
            background-color: #e74c3c;
            color: white;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-success {
            background-color: #2ecc71;
            color: white;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>

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
                <span class="role-text"><?= htmlspecialchars(strtoupper($_SESSION['usuario_perfil'])) ?></span>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="<?= $_SESSION['usuario_perfil'] === 'Admin' ? 'admin.php' : 'recep.php' ?>">Painel de Gestão</a></li>
            <li><a href="cadastrocliente.php" class="active">Cadastro Cliente</a></li>
            <li><a href="cadastroveiculo.php">Cadastro Veículo</a></li>
            <li><a href="ordens.php">Ordens de Serviços</a></li>
            <li><a href="estoque-critico.php">Estoque de Peças</a></li>
            <li><a href="historico-veiculos.php">Histórico de Veículos</a></li>
            <li><a href="financeiro.php">Financeiro</a></li>
            <li><a href="relatorios.php">Relatórios</a></li>
            <li><a href="minha-conta.php">Minha conta</a></li>
            <li><a href="index.php?logout=1" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="orders-container">
            <div class="orders-header">
                <h2>Clientes Cadastrados</h2>
                <div class="search-box">
                    <input type="text" id="searchCliente" placeholder="Pesquisar por nome ou CPF...">
                </div>
            </div>

            <?php if (!empty($erro)): ?>
                <div class="alert-error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <?php if (!empty($sucesso)): ?>
                <div class="alert-success"><?= htmlspecialchars($sucesso) ?></div>
            <?php endif; ?>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>NOME COMPLETO</th>
                            <th>CPF / CNPJ</th>
                            <th>TELEFONE</th>
                            <th>E-MAIL</th>
                            <th>AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody id="tabela-clientes-corpo">
                        <?php if (empty($clientes)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: #aaa;">Nenhum cliente cadastrado no momento.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($clientes as $c): ?>
                            <tr>
                                <td data-label="NOME"><strong><?= htmlspecialchars($c['nome completo']) ?></strong></td>
                                <td data-label="CPF/CNPJ"><?= htmlspecialchars($c['cpf']) ?></td>
                                <td data-label="TELEFONE"><?= htmlspecialchars($c['telefone']) ?></td>
                                <td data-label="E-MAIL"><?= htmlspecialchars($c['email'] ?: 'Não informado') ?></td>
                                <td data-label="AÇÕES">
                                    <div class="acoes-flex">
                                        <a href="cadastroveiculo.php?cliente_cpf=<?= $c['cpf'] ?>" class="btn-editar" style="background-color: #3498db; margin-right: 5px;">VEÍCULOS</a>
                                        <a href="cadastrocliente.php?excluir=<?= $c['cpf'] ?>" class="btn-excluir" onclick="return confirm('Tem certeza que deseja excluir este cliente?')">EXCLUIR</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="area-botao-novo">
                <a href="novo-cliente.php" class="btn-nova-ordem btn-espacado">+ NOVO CLIENTE</a>
            </div>
        </div>
    </main>

    <div id="modal-conta" class="modal-overlay">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Minha Conta</h2>
            <div class="conta-dados">
                <p><strong>Status:</strong> <span style="color: #00cc44;">Ativo ✔️</span></p>
                <p><strong>Nome:</strong> <?= htmlspecialchars($_SESSION['usuario_nome']) ?></p>
                <p><strong>Perfil:</strong> <?= htmlspecialchars($_SESSION['usuario_perfil']) ?></p>
            </div>
            <button class="btn-fechar-modal">Fechar</button>
        </div>
    </div>

    <script>
        const btnMobile = document.querySelector('.hamburger-btn');
        const sidebar = document.querySelector('#sidebar');

        // Abre e fecha o menu lateral
        if(btnMobile) {
            btnMobile.addEventListener('click', () => {
                sidebar.classList.toggle('open');
            });
        }

        // Fecha o menu ao clicar em um link
        const links = document.querySelectorAll('.nav-links a');
        links.forEach(link => {
            link.addEventListener('click', () => {
                sidebar.classList.remove('open');
            });
        });

        // Lógica do Modal de Conta
        const linkConta = document.querySelector('a[href="minha-conta.php"]'); 
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

        // Filtro de pesquisa em tempo real
        const searchInput = document.getElementById('searchCliente');
        searchInput.addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#tabela-clientes-corpo tr');
            
            rows.forEach(row => {
                const nomeCell = row.querySelector('td[data-label="NOME"]');
                const cpfCell = row.querySelector('td[data-label="CPF/CNPJ"]');
                if (nomeCell && cpfCell) {
                    const nome = nomeCell.textContent.toLowerCase();
                    const cpf = cpfCell.textContent.toLowerCase();
                    if (nome.includes(filter) || cpf.includes(filter)) {
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
