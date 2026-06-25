<?php 
require_once('conexao/conexao.php');

// Proteção de sessão
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_perfil'], ['Admin', 'Recepcionista'])) {
    header("Location: index.php");
    exit;
}

$erro = "";
$sucesso = "";

// 1. Processar Cadastro via POST (vindo de novo-veiculo-recep.php)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['marca']) && isset($_POST['modelo'])) {
    $marca = trim($_POST['marca']);
    $modelo = trim($_POST['modelo']);
    $placa = strtoupper(trim($_POST['placa']));
    $ano = filter_var($_POST['ano'], FILTER_VALIDATE_INT);
    $cor = trim($_POST['cor']);
    $clientes_cpf = preg_replace('/\D/', '', $_POST['clientes_cpf']);

    if (!empty($marca) && !empty($modelo) && !empty($placa) && $ano && !empty($clientes_cpf)) {
        try {
            // Verificar se a placa já existe
            $stmt_check = $pdo->prepare("SELECT id FROM veiculo WHERE placa = ?");
            $stmt_check->execute([$placa]);
            if ($stmt_check->fetch()) {
                $erro = "Erro: Já existe um veículo cadastrado com esta placa!";
            } else {
                // Obter o nome do cliente a partir do CPF
                $stmt_cli = $pdo->prepare("SELECT `nome completo` FROM clientes WHERE cpf = ?");
                $stmt_cli->execute([$clientes_cpf]);
                $cliente = $stmt_cli->fetch();

                if ($cliente) {
                    $cliente_nome = $cliente['nome completo'];
                    $marca_modelo = $marca . '/' . $modelo;

                    // Inserir no banco
                    $stmt = $pdo->prepare("INSERT INTO veiculo (placa, `marca/modelo`, ano, cor, cliente, clientes_cpf) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$placa, $marca_modelo, $ano, $cor, $cliente_nome, $clientes_cpf]);
                    $sucesso = "Veículo cadastrado com sucesso!";
                } else {
                    $erro = "Erro: Proprietário não encontrado no sistema!";
                }
            }
        } catch (PDOException $e) {
            $erro = "Erro ao cadastrar veículo: " . $e->getMessage();
        }
    } else {
        $erro = "Preencha todos os campos corretamente!";
    }
}

// 2. Processar Exclusão via GET
if (isset($_GET['excluir'])) {
    $id_excluir = filter_var($_GET['excluir'], FILTER_VALIDATE_INT);
    if ($id_excluir) {
        try {
            // Verificar se o veículo possui Ordens de Serviço antes de excluir
            $stmt_check_os = $pdo->prepare("SELECT id FROM OS WHERE veiculo_id1 = ?");
            $stmt_check_os->execute([$id_excluir]);
            if ($stmt_check_os->fetch()) {
                $erro = "Erro: Não é possível excluir o veículo porque ele possui ordens de serviço vinculadas!";
            } else {
                $stmt = $pdo->prepare("DELETE FROM veiculo WHERE id = ?");
                $stmt->execute([$id_excluir]);
                $sucesso = "Veículo excluído com sucesso!";
            }
        } catch (PDOException $e) {
            $erro = "Erro ao excluir veículo: " . $e->getMessage();
        }
    }
}

// 3. Buscar todos os veículos
$stmt_veiculos = $pdo->query("SELECT * FROM veiculo ORDER BY id DESC");
$veiculos = $stmt_veiculos->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Veículos Cadastrados (Recepcionista)</title>
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
                <span class="role-text" style="color: #3399ff;">RECEPCIONISTA</span>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="recep.php">Painel de Gestão</a></li>
            <li><a href="cadastrocliente-recep.php">Cadastro Cliente</a></li>
            <li><a href="cadastroveiculo-recep.php" class="active">Cadastro Veículo</a></li>
            <li><a href="ordens-recep.php">Ordens de Serviços</a></li> 
            <li><a href="historico-veiculos-recep.php">Histórico de Veículos</a></li>
            <li><a href="minha-conta-recep.php">Minha conta</a></li> 
            <li><a href="index.php?logout=1" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="orders-container">
            <div class="orders-header">
                <h2>Veículos Cadastrados - Recepção</h2>
                <div class="search-box">
                    <input type="text" id="searchVeiculo" placeholder="Pesquisar por placa ou modelo...">
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
                            <th>MARCA</th>
                            <th>MODELO</th>
                            <th>PLACA</th>
                            <th>ANO</th>
                            <th>COR</th>
                            <th>PROPRIETÁRIO</th>
                            <th>AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody id="tabela-veiculos-corpo">
                        <?php if (empty($veiculos)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; color: #aaa;">Nenhum veículo cadastrado no momento.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($veiculos as $v): 
                                $parts = explode('/', $v['marca/modelo'], 2);
                                $marca = $parts[0] ?? 'Desconhecida';
                                $modelo = $parts[1] ?? 'Desconhecido';
                            ?>
                            <tr>
                                <td data-label="MARCA"><?= htmlspecialchars($marca) ?></td>
                                <td data-label="MODELO"><strong><?= htmlspecialchars($modelo) ?></strong></td>
                                <td data-label="PLACA" class="placa-texto"><?= htmlspecialchars($v['placa']) ?></td>
                                <td data-label="ANO"><?= htmlspecialchars($v['ano']) ?></td>
                                <td data-label="COR"><?= htmlspecialchars($v['cor']) ?></td>
                                <td data-label="PROPRIETÁRIO"><?= htmlspecialchars($v['cliente']) ?></td>
                                <td data-label="AÇÕES">
                                    <div class="acoes-flex">
                                        <a href="editar-veiculo-recep.php?id=<?= $v['id'] ?>" class="btn-editar" style="background-color: #2ecc71; margin-right: 5px;">EDITAR</a>
                                        <a href="excluir-veiculo-recep.php?id=<?= $v['id'] ?>" class="btn-excluir">EXCLUIR</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="area-botao-novo">
                <a href="novo-veiculo-recep.php" class="btn-nova-ordem btn-espacado">+ NOVO VEÍCULO</a>
            </div>
        </div>
    </main>

    <div id="modal-conta" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Minha Conta</h2>
            <div class="conta-dados">
                <p><strong>Nome:</strong> <?= htmlspecialchars($_SESSION['usuario_nome'] ?? '') ?></p>
                <p><strong>Perfil:</strong> <?= htmlspecialchars($_SESSION['usuario_perfil'] ?? '') ?></p>
                <p><strong>Status:</strong> <span style="color: #00cc44;">Ativo ✔️</span></p>
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

        const linkConta = document.querySelector('a[href="minha-conta-recep.php"]'); 
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
        const searchInput = document.getElementById('searchVeiculo');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const filter = this.value.toLowerCase();
                const rows = document.querySelectorAll('#tabela-veiculos-corpo tr');
                
                rows.forEach(row => {
                    const modelCell = row.querySelector('td[data-label="MODELO"]');
                    const placaCell = row.querySelector('td[data-label="PLACA"]');
                    if (modelCell && placaCell) {
                        const modelo = modelCell.textContent.toLowerCase();
                        const placa = placaCell.textContent.toLowerCase();
                        if (modelo.includes(filter) || placa.includes(filter)) {
                            row.style.display = "";
                        } else {
                            row.style.display = "none";
                        }
                    }
                });
            });
        }
    </script>
</body>
</html>