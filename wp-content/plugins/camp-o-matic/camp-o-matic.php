<?php

/**
 * Camp-o-matic will enhance your WordCamp experience
 *
 * @package Campomatic
 */

/*
Plugin Name: Camp-O-Matic
Version: 1.0
Author: Hotchkiss Consulting Group
Author URI: http://hotchkissconsulting.net/
*/
$upload_dir = wp_upload_dir();

define('CAMPOMATIC_VERSION', '1.0');
define('CAMPOMATIC_URL', home_url() . '/campomatic/#/' );
define('HEARTBEAT_DIR', $upload_dir['basedir'] . '/campomatic-hb/' );
// loads in custom endpoints for the WP REST API
require('inc.api.php');
// create url / template management
require( 'inc.url-management.php' );
// custom post type management
require( 'inc.cpt.php' );

/**
 * Our activation hook
 *
 * Creates a directory for storing heartbeat files. Creates a heartbeat file for all existing
 */
function campomatic_activate() {

    mkdir(HEARTBEAT_DIR);

    $args = array(
        'post_type'=>'wcb_session',
        'posts_per_page'=>-1,
        'meta_key'=>'_wcpt_session_type',
        'meta_value'=>'session'
    );

    $sessions = get_posts($args);
    if( !is_array($sessions) || empty($sessions))
        return;

    foreach( $sessions as $s ) {
        campomatic_update_heartbeat( $s->ID, $version);
    }
}
register_activation_hook( __FILE__, 'campomatic_activate' );

?>