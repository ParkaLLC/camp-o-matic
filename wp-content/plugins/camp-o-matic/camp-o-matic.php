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
define('CAMPOMATIC_VERSION', '1.0');

define('CAMPOMATIC_URL', home_url() . '/campomatic/#/' );
// loads in custom endpoints for the WP REST API
require('inc.api.php');
// create url / template management
require( 'inc.url-management.php' );

?>