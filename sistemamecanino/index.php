<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Sistema</title>
    <link rel="stylesheet" href="oficina.css">
</head>
<body>

    <main class="container-central">
        
        <div class="logo-area">
            <img src="img/download.png" alt="Logo Auto Repair" method = "post">
        </div>

        <div class="forms-area">
            
            <div id="login-screen">
                <h1 class="title">LOGIN</h1>
                <form action="cadastro.php">
                <form id="form-login" action="admin.html">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" placeholder="email@gmail.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="senha">Senha</label>
                        <input type="password" id="senha" placeholder="******" minlength="6" maxlength="10" required>
                    </div>
                </form>
                    <div class="roles-container">
                        <input type="radio" id="admin" name="cargo" value="admin" required>
                        <label for="admin">ADMIN</label>

                        <input type="radio" id="recep" name="cargo" value="recep">
                        <label for="recep">RECEP.</label>

                        <input type="radio" id="mecan" name="cargo" value="mecan">
                        <label for="mecan">MECÂN.</label>
                    </div>

                    <div class="link-container">
                        não possui uma conta? <a id="link-ir-cadastro" style="cursor: pointer;">Cadastrar - se</a>
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
                            <input type="text" id="nome" name="nome" placeholder="Tássio" required>
                        </div>
                        <div class="form-group half-width">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="email@gmail.com" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group half-width">
                            <label for="reg-cpf">CPF</label>
                            <input type="text" id="cpf" name = "cpf" placeholder="000.000.000-00" maxlength="14" required>
                        </div>
                        <div class="form-group half-width">
                            <label for="telefone">Telefone</label>
                            <input type="text" id="telefone" name = "telefone" placeholder="(00) 00000-0000" maxlength="15" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group half-width">
                            <label for="senha">Senha</label>
                            <input type="password" id="senha" name = "senha" placeholder="******" minlength="6" maxlength="10" required>
                        </div>
                        <div class="form-group half-width">
                            <label for="reg-confirma-senha">Confirmação de senha</label>
                            <input type="password" id="reg-confirma-senha" name = "senha" placeholder="******" minlength="6" maxlength="10" required>
                        </div>
                    </div>

                    <label class="label-cargo">Cargo</label>
                    <div class="roles-container roles-register">
                        <input type="radio" id="admin" name="cargo" value="admin" required>
                        <label for="admin">ADMIN</label>

                        <input type="radio" id="recep" name="cargo" value="recep">
                        <label for="recep">RECEP.</label>

                        <input type="radio" id="mecan" name="cargo" value="mecan">
                        <label for="mecan">MECÂN.</label>
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
