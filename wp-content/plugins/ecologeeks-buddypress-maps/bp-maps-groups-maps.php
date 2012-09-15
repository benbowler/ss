<?php

function bp_maps_groups_maps_are_enabled() {

	if ((bp_maps_groups_custom_markers_enabled()) || (bp_maps_groups_members_markers_enabled())) return true;
		
	return false;
}

function bp_maps_groups_custom_markers_enabled() {
	global $bp;

	if ($bp->maps->options['plugins']['groups_maps_custom']['enabled']) return true;
	return false;

}

function bp_maps_groups_members_markers_enabled() {
	global $bp;
	
	if ($bp->maps->options['plugins']['groups_maps_members']['enabled']) return true;
	return false;

}


function bp_maps_groups_setup_nav() {
	global $bp;

	if (!bp_maps_groups_maps_are_enabled()) return false;
	
	$group_link = $bp->root_domain . '/' . $bp->groups->slug . '/' . $bp->groups->current_group->slug . '/';
	
	//TO FIX : set right position
	
	bp_core_new_subnav_item( array( 'name' => __( 'Map', 'bp-maps' ), 'slug' => 'map', 'parent_url' => $group_link, 'parent_slug' => $bp->groups->slug, 'screen_function' => 'bp_maps_groups_screen_map', 'item_css_id' => 'map', 'position' => 80, 'user_has_access' => $bp->groups->current_group->user_has_access ) );
}

function bp_maps_groups_screen_map() {
	global $bp;

	if ( $bp->is_single_item )
		bp_core_load_template( apply_filters( 'bp_maps_groups_template_map', 'groups/single/plugins' ) );
}

function bp_is_group_map_page() {
	global $bp;

	if ( BP_GROUPS_SLUG == $bp->current_component && $bp->is_single_item && __('map','bp-maps-slugs') == $bp->current_action )
		return true;

	return false;
}


class Group_Map_Extension extends BP_Group_Extension {

	function group_map_extension() {
		global $bp;
		$this->name = __( 'Group Markers', 'bp-maps' );
		$this->slug = __( 'map', 'bp-maps-slugs' );
		$this->nav_item_name = __( 'Map', 'map' );

		$this->create_step_position = 36;
		$this->nav_item_position = 46;
	}

	function create_screen() {
		global $bp;

		if ( !bp_is_group_creation_step( $this->slug ) )
			return false;

		bp_maps_groups_edit_map();
		
		wp_nonce_field( 'groups_create_save_' . $this->slug );
	}


	function edit_screen() {
		global $bp;
		
		if ( !bp_is_group_admin_screen( $this->slug ) )
			return false; ?>

		<h2><?php echo attribute_escape( $this->name ) ?></h2>
		<?php

		bp_maps_groups_edit_map();
	}
	//needed but empty as ajax does the job
	function create_screen_save() {
	}
	function edit_screen_save() {
	}
	
	function display(){
	}
}
	

function bp_maps_group_get_members_markers_ids($group_id=false) {
	global $wpdb;
	global $bp;
	
	if (!$group_id)
		$group_id = $bp->groups->current_group->id;
	
	$maps_options = get_site_option( 'bp_maps_options');
	if (!$maps_options['plugins']['groups_maps_members']['enabled']) return false;
	
	
	if (!bp_group_has_members( 'group_id='.$group_id.'&exclude_admins_mods=0' ) ) return false;

	while ( bp_group_members() ) : bp_group_the_member();
	
		$users_ids[]= bp_get_group_member_id();
	
	endwhile;
	
	if (!$users_ids) return false;

	//THIS IS FOR FETCHING THE MARKERS
	$ids_str=implode(",",$users_ids);
	$query = $wpdb->prepare( "SELECT id FROM `{$bp->maps->table_name_markers}` mk WHERE user_id IN ({$ids_str}) AND type='member_profile'");
	$markers_ids = $wpdb->get_col($query );

	return $markers_ids;
}

