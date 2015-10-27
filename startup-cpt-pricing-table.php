<?php
/*
Plugin Name: StartUp Pricing Table
Description: Le plugin pour activer le Custom Post Pricing Table
Author: Yann Caplain
Version: 1.2.0
*/

//GitHub Plugin Updater
function startup_reloaded_pricing_updater() {
	include_once 'lib/updater.php';
	//define( 'WP_GITHUB_FORCE_UPDATE', true );
	if ( is_admin() ) {
		$config = array(
			'slug' => plugin_basename( __FILE__ ),
			'proper_folder_name' => 'startup-cpt-pricing-table',
			'api_url' => 'https://api.github.com/repos/yozzi/startup-cpt-pricing-table',
			'raw_url' => 'https://raw.github.com/yozzi/startup-cpt-pricing-table/master',
			'github_url' => 'https://github.com/yozzi/startup-cpt-pricing-table',
			'zip_url' => 'https://github.com/yozzi/startup-cpt-pricing-table/archive/master.zip',
			'sslverify' => true,
			'requires' => '3.0',
			'tested' => '3.3',
			'readme' => 'README.md',
			'access_token' => '',
		);
		new WP_GitHub_Updater( $config );
	}
}

add_action( 'init', 'startup_reloaded_pricing_updater' );

//CPT
function startup_reloaded_pricing() {
	$labels = array(
		'name'                => _x( 'Price table items', 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( 'Price table item', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( 'Pricing table', 'text_domain' ),
		'name_admin_bar'      => __( 'Pricing table', 'text_domain' ),
		'parent_item_colon'   => __( 'Parent Item:', 'text_domain' ),
		'all_items'           => __( 'All Items', 'text_domain' ),
		'add_new_item'        => __( 'Add New Item', 'text_domain' ),
		'add_new'             => __( 'Add New', 'text_domain' ),
		'new_item'            => __( 'New Item', 'text_domain' ),
		'edit_item'           => __( 'Edit Item', 'text_domain' ),
		'update_item'         => __( 'Update Item', 'text_domain' ),
		'view_item'           => __( 'View Item', 'text_domain' ),
		'search_items'        => __( 'Search Item', 'text_domain' ),
		'not_found'           => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' )
	);
	$args = array(
		'label'               => __( 'pricing', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'revisions' ),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-cart',
		'show_in_admin_bar'   => false,
		'show_in_nav_menus'   => false,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
        'capability_type'     => array('pricing','pricings'),
        'map_meta_cap'        => true
	);
	register_post_type( 'pricing', $args );

}

add_action( 'init', 'startup_reloaded_pricing', 0 );

//Flusher les permalink à l'activation du plugin pour qu'ils fonctionnent sans mise à jour manuelle
function startup_reloaded_pricing_rewrite_flush() {
    startup_reloaded_pricing();
    flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'startup_reloaded_pricing_rewrite_flush' );

// Capabilities
function startup_reloaded_pricing_caps() {	
	$role_admin = get_role( 'administrator' );
	$role_admin->add_cap( 'edit_pricing' );
	$role_admin->add_cap( 'read_pricing' );
	$role_admin->add_cap( 'delete_pricing' );
	$role_admin->add_cap( 'edit_others_pricings' );
	$role_admin->add_cap( 'publish_pricings' );
	$role_admin->add_cap( 'edit_pricings' );
	$role_admin->add_cap( 'read_private_pricings' );
	$role_admin->add_cap( 'delete_pricings' );
	$role_admin->add_cap( 'delete_private_pricings' );
	$role_admin->add_cap( 'delete_published_pricings' );
	$role_admin->add_cap( 'delete_others_pricings' );
	$role_admin->add_cap( 'edit_private_pricings' );
	$role_admin->add_cap( 'edit_published_pricings' );
}

register_activation_hook( __FILE__, 'startup_reloaded_pricing_caps' );

// Metaboxes
function startup_reloaded_pricing_meta() {
    require get_template_directory() . '/inc/font-awesome.php';

	// Start with an underscore to hide fields from custom fields list
	$prefix = '_startup_reloaded_pricing_';

	$cmb_box = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => __( 'Pricing details', 'cmb2' ),
		'object_types'  => array( 'pricing' )
	) );
    
    $cmb_box->add_field( array(
		'name'       => __( 'Currency', 'cmb2' ),
		'id'         => $prefix . 'currency',
        'default'     => '$',
		'type'       => 'text'
	) );

    $cmb_box->add_field( array(
		'name'       => __( 'Price', 'cmb2' ),
		'id'         => $prefix . 'price',
		'type'       => 'text'
	) );
    
    $cmb_box->add_field( array(
		'name'       => __( 'Unit', 'cmb2' ),
		'id'         => $prefix . 'unit',
        'default'     => '/MO.',
		'type'       => 'text'
	) );
    
    $cmb_box->add_field( array(
		'name'             => __( 'Featured', 'cmb2' ),
		'id'               => $prefix . 'featured',
		'type'             => 'checkbox'
	) );
    
    $cmb_box->add_field( array(
		'name'       => __( 'Button text', 'cmb2' ),
		'id'         => $prefix . 'button_text',
		'type'       => 'text'
	) );
    
    $cmb_box->add_field( array(
		'name'       => __( 'Button url', 'cmb2' ),
		'id'         => $prefix . 'button_url',
		'type'       => 'text'
	) );
    
    $cmb_box->add_field( array(
        'name'             => __( 'Icon 1', 'cmb2' ),
        'id'               => $prefix . 'icon_1',
        'type'             => 'select',
        'show_option_none' => true,
        'options'          => $font_awesome
    ) );

    $cmb_box->add_field( array(
		'name'       => __( 'Text 1', 'cmb2' ),
		'id'         => $prefix . 'text_1',
		'type'       => 'text'
	) );
    
    $cmb_box->add_field( array(
        'name'             => __( 'Icon 2', 'cmb2' ),
        'id'               => $prefix . 'icon_2',
        'type'             => 'select',
        'show_option_none' => true,
        'options'          => $font_awesome
    ) );

    $cmb_box->add_field( array(
		'name'       => __( 'Text 2', 'cmb2' ),
		'id'         => $prefix . 'text_2',
		'type'       => 'text'
	) );
    
    $cmb_box->add_field( array(
        'name'             => __( 'Icon 3', 'cmb2' ),
        'id'               => $prefix . 'icon_3',
        'type'             => 'select',
        'show_option_none' => true,
        'options'          => $font_awesome
    ) );

    $cmb_box->add_field( array(
		'name'       => __( 'Text 3', 'cmb2' ),
		'id'         => $prefix . 'text_3',
		'type'       => 'text'
	) );
    
    $cmb_box->add_field( array(
        'name'             => __( 'Icon 4', 'cmb2' ),
        'id'               => $prefix . 'icon_4',
        'type'             => 'select',
        'show_option_none' => true,
        'options'          => $font_awesome
    ) );

    $cmb_box->add_field( array(
		'name'       => __( 'Text 4', 'cmb2' ),
		'id'         => $prefix . 'text_4',
		'type'       => 'text'
	) );
}

add_action( 'cmb2_init', 'startup_reloaded_pricing_meta' );
?>