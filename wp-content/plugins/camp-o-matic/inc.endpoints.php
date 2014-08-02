<?php

function campomatic_init() {
    $globals = array(
        'nonce'=> wp_create_nonce('wp_json')
    );
    echo json_encode($globals);
    exit;
}

if(isset($_GET['campomatic_init'])) {
    add_action('init', 'campomatic_init');
}
?>