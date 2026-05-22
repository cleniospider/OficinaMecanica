<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurações do Banco de Dados
$servidor = "127.0.0.1";
$usuario  = "root"; // Ajuste aqui conforme seu banco
$senha    = "senha";
$banco    = "oficinamecanica";
$porta    = 3306;

// 1. Criando a conexão (Usando Estilo Orientado a Objetos do MySQLi)
$conexao = new mysqli($servidor, $usuario, $senha, $banco, $porta);

// 2. Verificando se houve erro
if ($conexao->connect_error) {
    // Em desenvolvimento, mostramos o erro. Em produção, use o logger.
    error_log("Erro de conexão: " . $conexao->connect_error);
    die("Desculpe, ocorreu um problema técnico.");
}

// 3. Definindo o charset (Essencial para acentos e caracteres especiais)
$conexao->set_charset("utf8mb4");

// Incluindo ferramentas e constantes
require_once __DIR__ . '/../includes/logger.php';
define('BASE_URL', 'http://localhost/oficinamecanica/');
?>