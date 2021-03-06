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

        $routes['/campomatic/auth'] = array(
            array( array( $this, 'authorize_user'), WP_JSON_Server::READABLE ),
        );

        $routes['/campomatic/get_login'] = array(
            array( array( $this, 'get_login'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON  ),
        );

        return $routes;
    }

    public function get_login($data) {
        $response = new WP_JSON_Response();

        if( !isset($data['email']) || ! is_email($data['email']) ) {
            $result = array(
                'error'=>true,
                'message'=>'That\'s not an email address.',
            );
            $response->set_data($result);
            return $response;
        }


        $user = get_user_by('email', $data['email']);

        if( empty($user) ) {
            $result = array(
                'error'=>true,
                'message'=>'We have no record of that email.',
            );
            $response->set_data($result);
            return $response;
        }

        $key = get_user_meta($user->ID, '_campomatic_access_key', true );

        if( empty($key) ) {
            $key =wp_generate_password(20, false);
            update_user_meta($user->ID, '_campomatic_access_key', $key );
        }

        $access_url = CAMPOMATIC_URL . 'connect/' . $key;
        $message = "You can click the following link anytime you want to login to camp-o-matic: \n" . $access_url;
        $subject = 'Camp-o-matic: ' . get_bloginfo( 'name' ) . ' Login Link';
        wp_mail($user->user_email, $subject, $message);

        $result = array(
            'error'=>false,
            'message'=> 'We sent you a login link.',
        );
        $response->set_data($result);

        return $response;
    }

    public function authorize_user() {
        $response = new WP_JSON_Response();

        if( !is_user_logged_in() ) {
            $result = array(
                'error'=>true,
                'message'=>'Authorization required.',
            );
            $response->set_data($result);
            return $response;
        }

        global $current_user;
        $twitter = get_user_meta($current_user->ID, '_campomatic_twitter', true);
        $is_admin = false;
        if( current_user_can('activate_plugins') )
            $is_admin = true;
        $result = array(
            'error'=>false,
            'message'=> array(
                'ID'=>$current_user->ID,
                'display_name'=> $current_user->display_name,
                'twitter'=> $twitter,
                'is_admin'=> $is_admin,
            ),
        );
        $response->set_data($result);

        return $response;
    }

    public function login_user($data = array() ) {
        $response = new WP_JSON_Response();

        if( is_user_logged_in() ) {
            $result = array(
                'error'=>false,
                'message'=>'Success!',
            );
            $response->set_data($result);
            return $response;
        }

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
        wp_update_user( array( 'ID'=>$user_id, 'display_name'=>$data['name']));
        update_user_meta($user_id, '_campomatic_access_key', $access_key );
        $twitter = ltrim( $data['twitter'], '@');
        update_user_meta( $user_id, '_campomatic_twitter', $data['twitter'] );
        $access_url = CAMPOMATIC_URL . 'connect/' . $access_key;
        $message = "Thanks for registering with Camp-o-matic.";
        $message .= "You can click the following link anytime you want to login: \n" . $access_url;
        $subject = 'Camp-o-matic: ' . get_bloginfo( 'name' ) . ' Registration';
        wp_mail($data['email'], $subject, $message);

        wp_set_auth_cookie( $user_id );

        $result = array(
            'error'=>false,
            'message'=> 'Success!',
        );
        $response->set_data($result);

        return $response;
    }

}

?>