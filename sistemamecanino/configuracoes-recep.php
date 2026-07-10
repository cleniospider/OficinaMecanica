
<?php 
require_once('conexao/conexao.php');

?>




<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Repair - Configurações (Recepção)</title>
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
                <span class="role-text" style="color: #3399ff;">RECEPCIONISTA</span>
            </div>
        </div>
        <ul class="nav-links">
            <li><a href="recep.html">Painel de Gestão</a></li>
            <li><a href="cadastrocliente-recep.html">Cadastro Cliente</a></li>
            <li><a href="cadastroveiculo-recep.html">Cadastro Veículo</a></li>
            <li><a href="ordens-recep.html">Ordens de Serviços</a></li> 
            <li><a href="historico-veiculos-recep.html">Histórico de Veículos</a></li>
            <li><a href="minha-conta-recep.html" class="active">Minha conta</a></li> 
            <li><a href="index.html" class="logout-link">Sair</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="config-container">
            <div class="config-header">
                <h2>⚙️ Configurações da Conta - Recepção</h2>
                <p>Gerencie seus dados e preferências de acesso.</p>
            </div>

            <section class="config-section">
                <h3>Perfil do Usuário</h3>
                <div class="config-form">
                    <div class="input-group">
                        <label>Nome do Estabelecimento</label>
                        <input type="text" value="Auto Repair Oficina" disabled style="opacity: 0.6; cursor: not-allowed;">
                    </div>
                    <div class="input-group">
                        <label>E-mail de Contato</label>
                        <input type="email" value="mariana@autorepair.com">
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
                        <label>Senha Atual</label>
                        <input type="password" id="senha-atual" placeholder="********" minlength="6" maxlength="10">
                    </div>
                    <div class="input-group">
                        <label>Nova Senha</label>
                        <input type="password" id="nova-senha" placeholder="Digite a nova senha" minlength="6" maxlength="10">
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
                <a href="minha-conta-recep.php" class="btn-save" style="text-decoration: none; display: inline-block; text-align: center;">SALVAR ALTERAÇÕES</a>
                <a href="minha-conta-recep.php" class="btn-back">VOLTAR</a>
            </div>
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

        const links = document.querySelectorAll('.nav-links a');
        links.forEach(link => {
            link.addEventListener('click', () => {
                sidebar.classList.remove('open');
            });
        });

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