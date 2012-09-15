<?php
/*
Plugin Name: BuddyPress Maps
Plugin URI:  http://dev.benoitgreant.be/blog/category/buddypress/buddypress-maps/
Description: BuddyPress Maps is a component that allows to find and display location markers on a Google Map.  It includes several plugins to work with BuddyPress and its API has been coded to allow others plugins to use the component.
Version: 0.30
Revision Date: March 30 2010
Requires at least: WP 2.9.1, BuddyPress 1.2
Tested up to: WP 3.0-alpha, BuddyPress 1.2.2
License: (Maps: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html)
Author: G.Breant
Author URI: http://dev.benoitgreant.be
Site Wide Only: true
*/


/*** Make sure BuddyPress is loaded ********************************/
if ( !function_exists( 'bp_core_install' ) ) {
	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
	if ( is_plugin_active( 'buddypress/bp-loader.php' ) )
		require_once ( WP_PLUGIN_DIR . '/buddypress/bp-loader.php' );
	else
		return;
}
/*******************************************************************/

function bp_maps_init() {
	/* Define the slug for the component */
	if ( !defined( 'BP_MAPS_SLUG' ) )
		define ( 'BP_MAPS_SLUG', __('maps','bp-maps-slugs') );

	/////////
	// Important Internal Constants
	// *** DO NOT MODIFY THESE ***
	define ( 'BP_MAPS_IS_INSTALLED', 1 );
	define ( 'BP_MAPS_VERSION', '0.30' );
	define ( 'BP_MAPS_DB_VERSION', '1100' );
	define ( 'BP_MAPS_PLUGIN_NAME', 'buddypress-maps' );
	define ( 'BP_MAPS_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . BP_MAPS_PLUGIN_NAME );
	define ( 'BP_MAPS_PLUGIN_URL', WP_PLUGIN_URL . '/' . BP_MAPS_PLUGIN_NAME );


	/////////

	// lets do it
	require_once 'bp-maps.php';
}

if ( defined( 'BP_VERSION' ) )
	bp_maps_init();
else
	add_action( 'bp_init', 'bp_maps_init' );



?>