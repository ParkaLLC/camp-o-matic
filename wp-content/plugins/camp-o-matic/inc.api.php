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
    require('classes/campomatic_connection.php');
    $campomatic_connection = new Campomatic_Connection();
    add_filter( 'json_endpoints', array( $campomatic_connection, 'register_routes' ) );
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
 * Adds meta to the wcb_session post type
 * @param $meta
 * @param $post_id
 */
function campomatic_session_meta( $_post, $post, $context ) {

    if( $_post['type'] != 'wcb_session')
        return $_post;

    if( $context != 'view' )
        return $_post;

    $time_meta = get_post_meta( $_post['ID'], '_wcpt_session_time', true );
    if( !empty($time_meta) )
        $_post['meta']['time'] = date('D, g:i a', $time_meta);

    $speaker_meta = get_post_meta( $_post['ID'], '_wcb_session_speakers', true);
    $speaker_id = get_post_meta( $_post['ID'], '_wcpt_speaker_id', true);
    $speaker_gravatar = get_post_meta( $speaker_id, '_wcb_speaker_email', true );

    $speaker = get_post( $speaker_id );

    if( !empty( $speaker ) )
        $_post['meta']['speaker_slug'] = $speaker->post_name;

    if ( !empty( $speaker_gravatar ) ) {
        $_post['meta']['speaker_grav'] = md5( strtolower( trim( $speaker_gravatar) ) );
    } else {
        $_post['meta']['speaker_grav'] = '';
    }

    if( !empty($speaker_meta))
        $_post['meta']['speaker'] = rtrim( $speaker_meta, ',');

    return $_post;
}
add_filter('json_prepare_post', 'campomatic_session_meta', 10, 3);

function campomatic_update_heartbeat( $post_id ) {
    $version = wp_generate_password(20, false);
    update_post_meta($post_id, '_campomatic_version', $version);
    $version_heartbeat_file = HEARTBEAT_DIR . $post_id . '.txt';
    $handle = fopen($version_heartbeat_file, 'w');
    fwrite( $handle, $version );
}

function campomatic_session_publish( $ID, $post ) {
   campomatic_update_heartbeat($ID);
}
add_action(  'publish_wcb_session',  'campomatic_session_publish', 10, 2 );

?>