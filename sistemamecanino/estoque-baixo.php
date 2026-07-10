<?php 
require_once('conexao/conexao.php');

// Proteção de sessão
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_perfil'], ['Admin', 'Mecanico'])) {
    header("Location: index.php");
    exit;
}

try {
    $stmt = $pdo->query("SELECT * FROM pecas WHERE estoque_atual > 2 AND estoque_atual <= estoque_minimo ORDER BY nome ASC");
    $pecas = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao carregar estoque baixo: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Estoque Baixo</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/ordens.css">
    <link rel="stylesheet" href="css/estoque.css">
    <style>
        .peca-card-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        .acoes-peca {
            display: flex;
            gap: 8px;
        }
        .btn-peca-edit {
            background-color: #2ecc71;
            color: #fff;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
        }
        .btn-peca-del {
            background-color: #e74c3c;
            color: #fff;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
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
            <li><a href="<?= $_SESSION['usuario_perfil'] === 'Admin' ? 'admin.php' : ($_SESSION['usuario_perfil'] === 'Mecanico' ? 'mecan.php' : 'recep.php') ?>">Painel de Gestão</a></li>
            <?php if ($_SESSION['usuario_perfil'] === 'Admin'): ?>
                <li><a href="bd/lista.php">Gerenciar Usuários</a></li>
            <?php endif; ?>
            <li><a href="cadastrocliente.php">Cadastro Cliente</a></li>
            <li><a href="cadastroveiculo.php">Cadastro Veículo</a></li>
            <li><a href="ordens.php">Ordens de Serviços</a></li>
            <li><a href="servicos.php">Serviços</a></li>
            <li><a href="estoque-critico.php" class="active">Estoque de Peças</a></li>
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
                <h2>Controle de Estoque</h2>
            </div>

            <div class="estoque-wrapper">
                <div class="estoque-controles">
                    <div class="filtros-grupo">
                        <a href="estoque-critico.php" class="btn-f b-critico">CRÍTICO</a>
                        <a href="estoque-baixo.php" class="btn-f b-baixo ativo">BAIXO</a>
                        <a href="estoque-ok.php" class="btn-f b-ok">OK</a>
                    </div>

                    <div class="busca-acoes">
                        <input type="text" id="searchPeca" class="input-busca" placeholder=" Pesquisar por nome...">
                        <a href="nova-peca.php" class="btn-nova-peca">+ NOVA PEÇA</a>
                    </div>
                </div>

                <div class="lista-pecas" id="listaPecasContainer">
                    <?php if (empty($pecas)): ?>
                        <p style="text-align: center; color: #aaa; width: 100%; margin-top: 20px;">Nenhuma peça no estoque baixo.</p>
                    <?php else: ?>
                        <?php foreach ($pecas as $p): 
                            $img = !empty($p['url_imagem']) ? $p['url_imagem'] : 'img/pastilha.jpg'; // fallback
                        ?>
                            <div class="peca-card">
                                <div class="peca-card-flex">
                                    <div class="peca-info-esquerda">
                                        <div class="peca-img-caixa"><img src="<?= htmlspecialchars($img) ?>" alt="Imagem da peça"></div>
                                        <div class="peca-texto">
                                            <h4><?= htmlspecialchars($p['nome']) ?></h4>
                                            <p>R$ <?= number_format($p['preco_venda'], 2, ',', '.') ?></p>
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div class="peca-qtd-direita" style="margin-bottom: 5px;"><?= htmlspecialchars($p['estoque_atual']) ?> un.</div>
                                        <div class="acoes-peca">
                                            <a href="editar-peca.php?id=<?= $p['id'] ?>" class="btn-peca-edit">EDITAR</a>
                                            <a href="excluir-peca.php?id=<?= $p['id'] ?>" class="btn-peca-del" onclick="return confirm('Deseja realmente excluir esta peça do estoque?')">EXCLUIR</a>
                                        </div>
                                    </div>
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
        const searchPeca = document.getElementById('searchPeca');
        if (searchPeca) {
            searchPeca.addEventListener('input', function() {
                const filter = this.value.toLowerCase();
                const cards = document.querySelectorAll('.peca-card');
                
                cards.forEach(card => {
                    const titleNode = card.querySelector('.peca-texto h4');
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
