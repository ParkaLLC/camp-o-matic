<?php

function campomatic_endpoint_registrar() {
    global $campomatic_connection;

    $campomatic_connection = new Campomatic_Connection();
    add_filter( 'json_endpoints', array( $campomatic_connection, 'register_routes' ) );
}
add_action( 'wp_json_server_before_serve', 'campomatic_endpoint_registrar' );

class Campomatic_Connection {
    public function register_routes( $routes ) {
        $routes['/myplugin/mytypeitems'] = array(
            array( array( $this, 'new_connection'), WP_JSON_Server::CREATABLE ),
        );

        // Add more custom routes here

        return $routes;
    }

    public function create() {

    }
}

?>