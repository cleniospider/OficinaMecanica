<?php
// Redireciona para a página de conta unificada
require_once('conexao/conexao.php');
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}
header("Location: minha-conta.php");
exit;