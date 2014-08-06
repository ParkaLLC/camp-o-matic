<?php
/**
 * Class Campomatic_Connection logs a user in creating a new connection to the Campomatic app
 */
class Campomatic_Connection {
    public function register_routes( $routes ) {
        $routes['/campomatic/connect'] = array(
            array( array( $this, 'create'), WP_JSON_Server::READABLE ),
        );

        // Add more custom routes here

        return $routes;
    }

    public function create() {
        $response = new WP_JSON_Response();
        return $response;
    }

}

?>