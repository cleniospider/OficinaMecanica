<?php
require_once __DIR__ . '/../conexao/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $cpf = preg_replace('/\D/', '', $_POST['cpf']); // Remove formatação de CPF
    $telefone = trim($_POST['telefone']);
    $senha = $_POST['senha'];
    $confirma_senha = $_POST['confirma_senha'];
    $cargo = $_POST['cargo'];

    // Validações básicas
    if (empty($nome) || empty($email) || empty($cpf) || empty($senha) || empty($cargo)) {
        header("Location: ../index.php?cadastro_erro=" . urlencode("Todos os campos obrigatórios devem ser preenchidos!"));
        exit;
    }

    if (strlen($cpf) !== 11) {
        header("Location: ../index.php?cadastro_erro=" . urlencode("O CPF deve conter exatamente 11 dígitos!"));
        exit;
    }

    if ($senha !== $confirma_senha) {
        header("Location: ../index.php?cadastro_erro=" . urlencode("As senhas informadas não coincidem!"));
        exit;
    }

    // Mapeamento de cargo para perfil do ENUM
    $perfil = "";
    if ($cargo === 'admin') {
        $perfil = 'Admin';
    } elseif ($cargo === 'recep') {
        $perfil = 'Recepcionista';
    } elseif ($cargo === 'mecan') {
        $perfil = 'Mecanico';
    } else {
        header("Location: ../index.php?cadastro_erro=" . urlencode("Cargo inválido selecionado!"));
        exit;
    }

    try {
        // Verificar se CPF ou E-mail já estão cadastrados
        $stmt_check = $pdo->prepare("SELECT id FROM usuarios WHERE cpf = ? OR email = ?");
        $stmt_check->execute([$cpf, $email]);
        if ($stmt_check->fetch()) {
            header("Location: ../index.php?cadastro_erro=" . urlencode("CPF ou E-mail já cadastrado no sistema!"));
            exit;
        }

        // Criptografar a senha
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        // Inserir no banco
        $stmt = $pdo->prepare("INSERT INTO usuarios (nome_completo, email, cpf, perfil, telefone, senha) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nome, $email, $cpf, $perfil, $telefone, $senha_hash]);

        header("Location: ../index.php?cadastro_sucesso=1");
        exit;
    } catch (PDOException $e) {
        error_log("Erro no cadastro: " . $e->getMessage());
        header("Location: ../index.php?cadastro_erro=" . urlencode("Erro interno ao processar o cadastro. Tente novamente."));
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
?>
