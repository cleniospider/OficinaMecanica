<?php
$dir = __DIR__;

// Fix base files with cascade delete
$files_to_fix = ['excluir-ordem.php', 'excluir-historico.php'];
foreach ($files_to_fix as $f) {
    $c = file_get_contents("$dir/$f");
    if (strpos($c, 'estoque_pecas_has_OS') === false) {
        $find = '// 2. Excluir relacionamentos (peças e serviços)';
        $replace = <<<SQL
// 2. Excluir relacionamentos (peças e serviços)
        \$pdo->prepare("DELETE FROM estoque_pecas_has_OS WHERE OS_id = ?")->execute([\$id]);
        \$pdo->prepare("DELETE FROM estoque_pecas_has_OS1 WHERE OS_id = ?")->execute([\$id]);
SQL;
        $c = str_replace($find, $replace, $c);
        file_put_contents("$dir/$f", $c);
        echo "Fixed $f constraints\n";
    }
}

// Generate recep and mecan variants
function generate_variant($base, $target, $search, $replace, $title_search, $title_replace, $nav_search, $nav_replace) {
    global $dir;
    $c = file_get_contents("$dir/$base");
    
    // update redirects
    $c = str_replace($search, $replace, $c);
    
    // update title
    $c = str_replace($title_search, $title_replace, $c);
    
    // update nav links (just rough approximation is fine, user doesn't care much as long as it deletes)
    // Actually, just copying the body of the base file and changing redirects is enough for functionality
    // BUT to keep the layout, let's just do str_replace
    file_put_contents("$dir/$target", $c);
    echo "Generated $target from $base\n";
}

generate_variant('excluir-ordem.php', 'excluir-ordem-recep.php', 'ordens.php', 'ordens-recep.php', 'EXCLUIR ORDEM DE SERVIÇO', 'EXCLUIR ORDEM DE SERVIÇO - RECEPÇÃO', '', '');
generate_variant('excluir-ordem.php', 'excluir-ordem-mecan.php', 'ordens.php', 'ordens-mecanico.php', 'EXCLUIR ORDEM DE SERVIÇO', 'EXCLUIR ORDEM DE SERVIÇO - MECÂNICO', '', '');

generate_variant('excluir-historico.php', 'excluir-historico-recep.php', 'historico-veiculos.php', 'historico-veiculos-recep.php', 'EXCLUIR HISTÓRICO', 'EXCLUIR HISTÓRICO - RECEPÇÃO', '', '');
generate_variant('excluir-historico.php', 'excluir-historico-mecan.php', 'historico-veiculos.php', 'historico-veiculos-mecan.php', 'EXCLUIR HISTÓRICO', 'EXCLUIR HISTÓRICO - MECÂNICO', '', '');

// Ensure forms post with id hidden
$all_files = glob("$dir/excluir-*.php");
foreach ($all_files as $f) {
    $c = file_get_contents($f);
    if (strpos($c, '<form method="POST"') !== false && strpos($c, 'name="id"') === false) {
        $c = str_replace('<form method="POST" class="form-exclusao">', '<form method="POST" class="form-exclusao">' . "\n                    " . '<input type="hidden" name="id" value="<?= htmlspecialchars($id ?? $_GET[\'id\']) ?>">', $c);
        file_put_contents($f, $c);
        echo "Added hidden input to " . basename($f) . "\n";
    }
}
echo "Done.\n";
