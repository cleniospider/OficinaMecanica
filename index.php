<?php 
require_once('conexao/conexao.php');

$erro = "";
$sucesso = "";

// Tratar Logout
if (isset($_GET['logout'])) {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header("Location: index.php");
    exit;
}

// Tratar Login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao']) && $_POST['acao'] === 'login') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];

    if (!empty($email) && !empty($senha)) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome_completo'];
            $_SESSION['usuario_perfil'] = $usuario['perfil'];

            if ($usuario['perfil'] === 'Admin') {
                header("Location: admin.php");
                exit;
            } elseif ($usuario['perfil'] === 'Mecanico') {
                header("Location: mecan.php");
                exit;
            } elseif ($usuario['perfil'] === 'Recepcionista') {
                header("Location: recep.php");
                exit;
            }
        } else {
            $erro = "E-mail ou senha incorretos!";
        }
    } else {
        $erro = "Por favor, preencha todos os campos!";
    }
}

// Mensagens de retorno do cadastro
if (isset($_GET['cadastro_sucesso'])) {
    $sucesso = "Cadastro realizado com sucesso! Faça seu login.";
} elseif (isset($_GET['cadastro_erro'])) {
    $erro = htmlspecialchars($_GET['cadastro_erro']);
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Sistema</title>
    <link rel="stylesheet" href="oficina.css">
    <style>
        .alert-error {
            background-color: #e74c3c;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14px;
        }
        .alert-success {
            background-color: #2ecc71;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <main class="container-central">
        
        <div class="logo-area">
            <img src="img/download.png" alt="Logo Auto Repair">
        </div>

        <div class="forms-area">
            
            <div id="login-screen">
                <h1 class="title">LOGIN</h1>
                
                <?php if (!empty($erro)): ?>
                    <div class="alert-error"><?= $erro ?></div>
                <?php endif; ?>
                
                <?php if (!empty($sucesso)): ?>
                    <div class="alert-success"><?= $sucesso ?></div>
                <?php endif; ?>

                <form id="form-login" action="index.php" method="POST">
                    <input type="hidden" name="acao" value="login">
                    
                    <div class="form-group">
                        <label for="login-email">Email</label>
                        <input type="email" id="login-email" name="email" placeholder="email@gmail.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="login-senha">Senha</label>
                        <input type="password" id="login-senha" name="senha" placeholder="******" minlength="6" maxlength="10" required>
                    </div>

                    <div class="link-container">
                        não possui uma conta? <a id="link-ir-cadastro" style="cursor: pointer;">Cadastrar-se</a>
                    </div>

                    <button type="submit" class="btn-submit">Entrar</button>
                </form>
            </div>

            <div id="register-screen" class="hidden">
                <h1 class="title title-register">CADASTRO PROFISSIONAL</h1>

                <form id="form-register" action="bd/cadastro.php" method="post">
                    <div class="form-row">
                        <div class="form-group half-width">
                            <label for="reg-nome">Nome Completo</label>
                            <input type="text" id="reg-nome" name="nome" placeholder="Tássio" required>
                        </div>
                        <div class="form-group half-width">
                            <label for="reg-email">Email</label>
                            <input type="email" id="reg-email" name="email" placeholder="email@gmail.com" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group half-width">
                            <label for="reg-cpf">CPF</label>
                            <input type="text" id="reg-cpf" name="cpf" placeholder="000.000.000-00" maxlength="14" required>
                        </div>
                        <div class="form-group half-width">
                            <label for="reg-tel">Telefone</label>
                            <input type="text" id="reg-tel" name="telefone" placeholder="(00) 00000-0000" maxlength="15" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group half-width">
                            <label for="reg-senha">Senha</label>
                            <input type="password" id="reg-senha" name="senha" placeholder="******" minlength="6" maxlength="10" required>
                        </div>
                        <div class="form-group half-width">
                            <label for="reg-confirma-senha">Confirmação de senha</label>
                            <input type="password" id="reg-confirma-senha" name="confirma_senha" placeholder="******" minlength="6" maxlength="10" required>
                        </div>
                    </div>

                    <label class="label-cargo">Cargo</label>
                    <div class="roles-container roles-register">
                        <input type="radio" id="reg-admin" name="cargo" value="admin" required>
                        <label for="reg-admin">ADMIN</label>

                        <input type="radio" id="reg-recep" name="cargo" value="recep">
                        <label for="reg-recep">RECEP.</label>

                        <input type="radio" id="reg-mecan" name="cargo" value="mecan">
                        <label for="reg-mecan">MECÂN.</label>
                    </div>

                    <button type="submit" class="btn-submit">Cadastrar</button>

                    <div class="link-container link-back-container">
                        <a id="link-ir-login" style="cursor: pointer;">← Voltar para o Login</a>
                    </div>
                </form>
            </div>

        </div>
    </main>

    <script>
        // Lógica de alternância entre as telas
        const loginScreen = document.getElementById('login-screen');
        const registerScreen = document.getElementById('register-screen');
        const linkIrCadastro = document.getElementById('link-ir-cadastro');
        const linkIrLogin = document.getElementById('link-ir-login');

        linkIrCadastro.addEventListener('click', () => {
            loginScreen.classList.add('hidden');
            registerScreen.classList.remove('hidden');
        });

        linkIrLogin.addEventListener('click', () => {
            registerScreen.classList.add('hidden');
            loginScreen.classList.remove('hidden');
        });

        // Máscara do CPF (000.000.000-00)
        const inputCpf = document.getElementById('reg-cpf');
        inputCpf.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, "");
            if (value.length > 3) value = value.slice(0, 3) + "." + value.slice(3);
            if (value.length > 7) value = value.slice(0, 7) + "." + value.slice(7);
            if (value.length > 11) value = value.slice(0, 11) + "-" + value.slice(11);
            e.target.value = value.slice(0, 14);
        });

        // Máscara do Telefone ((00) 00000-0000)
        const inputTel = document.getElementById('reg-tel');
        inputTel.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, "");
            if (value.length > 0) value = "(" + value;
            if (value.length > 3) value = value.slice(0, 3) + ") " + value.slice(3);
            if (value.length > 10) value = value.slice(0, 10) + "-" + value.slice(10);
            e.target.value = value.slice(0, 15);
        });
    </script>
</body>
</html>
