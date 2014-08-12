<?php
/**
 * Class Campomatic_Connection logs a user in creating a new connection to the Campomatic app
 */
class Campomatic_Connection {
    public function register_routes( $routes ) {
        $routes['/campomatic/login'] = array(
            array( array( $this, 'login_user'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
        );

        $routes['/campomatic/register'] = array(
            array( array( $this, 'register_user'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON  ),
        );

        return $routes;
    }

    public function login_user($data = array() ) {
        $response = new WP_JSON_Response();

        if( empty( $data['key'] )) {
            $result = array(
                'error'=>true,
                'message'=>'A key is required',
            );
            $response->set_data($result);
            return $response;
        }

        $args = array(
            'meta_query'=> array(
                array(
                    'key'=>'_campomatic_access_key',
                    'value'=>$data['key']
                )
            )
        );

        $query = new WP_User_Query($args);
        if( empty( $query->results )) {
            $result = array(
                'error'=>true,
                'message'=>'We could not find your access key.',
            );
            $response->set_data($result);
            return $response;
        }

        $user = $query->results[0];
        error_log( print_r($user,true),1,'rocco@hotchkissconsulting.net');
        wp_set_auth_cookie( $user->ID );

        $result = array(
            'error'=>false,
            'message'=>'Success!',
        );
        $response->set_data($result);
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

        $user_id = wp_create_user($data['email'], $pass, $data['email']);

        if( $user_id instanceof WP_Error ) {
            $result = array(
                'error'=>true,
                'message'=> $user_id->get_error_message(),
            );
            $response->set_data($result);
            return $response;
        }

        $access_key = wp_generate_password(20, false);
        update_user_meta($user_id, '_campomatic_access_key', $access_key );
        $twitter = ltrim( $data['twitter'], '@');
        update_user_meta( $user_id, '_campomatic_twitter', $data['twitter'] );
        $access_url = CAMPOMATIC_URL . 'connect/' . $access_key;
        $message = "Thanks for registering with Camp-o-matic. Click the link to login: \n" . $access_url;
        $subject = 'Camp-o-matic: ' . get_bloginfo( 'name' );
        wp_mail($data['email'], $subject, $message);

        $result = array(
            'error'=>false,
            'message'=> 'Thanks for registering. We\'ve emailed you a login link',
        );
        $response->set_data($result);

        return $response;
    }

}

?>