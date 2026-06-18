<?php
require_once __DIR__ . '/../conexao/conexao.php';

// Proteção para apenas Administradores acessarem
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil'] !== 'Admin') {
    header("Location: ../index.php");
    exit;
}

$query = $pdo->query("SELECT * FROM usuarios ORDER BY nome_completo ASC");
$usuarios = $query->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Profissionais - Auto Repair</title>
    <link rel="stylesheet" href="../oficina.css"> 
    <style>
        .tabela-container { width: 90%; margin: 50px auto; background: rgba(0,0,0,0.8); padding: 20px; border-radius: 10px; color: white; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #444; text-align: left; }
        th { background-color: #f1c40f; color: black; }
        .btn-edit { color: #3498db; text-decoration: none; font-weight: bold; }
        .btn-del { color: #e74c3c; text-decoration: none; margin-left: 10px; font-weight: bold; }
    </style>
</head>
<body>

<div class="tabela-container">
    <h2>Profissionais Cadastrados</h2>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome Completo</th>
                <th>Email</th>
                <th>CPF</th>
                <th>Cargo / Perfil</th>
                <th>Telefone</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['id']) ?></td>
                <td><?= htmlspecialchars($u['nome_completo']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['cpf']) ?></td>
                <td><?= htmlspecialchars($u['perfil']) ?></td>
                <td><?= htmlspecialchars($u['telefone']) ?></td>
                <td>
                    <a href="editar.php?id=<?= $u['id'] ?>" class="btn-edit">Editar</a>
                    <a href="excluir.php?id=<?= $u['id'] ?>" class="btn-del" onclick="return confirm('Deseja excluir este funcionário?')">Excluir</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <br>
    <a href="../admin.php" style="color: #fff;">← Voltar ao Painel</a>
</div>

</body>
</html>
