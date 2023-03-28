<?php

/**

 *	Plugin Name: Store Locations

 *	Description: An easy to use store locations management system that give users to search for stores. [store-locations]

 *	Version: 1.2
 */



define( 'LOCATION_DIR_URI', plugin_dir_url( __FILE__ ));
define( 'LOCATION_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DISTANCE_MILES', 50);


/**
 * Plugin page links
 */
function store_location_plugin_links( $links ) {

	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=set-store-loc-setting-page' ) . '">' . __( 'Settings', 'store-location' ) . '</a>',
	);

	return array_merge( $plugin_links, $links );
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'store_location_plugin_links' );

/**
 * Load the main plugin file that will handle all actions
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/store-locations-class.php';
$store_loc_obj = new Store_Locations();

