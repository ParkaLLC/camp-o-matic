<?php

function campomatic_init() {
    $globals = array(
        'nonce'=> wp_create_nonce('wp_api')
    );
    echo json_encode($globals);
    exit;
}

if(isset($_GET['campomatic_init'])) {
    add_action('init', 'campomatic_init');
}
?>