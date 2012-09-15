<?php
/*
Plugin Name: BuddyPress Pending Activations
Plugin URI: http://wordpress.org/extend/plugins/buddypress-pending-activations/
Description: Manage pending member activations (keys) for main BuddyPress installation - (not for multisite blogs signups - yet.)
Author: rich @etiviti
Author URI: http://etivite.com
License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
Version: 0.5.2
Text Domain: bp-pending-activations
Network: true
*/

//http://wordpress.org/extend/plugins/unconfirmed/ - boone for overall wp and ms

function etivite_bp_pending_activations_init() {

	//don't load for MS
	if ( is_multisite() )
		return false;

	if ( file_exists( dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo' ) )
		load_textdomain( 'bp-pending-activiations', dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo' );
		
	require( dirname( __FILE__ ) . '/bp-pending-activations.php' );
	
	add_action( bp_core_admin_hook(), 'etivite_bp_pending_activations_admin_add_admin_menu' );
	
}
add_action( 'bp_include', 'etivite_bp_pending_activations_init', 88 );
//add_action( 'bp_init', 'etivite_bp_pending_activations_init', 88 );

//add admin_menu page
function etivite_bp_pending_activations_admin_add_admin_menu() {
	global $bp;

	if ( !is_super_admin() )
		return false;

	//Add the component's administration tab under the "BuddyPress" menu for site administrators
	require ( dirname( __FILE__ ) . '/admin/bp-pending-activations-admin.php' );

	$count = sprintf( __('Pending Activations (%s)'), etivite_bp_pending_activations_users_count() );

	add_submenu_page( 'bp-general-settings', __( 'Pending Activations', 'bp-pending-activations' ), $count, 'manage_options', 'bp-pending-activations-settings', 'etivite_bp_pending_activations_admin' );	

}

/* Stolen from Welcome Pack - thanks, Paul! then stolen from boone*/
function etivite_bp_pending_activations_admin_add_action_link( $links, $file ) {
	
	//don't load for MS
	if ( is_multisite() )
		return $links;
	
	if ( 'buddypress-pending-activations/bp-pending-activations-loader.php' != $file )
		return $links;

	if ( function_exists( 'bp_core_do_network_admin' ) ) {
		$settings_url = add_query_arg( 'page', 'bp-pending-activations-settings', bp_core_do_network_admin() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' ) );
	} else {
		$settings_url = add_query_arg( 'page', 'bp-pending-activations-settings', is_multisite() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' ) );
	}

	$settings_link = '<a href="' . $settings_url . '">' . __( 'Settings', 'bp-activity-edit' ) . '</a>';
	array_unshift( $links, $settings_link );

	return $links;
}
add_filter( 'plugin_action_links', 'etivite_bp_pending_activations_admin_add_action_link', 10, 2 );
?>
