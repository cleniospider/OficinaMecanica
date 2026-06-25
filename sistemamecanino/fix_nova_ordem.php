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

generate_variant('nova-ordem.php', 'nova-ordem-recep.php', 'ordens.php', 'ordens-recep.php', 'Nova Ordem', 'Nova Ordem (Recepção)', 'Recepcionista');
generate_variant('nova-ordem.php', 'nova-ordem-mecan.php', 'ordens.php', 'ordens-mecan.php', 'Nova Ordem', 'Nova Ordem (Mecânico)', 'Mecanico');

echo "Done.\n";
