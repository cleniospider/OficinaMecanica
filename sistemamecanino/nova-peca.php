<?php 
require_once('conexao/conexao.php');

// Proteção de sessão
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_perfil'], ['Admin', 'Mecanico'])) {
    header("Location: index.php");
    exit;
}

$erro = "";
$sucesso = "";

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
            $stmt = $pdo->prepare("
                INSERT INTO pecas (nome, preco_venda, estoque_atual, estoque_minimo, url_imagem) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$nome, $preco_venda, $estoque_atual, $estoque_minimo, $url_imagem]);
            $sucesso = "Peça cadastrada com sucesso!";
            header("Location: estoque-critico.php");
            exit;
        } catch (PDOException $e) {
            $erro = "Erro ao cadastrar peça: " . $e->getMessage();
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
    <title>Auto Repair - Nova Peça</title>
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
                <span class="role-text"><?= htmlspecialchars(strtoupper($_SESSION['usuario_perfil'])) ?></span>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="admin.php">Painel de Gestão</a></li>
            <li><a href="cadastrocliente.php">Cadastro Cliente</a></li>
            <li><a href="cadastroveiculo.php">Cadastro Veículo</a></li>
            <li><a href="ordens.php">Ordens de Serviços</a></li>
            <li><a href="estoque-critico.php" class="active">Estoque de Peças</a></li>
            <li><a href="historico-veiculos.php">Histórico de Veículos</a></li>
            <li><a href="financeiro.php">Financeiro</a></li>
            <li><a href="relatorios.php">Relatórios</a></li>
            <li><a href="minha-conta.php">Minha conta</a></li>
            <li><a href="index.php?logout=1" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="container-form-dark">
            <h2 class="titulo-sessao">CADASTRAR NOVA PEÇA NO ESTOQUE</h2>
            
            <?php if (!empty($erro)): ?>
                <div class="alert-error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <div class="card-dark">
                <form method="POST">
                    
                    <div class="form-row">
                        <div class="grupo-input-dark flex-3">
                            <label>NOME DA PEÇA</label>
                            <input type="text" name="nome" placeholder="Ex: Pastilha de Freio" required>
                        </div>
                        <div class="grupo-input-dark flex-1">
                            <label>PREÇO DE VENDA</label>
                            <input type="text" id="preco_venda" name="preco_venda" placeholder="R$ 0,00" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="grupo-input-dark flex-1">
                            <label>ESTOQUE ATUAL</label>
                            <input type="number" name="estoque_atual" placeholder="Ex: 10" min="0" required>
                        </div>
                        <div class="grupo-input-dark flex-1">
                            <label>ESTOQUE MÍNIMO DE ALERTA</label>
                            <input type="number" name="estoque_minimo" placeholder="Ex: 5" min="0" required>
                        </div>
                    </div>

                    <div class="grupo-input-dark">
                        <label>URL DA IMAGEM DA PEÇA (OPCIONAL)</label>
                        <input type="text" name="url_imagem" placeholder="Ex: img/pastilha.jpg">
                    </div>

                    <div class="footer-acoes">
                        <button type="submit" class="btn-acao btn-salvar-os">SALVAR CADASTRO</button>
                        <a href="estoque-critico.php" class="btn-acao btn-voltar-os">VOLTAR</a>
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

        // Formatação simples do campo de preço
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
