<?php
/**
 * Class Campomatic_Connection logs a user in creating a new connection to the Campomatic app
 */
class Campomatic_Connection {
    public function register_routes( $routes ) {
        $routes['/campomatic/login/(?P<key>\w+)'] = array(
            array( array( $this, 'login_user'), WP_JSON_Server::ALLMETHODS ),
        );

        $routes['/campomatic/register'] = array(
            array( array( $this, 'register_user'), WP_JSON_Server::ALLMETHODS | WP_JSON_Server::ACCEPT_JSON ),
        );

        return $routes;
    }

    public function login_user($key) {
        $response = new WP_JSON_Response();
        $response->set_data($key);
        return $response;
    }

    public function register_user($data) {
        $response = new WP_JSON_Response();
        $response->set_data($data);
        return $response;
    }

}

?>