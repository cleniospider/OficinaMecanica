<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurações do Banco de Dados
$servidor = "localhost";
$usuario  = "root"; // Ajuste aqui conforme seu banco
$senha    = "";
$banco    = "oficinamecanica";
$porta    = 3306;

// 1. Criando a conexão (Usando Estilo Orientado a Objetos do MySQLi)
$conexao = new mysqli($servidor, $usuario, $senha, $banco, $porta);

// 2. Verificando se houve erro
if ($conexao->connect_error) {
    error_log("Erro de conexão: " . $conexao->connect_error);
    die("Desculpe, ocorreu um problema técnico.");
}

// 3. Definindo o charset
$conexao->set_charset("utf8");

// 4. Criando a conexão PDO unificada
try {
    $pdo = new PDO("mysql:host=$servidor;port=$porta;dbname=$banco;charset=utf8mb4", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erro de conexão PDO: " . $e->getMessage());
    die("Desculpe, ocorreu um problema técnico com o banco de dados.");
}

define('BASE_URL', 'http://localhost/oficinamecanica/index.php');
?>