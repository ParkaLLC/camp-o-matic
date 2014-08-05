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

    $campomatic_connection = new Campomatic_Connection();
    add_filter( 'json_endpoints', array( $campomatic_connection, 'register_routes' ) );
}
add_action( 'wp_json_server_before_serve', 'campomatic_endpoint_registrar' );

/**
 * Class Campomatic_Connection logs a user in creating a new connection to the Campomatic app
 */
class Campomatic_Connection {
    public function register_routes( $routes ) {
        $routes['/myplugin/mytypeitems'] = array(
            array( array( $this, 'create'), WP_JSON_Server::CREATABLE ),
        );

        // Add more custom routes here

        return $routes;
    }

    public function create() {

    }
}

?>