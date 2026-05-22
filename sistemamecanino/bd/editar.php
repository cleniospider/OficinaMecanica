<?php
include 'conexao.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM profissionais WHERE id = ?");
$stmt->execute([$id]);
$dados = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $cargo = $_POST['cargo'];

    $update = $pdo->prepare("UPDATE profissionais SET nome = ?, cargo = ? WHERE id = ?");
    $update->execute([$nome, $cargo, $id]);

    header("Location: index.php"); // Volta para a lista
}
?>

<form method="POST">
    <input type="text" name="nome" value="<?= $dados['nome'] ?>" required>
    <select name="cargo">
        <option value="admin" <?= $dados['cargo'] == 'admin' ? 'selected' : '' ?>>ADMIN</option>
        <option value="recep" <?= $dados['cargo'] == 'recep' ? 'selected' : '' ?>>RECEP</option>
        <option value="mecan" <?= $dados['cargo'] == 'mecan' ? 'selected' : '' ?>>MECÂN</option>
    </select>
    <button type="submit">Salvar Alterações</button>
</form>
