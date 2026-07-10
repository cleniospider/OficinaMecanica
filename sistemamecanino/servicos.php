<?php 
require_once('conexao/conexao.php');

// Proteção de sessão
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_perfil'], ['Admin', 'Mecanico', 'Recepcionista'])) {
    header("Location: index.php");
    exit;
}

try {
    $stmt = $pdo->query("SELECT * FROM servicos ORDER BY nome ASC");
    $servicos = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao carregar serviços: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Serviços</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/ordens.css">
    <link rel="stylesheet" href="css/estoque.css">
    <style>
        .servico-card {
            background-color: #1a1a1a;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .acoes-serv {
            display: flex;
            gap: 8px;
        }
        .btn-serv-edit {
            background-color: #2ecc71;
            color: #fff;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
        }
        .btn-serv-del {
            background-color: #e74c3c;
            color: #fff;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
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
                <span class="role-text"><?= htmlspecialchars(strtoupper($_SESSION['usuario_perfil'])) ?></span>
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
            <li><a href="servicos.php" class="active">Serviços</a></li>
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
                <h2>Catálogo de Serviços</h2>
            </div>

            <div class="estoque-wrapper">
                <div class="estoque-controles">
                    <div class="busca-acoes" style="justify-content: space-between; width: 100%;">
                        <input type="text" id="searchServ" class="input-busca" placeholder=" Pesquisar serviço..." style="flex: 1; margin-right: 15px;">
                        <a href="novo-servico.php" class="btn-nova-peca">+ NOVO SERVIÇO</a>
                    </div>
                </div>

                <div class="lista-pecas" id="listaServContainer">
                    <?php if (empty($servicos)): ?>
                        <p style="text-align: center; color: #aaa; width: 100%; margin-top: 20px;">Nenhum serviço cadastrado.</p>
                    <?php else: ?>
                        <?php foreach ($servicos as $s): ?>
                            <div class="servico-card">
                                <div>
                                    <h4 style="margin: 0; color: #fff; font-size: 16px;"><?= htmlspecialchars($s['nome']) ?></h4>
                                    <p style="margin: 5px 0 0; color: #aaa;">R$ <?= number_format($s['preco'], 2, ',', '.') ?></p>
                                </div>
                                <div class="acoes-serv">
                                    <a href="editar-servico.php?id=<?= $s['idservicos'] ?>" class="btn-serv-edit">EDITAR</a>
                                    <a href="excluir-servico.php?id=<?= $s['idservicos'] ?>" class="btn-serv-del" onclick="return confirm('Deseja realmente excluir este serviço?')">EXCLUIR</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

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

        // Filtro em tempo real
        const searchServ = document.getElementById('searchServ');
        if (searchServ) {
            searchServ.addEventListener('input', function() {
                const filter = this.value.toLowerCase();
                const cards = document.querySelectorAll('.servico-card');
                
                cards.forEach(card => {
                    const titleNode = card.querySelector('h4');
                    if (titleNode) {
                        const title = titleNode.textContent.toLowerCase();
                        if (title.includes(filter)) {
                            card.style.display = "";
                        } else {
                            card.style.display = "none";
                        }
                    }
                });
            });
        }
    </script>
</body>
</html>
