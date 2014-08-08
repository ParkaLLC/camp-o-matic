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
        $_post['meta']['time'] = date('F j, g:i a', $time_meta);

    $speaker_meta = get_post_meta( $_post['ID'], '_wcb_session_speakers', true);

    if( !empty($speaker_meta))
        $_post['meta']['speaker'] = rtrim( $speaker_meta, ',');

    return $_post;
}
add_filter('json_prepare_post', 'campomatic_session_meta', 10, 3);

?>