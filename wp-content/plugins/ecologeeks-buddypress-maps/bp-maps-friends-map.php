<?php
function bp_maps_friends_map_is_enabled() {
	global $bp;
	if ($bp->maps->options['plugins']['friends_map']['enabled']) return true;
		
	return false;
}

function bp_maps_friends_setup_nav() {
	if (!bp_maps_friends_map_is_enabled()) return false;
	
	global $bp;
	
	$friends_link = $bp->loggedin_user->domain . $bp->friends->slug . '/';
	bp_core_new_subnav_item( array( 'name' => __( 'Friends map', 'bp-maps' ), 'slug' => __( 'friends-map', 'bp-maps-slugs' ), 'parent_url' => $friends_link, 'parent_slug' => $bp->friends->slug, 'screen_function' => 'bp_maps_friends_map_screen', 'position' => 15, 'item_css_id' => 'friends-map' ) );
}

function bp_maps_friends_map_screen() {
	global $bp;

		bp_core_load_template( apply_filters( 'bp_maps_template_friends_map', 'members/single/plugins' ) );
}


function bp_maps_friends_map_display() {
	global $bp;
	
	$friends_markers_ids = bp_maps_profile_get_members_markers_ids('member_profile','user_id='.bp_displayed_user_id());
	$friends_custom_markers_ids = bp_maps_profile_get_members_markers_ids('custom','user_id='.bp_displayed_user_id());

	if ($friends_markers_ids) {
	
		do_action( 'bp_maps_before_friends_map' );
	
		global $bp;
		//THIS IS FOR FETCHING THE MARKERS

		
		$marker_args[] = array(
			'include_ids' => $friends_markers_ids,
			'name'=>__('Friends Locations','bp-maps')
		);
		
		$marker_args[] = array(
			'include_ids' => $friends_custom_markers_ids,
			'name'=>__('Friends Markers','bp-maps')
		);
		
		//THIS IS THE MAP PARAMS
		$map_args = array(
			'display'=>'dynamic',
			'width'=>'100%',
			'height'=>'500',
			'groups_args'	=>$marker_args,
			'slug'=>'profile_friends'
		);
		
		$bp->maps->current_map = new Bp_Map($map_args);
		bp_maps_locate_template( array( 'maps/map.php' ), true );
		
		
		do_action( 'bp_maps_after_friends_map' );
		
	}else {?>
	
	<div id="message" class="info">
		<p><?php _e( "Sorry, no members were found.", 'buddypress' ) ?></p>
	</div>
	
	<?php
	}
	
}

function bp_is_friends_map_page() {
	global $bp;
	
	if ($bp->current_component != BP_FRIENDS_SLUG) return false;
	
	if ($bp->current_action !=__( 'friends-map', 'bp-maps-slugs' )) return false;
	
	return true;
}

function bp_maps_friends_init() {
	if (bp_is_friends_map_page()) {
		bp_maps_head_init();
		add_action( 'bp_template_content','bp_maps_friends_map_display');
	}
	
}

add_action('bp_init','bp_maps_friends_init');
add_action( 'friends_setup_nav','bp_maps_friends_setup_nav');

?>