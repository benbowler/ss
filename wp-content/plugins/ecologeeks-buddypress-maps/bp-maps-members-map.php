<?php

function bp_maps_members_map_is_enabled() {
	global $bp;

	//TO FIX : do not work with this (HOOK PRIORITY PROBLEM)
	//if (!$bp->maps->options['plugins']['profiles_maps']) return false;
	
	$maps_options = get_site_option( 'bp_maps_options');
	if ($maps_options['plugins']['members_map']['enabled']) return true;
		
	return false;
}

///SITEWIDE MAP|START
function bp_maps_members_map_print_scripts() {
	global $bp;
	
	if (!bp_maps_members_map_is_enabled()) return false;

	if (!bp_maps_members_map_is_map_page()) return false;

	bp_maps_head_init();
	
}

function bp_maps_members_directory_link() {

	if (!bp_maps_members_map_is_enabled()) return false;
	
	if (!bp_maps_members_map_is_map_page())return false;


	?>
	<a class="button" href="<?php echo site_url() . '/' . BP_MEMBERS_SLUG ?>"><?php _e( 'Members Directory', 'buddypress' ) ?></a>
	<?php
}

function bp_maps_members_map_link() {

	if (!bp_maps_members_map_is_enabled()) return false;

	?>
	<a class="button" href="<?php echo bp_get_root_domain() . '/' . BP_MAPS_SLUG . '/'.BP_MEMBERS_SLUG ?>"><?php _e( 'Members Map', 'bp-maps' ) ?></a>
	<?php
}


function bp_maps_profile_title($title,$marker) {
	$user_id = $marker->user_id;
	return bp_core_get_username($user_id);
}


function bp_maps_members_map_is_map_page(){
	global $bp;

	if (( $bp->current_component != BP_MAPS_SLUG ) || ( $bp->current_action!=BP_MEMBERS_SLUG) )return false;
	
	return true;
	
}

function bp_maps_members_map_screen() {
	global $bp;
	global $marker_args;
	
	if (!bp_maps_members_map_is_enabled()) return false;
	
	if (!bp_maps_members_map_is_map_page())return false;
	
	bp_maps_head_init();
	
	$members_custom_markers_ids = bp_maps_profile_get_members_markers_ids('custom');


	//THIS IS FOR FETCHING THE MARKERS
	$marker_args[] = array(
		'type' => 'member_profile',
		'name'=>__('Members Locations','bp-maps'),
		'enable_desc'=>false
	);
	
	$marker_args[] = array(
		'include_ids'=>$members_custom_markers_ids,
		'name'=>__('Members Markers','bp-maps')
	);

	//THOSE ARE THE MAP PARAMS
	$map_args = array(
		'title' => 'Members Map',
		'slug' =>	__('members_map','bp-maps-slugs'),
		'display'	=>'dynamic',
		'width'	=> '100%',
		'height' => '400',
		'groups_args'	=>$marker_args
	);

	
	$bp->maps->current_map = new Bp_Map($map_args,$marker_args);

	bp_core_load_template( apply_filters( 'bp_maps_template_plugin', 'maps/single/home' ) );


}

add_action('bp_before_directory_members_content','bp_maps_members_map_link');
add_action('bp_map_header_actions','bp_maps_members_directory_link');
add_action('wp_print_scripts','bp_maps_members_map_print_scripts');
add_action( 'plugins_loaded','bp_maps_members_map_screen');

///SITEWIDE MAP|END

?>