<?php
/**
 * Creates re-write rules and template handling for the Campomatic app
 *
 * @package Campomatic
 */

/**
 * Creates a route for our app when WordPress initializes
 *
 * @return void
 */
function campomatic_url_init() {
	campomatic_register_rewrites();

	global $wp;
	$wp->add_query_var( 'campomatic_route' );
}
add_action( 'init', 'campomatic_url_init' );

/**
 * Creates rewrites based on our app's url prefix
 *
 * @return void
 */
function campomatic_register_rewrites() {
	add_rewrite_rule( '^' . campomatic_get_url_prefix() . '/?$','index.php?campomatic_route=/','top' );
	add_rewrite_rule( '^' . campomatic_get_url_prefix() . '(.*)?','index.php?campomatic_route=$matches[1]','top' );
}

/**
 * Defines our app's url prefix
 *
 * @return string
 */
function campomatic_get_url_prefix() {
	return 'campomatic';
}

/**
 * Determine if the rewrite rules should be flushed.
 *
 * @return void
 */
function campomatic_maybe_flush_rewrites() {
	$version = get_option( 'campomatic_plugin_version', null );

	if ( empty( $version ) ||  $version !== CAMPOMATIC_VERSION ) {
		flush_rewrite_rules();
		update_option( 'campomatic_plugin_version', CAMPOMATIC_VERSION );
	}

}
add_action( 'init', 'campomatic_maybe_flush_rewrites', 999 );

/**
 * If we are within our app's url route, make sure we return app.php as the template file
 *
 * @return string
 */
function campomatic_loaded($template) {
	if ( empty( $GLOBALS['wp']->query_vars['campomatic_route'] ) )
		return $template;

	$new_template = dirname( __FILE__ ) . '/views/app.php';
    return $new_template;
}
add_action( 'template_include', 'campomatic_loaded', 99 );

?>