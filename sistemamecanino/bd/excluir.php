<?php
include 'conexao.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM profissionais WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location:index.php");
exit;
