<?php 
require_once('conexao/conexao.php');

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_perfil'], ['Admin', 'Recepcionista'])) {
    header("Location: index.php");
    exit;
}

$erro = "";
$sucesso = "";

$cpf = $_GET['cpf'] ?? '';
$cpf_limpo = preg_replace('/\D/', '', $cpf);

// Buscar cliente
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE cpf = ?");
$stmt->execute([$cpf_limpo]);
$cliente = $stmt->fetch();

if (!$cliente) {
    header("Location: cadastrocliente.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);

    if (!empty($nome) && !empty($telefone)) {
        try {
            $stmt_update = $pdo->prepare("UPDATE clientes SET `nome completo` = ?, telefone = ?, email = ? WHERE cpf = ?");
            $stmt_update->execute([$nome, $telefone, $email, $cpf_limpo]);
            
            // Sincronizar o nome do cliente na tabela de veículos
            $stmt_up_veic = $pdo->prepare("UPDATE veiculo SET cliente = ? WHERE clientes_cpf = ?");
            $stmt_up_veic->execute([$nome, $cpf_limpo]);

            header("Location: cadastrocliente.php?cadastro_sucesso=" . urlencode("Cliente atualizado com sucesso!"));
            exit;
        } catch (PDOException $e) {
            $erro = "Erro ao atualizar cliente: " . $e->getMessage();
        }
    } else {
        $erro = "Preencha todos os campos obrigatórios!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Editar Cliente</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/editar_cliente.css">
    <style>
        .alert-error {
            background-color: #e74c3c;
            color: white;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body class="dark-theme">

    <header class="top-header">
        <button class="hamburger-btn">
            <span></span><span></span><span></span>
        </button>
        <div class="header-logo-text">AUTO REPAIR</div>
    </header>

    <aside class="sidebar" id="sidebar">
        <div class="profile-area">
            <img src="img/download.png" alt="Avatar" class="avatar"> 
            <div class="mobile-profile-text">
                AUTO REPAIR<br>
                <span class="role-text">ADMINISTRADOR</span>
            </div>
        </div>

        <ul class="nav-links">
            <li><a href="admin.php" >Painel de Gestão</a></li>
            <li><a href="cadastrocliente.php" class="active">Cadastro Cliente</a></li>
            <li><a href="cadastroveiculo.php">Cadastro Veículo</a></li>
            <li><a href="ordens.php">Ordens de Serviços</a></li>
            <li><a href="estoque-critico.php">Estoque de Peças</a></li>
            <li><a href="historico-veiculos.php">Histórico de Veículos</a></li>
            <li><a href="financeiro.php">Financeiro</a></li>
            <li><a href="relatorios.php">Relatórios</a></li>
            <li><a href="minha-conta.php">Minha Conta</a></li>
            <li><a href="index.php?logout=1" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="container-os">
            
            <h2 class="titulo-os">EDITAR CADASTRO <span class="n-os">DE CLIENTE</span></h2>

            <?php if (!empty($erro)): ?>
                <div class="alert-error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <div class="caixa-gerenciar">
                <form class="form-os" method="POST">
                    
                    <div class="campo-grupo">
                        <label>Nome Completo:</label>
                        <input type="text" value="<?= htmlspecialchars($cliente['nome completo']) ?>" name="nome" required>
                    </div>

                    <div class="campo-grupo">
                        <label>CPF / CNPJ:</label>
                        <input type="text" id="cpf_cnpj" value="<?= htmlspecialchars($cliente['cpf']) ?>" readonly style="background: #2a2a2a; color: #888; cursor: not-allowed;">
                    </div>

                    <div class="linha-dupla">
                        <div class="campo-grupo">
                            <label>Telefone / WhatsApp:</label>
                            <input type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($cliente['telefone']) ?>" required maxlength="15">
                        </div>

                        <div class="campo-grupo">
                            <label>E-mail:</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($cliente['email']) ?>">
                        </div>
                    </div>

                    <div class="botoes-os">
                        <button type="submit" class="btn-finalizar-os">ATUALIZAR CADASTRO</button>
                        <a href="cadastrocliente.php" class="btn-voltar-os">CANCELAR</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Máscara de Telefone ( (11) 99999-9999 )
        const handlePhone = (event) => {
            let input = event.target;
            input.value = phoneMask(input.value);
        }

        const phoneMask = (value) => {
            if (!value) return "";
            value = value.replace(/\D/g, '');
            value = value.replace(/(\d{2})(\d)/, "($1) $2");
            value = value.replace(/(\d)(\d{4})$/, "$1-$2");
            return value;
        }

        // Máscara de CPF / CNPJ
        const handleCpfCnpj = (event) => {
            let input = event.target;
            let value = input.value.replace(/\D/g, '');
            
            if (value.length <= 11) {
                // CPF: 000.000.000-00
                value = value.replace(/(\d{3})(\d)/, "$1.$2");
                value = value.replace(/(\d{3})(\d)/, "$1.$2");
                value = value.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
            } else {
                // CNPJ: 00.000.000/0000-00
                value = value.replace(/^(\d{2})(\d)/, "$1.$2");
                value = value.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
                value = value.replace(/\.(\d{3})(\d)/, ".$1/$2");
                value = value.replace(/(\d{4})(\d)/, "$1-$2");
            }
            input.value = value;
        }

        // Aplicando os eventos nos inputs
        document.getElementById('telefone').addEventListener('keyup', handlePhone);
        document.getElementById('cpf_cnpj').addEventListener('keyup', handleCpfCnpj);
    </script>
    <script>
        const btnMobile = document.querySelector('.hamburger-btn');
        const sidebar = document.querySelector('#sidebar');

        // Abre e fecha o menu lateral
        btnMobile.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        // Fecha o menu ao clicar em um link (essencial para mobile)
        const links = document.querySelectorAll('.nav-links a');
        links.forEach(link => {
            link.addEventListener('click', () => {
                sidebar.classList.remove('open');
            });
        });

        // Lógica do Modal de Conta
        const linkConta = document.querySelector('a[style*="cursor:pointer"]'); 
        const modal = document.querySelector('#modal-conta');
        const btnFechar = document.querySelector('.btn-fechar-modal');
        const btnX = document.querySelector('.close-btn');

        if(linkConta) {
            linkConta.addEventListener('click', (e) => {
                e.preventDefault();
                modal.style.display = 'flex';
            });
        }

        [btnFechar, btnX].forEach(btn => {
            if(btn) {
                btn.addEventListener('click', () => {
                    modal.style.display = 'none';
                });
            }
        });

        // Fecha o modal se clicar fora dele
        window.addEventListener('click', (e) => {
            if (e.target == modal) {
                modal.style.display = 'none';
            }
        });
    </script>
</body> </html>

</body>
</html>
