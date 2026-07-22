<?php 
require_once('conexao/conexao.php');

// Proteção de sessão
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_perfil'], ['Admin', 'Recepcionista'])) {
    header("Location: index.php");
    exit;
}

$erro = "";
$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: cadastroveiculo.php");
    exit;
}

// Buscar dados do veículo
try {
    $stmt_veic = $pdo->prepare("SELECT * FROM veiculo WHERE id = ?");
    $stmt_veic->execute([$id]);
    $veic = $stmt_veic->fetch();

    if (!$veic) {
        header("Location: cadastroveiculo.php");
        exit;
    }
} catch (PDOException $e) {
    die("Erro ao carregar dados: " . $e->getMessage());
}

// Processar exclusão
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Verificar se possui ordens de serviço
        $stmt_check_os = $pdo->prepare("SELECT id FROM OS WHERE veiculo_id1 = ?");
        $stmt_check_os->execute([$id]);
        if ($stmt_check_os->fetch()) {
            $erro = "Não é possível excluir o veículo porque ele possui ordens de serviço vinculadas!";
        } else {
            $stmt_del = $pdo->prepare("DELETE FROM veiculo WHERE id = ?");
            $stmt_del->execute([$id]);
            header("Location: cadastroveiculo.php?cadastro_sucesso=" . urlencode("Veículo excluído com sucesso!"));
            exit;
        }
    } catch (PDOException $e) {
        $erro = "Erro ao excluir veículo: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Excluir Veículo</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/excluir-veiculo.css">
    <style>
        .alert-error {
            background-color: #e74c3c;
            color: white;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
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
                <span class="role-text">ADMINISTRADOR</span>
            </div>
        </div>

        <ul class="nav-links">
            <li><a href="admin.php" >Painel de Gestão</a></li>
            <li><a href="cadastrocliente.php" >Cadastro Cliente</a></li>
            <li><a href="cadastroveiculo.php" class="active">Cadastro Veículo</a></li>
            <li><a href="ordens.php">Ordens de Serviços</a></li>
            <li><a href="estoque-critico.php">Estoque de Peças</a></li>
            <li><a href="historico-veiculos.php">Histórico de Veículos</a></li>
            <li><a href="financeiro.php">Financeiro</a></li>
            <li><a href="relatorios.php">Relatórios</a></li>
            <li><a href="minha-conta.php">Minha Conta</a></li>
            <li><a href="index.php?logout=1" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="veiculo-container">
            <h2 class="titulo-sessao">EXCLUIR VEÍCULO</h2>

            <?php if (!empty($erro)): ?>
                <div class="alert-error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <div class="caixa-exclusao">
                <div class="icone-alerta">⚠️</div>
                <h3>Tem certeza que deseja excluir o veículo "<?= htmlspecialchars($veic['marca/modelo']) ?>" (Placa: <?= htmlspecialchars($veic['placa']) ?>)?</h3>
                <p class="aviso-texto">Esta ação removerá permanentemente o veículo do sistema.</p>
                
                <form method="POST" class="form-exclusao">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id ?? $_GET['id']) ?>">
                    <div class="botoes-acao-excluir">
                        <a href="cadastroveiculo.php" class="btn-cancelar-exclusao">CANCELAR</a>
                        <button type="submit" class="btn-confirmar-exclusao">SIM, EXCLUIR</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        const btnMobile = document.querySelector('.hamburger-btn');
        const sidebar = document.querySelector('#sidebar');
        if(btnMobile && sidebar) {
            btnMobile.addEventListener('click', () => sidebar.classList.toggle('open'));
        }
    </script>
</body>
</html>
