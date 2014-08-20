<?php
/**
 * Class Campomatic_Connection logs a user in creating a new connection to the Campomatic app
 */
class Campomatic_Question {

    public function register_routes( $routes ) {
        $routes['/campomatic/ask'] = array(
            array( array( $this, 'publish_question'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
        );
        $routes['/campomatic/question/(?P<id>\d+)'] = array(
            array( array( $this, 'delete_question'), WP_JSON_Server::DELETABLE | WP_JSON_Server::ACCEPT_JSON ),
        );
        $routes['/campomatic/upvote'] = array(
            array( array( $this, 'upvote_question'), WP_JSON_Server::CREATABLE | WP_JSON_Server::ACCEPT_JSON ),
        );
        return $routes;
    }

    public function upvote_question( $data ) {

        $response = new WP_JSON_Response();
        $votes = get_post_meta( $data['id'], '_campomatic_votes', true);
        if( $data['vote_direction'] == 'up') {

            if( empty( $votes ) ) {
                $votes = array( $data['user'] );
            } else {
                $user_has_voted = array_search( $data['user'], $votes );
                if( false === $user_has_voted )
                    $votes[] = $data['user'];
            }

        }

        if( $data['vote_direction'] == 'down') {

            if( !empty( $votes ) ) {
                $user_has_voted = array_search( $data['user'], $votes );
                if( $user_has_voted !== false )
                    unset( $votes[$user_has_voted] );
            }

        }

        update_post_meta( $data['ID'], '_campomatic_votes', $votes );
        $session_id = get_post_meta($data['id'], '_campomatic_session_id', true);
        campomatic_update_heartbeat($session_id);

        $result = array(
            'error'=>false,
            'message'=>'Question updated.',
        );

        $response->set_data($result);

        return $response;

    }

    public function delete_question( $id ) {

        $response = new WP_JSON_Response();

        if( !current_user_can('edit_post', $id) ) {
            $result = array(
                'error'=>true,
                'message'=>'You are not authorized to delete this question.',
            );
            $response->set_data($result);
            return $response;
        }

        $question = get_post( $id );
        $session_id = get_post_meta($id, '_campomatic_session_id', true);
        campomatic_update_heartbeat($session_id);
        $deleted = wp_trash_post( $id );

        if ( ! $deleted ) {
            $result = array(
                'error'=>true,
                'message'=>'This question cannot be deleted.',
            );
            $response->set_data($result);
            return $response;
        }

        $result = array(
            'error'=>false,
            'message'=>'Deleted!',
        );

        $response->set_data( $result );
        return $response;
    }

    public function publish_question($data) {
        $response = new WP_JSON_Response();
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
        campomatic_update_heartbeat( $data['session_id'] );
        $result = array(
            'error'=>false,
            'message'=> 'Boom! Question asked. Ask another?',
        );
        $response->set_data($result);
        return $response;
    }

}

?>