function bp_maps_groups_edit_map() {
	global $bp;
	
	
	?>
	<div class="info" id="message">
		<?php if (bp_maps_groups_members_markers_enabled()) {?>
			<p><?php _e("Members markers are enabled for groups; wich means your group will have a tab to display the locations of the group's members.","bp-maps");?><br/>
			<?php _e("If you want, you can also add custom group markers with locations of interest for this group.","bp-maps");?>
			</p>
		<?php }else {?>
		<p><?php _e("If you want, you can add custom group markers with locations of interest for this group.","bp-maps");?></p>
		<?php }?>
	</div>
	<?php
	
	//THIS IS FOR FETCHING THE MARKERS
	$marker_args[] = array(
		'type' => 'group',
		'editable'	=>true,
		'secondary_id'	=> $bp->groups->current_group->id,
		'name'=>__('Group Markers','bp-maps'),
		'markers_max'=>20
	);
	
	//THOSE ARE THE MAP PARAMS
	$map_args = array(
		'groups_args'	=>$marker_args
	);
	
	$bp->maps->current_map = new Bp_Map($map_args);
	bp_maps_locate_template( array( 'maps/map.php' ), true );

}

function bp_maps_groups_display_map() {
	global $bp;

	$members_markers_ids = bp_maps_group_get_members_markers_ids();

	if (($members_markers_ids) || (bp_maps_groups_custom_markers_enabled())) : ?>

	<?php do_action( 'bp_before_group_members_map' ) ?>

	<?php 

	
	//THIS IS FOR FETCHING THE MARKERS
	if ($members_markers_ids) {
		$marker_args[] = array(
			'include_ids' => $members_markers_ids,
			'name'=>__('Members Locations','bp-maps')
		);
	}
	if (bp_maps_groups_custom_markers_enabled()) {
		$marker_args[] = array(
			'include_ids' => $members_markers_ids,
			'name'=>__('Group Markers','bp-maps')
		);
	}

	//THIS IS THE MAP PARAMS
	$map_args = array(
		'display'=>'dynamic',
		'width'=>'100%',
		'height'=>'500',
		'groups_args'	=>$marker_args
	);
	
	$bp->maps->current_map = new Bp_Map($map_args);
	bp_maps_locate_template( array( 'maps/map.php' ), true );
	
	
	?>
	
	<?php do_action( 'bp_after_group_members_map' ) ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( 'This group has no map.', 'bp-maps' ); ?></p>
	</div>

<?php endif;
}

function bp_maps_groups_is_admin_screen() {
	global $bp;

	if ($bp->current_component != BP_GROUPS_SLUG) return false;
	
	//edition
	if (($bp->current_action == 'admin') && ($bp->action_variables[0]==__( 'map', 'bp-maps-slugs' ))) return true;
	
	//creation
	if (($bp->current_action == 'create') && ($bp->action_variables[0]=='step') && ($bp->action_variables[1]==__( 'map', 'bp-maps-slugs' ))) return true;

	return false;

	
}

function bp_maps_groups_custom_markers_init() {
	global $bp;
	
	if (!class_exists('Bp_Map')) return false;

	if (!bp_maps_groups_custom_markers_enabled()) return false;

	bp_register_group_extension( 'Group_Map_Extension' );
	
	//TO FIX STATEMENTS
	
	if (bp_maps_groups_is_admin_screen()) { //creation or edition screen
		bp_maps_head_init();
	}
	
}



function bp_maps_groups_init() {

	if (!bp_maps_groups_maps_are_enabled()) return false;

	if (!bp_is_group_map_page()) return false;
	//INIT MAPS JS
	bp_maps_head_init();
	//
	
	add_action( 'bp_template_content', 'bp_maps_groups_display_map' );

}

add_action( 'bp_init','bp_maps_groups_init');
add_action( 'bp_init', 'bp_maps_groups_custom_markers_init');
add_action( 'bp_setup_nav', 'bp_maps_groups_setup_nav' );

?>