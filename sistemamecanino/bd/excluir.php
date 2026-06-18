<?php
require_once __DIR__ . '/../conexao/conexao.php';

// Proteção para apenas Administradores
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil'] !== 'Admin') {
    header("Location: ../index.php");
    exit;
}

$id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

if ($id) {
    // Evitar que o usuário logado se exclua
    if ($id == $_SESSION['usuario_id']) {
        echo "<script>alert('Você não pode excluir sua própria conta!'); window.location.href='lista.php';</script>";
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
    } catch (PDOException $e) {
        // Tratar erro silenciosamente ou registrar
        error_log("Erro ao excluir usuário: " . $e->getMessage());
    }
}

header("Location: lista.php");
exit;
?>
