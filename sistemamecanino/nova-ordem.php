<?php
require_once 'conexao.php';

// Valores iniciais vazios para modo de cadastro
$os = [ 'id' => '', 'clientes_cpf' => '', 'veiculo_id1' => '', 'problema' => '', 'servicos' => '', 'pecas_usadas' => '', 'valor_total' => '', 'status' => 'ativo' ];

// Se receber parâmetro via GET, carrega dados para edição
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM OS WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($resultado) {
        $os = $resultado;
        $os['valor_total'] = 'R$ ' . number_format($resultado['valor_total'], 2, ',', '.');
    }
}

// Busca listas do banco de dados para alimentar os campos Select do formulário
$listaClientes = $pdo->query("SELECT cpf, `nome completo` FROM clientes ORDER BY `nome completo`")->fetchAll(PDO::FETCH_ASSOC);
$listaVeiculos = $pdo->query("SELECT id, `marca/modelo`, placa FROM veiculo ORDER BY `marca/modelo`")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Auto Repair - Formulário OS</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/ordens.css">
    <link rel="stylesheet" href="css/nova-ordem.css">
</head>
<body class="dark-theme">

    <main class="main-content">
        <div class="orders-container">
            <h2><?= $os['id'] ? "EDITAR ORDEM DE SERVIÇO #".$os['id'] : "NOVA ORDEM DE SERVIÇO" ?></h2>
            
            <form action="processar_os.php" method="POST" class="form-nova-ordem">
                <input type="hidden" name="id" value="<?= $os['id'] ?>">

                <div class="form-header-info">
                    <div class="grupo-input">
                        <label>Cliente / Proprietário:</label>
                        <select name="clientes_cpf" required style="padding: 8px; border-radius: 4px; background: #333; color: #fff; width: 100%;">
                            <option value="">Selecione o Cliente</option>
                            <?php foreach($listaClientes as $c): ?>
                                <option value="<?= $c['cpf'] ?>" <?= $os['clientes_cpf'] == $c['cpf'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['nome completo']) ?> (CPF: <?= $c['cpf'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="grupo-input">
                        <label>Veículo Alvo:</label>
                        <select name="veiculo_id" required style="padding: 8px; border-radius: 4px; background: #333; color: #fff; width: 100%;">
                            <option value="">Selecione o Veículo</option>
                            <?php foreach($listaVeiculos as $v): ?>
                                <option value="<?= $v['id'] ?>" <?= $os['veiculo_id1'] == $v['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($v['marca/modelo']) ?> [Placa: <?= $v['placa'] ?>]
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
        
                <div class="form-corpo-ordem">
                    <div class="grupo-input-dark">
                        <label>Problema Constatado:</label>
                        <textarea name="problema" rows="3"><?= htmlspecialchars($os['problema']) ?></textarea>
                    </div>
                    <div class="grupo-input-dark">
                        <label>Serviços Realizados:</label>
                        <textarea name="servicos" rows="3"><?= htmlspecialchars($os['servicos']) ?></textarea>
                    </div>
                    <div class="grupo-input-dark">
                        <label>Componentes/Peças Utilizadas:</label>
                        <textarea name="pecas_usadas" rows="2"><?= htmlspecialchars($os['pecas_usadas']) ?></textarea>
                    </div>

                    <div class="flex-row">
                        <div class="grupo-input-dark">
                            <label>Valor total:</label>
                            <input type="text" name="valor_total" class="money-mask" value="<?= $os['valor_total'] ?>" placeholder="R$ 0,00">
                        </div>
                        <div class="grupo-input-dark">
                            <label>Status Atual:</label>
                            <select name="status" class="select-dark">
                                <option value="ativo" <?= $os['status'] == 'ativo' ? 'selected' : '' ?>>Ativo</option>
                                <option value="finalizado" <?= $os['status'] == 'finalizado' ? 'selected' : '' ?>>Finalizado</option>
                                <option value="parado" <?= $os['status'] == 'parado' ? 'selected' : '' ?>>Parado</option>
                            </select>
                        </div>
                    </div>
            
                    <div class="botoes-acao">
                        <button type="submit" class="btn-os btn-salvar-red">SALVAR ALTERAÇÕES</button>
                        <a href="ordens.php" class="btn-os btn-voltar-dark">CANCELAR</a>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script>
        const inputValor = document.querySelector('.money-mask');
        if(inputValor) {
            inputValor.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                if (!value) { e.target.value = ''; return; }
                value = (parseInt(value) / 100).toFixed(2);
                e.target.value = parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
            });
        }
    </script>
</body>
</html>