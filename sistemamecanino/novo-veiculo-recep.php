<?php 
require_once('conexao/conexao.php');

// Proteção de sessão
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_perfil'], ['Admin', 'Recepcionista'])) {
    header("Location: index.php");
    exit;
}

// Buscar clientes para o dropdown
try {
    $stmt_cli = $pdo->query("SELECT cpf, `nome completo` FROM clientes ORDER BY `nome completo` ASC");
    $clientes_lista = $stmt_cli->fetchAll();
} catch (PDOException $e) {
    die("Erro ao carregar proprietários: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Novo Veículo (Recepção)</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/ordens.css">
    <link rel="stylesheet" href="css/novo-veiculo.css">
</head>
<body>

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
            <li><a href="minha-conta-recep.php">Minha conta</a></li>
            <li><a href="index.php?logout=1" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="orders-container">
            <div class="orders-header">
                <h2>Cadastrar Novo Veículo - Recepção</h2>
            </div>

            <div class="caixa-formulario">
                <form action="cadastroveiculo-recep.php" method="POST" class="form-estilizado">
                    <div class="grupo-input">
                        <label for="marcas-veiculo">Marca do veículo:</label>
                        <select name="marca" id="marcas-veiculo" required>
                            <option value="">Selecione uma marca</option>
                            <option value="Acura">Acura</option>
                            <option value="Agrale">Agrale</option>
                            <option value="Alfa Romeo">Alfa Romeo</option>
                            <option value="Aston Martin">Aston Martin</option>
                            <option value="Audi">Audi</option>
                            <option value="BMW">BMW</option>
                            <option value="BYD">BYD</option>
                            <option value="CAOA Chery">CAOA Chery</option>
                            <option value="Chevrolet">Chevrolet</option>
                            <option value="Chrysler">Chrysler</option>
                            <option value="Citroën">Citroën</option>
                            <option value="Dodge">Dodge</option>
                            <option value="Ferrari">Ferrari</option>
                            <option value="Fiat">Fiat</option>
                            <option value="Ford">Ford</option>
                            <option value="GWM">GWM</option>
                            <option value="Honda">Honda</option>
                            <option value="Hyundai">Hyundai</option>
                            <option value="Iveco">Iveco</option>
                            <option value="Jac Motors">Jac Motors</option>
                            <option value="Jaguar">Jaguar</option>
                            <option value="Jeep">Jeep</option>
                            <option value="Kia">Kia</option>
                            <option value="Lamborghini">Lamborghini</option>
                            <option value="Land Rover">Land Rover</option>
                            <option value="Lexus">Lexus</option>
                            <option value="Lifan">Lifan</option>
                            <option value="Maserati">Maserati</option>
                            <option value="McLaren">McLaren</option>
                            <option value="Mercedes-Benz">Mercedes-Benz</option>
                            <option value="Mini">Mini</option>
                            <option value="Mitsubishi">Mitsubishi</option>
                            <option value="Nissan">Nissan</option>
                            <option value="Peugeot">Peugeot</option>
                            <option value="Porsche">Porsche</option>
                            <option value="RAM">RAM</option>
                            <option value="Renault">Renault</option>
                            <option value="Rolls-Royce">Rolls-Royce</option>
                            <option value="Subaru">Subaru</option>
                            <option value="Suzuki">Suzuki</option>
                            <option value="Toyota">Toyota</option>
                            <option value="Troller">Troller</option>
                            <option value="Volkswagen">Volkswagen</option>
                            <option value="Volvo">Volvo</option>
                        </select>
                    </div>
                    <div class="grupo-input">
                        <label>Modelo</label>
                        <input type="text" name="modelo" placeholder="Ex: CBR 600RR" required>
                    </div>

                    <div class="grupo-input">
                        <label>Placa</label>
                        <input type="text" name="placa" placeholder="Ex: ABC-1234" required style="text-transform: uppercase;">
                    </div>

                    <div class="grupo-input">
                        <label for="ano-veiculo">Ano</label>
                        <input type="tel" id="ano-veiculo" name="ano" placeholder="Ex: 2024" required>
                    </div>

                    <div class="grupo-input">
                        <label>Cor</label>
                        <input type="text" name="cor" placeholder="Ex: Prata" required>
                    </div>

                    <div class="grupo-input">
                        <label>Proprietário</label>
                        <select name="clientes_cpf" required>
                            <option value="" disabled selected>Selecione um cliente</option>
                            <?php foreach ($clientes_lista as $cli): ?>
                                <option value="<?= htmlspecialchars($cli['cpf']) ?>"><?= htmlspecialchars($cli['nome completo']) ?> (CPF: <?= htmlspecialchars($cli['cpf']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="botoes-acao">
                        <button type="submit" class="btn-salvar">Salvar Veículo</button>
                        <a href="cadastroveiculo-recep.php" class="btn-voltar">Cancelar</a>
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
                <p><strong>Nome:</strong> <?= htmlspecialchars($_SESSION['usuario_nome'] ?? '') ?></p>
                <p><strong>Perfil:</strong> <?= htmlspecialchars($_SESSION['usuario_perfil'] ?? '') ?></p>
                <p><strong>Status:</strong> <span style="color: #00cc44;">Ativo ✔️</span></p>
            </div>
            <button class="btn-fechar-modal">Fechar</button>
        </div>
    </div>

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

        // Lógica do Modal de Conta
        const linkConta = document.querySelector('a[href="minha-conta-recep.php"]'); 
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

        // === SCRIPT DE VALIDAÇÃO DO ANO (MÁXIMO 4 DÍGITOS NUMÉRICOS) ===
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