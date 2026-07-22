<?php 
session_start();
require_once('conexao/conexao.php');

// Proteção de sessão rigorosa para o Mecânico
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_perfil'] !== 'Mecanico') {
    header("Location: index.php");
    exit;
}

// Buscar dados reais e atualizados do mecânico no banco de dados
$usuario = null;
try {
    $stmt = $pdo->prepare("SELECT id, nome_completo, email FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    $usuario = $stmt->fetch();
} catch (PDOException $e) {
    // Fallback caso ocorra erro no banco
}

// Definição das variáveis com dados do banco ou da sessão atual
$nome  = $usuario['nome_completo'] ?? $_SESSION['usuario_nome'] ?? '';
$email = $usuario['email']         ?? 'Não informado';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Configurações (Mecânico)</title>
    <link rel="stylesheet" href="css/admin.css"> 
    <link rel="stylesheet" href="css/configuracoes.css"> 
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
                <span class="role-text" style="color: #ffaa00;">MECÂNICO</span>
            </div>
        </div>

        <ul class="nav-links">
            <li><a href="mecan.php">Painel de Gestão</a></li>
            <li><a href="ordens-mecanico.php">Ordens de Serviços</a></li>
            <li><a href="estoque-critico-mecan.php">Estoque de Peças</a></li>
            <li><a href="historico-veiculos-mecan.php">Histórico de Veículos</a></li>
            <li><a href="minha-conta-mecan.php" class="active">Minha Conta</a></li>
            <li><a href="index.php?logout=1" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="config-container">
            <div class="config-header">
                <h2>⚙️ Configurações da Conta</h2>
                <p>Gerencie seus dados e preferências de acesso.</p>
            </div>

            <section class="config-section">
                <h3>Perfil do Usuário</h3>
                <div class="config-form">
                    <div class="input-group">
                        <label>Nome Completo</label>
                        <input type="text" value="<?= htmlspecialchars($nome) ?>">
                    </div>
                    <div class="input-group">
                        <label>E-mail de Contato</label>
                        <input type="email" value="<?= htmlspecialchars($email) ?>">
                    </div>
                    <div class="input-group">
                        <label>Telefone / WhatsApp</label>
                        <input type="text" id="whatsapp" name="telefone" value="(11) 99999-9999" maxlength="15">
                    </div>
                </div>
            </section>

            <section class="config-section">
                <h3>Segurança</h3>
                <div class="config-form">
                    <div class="input-group">
                        <label>Senha Atual</label>
                        <input type="password" id="senha-atual" name="senha" placeholder="********" minlength="6" maxlength="10">
                    </div>
                    <div class="input-group">
                        <label>Nova Senha</label>
                        <input type="password" id="nova-senha" name="senha" placeholder="Digite a nova senha" minlength="6" maxlength="10">
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
                            <option>Espanhol (Español)</option>
                            <option>Francês (Français)</option>
                            <option>Alemão (Deutsch)</option>
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
                <a href="minha-conta-mecan.php" class="btn-save" style="text-decoration: none; display: inline-block; text-align: center;">SALVAR ALTERAÇÕES</a>
                <a href="minha-conta-mecan.php" class="btn-back">VOLTAR</a>
            </div>
        </div>
    </main>

    <script>
        // Lógica do Menu Lateral
        const btnMobile = document.getElementById('btn-mobile');
        const sidebar = document.getElementById('sidebar');

        if (btnMobile && sidebar) {
            btnMobile.addEventListener('click', () => {
                sidebar.classList.toggle('open');
            });
        }

        // Script de Máscara de Digitação para o WhatsApp
        const inputWhatsapp = document.getElementById('whatsapp');

        if (inputWhatsapp) {
            inputWhatsapp.addEventListener('input', (e) => {
                let value = e.target.value;
                value = value.replace(/\D/g, "");
                if (value.length > 0) {
                    value = "(" + value;
                }
                if (value.length > 3) {
                    value = [value.slice(0, 3), ") ", value.slice(3)].join("");
                }
                if (value.length > 10) {
                    value = [value.slice(0, 10), "-", value.slice(10)].join("");
                }
                e.target.value = value.slice(0, 15);
            });
        }
    </script>
</body>
</html>