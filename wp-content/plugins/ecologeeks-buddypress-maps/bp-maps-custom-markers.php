<?php

function bp_maps_custom_markers_enabled() {
	global $bp;
	
	//TO FIX
	return true;

	if ($bp->maps->options['plugins']['custom_markers']['enabled']) return true;
	return false;

}

function bp_maps_custom_markers_setup_nav() {
	global $bp;
	
	if (!bp_maps_custom_markers_enabled()) return false;
	
	bp_core_new_nav_item( array( 'name' => sprintf( __( 'Custom Markers <span>(%d)</span>', 'bp-maps' ), bp_maps_custom_markers_count() ), 'slug' => __('map','bp-maps-slugs'), 'position' => 90, 'screen_function' => 'bp_maps_custom_markers_screen', 'default_subnav_slug' => __('my-markers','bp-maps-slugs'), 'item_css_id' => $bp->maps->id ) );
	do_action( 'bp_maps_custom_markers_setup_nav');
}

function bp_maps_custom_markers_screen() {
	global $bp;

	do_action( 'bp_maps_custom_markers_screen' );

	bp_core_load_template( apply_filters( 'bp_maps_template_custom_markers', 'members/single/plugins' ) );
	
}

function bp_maps_custom_markers_count($user_id=false) {
	$markers_ids=bp_maps_get_custom_markers($user_id);
	return $markers_ids['total'];
}

function bp_maps_get_custom_markers($user_id=false) {
	global $bp;
	
	if (!$user_id)
		$user_id=$bp->displayed_user->id;

	$markers = BP_Maps_Map_Marker::get_markers( false, false, $user_id, false, 'custom');

	return $markers;

}


function bp_maps_custom_markers_is_map_page() {
	global $bp;
	
	//TO FIX 
	//bp_is_user_profile() return false !!!
	/*
	if (!bp_is_user_profile()) return false;
	if ($bp->current_component!=__( 'map', 'bp-maps-slugs' )) return false;
	*/
	
	if (!$bp->displayed_user->id) return false;
	if ($bp->current_component!=__( 'map', 'bp-maps-slugs' )) return false;
	
	return true;
}


function bp_maps_custom_markers_content() {
	if (!bp_maps_custom_markers_enabled()) return false;
	if (!bp_maps_custom_markers_is_map_page()) return false;
	
	
	global $bp;
	
	if ($bp->displayed_user->id==$bp->loggedin_user->id)
		$editable=true;
	
	//THIS IS FOR FETCHING THE MARKERS
	$marker_args[] = array(
		'type' => 'custom',
		'editable'=>$editable,
		'user_id'=>$bp->displayed_user->id,
		'markers_max'=>20,
		'name'=>__('Custom Markers','bp-maps')
	);

	//THOSE ARE THE MAP PARAMS
	$map_args = array(
		'title' => 'Members Map',
		'slug' =>	__('custom_markers','bp-maps-slugs'),
		'display'	=>'dynamic',
		'width'	=> '100%',
		'height' => '400',
		'groups_args'	=>$marker_args
	);
	
	$bp->maps->current_map = new Bp_Map($map_args);

	bp_maps_locate_template( array( 'maps/map.php' ), true );
	
}

function bp_maps_custom_markers_init() {
	global $bp;
	
	if (!class_exists('Bp_Map')) return false;

	if (!bp_maps_custom_markers_enabled()) return false;
	
	if (!bp_maps_custom_markers_is_map_page()) return false;

	//TO FIX STATEMENTS
	//if (bp_maps_groups_is_admin_screen()) { //creation or edition screen
		bp_maps_head_init();
	//}
	
}
add_action( 'bp_init', 'bp_maps_custom_markers_init');
add_action( 'plugins_loaded', 'bp_maps_custom_markers_setup_nav' );
add_action('bp_template_content','bp_maps_custom_markers_content');


?>