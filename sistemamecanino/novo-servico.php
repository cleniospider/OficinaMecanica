<?php 
require_once('conexao/conexao.php');

// Proteção de sessão
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_perfil'], ['Admin', 'Mecanico', 'Recepcionista'])) {
    header("Location: index.php");
    exit;
}

$erro = "";
$sucesso = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nome'])) {
    $nome = trim($_POST['nome']);
    
    // Limpar preco
    $valor_str = $_POST['preco'];
    $valor_clean = str_replace(['R$', ' ', '.', ','], ['', '', '', '.'], $valor_str);
    $preco = floatval($valor_clean);

    if (!empty($nome) && $preco >= 0) {
        try {
            $stmt = $pdo->prepare("INSERT INTO servicos (nome, preco) VALUES (?, ?)");
            $stmt->execute([$nome, $preco]);
            header("Location: servicos.php");
            exit;
        } catch (PDOException $e) {
            $erro = "Erro ao cadastrar serviço: " . $e->getMessage();
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
    <title>Auto Repair - Novo Serviço</title>
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
            <li><a href="<?= $_SESSION['usuario_perfil'] === 'Admin' ? 'admin.php' : ($_SESSION['usuario_perfil'] === 'Mecanico' ? 'mecan.php' : 'recep.php') ?>">Painel de Gestão</a></li>
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
        <div class="container-form-dark">
            <h2 class="titulo-sessao">CADASTRAR NOVO SERVIÇO</h2>
            
            <?php if (!empty($erro)): ?>
                <div class="alert-error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <div class="card-dark">
                <form method="POST">
                    
                    <div class="form-row">
                        <div class="grupo-input-dark flex-3">
                            <label>NOME DO SERVIÇO</label>
                            <input type="text" name="nome" placeholder="Ex: Troca de Óleo" required>
                        </div>
                        <div class="grupo-input-dark flex-1">
                            <label>PREÇO</label>
                            <input type="text" id="preco" name="preco" placeholder="R$ 0,00" required>
                        </div>
                    </div>

                    <div class="footer-acoes">
                        <button type="submit" class="btn-acao btn-salvar-os">SALVAR CADASTRO</button>
                        <a href="servicos.php" class="btn-acao btn-voltar-os">VOLTAR</a>
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

        const precoInput = document.getElementById('preco');
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
