<?php
/**
 * Registers custom endpoints for the WordPress REST API
 *
 * @package Campomatic
 */

/**
 * Hooks into the REST API before anything is served
 *
 * Classes are delcared to handle the creation / serving of new enpoints and their respective data
 *
 * @return void
 */
function campomatic_endpoint_registrar() {
    global $campomatic_connection;
    require('classes/campomatic_connection.php');
    $campomatic_connection = new Campomatic_Connection();
    add_filter( 'json_endpoints', array( $campomatic_connection, 'register_routes' ) );
}
add_action( 'wp_json_server_before_serve', 'campomatic_endpoint_registrar' );

function campomatic_query_vars( $vars ) {
    $campomatic_vars = array(
        'meta_key',
        'meta_value',
        'meta_query'
    );

    foreach( $campomatic_vars as $v ) {
        $vars[] = $v;
    }
    return $vars;
}
add_filter('json_query_vars', 'campomatic_query_vars');

?>