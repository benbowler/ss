<?php
if ( !defined( 'ABSPATH' ) ) exit;

function etivite_bp_pending_activations_activated_user( $user_id, $key, $user ) {

	delete_user_meta( $user_id, 'activation_key_resent' );
	wp_cache_delete( 'etivite_bp_pending_activations_count' );

}
add_action( 'bp_core_activated_user', 'etivite_bp_pending_activations_activated_user', 3, 3 );

function etivite_bp_pending_activations_users_count() {
	global $wpdb;
	
	//if no cache is found
	if ( !$count = wp_cache_get( 'etivite_bp_pending_activations_count', 'bp' ) ) {
		
		$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(u.ID) FROM $wpdb->usermeta m1, $wpdb->users u WHERE u.ID = m1.user_id AND u.user_status = 2 AND m1.meta_key = 'activation_key' ORDER BY u.user_registered ASC" ) );
		
		if ( empty($count) ) $count = 0;
		
		/* Cache the link */
		if ( !empty( $count ) )
			wp_cache_set( 'etivite_bp_pending_activations_count', $count, 'bp' );
	}
	
	return $count;
}

function etivite_bp_pending_activations_clear_cache() {
	wp_cache_delete( 'etivite_bp_pending_activations_count' );
}
add_action( 'bp_core_signup_user', 'etivite_bp_pending_activations_clear_cache' );

?>
