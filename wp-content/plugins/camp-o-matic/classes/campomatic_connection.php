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
            array( array( $this, 'register_user'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
        );

        return $routes;
    }

    public function login_user($key = false ) {
        $response = new WP_JSON_Response();
        $response->set_data($key);
        return $response;
    }

    public function register_user($data = array()) {


        $response = new WP_JSON_Response();

        if( !isset($data['email']) || ! is_email($data['email']) ) {
            $result = array(
                'error'=>true,
                'message'=>'A valid email is required.',
            );
            $response->set_data($result);
            return $response;
        }

        if( !isset($data['name']) ) {
            $result = array(
                'error'=>true,
                'message'=>'Your name is required.',
            );
            $response->set_data($result);
            return $response;
        }
        $pass = wp_generate_password();
        $userdata = array(
            'user_pass' => $pass,
            'user_login'=> $data['email'],
            'display_name'=>$data['name'],
            'user_email'=>$data['email'],
        );
        $user_id = wp_insert_user( $userdata );

        if( $user_id instanceof WP_Error ) {
            $result = array(
                'error'=>true,
                'message'=> $user_id->get_error_message(),
            );
            $response->set_data($result);
            return $response;
        }


        return $response;
    }

}

?>