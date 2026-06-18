<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$config_path = __DIR__ . '/config.json';

// 1. Carregar Configurações (Carrega do JSON ou usa valores padrão)
if (file_exists($config_path)) {
    $config = json_decode(file_get_contents($config_path), true);
    $servidor = $config['servidor'] ?? "localhost";
    $usuario  = $config['usuario'] ?? "root";
    $senha    = $config['senha'] ?? "";
    $banco    = $config['banco'] ?? "oficinamecanica";
    $porta    = intval($config['porta'] ?? 3306);
} else {
    $servidor = "localhost";
    $usuario  = "root";
    $senha    = "";
    $banco    = "oficinamecanica";
    $porta    = 3306;
}

// 2. Tratar alteração manual de configurações
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_config') {
    $new_config = [
        'servidor' => trim($_POST['servidor'] ?? 'localhost'),
        'usuario' => trim($_POST['usuario'] ?? 'root'),
        'senha' => $_POST['senha'] ?? '',
        'banco' => trim($_POST['banco'] ?? 'oficinamecanica'),
        'porta' => intval($_POST['porta'] ?? 3306)
    ];
    file_put_contents($config_path, json_encode($new_config, JSON_PRETTY_PRINT));
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// 3. Tratar restauração das configurações padrão
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset_config') {
    if (file_exists($config_path)) {
        @unlink($config_path);
    }
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// Desativa o reporte automático de exceções para tratar erros manualmente
mysqli_report(MYSQLI_REPORT_OFF);

// 4. Tratar Instalação/Importação Automática do Banco e Tabelas
$install_error = "";
$install_success = false;

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'install') {
    $temp_conexao = @new mysqli($servidor, $usuario, $senha, '', $porta);
    if ($temp_conexao->connect_error) {
        $install_error = "Não foi possível conectar ao servidor MySQL para instalação: " . $temp_conexao->connect_error;
    } else {
        // Tenta criar o banco de dados se não existir
        $create_db_query = "CREATE DATABASE IF NOT EXISTS `$banco` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
        if (!$temp_conexao->query($create_db_query)) {
            $install_error = "Erro ao criar o banco de dados '$banco': " . $temp_conexao->error;
        } else {
            // Seleciona o banco recém-criado/existente
            $temp_conexao->select_db($banco);
            
            $schema_file = __DIR__ . '/schema.sql';
            if (!file_exists($schema_file)) {
                $install_error = "O arquivo de estrutura SQL (schema.sql) não foi encontrado na pasta conexao!";
            } else {
                $sql = file_get_contents($schema_file);
                
                // Executa as queries do schema
                if ($temp_conexao->multi_query($sql)) {
                    do {
                        // Limpa os resultados das queries anteriores
                        if ($result = $temp_conexao->store_result()) {
                            $result->free();
                        }
                    } while ($temp_conexao->next_result());
                    
                    if ($temp_conexao->error) {
                        $install_error = "Erro ao criar as tabelas no banco de dados: " . $temp_conexao->error;
                    } else {
                        // Tenta cadastrar o administrador padrão caso a tabela esteja vazia
                        $admin_email = 'admin@admin.com';
                        $admin_nome = 'Administrador Geral';
                        $admin_cpf = '11122233344';
                        $admin_telefone = '(11) 99999-9999';
                        $admin_perfil = 'Admin';
                        $admin_senha_hash = password_hash('admin123', PASSWORD_DEFAULT);
                        
                        $stmt_admin = $temp_conexao->prepare("INSERT INTO usuarios (nome_completo, email, cpf, perfil, telefone, senha) VALUES (?, ?, ?, ?, ?, ?)");
                        if ($stmt_admin) {
                            $stmt_admin->bind_param("ssssss", $admin_nome, $admin_email, $admin_cpf, $admin_perfil, $admin_telefone, $admin_senha_hash);
                            $stmt_admin->execute();
                            $stmt_admin->close();
                        }
                        $install_success = true;
                    }
                } else {
                    $install_error = "Falha ao ler o script do banco de dados: " . $temp_conexao->error;
                }
            }
        }
        $temp_conexao->close();
    }
}

