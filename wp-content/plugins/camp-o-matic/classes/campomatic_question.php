<?php
/**
 * Class Campomatic_Connection logs a user in creating a new connection to the Campomatic app
 */
class Campomatic_Question {
    public function register_routes( $routes ) {
        $routes['/campomatic/ask'] = array(
            array( array( $this, 'publish_question'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
        );

        return $routes;
    }

    public function publish_question($data) {
        $response = new WP_JSON_Response();
        error_log( print_r($data, true), 1, 'rocco@hcg.bz');
        if( !is_user_logged_in() ) {
            $result = array(
                'error'=>true,
                'message'=>'You gotta login to make a question.',
            );
            $response->set_data($result);
            return $response;
        }

        if( empty($data['question']) ) {
            $result = array(
                'error'=>true,
                'message'=>'That\'s not a question.',
            );
            $response->set_data($result);
            return $response;
        }

        $post_args = array(
            'post_type'=>'happiness',
            'post_content'=> wp_strip_all_tags( $data['question'] ),
            'post_status'=>'publish',
        );

        $post_id = wp_insert_post( $post_args, true );


        if( $post_id instanceof WP_Error ) {
            $result = array(
                'error'=>true,
                'message'=> $post_id->get_error_message(),
            );
            $response->set_data($result);
            return $response;
        }

        update_post_meta( $post_id, '_campomatic_session_id', $data['session_id']);
        campomatic_update_heartbeat( $post_id );
        $result = array(
            'error'=>false,
            'message'=> 'Boom! Question asked.',
        );
        $response->set_data($result);
        return $response;
    }

}

?>