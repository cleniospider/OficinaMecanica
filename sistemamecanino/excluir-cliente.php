<?php 
require_once('conexao/conexao.php');

// Proteção de sessão
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_perfil'], ['Admin', 'Recepcionista'])) {
    header("Location: index.php");
    exit;
}

$erro = "";
$cpf = $_GET['cpf'] ?? '';
$cpf_limpo = preg_replace('/\D/', '', $cpf);

// Buscar cliente
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE cpf = ?");
$stmt->execute([$cpf_limpo]);
$cliente = $stmt->fetch();

if (!$cliente) {
    header("Location: cadastrocliente.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Verificar se possui veículos
        $stmt_check = $pdo->prepare("SELECT id FROM veiculo WHERE clientes_cpf = ?");
        $stmt_check->execute([$cpf_limpo]);
        if ($stmt_check->fetch()) {
            $erro = "Não é possível excluir o cliente porque ele possui veículos vinculados!";
        } else {
            $stmt_del = $pdo->prepare("DELETE FROM clientes WHERE cpf = ?");
            $stmt_del->execute([$cpf_limpo]);
            header("Location: cadastrocliente.php?cadastro_sucesso=" . urlencode("Cliente excluído com sucesso!"));
            exit;
        }
    } catch (PDOException $e) {
        $erro = "Erro ao excluir cliente: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Excluir Cliente</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/excluir-cliente.css">
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
        <div class="cliente-exclusao-container">
            <h2 class="titulo-sessao">EXCLUIR CLIENTE</h2>

            <?php if (!empty($erro)): ?>
                <div class="alert-error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <div class="caixa-exclusao">
                <div class="icone-alerta">⚠️</div>
                <h3>Tem certeza que deseja excluir o cliente "<?= htmlspecialchars($cliente['nome completo']) ?>"?</h3>
                <p class="aviso-texto">Esta ação removerá permanentemente os dados do cliente. Você não poderá excluir caso existam veículos vinculados.</p>
                
                <form method="POST" class="form-exclusao">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id ?? $_GET['id']) ?>">
                    <div class="botoes-acao-excluir">
                        <a href="cadastrocliente.php" class="btn-cancelar-exclusao">CANCELAR</a>
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
