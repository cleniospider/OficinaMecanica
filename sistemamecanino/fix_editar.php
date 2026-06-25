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
    // Base is: if (!isset($_SESSION['usuario_id'])) {
    $search_session = "if (!isset(\$_SESSION['usuario_id'])) {";
    $replace_session = "if (!isset(\$_SESSION['usuario_id']) || !in_array(\$_SESSION['usuario_perfil'], ['Admin', '$role'])) {";
    $c = str_replace($search_session, $replace_session, $c);
    
    file_put_contents("$dir/$target", $c);
    echo "Generated $target from $base\n";
}

generate_variant('editar-ordem.php', 'editar-ordem-recep.php', 'ordens.php', 'ordens-recep.php', 'Gerenciar OS', 'Gerenciar OS (Recepção)', 'Recepcionista');
generate_variant('editar-ordem.php', 'editar-ordem-mecan.php', 'ordens.php', 'ordens-mecan.php', 'Gerenciar OS', 'Gerenciar OS (Mecânico)', 'Mecanico');

echo "Done.\n";
