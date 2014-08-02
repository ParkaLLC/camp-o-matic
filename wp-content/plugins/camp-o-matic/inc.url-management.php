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
function campomatic_maybe_flush_rewrites() {
	$version = get_option( 'campomatic_plugin_version', null );

	if ( empty( $version ) ||  $version !== CAMPOMATIC_VERSION ) {
		flush_rewrite_rules();
		update_option( 'campomatic_plugin_version', CAMPOMATIC_VERSION );
	}

}
add_action( 'init', 'campomatic_maybe_flush_rewrites', 999 );

/**
 * Load Camp-o-matic
 */
function campomatic_loaded($template) {
	if ( empty( $GLOBALS['wp']->query_vars['campomatic_route'] ) )
		return $template;

	$new_template = dirname( __FILE__ ) . '/views/app.php';
    return $new_template;
}
add_action( 'template_include', 'campomatic_loaded', 99 );