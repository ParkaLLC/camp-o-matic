<?php
/**
 * Register our rewrite rules for the API
 */
function campomatic_url_init() {
	campomatic_register_rewrites();

	global $wp;
	$wp->add_query_var( 'campomatic_route' );
}
add_action( 'init', 'campomatic_url_init' );

function campomatic_register_rewrites() {
	add_rewrite_rule( '^' . campomatic_get_url_prefix() . '/?$','index.php?campomatic_route=/','top' );
	add_rewrite_rule( '^' . campomatic_get_url_prefix() . '(.*)?','index.php?campomatic_route=$matches[1]','top' );
}

function campomatic_get_url_prefix() {
	return 'campomatic';
}

/**
 * Determine if the rewrite rules should be flushed.
 */
function json_api_maybe_flush_rewrites() {
	$version = get_option( 'json_api_plugin_version', null );

	if ( empty( $version ) ||  $version !== JSON_API_VERSION ) {
		flush_rewrite_rules();
		update_option( 'json_api_plugin_version', JSON_API_VERSION );
	}

}
add_action( 'init', 'json_api_maybe_flush_rewrites', 999 );

/**
 * Load Camp-o-matic
 */
function campomatic_loaded() {
	if ( empty( $GLOBALS['wp']->query_vars['campomatic_route'] ) )
		return;

	echo '<h3>Hoorah!</h3>';
	echo $GLOBALS['wp']->query_vars['campomatic_route'];

	// Finish off our request
	die();
}
add_action( 'template_redirect', 'campomatic_loaded', -100 );