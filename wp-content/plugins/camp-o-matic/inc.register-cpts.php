<?php
/**
 * Registers the COM_Session custom post type
 *
 * @package Campomatic
 */
function com_session_register_cpt() {

	$labels = array(
		'name'                => _x( 'COM Sessions', 'Post Type General Name', 'campomatic' ),
		'singular_name'       => _x( 'COM Session', 'Post Type Singular Name', 'campomatic' ),
		'menu_name'           => __( 'COM Sessions', 'campomatic' ),
		'parent_item_colon'   => __( 'Parent Session:', 'campomatic' ),
		'all_items'           => __( 'All Sessions', 'campomatic' ),
		'view_item'           => __( 'View Session', 'campomatic' ),
		'add_new_item'        => __( 'Add New Session', 'campomatic' ),
		'add_new'             => __( 'Add New', 'campomatic' ),
		'edit_item'           => __( 'Edit Session', 'campomatic' ),
		'update_item'         => __( 'Update Session', 'campomatic' ),
		'search_items'        => __( 'Search Session', 'campomatic' ),
		'not_found'           => __( 'Not found', 'campomatic' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'campomatic' ),
	);
	$args = array(
		'label'               => __( 'com_session', 'campomatic' ),
		'description'         => __( 'Camp-O-Matic Session', 'campomatic' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'comments', ),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);
	register_post_type( 'com_session', $args );

}

// Hook into the 'init' action
add_action( 'init', 'com_session_register_cpt', 0 );

?>