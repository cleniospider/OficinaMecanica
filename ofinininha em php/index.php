<?php
// Inicializa variáveis para mostrar mensagens na tela
$mensagem_erro = "";
$mensagem_sucesso = "";

// Verifica se algum formulário foi enviado (método POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Identifica qual formulário foi enviado através do campo oculto 'acao'
    $acao = $_POST['acao'] ?? '';

    // ==========================================
    // LÓGICA DE CADASTRO
    // ==========================================
    if ($acao == 'cadastrar') {
        // Captura os dados usando o atributo 'name' dos inputs
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $cpf = $_POST['cpf'];
        $telefone = $_POST['telefone'];
        $senha = $_POST['senha'];
        $confirma_senha = $_POST['confirma_senha'];
        $cargo = $_POST['reg-cargo'];

        // Validação básica
        if ($senha !== $confirma_senha) {
            $mensagem_erro = "Erro no cadastro: As senhas não coincidem!";
        } else {
            // Aqui você conectaria com seu Banco de Dados (MySQL, por exemplo)
            // Exemplo do que aconteceria aqui:
            // 1. Criptografar a senha: $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            // 2. Inserir no banco: INSERT INTO usuarios (nome, email, cpf...) VALUES (...)
            
            $mensagem_sucesso = "Cadastro realizado com sucesso! Você já pode fazer login.";
        }
    }

    // ==========================================
    // LÓGICA DE LOGIN
    // ==========================================
    elseif ($acao == 'login') {
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        $cargo = $_POST['login-cargo'];

        // Aqui você faria a busca no Banco de Dados para ver se o usuário existe
        // Exemplo: SELECT * FROM usuarios WHERE email = '$email' AND cargo = '$cargo'
        
        // Simulação de login (apenas para teste)
        if ($email == 'admin@gmail.com' && $senha == '123456' && $cargo == 'admin') {
            // Inicia a sessão e redireciona para a página do sistema
            session_start();
            $_SESSION['logado'] = true;
            $_SESSION['usuario'] = "Admin";
            
            // Redireciona para admin.html (ou admin.php)
            header("Location: admin.html");
            exit();
        } else {
            $mensagem_erro = "Erro no login: E-mail, senha ou cargo incorretos!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Sistema</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* CSS extra apenas para as mensagens de erro/sucesso do PHP */
        .msg-erro { background-color: #ffcccc; color: #cc0000; padding: 10px; text-align: center; border-radius: 5px; margin-bottom: 15px; font-weight: bold; }
        .msg-sucesso { background-color: #ccffcc; color: #006600; padding: 10px; text-align: center; border-radius: 5px; margin-bottom: 15px; font-weight: bold; }
    </style>
</head>
<body>

    <main class="container-central">
        
        <div class="logo-area">
            <img src="img/download.png" alt="Logo Auto Repair">
        </div>

        <div class="forms-area">
            
            <?php if (!empty($mensagem_erro)): ?>
                <div class="msg-erro"><?php echo $mensagem_erro; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($mensagem_sucesso)): ?>
                <div class="msg-sucesso"><?php echo $mensagem_sucesso; ?></div>
            <?php endif; ?>

            <div id="login-screen">
                <h1 class="title">LOGIN</h1>

                <form id="form-login" action="" method="POST">
                    
                    <input type="hidden" name="acao" value="login">

                    <div class="form-group">
                        <label for="login-email">Email</label>
                        <input type="email" id="login-email" name="email" placeholder="email@gmail.com" required>
                    </div>

                    <div class="form-group">
                        <label for="login-senha">Senha</label>
                        <input type="password" id="login-senha" name="senha" placeholder="******" minlength="6" maxlength="10" required>
                    </div>

                    <div class="roles-container">
                        <input type="radio" id="login-admin" name="login-cargo" value="admin" required>
                        <label for="login-admin">ADMIN</label>

                        <input type="radio" id="login-recep" name="login-cargo" value="recep">
                        <label for="login-recep">RECEP.</label>

                        <input type="radio" id="login-mecan" name="login-cargo" value="mecan">
                        <label for="login-mecan">MECÂN.</label>
                    </div>

                    <div class="link-container">
                        não possui uma conta? <a id="link-ir-cadastro" style="cursor: pointer;">Cadastrar - se</a>
                    </div>

                    <button type="submit" class="btn-submit">Entrar</button>
                </form>
            </div>

            <div id="register-screen" class="hidden">
                <h1 class="title title-register">CADASTRO PROFISSIONAL</h1>

                <form id="form-register" action="" method="POST">
                    
                    <input type="hidden" name="acao" value="cadastrar">

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
                        <input type="radio" id="reg-admin" name="reg-cargo" value="admin" required>
                        <label for="reg-admin">ADMIN</label>

                        <input type="radio" id="reg-recep" name="reg-cargo" value="recep">
                        <label for="reg-recep">RECEP.</label>

                        <input type="radio" id="reg-mecan" name="reg-cargo" value="mecan">
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
        // O Javascript continua exatamente igual, controlando a parte visual
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

        const inputCpf = document.getElementById('reg-cpf');
        inputCpf.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, "");
            if (value.length > 3) value = value.slice(0, 3) + "." + value.slice(3);
            if (value.length > 7) value = value.slice(0, 7) + "." + value.slice(7);
            if (value.length > 11) value = value.slice(0, 11) + "-" + value.slice(11);
            e.target.value = value.slice(0, 14);
        });

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