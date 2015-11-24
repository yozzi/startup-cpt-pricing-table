<?php
/*
Plugin Name: StartUp CPT Pricing Table
Description: Le plugin pour activer le Custom Post Pricing Table
Author: Yann Caplain
Version: 1.2.0
Text Domain: startup-cpt-pricing-table
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

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

//add_action( 'init', 'startup_reloaded_pricing_updater' );

//CPT
function startup_reloaded_pricing() {
	$labels = array(
		'name'                => _x( 'Price table items', 'Post Type General Name', 'startup-cpt-pricing-table' ),
		'singular_name'       => _x( 'Price table item', 'Post Type Singular Name', 'startup-cpt-pricing-table' ),
		'menu_name'           => __( 'Pricing table', 'startup-cpt-pricing-table' ),
		'name_admin_bar'      => __( 'Pricing table', 'startup-cpt-pricing-table' ),
		'parent_item_colon'   => __( 'Parent Item:', 'startup-cpt-pricing-table' ),
		'all_items'           => __( 'All Items', 'startup-cpt-pricing-table' ),
		'add_new_item'        => __( 'Add New Item', 'startup-cpt-pricing-table' ),
		'add_new'             => __( 'Add New', 'startup-cpt-pricing-table' ),
		'new_item'            => __( 'New Item', 'startup-cpt-pricing-table' ),
		'edit_item'           => __( 'Edit Item', 'startup-cpt-pricing-table' ),
		'update_item'         => __( 'Update Item', 'startup-cpt-pricing-table' ),
		'view_item'           => __( 'View Item', 'startup-cpt-pricing-table' ),
		'search_items'        => __( 'Search Item', 'startup-cpt-pricing-table' ),
		'not_found'           => __( 'Not found', 'startup-cpt-pricing-table' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'startup-cpt-pricing-table' )
	);
	$args = array(
		'label'               => __( 'pricing', 'startup-cpt-pricing-table' ),
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
		'title'         => __( 'Pricing details', 'startup-cpt-pricing-table' ),
		'object_types'  => array( 'pricing' )
	) );
    
    $cmb_box->add_field( array(
		'name'       => __( 'Currency', 'startup-cpt-pricing-table' ),
		'id'         => $prefix . 'currency',
        'default'     => '$',
		'type'       => 'text'
	) );

    $cmb_box->add_field( array(
		'name'       => __( 'Price', 'startup-cpt-pricing-table' ),
		'id'         => $prefix . 'price',
		'type'       => 'text'
	) );
    
    $cmb_box->add_field( array(
		'name'       => __( 'Unit', 'startup-cpt-pricing-table' ),
		'id'         => $prefix . 'unit',
        'default'     => '/MO.',
		'type'       => 'text'
	) );
    
    $cmb_box->add_field( array(
		'name'             => __( 'Featured', 'startup-cpt-pricing-table' ),
		'id'               => $prefix . 'featured',
		'type'             => 'checkbox'
	) );
    
    $cmb_box->add_field( array(
		'name'       => __( 'Button text', 'startup-cpt-pricing-table' ),
		'id'         => $prefix . 'button_text',
		'type'       => 'text'
	) );
    
    $cmb_box->add_field( array(
		'name'       => __( 'Button url', 'startup-cpt-pricing-table' ),
		'id'         => $prefix . 'button_url',
		'type'       => 'text'
	) );
    
    $cmb_box->add_field( array(
        'name'             => __( 'Icon 1', 'startup-cpt-pricing-table' ),
        'id'               => $prefix . 'icon_1',
        'type'             => 'select',
        'show_option_none' => true,
        'options'          => $font_awesome
    ) );

    $cmb_box->add_field( array(
		'name'       => __( 'Text 1', 'startup-cpt-pricing-table' ),
		'id'         => $prefix . 'text_1',
		'type'       => 'text'
	) );
    
    $cmb_box->add_field( array(
        'name'             => __( 'Icon 2', 'startup-cpt-pricing-table' ),
        'id'               => $prefix . 'icon_2',
        'type'             => 'select',
        'show_option_none' => true,
        'options'          => $font_awesome
    ) );

    $cmb_box->add_field( array(
		'name'       => __( 'Text 2', 'startup-cpt-pricing-table' ),
		'id'         => $prefix . 'text_2',
		'type'       => 'text'
	) );
    
    $cmb_box->add_field( array(
        'name'             => __( 'Icon 3', 'startup-cpt-pricing-table' ),
        'id'               => $prefix . 'icon_3',
        'type'             => 'select',
        'show_option_none' => true,
        'options'          => $font_awesome
    ) );

    $cmb_box->add_field( array(
		'name'       => __( 'Text 3', 'startup-cpt-pricing-table' ),
		'id'         => $prefix . 'text_3',
		'type'       => 'text'
	) );
    
    $cmb_box->add_field( array(
        'name'             => __( 'Icon 4', 'startup-cpt-pricing-table' ),
        'id'               => $prefix . 'icon_4',
        'type'             => 'select',
        'show_option_none' => true,
        'options'          => $font_awesome
    ) );

    $cmb_box->add_field( array(
		'name'       => __( 'Text 4', 'startup-cpt-pricing-table' ),
		'id'         => $prefix . 'text_4',
		'type'       => 'text'
	) );
}

add_action( 'cmb2_admin_init', 'startup_reloaded_pricing_meta' );

// Shortcode
function startup_reloaded_pricing_shortcode( $atts ) {

	// Attributes
    $atts = shortcode_atts(array(
            'bg' => ''
        ), $atts);
    
	// Code
        ob_start();
        require get_template_directory() . '/template-parts/content-pricing.php';
        return ob_get_clean();    
}
add_shortcode( 'pricing', 'startup_reloaded_pricing_shortcode' );

// Enqueue scripts and styles.
function startup_cpt_pricing_scripts() {
    wp_enqueue_style( 'startup-cpt-pricing-style', plugins_url( '/css/startup-cpt-pricing-table.css', __FILE__ ), array( ), false, 'all' );
}

add_action( 'wp_enqueue_scripts', 'startup_cpt_pricing_scripts' );
?>