// 5. Verificar o Status de Conexão e Disponibilidade
$mysql_connected = false;
$db_exists = false;
$tables_exist = false;
$error_msg = "";

$temp_conexao = @new mysqli($servidor, $usuario, $senha, '', $porta);
if ($temp_conexao && !$temp_conexao->connect_error) {
    $mysql_connected = true;
    
    // Verifica se consegue selecionar o banco de dados
    if ($temp_conexao->select_db($banco)) {
        $db_exists = true;
        
        // Verifica se a tabela principal (usuarios) já foi criada
        $table_check = $temp_conexao->query("SHOW TABLES LIKE 'usuarios'");
        if ($table_check && $table_check->num_rows > 0) {
            $tables_exist = true;
        }
    }
    $temp_conexao->close();
} else {
    $error_msg = $temp_conexao ? $temp_conexao->connect_error : "Tempo limite de conexão esgotado.";
}

// Se alguma das verificações falhar ou se a instalação acabou de ser executada, exibe a página de configuração
if (!$mysql_connected || !$db_exists || !$tables_exist || $install_success) {
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Configuração do Sistema - Auto Repair</title>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            :root {
                --bg-primary: #0b0f19;
                --bg-secondary: #161c2d;
                --text-primary: #f8fafc;
                --text-secondary: #94a3b8;
                --primary: #6366f1;
                --primary-hover: #4f46e5;
                --success: #10b981;
                --error: #ef4444;
                --warning: #f59e0b;
            }

            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
                font-family: 'Outfit', sans-serif;
            }

            body {
                background: radial-gradient(circle at 50% 50%, #1e1b4b 0%, #0f172a 100%);
                color: var(--text-primary);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }

            .card {
                background: rgba(30, 41, 59, 0.7);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 24px;
                width: 100%;
                max-width: 550px;
                padding: 40px;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
                animation: fadeIn 0.6s ease-out;
            }

            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .logo-area {
                text-align: center;
                margin-bottom: 30px;
            }

            .logo-area h2 {
                font-size: 28px;
                font-weight: 700;
                background: linear-gradient(to right, #a5b4fc, #6366f1);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                letter-spacing: -0.5px;
            }

            .logo-area p {
                color: var(--text-secondary);
                font-size: 14px;
                margin-top: 5px;
            }

            .status-container {
                background: rgba(15, 23, 42, 0.6);
                border-radius: 16px;
                padding: 20px;
                margin-bottom: 30px;
                border: 1px solid rgba(255, 255, 255, 0.05);
            }

            .status-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 12px;
            }

            .status-item:last-child {
                margin-bottom: 0;
            }

            .status-label {
                font-size: 15px;
                font-weight: 500;
            }

            .badge {
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 12px;
                font-weight: 600;
                padding: 4px 12px;
                border-radius: 12px;
                text-transform: uppercase;
            }

            .badge-success {
                background: rgba(16, 185, 129, 0.15);
                color: var(--success);
                border: 1px solid rgba(16, 185, 129, 0.3);
            }

            .badge-error {
                background: rgba(239, 68, 68, 0.15);
                color: var(--error);
                border: 1px solid rgba(239, 68, 68, 0.3);
            }

            .badge-warning {
                background: rgba(245, 158, 11, 0.15);
                color: var(--warning);
                border: 1px solid rgba(245, 158, 11, 0.3);
            }

            .badge-dot {
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background-color: currentColor;
                display: inline-block;
            }

            .pulse {
                animation: pulse-animation 2s infinite;
            }

            @keyframes pulse-animation {
                0% { opacity: 0.4; }
                50% { opacity: 1; }
                100% { opacity: 0.4; }
            }

            .alert {
                padding: 16px;
                border-radius: 12px;
                margin-bottom: 30px;
                font-size: 14px;
                line-height: 1.5;
            }

            .alert-error {
                background: rgba(239, 68, 68, 0.1);
                border: 1px solid rgba(239, 68, 68, 0.2);
                color: #fca5a5;
            }

            .alert-success {
                background: rgba(16, 185, 129, 0.1);
                border: 1px solid rgba(16, 185, 129, 0.2);
                color: #a7f3d0;
            }

            .alert-warning {
                background: rgba(245, 158, 11, 0.1);
                border: 1px solid rgba(245, 158, 11, 0.2);
                color: #fde047;
            }

            .instructions {
                margin-bottom: 30px;
                font-size: 14px;
                color: var(--text-secondary);
                line-height: 1.6;
            }

            .instructions h3 {
                color: var(--text-primary);
                font-size: 16px;
                font-weight: 600;
                margin-bottom: 8px;
            }

            .instructions ol {
                margin-left: 20px;
                margin-top: 5px;
            }

            .instructions li {
                margin-bottom: 6px;
            }

            .form-title {
                font-size: 16px;
                font-weight: 600;
                margin-bottom: 15px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                padding-bottom: 8px;
            }

            .form-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 15px;
                margin-bottom: 15px;
            }

            .form-group {
                margin-bottom: 15px;
            }

            .form-group.full-width {
                grid-column: span 2;
            }

            label {
                display: block;
                font-size: 13px;
                color: var(--text-secondary);
                margin-bottom: 6px;
                font-weight: 500;
            }

            input {
                width: 100%;
                background: rgba(15, 23, 42, 0.5);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 8px;
                padding: 10px 14px;
                color: var(--text-primary);
                font-size: 14px;
                transition: all 0.3s;
            }

            input:focus {
                outline: none;
                border-color: var(--primary);
                box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
            }

            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 100%;
                padding: 12px 24px;
                border-radius: 10px;
                font-size: 15px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s;
                border: none;
                text-decoration: none;
            }

            .btn-primary {
                background: linear-gradient(135deg, var(--primary) 0%, #4f46e5 100%);
                color: white;
                box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 18px rgba(99, 102, 241, 0.4);
            }

            .btn-secondary {
                background: rgba(255, 255, 255, 0.05);
                color: var(--text-primary);
                border: 1px solid rgba(255, 255, 255, 0.1);
                margin-top: 10px;
            }

            .btn-secondary:hover {
                background: rgba(255, 255, 255, 0.1);
            }

            .btn:active {
                transform: translateY(0);
            }

            .accordion-trigger {
                color: var(--primary);
                cursor: pointer;
                font-size: 14px;
                font-weight: 500;
                text-align: center;
                margin-top: 15px;
                display: block;
                text-decoration: underline;
            }
            
            .hidden {
                display: none;
            }
        </style>
    </head>
    <body>

    <div class="card">
        <div class="logo-area">
            <h2>AUTO REPAIR</h2>
            <p>Configuração e Diagnóstico de Banco de Dados</p>
        </div>

        <?php if ($install_success): ?>
            <div class="alert alert-success">
                <strong>Instalação concluída com sucesso!</strong><br>
                O banco de dados <strong><?= htmlspecialchars($banco) ?></strong> foi configurado e as tabelas foram criadas com êxito.
            </div>
            <a href="index.php" class="btn btn-primary">Ir para a Página Inicial</a>
        <?php else: ?>

            <?php if (!empty($install_error)): ?>
                <div class="alert alert-error">
                    <strong>Erro na instalação:</strong><br>
                    <?= htmlspecialchars($install_error) ?>
                </div>
            <?php endif; ?>

            <div class="status-container">
                <div class="status-item">
                    <span class="status-label">Servidor MySQL</span>
                    <?php if ($mysql_connected): ?>
                        <span class="badge badge-success"><span class="badge-dot pulse"></span>Online</span>
                    <?php else: ?>
                        <span class="badge badge-error"><span class="badge-dot"></span>Offline</span>
                    <?php endif; ?>
                </div>
                
                <div class="status-item">
                    <span class="status-label">Banco de Dados (<?= htmlspecialchars($banco) ?>)</span>
                    <?php if (!$mysql_connected): ?>
                        <span class="badge badge-warning"><span class="badge-dot"></span>Pendente</span>
                    <?php elseif ($db_exists): ?>
                        <span class="badge badge-success"><span class="badge-dot pulse"></span>Existe</span>
                    <?php else: ?>
                        <span class="badge badge-error"><span class="badge-dot"></span>Inexistente</span>
                    <?php endif; ?>
                </div>

                <div class="status-item">
                    <span class="status-label">Tabelas do Sistema</span>
                    <?php if (!$mysql_connected || !$db_exists): ?>
                        <span class="badge badge-warning"><span class="badge-dot"></span>Pendente</span>
                    <?php elseif ($tables_exist): ?>
                        <span class="badge badge-success"><span class="badge-dot pulse"></span>Prontas</span>
                    <?php else: ?>
                        <span class="badge badge-error"><span class="badge-dot"></span>Ausentes</span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!$mysql_connected): ?>
                <div class="alert alert-error">
                    <strong>Não foi possível conectar ao servidor MySQL.</strong><br>
                    Motivo: <?= htmlspecialchars($error_msg) ?>
                </div>
                <div class="instructions">
                    <h3>Como resolver no XAMPP?</h3>
                    <ol>
                        <li>Abra o painel de controle do XAMPP (XAMPP Control Panel).</li>
                        <li>Clique no botão <strong>Start</strong> ao lado do serviço <strong>MySQL</strong> para iniciá-lo.</li>
                        <li>O indicador do MySQL deverá ficar verde no XAMPP. Recarregue esta página após iniciar.</li>
                    </ol>
                </div>
            <?php elseif (!$db_exists || !$tables_exist): ?>
                <div class="alert alert-warning">
                    <strong>O sistema precisa ser inicializado!</strong><br>
                    <?= !$db_exists 
                        ? "O banco de dados <strong>" . htmlspecialchars($banco) . "</strong> ainda não foi criado."
                        : "O banco de dados existe, mas as tabelas do sistema estão faltando." ?>
                </div>
                
                <form method="POST" style="margin-bottom: 20px;">
                    <input type="hidden" name="action" value="install">
                    <button type="submit" class="btn btn-primary">
                        Configurar Banco de Dados (Instalação Automática)
                    </button>
                </form>
            <?php endif; ?>

            <a class="accordion-trigger" onclick="toggleConfigForm()">Exibir configurações avançadas de conexão</a>

            <div id="config-form" class="hidden" style="margin-top: 25px;">
                <div class="form-title">Ajustar Credenciais do Banco</div>
                <form method="POST">
                    <input type="hidden" name="action" value="save_config">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="servidor">Host</label>
                            <input type="text" id="servidor" name="servidor" value="<?= htmlspecialchars($servidor) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="porta">Porta</label>
                            <input type="number" id="porta" name="porta" value="<?= htmlspecialchars($porta) ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="usuario">Usuário</label>
                            <input type="text" id="usuario" name="usuario" value="<?= htmlspecialchars($usuario) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="senha">Senha</label>
                            <input type="password" id="senha" name="senha" value="<?= htmlspecialchars($senha) ?>">
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="banco">Nome do Banco de Dados</label>
                        <input type="text" id="banco" name="banco" value="<?= htmlspecialchars($banco) ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="margin-top: 5px;">Salvar e Reconectar</button>
                </form>

                <?php if (file_exists($config_path)): ?>
                    <form method="POST" style="margin-top: 10px;">
                        <input type="hidden" name="action" value="reset_config">
                        <button type="submit" class="btn btn-secondary">Restaurar Credenciais Padrão</button>
                    </form>
                <?php endif; ?>
            </div>

        <?php endif; ?>
    </div>

    <script>
        function toggleConfigForm() {
            const form = document.getElementById('config-form');
            const trigger = document.querySelector('.accordion-trigger');
            if (form.classList.contains('hidden')) {
                form.classList.remove('hidden');
                trigger.textContent = 'Ocultar configurações avançadas';
            } else {
                form.classList.add('hidden');
                trigger.textContent = 'Exibir configurações avançadas de conexão';
            }
        }
    </script>
    </body>
    </html>
    <?php
    exit;
}

// 6. Estabelecer conexões reais para o sistema funcionar
$conexao = new mysqli($servidor, $usuario, $senha, $banco, $porta);
if ($conexao->connect_error) {
    die("Erro de conexão crítico: " . $conexao->connect_error);
}
$conexao->set_charset("utf8");

try {
    $pdo = new PDO("mysql:host=$servidor;port=$porta;dbname=$banco;charset=utf8mb4", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro de conexão PDO crítico: " . $e->getMessage());
}

define('BASE_URL', 'http://localhost/oficinamecanica/index.php');
?>