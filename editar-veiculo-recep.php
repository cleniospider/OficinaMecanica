<?php 
require_once('conexao/conexao.php');

// Proteção de sessão
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_perfil'], ['Admin', 'Recepcionista'])) {
    header("Location: index.php");
    exit;
}

$erro = "";
$sucesso = "";

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: cadastroveiculo-recep.php");
    exit;
}

// Buscar dados do veículo
try {
    $stmt_veic = $pdo->prepare("SELECT * FROM veiculo WHERE id = ?");
    $stmt_veic->execute([$id]);
    $veic = $stmt_veic->fetch();

    if (!$veic) {
        header("Location: cadastroveiculo-recep.php");
        exit;
    }

    // Buscar proprietários
    $stmt_cli = $pdo->query("SELECT cpf, `nome completo` FROM clientes ORDER BY `nome completo` ASC");
    $clientes_lista = $stmt_cli->fetchAll();

    // Separar marca e modelo do campo marca/modelo
    $parts = explode('/', $veic['marca/modelo'], 2);
    $marca_atual = $parts[0] ?? '';
    $modelo_atual = $parts[1] ?? '';
} catch (PDOException $e) {
    die("Erro ao carregar dados do banco: " . $e->getMessage());
}

// Processar formulário de atualização
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $marca = trim($_POST['marca'] ?? '');
    $modelo = trim($_POST['modelo'] ?? '');
    $placa = strtoupper(trim($_POST['placa'] ?? ''));
    $ano = filter_var($_POST['ano'] ?? null, FILTER_VALIDATE_INT);
    $cor = trim($_POST['cor'] ?? '');
    $clientes_cpf = preg_replace('/\D/', '', $_POST['clientes_cpf'] ?? '');

    if (!empty($marca) && !empty($modelo) && !empty($placa) && $ano && !empty($clientes_cpf)) {
        try {
            // Verificar se outra placa igual já existe em outro ID
            $stmt_check = $pdo->prepare("SELECT id FROM veiculo WHERE placa = ? AND id <> ?");
            $stmt_check->execute([$placa, $id]);
            if ($stmt_check->fetch()) {
                $erro = "Erro: Já existe outro veículo cadastrado com esta placa!";
            } else {
                // Obter nome do cliente
                $stmt_c = $pdo->prepare("SELECT `nome completo` FROM clientes WHERE cpf = ?");
                $stmt_c->execute([$clientes_cpf]);
                $cli_nome = $stmt_c->fetchColumn();

                if ($cli_nome) {
                    $marca_modelo = $marca . '/' . $modelo;

                    $stmt_update = $pdo->prepare("
                        UPDATE veiculo 
                        SET placa = ?, `marca/modelo` = ?, ano = ?, cor = ?, cliente = ?, clientes_cpf = ? 
                        WHERE id = ?
                    ");
                    $stmt_update->execute([$placa, $marca_modelo, $ano, $cor, $cli_nome, $clientes_cpf, $id]);

                    header("Location: cadastroveiculo-recep.php?cadastro_sucesso=" . urlencode("Veículo atualizado com sucesso!"));
                    exit;
                } else {
                    $erro = "Proprietário selecionado inválido!";
                }
            }
        } catch (PDOException $e) {
            $erro = "Erro ao atualizar veículo: " . $e->getMessage();
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
    <title>Auto Repair - Editar Veículo (Recepção)</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/editar-veiculo.css"> 
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
                <span class="role-text" style="color: #3399ff;">RECEPCIONISTA</span>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="recep.php">Painel de Gestão</a></li>
            <li><a href="cadastrocliente-recep.php">Cadastro Cliente</a></li>
            <li><a href="cadastroveiculo-recep.php" class="active">Cadastro Veículo</a></li>
            <li><a href="ordens-recep.php">Ordens de Serviços</a></li> 
            <li><a href="historico-veiculos-recep.php">Histórico de Veículos</a></li>
            <li><a href="minha-conta-recep.php">Minha Conta</a></li> 
            <li><a href="index.php?logout=1" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="container-form-dark">
            <div class="titulo-sessao-container">
                <h2 class="titulo-sessao">EDITAR VEÍCULO <span class="destaque-red">#<?= htmlspecialchars($id) ?></span></h2>
                <div class="linha-decorativa"></div>
            </div>
            
            <?php if (!empty($erro)): ?>
                <div class="alert-error"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>

            <div class="card-dark">
                <form method="POST">
                    
                    <div class="form-row">
                        <div class="grupo-input-dark flex-1">
                            <label for="marcas-veiculo">Escolha a marca do veículo:</label>
                            <select name="marca" id="marcas-veiculo" required>
                                <option value="">Selecione uma marca</option>
                                <?php 
                                $marcas = ["Acura", "Agrale", "Alfa Romeo", "Aston Martin", "Audi", "BMW", "BYD", "CAOA Chery", "Chevrolet", "Chrysler", "Citroën", "Dodge", "Ferrari", "Fiat", "Ford", "GWM", "Honda", "Hyundai", "Iveco", "Jac Motors", "Jaguar", "Jeep", "Kia", "Lamborghini", "Land Rover", "Lexus", "Lifan", "Maserati", "McLaren", "Mercedes-Benz", "Mini", "Mitsubishi", "Nissan", "Peugeot", "Porsche", "RAM", "Renault", "Rolls-Royce", "Subaru", "Suzuki", "Toyota", "Troller", "Volkswagen", "Volvo"];
                                foreach ($marcas as $m):
                                    $sel = ($m === $marca_atual) ? 'selected' : '';
                                ?>
                                    <option value="<?= $m ?>" <?= $sel ?>><?= $m ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="grupo-input-dark flex-1">
                            <label class="label-red">MODELO</label>
                            <input type="text" name="modelo" value="<?= htmlspecialchars($modelo_atual) ?>" required>
                        </div>
                    </div>
    
                    <div class="form-row">
                        <div class="grupo-input-dark flex-1">
                            <label class="label-red">PLACA</label>
                            <input type="text" name="placa" value="<?= htmlspecialchars($veic['placa']) ?>" required style="text-transform: uppercase;">
                        </div>
                        <div class="grupo-input-dark flex-1">
                            <label for="ano-veiculo">ANO</label>
                            <input type="tel" id="ano-veiculo" name="ano" value="<?= htmlspecialchars($veic['ano']) ?>" required>
                        </div>
                        <div class="grupo-input-dark flex-1">
                            <label>COR</label>
                            <input type="text" name="cor" value="<?= htmlspecialchars($veic['cor']) ?>" required>
                        </div>
                    </div>
    
                    <div class="grupo-input-dark">
                        <label>PROPRIETÁRIO ATUAL</label>
                        <select name="clientes_cpf" required>
                            <option value="" disabled>Selecione o proprietário</option>
                            <?php foreach ($clientes_lista as $cli): 
                                $sel = ($cli['cpf'] === $veic['clientes_cpf']) ? 'selected' : '';
                            ?>
                                <option value="<?= htmlspecialchars($cli['cpf']) ?>" <?= $sel ?>><?= htmlspecialchars($cli['nome completo']) ?> (CPF: <?= htmlspecialchars($cli['cpf']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
    
                    <div class="footer-acoes">
                        <button type="submit" class="btn-submit btn-acao btn-salvar-fixo">SALVAR ALTERAÇÕES</button>
                        <a href="cadastroveiculo-recep.php" class="btn-acao btn-voltar-os">VOLTAR</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        const btnMobile = document.querySelector('.hamburger-btn');
        const sidebar = document.querySelector('#sidebar');

        // Abre e fecha o menu lateral
        if (btnMobile) {
            btnMobile.addEventListener('click', () => {
                sidebar.classList.toggle('open');
            });
        }

        // Fecha o menu ao clicar em um link
        const links = document.querySelectorAll('.nav-links a');
        links.forEach(link => {
            link.addEventListener('click', () => {
                sidebar.classList.remove('open');
            });
        });

        // Validação do campo ano (MÁXIMO 4 DÍGITOS NUMÉRICOS)
        const inputAno = document.getElementById('ano-veiculo');
        if (inputAno) {
            inputAno.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, "");
                if (value.length > 4) {
                    value = value.slice(0, 4);
                }
                e.target.value = value;
            });
        }
    </script>
</body>
</html>