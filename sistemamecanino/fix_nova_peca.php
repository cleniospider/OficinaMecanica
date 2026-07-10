<?php
$dir = __DIR__;

function generate_variant($base, $target, $search, $replace, $title_search, $title_replace, $role) {
    global $dir;
    $c = file_get_contents("$dir/$base");
    
    // update redirects
    $c = str_replace($search, $replace, $c);
    
    // update title
    $c = str_replace($title_search, $title_replace, $c);
    
    // update session check
    $search_session = "if (!isset(\$_SESSION['usuario_id'])) {";
    $replace_session = "if (!isset(\$_SESSION['usuario_id']) || !in_array(\$_SESSION['usuario_perfil'], ['Admin', '$role'])) {";
    $c = str_replace($search_session, $replace_session, $c);
    
    file_put_contents("$dir/$target", $c);
    echo "Generated $target from $base\n";
}

generate_variant('nova-peca.php', 'nova-peca-mecan.php', 'estoque-critico.php', 'estoque-critico-mecan.php', 'Nova Peça', 'Nova Peça (Mecânico)', 'Mecanico');

echo "Done.\n";
