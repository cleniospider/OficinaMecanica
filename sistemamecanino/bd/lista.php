<?php

$pdo = new PDO("mysql:host=localhost;dbname=auto_repair", "root", "");

$query = $pdo->query("SELECT * FROM profissionais ORDER BY nome ASC");
$profissionais = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Profissionais - Auto Repair</title>
    <link rel="stylesheet" href="oficina.css"> <style>
        /* Estilo rápido para a tabela aparecer bem no seu fundo escuro */
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
                <th>Nome</th>
                <th>Email</th>
                <th>Cargo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($profissionais as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= $p['nome'] ?></td>
                <td><?= $p['email'] ?></td>
                <td><?= strtoupper($p['cargo']) ?></td>
                <td>
                    <a href="editar.php?id=<?= $p['id'] ?>" class="btn-edit">Editar</a>
                    <a href="excluir.php?id=<?= $p['id'] ?>" class="btn-del" onclick="return confirm('Deseja excluir este funcionário?')">Excluir</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <br>
    <a href="admin.html" style="color: #fff;">← Voltar ao Painel</a>
</div>

</body>
</html>
