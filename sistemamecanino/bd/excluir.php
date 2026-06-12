<?php
include 'conexao.php';

$id = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM profissionais WHERE id = ?");
    $stmt->execute([$id]);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $cargo = $_POST['cargo'];

    $update = $pdo->prepare("UPDATE profissionais SET nome = ?, cargo = ? WHERE id = ?");
    $update->execute([$nome, $cargo, $id]);

    header("Location: index.php"); // Volta para a lista
    exit; 
}
?>
