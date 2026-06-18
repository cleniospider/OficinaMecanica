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
    <title>Editar Profissional - Auto Repair</title>
    <link rel="stylesheet" href="../oficina.css">
    <style>
        .form-container { width: 50%; margin: 50px auto; background: rgba(0,0,0,0.8); padding: 30px; border-radius: 10px; color: white; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #f1c40f; }
        .form-group input, .form-group select { width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #444; background: #222; color: #fff; }
        .btn-salvar { background-color: #2ecc71; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .btn-cancelar { background-color: #7f8c8d; color: white; padding: 10px 15px; border: none; border-radius: 5px; text-decoration: none; display: inline-block; font-weight: bold; margin-left: 10px; }
        .alert-error { background-color: #e74c3c; color: white; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Editar Profissional</h2>
    
    <?php if (isset($erro)): ?>
        <div class="alert-error"><?= htmlspecialchars($erro) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="nome_completo">Nome Completo</label>
            <input type="text" id="nome_completo" name="nome_completo" value="<?= htmlspecialchars($dados['nome_completo']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($dados['email']) ?>" required>
        </div>

        <div class="form-group">
            <label for="telefone">Telefone</label>
            <input type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($dados['telefone']) ?>">
        </div>

        <div class="form-group">
            <label for="perfil">Cargo / Perfil</label>
            <select id="perfil" name="perfil">
                <option value="admin" <?= $dados['perfil'] == 'Admin' ? 'selected' : '' ?>>ADMIN</option>
                <option value="recep" <?= $dados['perfil'] == 'Recepcionista' ? 'selected' : '' ?>>RECEP</option>
                <option value="mecan" <?= $dados['perfil'] == 'Mecanico' ? 'selected' : '' ?>>MECÂN</option>
            </select>
        </div>
        
        <button type="submit" class="btn-salvar">Salvar Alterações</button>
        <a href="lista.php" class="btn-cancelar">Cancelar</a>
    </form>
</div>

</body>
</html>
