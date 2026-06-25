<?php 
require_once('conexao/conexao.php');

// Proteção de sessão
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_perfil'], ['Admin', 'Recepcionista'])) {
    header("Location: index.php");
    exit;
}

$erro = "";
$cpf = $_GET['cpf'] ?? '';
$cpf_limpo = preg_replace('/\D/', '', $cpf);

// Buscar cliente
$stmt = $pdo->prepare("SELECT * FROM clientes WHERE cpf = ?");
$stmt->execute([$cpf_limpo]);
$cliente = $stmt->fetch();

if (!$cliente) {
    header("Location: cadastrocliente-recep.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Verificar se possui veículos
        $stmt_check = $pdo->prepare("SELECT id FROM veiculo WHERE clientes_cpf = ?");
        $stmt_check->execute([$cpf_limpo]);
        if ($stmt_check->fetch()) {
            $erro = "Não é possível excluir o cliente porque ele possui veículos vinculados!";
        } else {
            $stmt_del = $pdo->prepare("DELETE FROM clientes WHERE cpf = ?");
            $stmt_del->execute([$cpf_limpo]);
            header("Location: cadastrocliente-recep.php?cadastro_sucesso=" . urlencode("Cliente excluído com sucesso!"));
            exit;
        }
    } catch (PDOException $e) {
        $erro = "Erro ao excluir cliente: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Excluir Cliente (Recepção)</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/excluir-cliente.css">
    <style>
        .alert-error {
            background-color: #e74c3c;
            color: white;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
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
                <span class="role-text" style="color: #3399ff;">RECEPCIONISTA</span>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="recep.php">Painel de Gestão</a></li>
            <li><a href="cadastrocliente-recep.php" class="active">Cadastro Cliente</a></li>
            <li><a href="cadastroveiculo-recep.php">Cadastro Veículo</a></li>
            <li><a href="ordens-recep.php">Ordens de Serviços</a></li> 
            <li><a href="historico-veiculos-recep.php">Histórico de Veículos</a></li>
            <li><a href="minha-conta-recep.php">Minha conta</a></li> 
            <li><a href="index.php?logout=1" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="cliente-exclusao-container">
            <h2 class="titulo-sessao">EXCLUIR CLIENTE - RECEPÇÃO</h2>

            <?php if (!empty($erro)): ?>
                <div class="alert-error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <div class="caixa-exclusao">
                <div class="icone-alerta">⚠️</div>
                <h3>Tem certeza que deseja excluir o cliente "<?= htmlspecialchars($cliente['nome completo']) ?>"?</h3>
                <p class="aviso-texto">Esta ação removerá permanentemente os dados do cliente. Você não poderá excluir caso existam veículos vinculados.</p>
                
                <form method="POST" class="form-exclusao">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id ?? $_GET['id']) ?>">
                    <div class="botoes-acao-excluir">
                        <a href="cadastrocliente-recep.php" class="btn-cancelar-exclusao">CANCELAR</a>
                        <button type="submit" class="btn-confirmar-exclusao">SIM, EXCLUIR</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <div id="modal-conta" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Minha Conta</h2>
            <div class="conta-dados">
                <p><strong>Status:</strong> <span style="color: #00cc44;">Ativo ✔️</span></p>
            </div>
            <button class="btn-fechar-modal">Fechar</button>
        </div>
    </div>

    <script>
        const btnMobile = document.querySelector('.hamburger-btn');
        const sidebar = document.querySelector('#sidebar');
        if(btnMobile && sidebar) {
            btnMobile.addEventListener('click', () => sidebar.classList.toggle('open'));
        }

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

        window.addEventListener('click', (e) => {
            if (e.target == modal) {
                modal.style.display = 'none';
            }
        });
    </script>
</body>
</html>