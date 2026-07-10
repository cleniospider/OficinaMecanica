<?php
require_once __DIR__ . '/../conexao/conexao.php';

// Proteção para apenas Administradores
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil'] !== 'Admin') {
    header("Location: ../index.php");
    exit;
}

$id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: lista.php");
    exit;
}

// Buscar dados do usuário
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$dados = $stmt->fetch();

if (!$dados) {
    header("Location: lista.php");
    exit;
}

$erro = "";
$sucesso = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome_completo']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $telefone = trim($_POST['telefone']);
    $perfil_form = $_POST['perfil'];

    // Mapeamento
    $perfil = "";
    if ($perfil_form === 'admin') {
        $perfil = 'Admin';
    } elseif ($perfil_form === 'recep') {
        $perfil = 'Recepcionista';
    } elseif ($perfil_form === 'mecan') {
        $perfil = 'Mecanico';
    }

    if (!empty($nome) && !empty($email) && !empty($perfil)) {
        try {
            $update = $pdo->prepare("UPDATE usuarios SET nome_completo = ?, email = ?, telefone = ?, perfil = ? WHERE id = ?");
            $update->execute([$nome, $email, $telefone, $perfil, $id]);
            $sucesso = "Usuário atualizado com sucesso!";
            header("Location: lista.php");
            exit;
        } catch (PDOException $e) {
            $erro = "Erro ao atualizar usuário: " . $e->getMessage();
        }
    } else {
        $erro = "Preencha todos os campos obrigatórios!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Editar Profissional</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/ordens.css">
    <link rel="stylesheet" href="../css/nova-ordem.css">
    <style>
        .form-edit-container {
            max-width: 600px;
            margin: 0 auto;
            background: #1c1c1e;
            padding: 30px;
            border-radius: 15px;
            border: 1px solid #2c2c2e;
        }
        .form-group-custom {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .form-group-custom label {
            color: #8e8e93;
            font-size: 0.85rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        .form-group-custom input, .form-group-custom select {
            background-color: #121212;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 12px;
            color: #fff;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.3s;
        }
        .form-group-custom input:focus, .form-group-custom select:focus {
            border-color: #ff0000;
        }
        .alert-error {
            background-color: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
            border: 1px solid #e74c3c;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
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
            <div class="os-header-detalhe">
                <h2>EDITAR <span class="text-red">PROFISSIONAL</span></h2>
            </div>

            <div class="form-edit-container">
                <?php if (!empty($erro)): ?>
                    <div class="alert-error"><?= htmlspecialchars($erro) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group-custom">
                        <label for="nome_completo">Nome Completo</label>
                        <input type="text" id="nome_completo" name="nome_completo" value="<?= htmlspecialchars($dados['nome_completo']) ?>" required>
                    </div>

                    <div class="form-group-custom">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($dados['email']) ?>" required>
                    </div>

                    <div class="form-group-custom">
                        <label for="telefone">Telefone</label>
                        <input type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($dados['telefone'] ?: '') ?>">
                    </div>

                    <div class="form-group-custom">
                        <label for="perfil">Cargo / Perfil</label>
                        <select id="perfil" name="perfil">
                            <option value="admin" <?= $dados['perfil'] == 'Admin' ? 'selected' : '' ?>>ADMIN</option>
                            <option value="recep" <?= $dados['perfil'] == 'Recepcionista' ? 'selected' : '' ?>>RECEPCIONISTA</option>
                            <option value="mecan" <?= $dados['perfil'] == 'Mecanico' ? 'selected' : '' ?>>MECÂNICO</option>
                        </select>
                    </div>

                    <div class="botoes-acao" style="margin-top: 25px;">
                        <button type="submit" class="btn-os btn-salvar-red" style="width: 48%;">SALVAR</button>
                        <a href="lista.php" class="btn-os btn-voltar-dark" style="width: 48%; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">CANCELAR</a>
                    </div>
                </form>
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
    </script>
</body>
</html>
