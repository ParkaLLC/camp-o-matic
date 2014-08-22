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
    global $campomatic_connection, $campomatic_question;
    require('classes/campomatic_connection.php');
    require('classes/campomatic_question.php');
    $campomatic_connection = new Campomatic_Connection();
    $campomatic_question = new Campomatic_Question();
    add_filter( 'json_endpoints', array( $campomatic_connection, 'register_routes' ) );
    add_filter( 'json_endpoints', array( $campomatic_question, 'register_routes' ) );
}
add_action( 'wp_json_server_before_serve', 'campomatic_endpoint_registrar' );

/**
 *  Filters the allowable json_query_vars to give campomatic more flexability
 *
 * @param $vars
 * @return array
 */
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

/**
 * Adds meta to the wcb_session and happiness post types, and passes it along with the json response
 * @param $meta
 * @param $post_id
 */
function campomatic_session_meta( $_post, $post, $context ) {

    $campomatic_filters = array( 'wcb_session', 'happiness');

    if( !in_array( $_post['type'], $campomatic_filters ) )
        return $_post;

    if( $context != 'view' )
        return $_post;

    if( $_post['type'] == 'happiness' ) {
        global $current_user;
        $asker = get_user_by( 'id', $_post['author']);
        $twitter = get_user_meta( $asker->ID, '_campomatic_twitter', true);
        if( empty($twitter) )
            $handle = $asker->display_name;
        else
            $handle = '@'.$twitter;
        $_post['meta']['author'] = $handle;

        $votes = get_post_meta( $_post['ID'], '_campomatic_votes', true);
        if( empty($votes))
            $votes = array();

        $voted = false;

        if( in_array($current_user->ID, $votes))
            $voted = true;

        $_post['meta']['voted'] = $voted;

        $num_votes = count($votes);
        $_post['meta']['total_votes'] = $num_votes;

        return $_post;
    }

    if( $_post['type'] == 'wcb_session' ) {
        $time_meta = get_post_meta( $_post['ID'], '_wcpt_session_time', true );
        if( !empty($time_meta) )
            $_post['meta']['time'] = date('D, g:i a', $time_meta);

        $speaker_meta = get_post_meta( $_post['ID'], '_wcb_session_speakers', true);
        $speaker_meta = rtrim( $speaker_meta, ',');
        $speaker_array = explode(",", $speaker_meta);

        $speaker_id = get_post_meta( $_post['ID'], '_wcpt_speaker_id', true);
        $speaker_gravatar = get_post_meta( $speaker_id, '_wcb_speaker_email', true );
        $session_version =  get_post_meta( $_post['ID'], '_campomatic_version', true);

        $speaker_number = count( $speaker_array );

        $speaker = get_post( $speaker_id );

        if( !empty( $speaker ) )
            $_post['meta']['speaker_slug'] = $speaker->post_name;

        if ( !empty( $speaker_gravatar ) && $speaker_number === 1  ) {
            $_post['meta']['speaker_grav'] = md5( strtolower( trim( $speaker_gravatar) ) );
        } else {
            $_post['meta']['speaker_grav'] = '';
        }

        if( !empty($speaker_meta))
            $_post['meta']['speaker'] = rtrim( $speaker_meta, ',');

        $_post['meta']['version'] = $session_version;

        return $_post;
    }


}
add_filter('json_prepare_post', 'campomatic_session_meta', 10, 3);

function campomatic_update_heartbeat( $post_id ) {
    $version = wp_generate_password(20, false);
    update_post_meta($post_id, '_campomatic_version', $version);
    $version_heartbeat_file = HEARTBEAT_DIR . $post_id . '.txt';
    $handle = fopen($version_heartbeat_file, 'w');
    fwrite( $handle, $version );
    fclose($handle);
    return $version;
}

function campomatic_session_publish( $ID, $post ) {
   campomatic_update_heartbeat($ID);
}
add_action(  'publish_wcb_session',  'campomatic_session_publish', 10, 2 );

?>