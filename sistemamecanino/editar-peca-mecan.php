<?php 
session_start(); // Inicializa a sessão para ler o perfil do usuário logado
require_once('conexao/conexao.php');

// Proteção de sessão
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_perfil'], ['Admin', 'Mecanico'])) {
    header("Location: index.php");
    exit;
}

$erro = "";
$sucesso = "";

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: estoque-critico-mecan.php");
    exit;
}

// Buscar dados da peça
$stmt = $pdo->prepare("SELECT * FROM pecas WHERE id = ?");
$stmt->execute([$id]);
$peca = $stmt->fetch();

if (!$peca) {
    header("Location: estoque-critico-mecan.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nome'])) {
    $nome = trim($_POST['nome']);
    $estoque_atual = filter_var($_POST['estoque_atual'], FILTER_VALIDATE_INT);
    $estoque_minimo = filter_var($_POST['estoque_minimo'], FILTER_VALIDATE_INT);
    $url_imagem = trim($_POST['url_imagem'] ?? '');

    // Limpar preco_venda
    $valor_str = $_POST['preco_venda'];
    $valor_clean = str_replace(['R$', ' ', '.', ','], ['', '', '', '.'], $valor_str);
    $preco_venda = floatval($valor_clean);

    if (!empty($nome) && $preco_venda >= 0 && $estoque_atual !== false && $estoque_minimo !== false) {
        try {
            $stmt_up = $pdo->prepare("
                UPDATE pecas SET nome = ?, preco_venda = ?, estoque_atual = ?, estoque_minimo = ?, url_imagem = ? 
                WHERE id = ?
            ");
            $stmt_up->execute([$nome, $preco_venda, $estoque_atual, $estoque_minimo, $url_imagem, $id]);
            
            // Decidir para qual tela de estoque voltar baseado no novo estoque (rotas do mecânico)
            if ($estoque_atual <= 2) {
                header("Location: estoque-critico-mecan.php");
            } elseif ($estoque_atual < $estoque_minimo) {
                header("Location: estoque-baixo-mecan.php");
            } else {
                header("Location: estoque-ok-mecan.php");
            }
            exit;
        } catch (PDOException $e) {
            $erro = "Erro ao atualizar peça: " . $e->getMessage();
        }
    } else {
        $erro = "Preencha todos os campos obrigatórios corretamente!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Editar Peça (Mecânico)</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/novo-cliente.css">
    <style>
        .alert-error {
            background-color: #e74c3c;
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
                <span class="role-text" style="color: #ffaa00;">MECÂNICO</span>
            </div>
        </div>

        <ul class="nav-links">
            <li><a href="mecan.php">Painel de Gestão</a></li>
            <li><a href="ordens-mecanico.php">Ordens de Serviços</a></li>
            <li><a href="estoque-critico-mecan.php" class="active">Estoque de Peças</a></li>
            <li><a href="historico-veiculos-mecan.php">Histórico de Veículos</a></li>
            <li><a href="minha-conta-mecan.php">Minha Conta</a></li>
            <li><a href="index.php?logout=1" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="container-form-dark">
            <h2 class="titulo-sessao">EDITAR PEÇA DO ESTOQUE</h2>
            
            <?php if (!empty($erro)): ?>
                <div class="alert-error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <div class="card-dark">
                <form method="POST">
                    
                    <div class="form-row">
                        <div class="grupo-input-dark flex-3">
                            <label>NOME DA PEÇA</label>
                            <input type="text" name="nome" value="<?= htmlspecialchars($peca['nome']) ?>" required>
                        </div>
                        <div class="grupo-input-dark flex-1">
                            <label>PREÇO DE VENDA</label>
                            <input type="text" id="preco_venda" name="preco_venda" value="R$ <?= number_format($peca['preco_venda'], 2, ',', '.') ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="grupo-input-dark flex-1">
                            <label>ESTOQUE ATUAL</label>
                            <input type="number" name="estoque_atual" value="<?= htmlspecialchars($peca['estoque_atual']) ?>" min="0" required>
                        </div>
                        <div class="grupo-input-dark flex-1">
                            <label>ESTOQUE MÍNIMO DE ALERTA</label>
                            <input type="number" name="estoque_minimo" value="<?= htmlspecialchars($peca['estoque_minimo']) ?>" min="0" required>
                        </div>
                    </div>

                    <div class="grupo-input-dark">
                        <label>URL DA IMAGEM DA PEÇA (OPCIONAL)</label>
                        <input type="text" name="url_imagem" value="<?= htmlspecialchars($peca['url_imagem'] ?? '') ?>">
                    </div>

                    <div class="footer-acoes">
                        <button type="submit" class="btn-acao btn-salvar-os">ATUALIZAR CADASTRO</button>
                        <a href="estoque-critico-mecan.php" class="btn-acao btn-voltar-os">VOLTAR</a>
                    </div>
                </form>
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

        // Formatação do campo de preço em tempo real
        const precoInput = document.getElementById('preco_venda');
        if (precoInput) {
            precoInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, "");
                value = (value / 100).toFixed(2) + "";
                value = value.replace(".", ",");
                value = value.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
                e.target.value = "R$ " + value;
            });
        }
    </script>
</body>
</html>