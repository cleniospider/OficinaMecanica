<?php 
session_start();
require_once('conexao/conexao.php');

// Proteção de sessão
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil'] !== 'Recepcionista') {
    header("Location: index.php");
    exit;
}

$id_usuario = $_SESSION['usuario_id'];
$erro = "";
$sucesso = "";

// 1. Buscar os dados REAIS do usuário logado no banco de dados
try {
    $stmt = $pdo->prepare("SELECT nome_completo, email FROM usuarios WHERE id = ?");
    $stmt->execute([$id_usuario]);
    $usuario = $stmt->fetch();
    
    if (!$usuario) {
        header("Location: index.php?logout=1");
        exit;
    }
} catch (PDOException $e) {
    $erro = "Erro ao carregar dados: " . $e->getMessage();
}

// 2. Processar a atualização real dos dados quando clicar em salvar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $senha_atual = $_POST['senha_atual'];
    $senha_nova = $_POST['senha_nova'];

    if (!$email) {
        $erro = "Por favor, insira um e-mail válido!";
    } else {
        try {
            // Verificar se o e-mail já existe em outro usuário
            $stmt_check = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
            $stmt_check->execute([$email, $id_usuario]);
            
            if ($stmt_check->fetch()) {
                $erro = "Este e-mail já está em uso por outro usuário!";
            } else {
                // Se deseja alterar a senha
                if (!empty($senha_nova)) {
                    if (empty($senha_atual)) {
                        $erro = "Você precisa digitar a senha atual para definir uma nova!";
                    } else {
                        // Verificar se a senha atual está correta (usando MD5 para manter o padrão do seu login)
                        $stmt_senha = $pdo->prepare("SELECT senha FROM usuarios WHERE id = ?");
                        $stmt_senha->execute([$id_usuario]);
                        $senha_banco = $stmt_senha->fetchColumn();

                        if (md5($senha_atual) !== $senha_banco) {
                            $erro = "A senha atual digitada está incorreta!";
                        } else {
                            // Atualiza e-mail e senha nova
                            $stmt_update = $pdo->prepare("UPDATE usuarios SET email = ?, senha = ? WHERE id = ?");
                            $stmt_update->execute([$email, md5($senha_nova), $id_usuario]);
                            $sucesso = "Configurações e nova senha salvas com sucesso!";
                        }
                    }
                } else {
                    // Atualiza apenas o e-mail
                    $stmt_update = $pdo->prepare("UPDATE usuarios SET email = ? WHERE id = ?");
                    $stmt_update->execute([$email, $id_usuario]);
                    $sucesso = "E-mail de contato atualizado com sucesso!";
                }

                // Atualizar dados na tela
                if (empty($erro)) {
                    $usuario['email'] = $email;
                }
            }
        } catch (PDOException $e) {
            $erro = "Erro ao salvar alterações: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Configurações (Recepção)</title>
    <link rel="stylesheet" href="css/admin.css"> 
    <link rel="stylesheet" href="css/configuracoes.css"> 
    <style>
        .alert-error {
            background-color: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
            border: 1px solid #e74c3c;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .alert-success {
            background-color: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
            border: 1px solid #2ecc71;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body class="dark-theme">

    <header class="top-header">
        <button class="hamburger-btn" id="btn-mobile">
            <span></span><span></span><span></span>
        </button>
        <div class="header-logo-text">AUTO REPAIR</div>
    </header>

    <aside class="sidebar" id="sidebar">
        <div class="profile-area">
            <img src="img/download.png" alt="Avatar" class="avatar"> 
            <div class="mobile-profile-text">
                AUTO REPAIR<br>
                <span class="role-text" style="color: #3399ff;">RECEPCIONISTA</span>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="recep.php">Painel de Gestão</a></li> <li><a href="cadastrocliente-recep.php">Cadastro Cliente</a></li>
            <li><a href="cadastroveiculo-recep.php">Cadastro Veículo</a></li>
            <li><a href="ordens-recep.php">Ordens de Serviços</a></li> 
            <li><a href="historico-veiculos-recep.php">Histórico de Veículos</a></li>
            <li><a href="minha-conta-recep.php" class="active">Minha Conta</a></li> 
            <li><a href="index.php?logout=1" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="config-container">
            <div class="config-header">
                <h2>⚙️ Configurações da Conta - Recepção</h2>
                <p>Gerencie seus dados e preferências de acesso.</p>
            </div>

            <?php if (!empty($erro)): ?>
                <div class="alert-error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <?php if (!empty($sucesso)): ?>
                <div class="alert-success"><?= htmlspecialchars($sucesso) ?></div>
            <?php endif; ?>

            <form method="POST">
                <section class="config-section">
                    <h3>Perfil do Usuário</h3>
                    <div class="config-form">
                        <div class="input-group">
                            <label>Nome Completo</label>
                            <input type="text" value="<?= htmlspecialchars($usuario['nome_completo']) ?>" disabled style="opacity: 0.6; cursor: not-allowed;">
                        </div>
                        <div class="input-group">
                            <label>E-mail de Contato</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
                        </div>
                        <div class="input-group">
                            <label>Telefone / WhatsApp</label>
                            <input type="text" id="whatsapp" value="(11) 98888-8888" maxlength="15">
                        </div>
                    </div>
                </section>

                <section class="config-section">
                    <h3>Segurança</h3>
                    <div class="config-form">
                        <div class="input-group">
                            <label>Senha Atual (Necessária para alterar a senha)</label>
                            <input type="password" name="senha_atual" id="senha-atual" placeholder="********" minlength="6" maxlength="15">
                        </div>
                        <div class="input-group">
                            <label>Nova Senha</label>
                            <input type="password" name="senha_nova" id="nova-senha" placeholder="Digite a nova senha se quiser mudar" minlength="6" maxlength="15">
                        </div>
                    </div>
                </section>

                <section class="config-section">
                    <h3>Preferências</h3>
                    <div class="config-form">
                        <div class="input-group">
                            <label>Idioma do Sistema</label>
                            <select>
                                <option>Português (Brasil)</option>
                                <option>Inglês (English)</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label>Receber Alertas de Estoque</label>
                            <select>
                                <option>Sim, sempre</option>
                                <option>Não</option>
                            </select>
                        </div>
                    </div>
                </section>

                <div class="actions-area">
                    <button type="submit" class="btn-save" style="border: none; cursor: pointer;">SALVAR ALTERAÇÕES</button>
                    <a href="minha-conta-recep.php" class="btn-back">VOLTAR</a>
                </div>
            </form>
        </div>
    </main>

    <script>
        const btnMobile = document.getElementById('btn-mobile');
        const sidebar = document.getElementById('sidebar');

        if (btnMobile) {
            btnMobile.addEventListener('click', () => {
                sidebar.classList.toggle('open');
            });
        }

        const inputWhatsapp = document.getElementById('whatsapp');
        if (inputWhatsapp) {
            inputWhatsapp.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, "");
                if (value.length > 0) value = "(" + value;
                if (value.length > 3) value = [value.slice(0, 3), ") ", value.slice(3)].join("");
                if (value.length > 10) value = [value.slice(0, 10), "-", value.slice(10)].join("");
                e.target.value = value.slice(0, 15);
            });
        }
    </script>
</body>
</html>