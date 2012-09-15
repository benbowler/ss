<?php

require_once ( BP_MAPS_PLUGIN_DIR . '/bp-maps-profile-batch-locate.php' );

//TO FIX :
//when displaying a user's map (not edition screen); the map do not appear if none of the group fields have been filled.
//delete member markers @ classified delete
//escape chars for bp_get_activity_latest_update?






function bp_maps_profile_get_member_location($member_id,$required_fields=false) {
	global $bp;
	
	$options = $bp->maps->options['plugins']['profiles_maps'];

	if (!$member_id) return false;
	if (!$options['fields']) return false;
	
	//check if user allows geolocation

	$geolocate = bp_maps_profile_geolocation_is_active($member_id);

	if (!$geolocate) return false;

	$row=array();
	
	foreach($options['fields'] as $field_id) {
		
		unset($data);
		
		$data = trim(xprofile_get_field_data($field_id,$member_id));

		if (($required_fields) && (in_array($field_id,$required_fields)) && (!$data)) return false;

		$row[]=$data;

	}

	$address = implode(' ',$row);
	
	return trim($address);

}


function bp_maps_profile_get_members_markers_ids($type,$members_args=false) {
	global $bp;
	global $wpdb;
	
	if ( bp_has_members( $members_args ) ) {
	
		while ( bp_members() ) : bp_the_member();

		$members_ids[]=bp_get_member_user_id();

		endwhile;

	}

	if (!$members_ids) return false;

	//THIS IS FOR FETCHING THE MARKERS
	$ids_str=implode(",",$members_ids);
	$query = $wpdb->prepare( "SELECT id FROM `{$bp->maps->table_name_markers}` mk WHERE user_id IN ({$ids_str}) AND type='{$type}'");
	$markers_ids = $wpdb->get_col($query );

	return $markers_ids;
}

///ADMIN///

function bp_maps_profile_create_groupdata($group_id=false,$map_field_id=false) {
	global $bp;
	global $wpdb;
	
	//CREATE LOCATION GROUP
	if ($group_id=='new') {
		
		unset($group_id);
		
		$location = new BP_XProfile_Group();
		$location->name=__('Location','bp-maps');
		
		$group_id = $location->save();

		$groups_count=BP_Groups_Group::get_total_group_count()-1;
		//BP_XProfile_Group::update_position( $options['group_id'], $groups_count);
		
		if (!$group_id){
			$errors->add('bp_maps_profile_group_creation',__( 'Error while trying to create the location group', 'bp-maps' ));
			return false;
		}		
		
		//LOCATION INFO FIELDS
		
		$textfields=array(
			__('Street','bp-maps'),
			__('N°','bp-maps'),
			__('Box','bp-maps'),
			__('City','bp-maps'),
			__('Code','bp-maps'),
			__('State','bp-maps'),
			__('Country','bp-maps')
		);
		
		$field_key=0;
		
		foreach ($textfields as $textfield_name) {
			$field_key++;
			$textfield = new BP_XProfile_Field();
			$textfield->group_id=$group_id;
			$textfield->type='textbox';
			$textfield->name=$textfield_name;
			$textfield->field_order=$field_key;

			$textfield->id = $textfield->save();
			$_options['fields'][]=$textfield->id;
		}
	}
	
	//CREATE GEOLOCATION RADIO FIELD
	if ($map_field_id=='new') {
	
		if (!$field_key) {//no field order
			$groups_args = array(
				'fetch_fields'=>true,
				'profile_group_id'=>$group_id
			);
			
			// Buzug : la méthode ci-dessous plante sur WP 2.8.2
			$group = BP_XProfile_Group::get($groups_args);
			$field_key=count($group[0]->fields);
		}
		
		unset($map_field_id);
		
		$field_key++;
		$radio = new BP_XProfile_Field();
		$radio->group_id=$group_id;
		$radio->type='radio';
		$radio->name=__('Show your address on a map ?','bp-maps');
		$radio->field_order=$field_key;

		$map_field_id = $radio->save();

		//CREATE GEOLOCATION OPTIONS
		if ($map_field_id) {
			
			$radio_options=array(__('Enable'),__('Disable'));

			
			$is_default_option=true;
			foreach ($radio_options as $key=>$name) {
				$parent_id=$map_field_id;
				$type='option';
				$order_by='default';
				$option_order=$key+1;
				
				if ($option_order==1) {
					$is_default_option=true;
				}else {
					unset($is_default_option);
				}
				
				
				//TO FIX do not hardcode it but there is problem with the core code
				$sql = $wpdb->prepare("INSERT INTO {$bp->profile->table_name_fields} (group_id,parent_id,type,name,is_default_option,option_order,order_by) VALUES (%d,%d,%s,%s,%d,%d,%s)",$group_id,$parent_id,$type,$name,$is_default_option,$option_order,$order_by);
				$wpdb->query( $sql );
				$is_default_option=false;
			}

		
		}else {
			$errors->add('bp_maps_profile_field_creation',__( 'Error while trying to create the map field', 'bp-maps' ));
			return false;
		}
	}	
	return array('group_id'=>$group_id,'map_field_id'=>$map_field_id);
}

function bp_maps_profile_is_enabled() {
	global $bp;

	//TO FIX : do not work with this (HOOK PRIORITY PROBLEM)
	//if (!$bp->maps->options['plugins']['profiles_maps']) return false;
	
	$maps_options = get_site_option( 'bp_maps_options');
	if (!$maps_options['plugins']['profiles_maps']) return false; //plugin's options
	if (!$maps_options['plugins']['profiles_maps']['enabled']) return false;
	if (!bp_maps_profile_is_setup()) return false;
		
	return true;
}


function bp_maps_profile_is_setup() {
	global $bp;
	$options=$bp->maps->options['plugins']['profiles_maps'];
	
	if (!bp_maps_profile_get_group_id()) return false;
	
	if (!$options['map_field_id']) return false;
	
	if (!$options['fields']) return false;
	
	if (!$options['fields_req']) return false;

	return true;
	
	
	
}

function bp_maps_profile_get_group_id() {
	global $bp;
	
	$options=$bp->maps->options['plugins']['profiles_maps'];
	
	if (!$options['group_id']) return false;
	
	$group=new BP_XProfile_Group($options['group_id']);

	return $group->id;

}

function bp_maps_profile_admin_screen_save() {
	global $bp;
	global $errors;
	
	$options=$bp->maps->options;

		
	if (!$errors)
		$errors = new WP_Error();


	switch ( $_POST['action'] ) {

		case 'bp-maps-profile':
		
		check_admin_referer('maps-plugins-profile');
		
		//profile maps
		$_options['plugins']['profiles_maps']['enabled']=$_POST['bp_maps_plugins_profiles'];
		//members map
		$_options['plugins']['members_map']['enabled']=$_POST['bp_maps_plugins_members'];
		//friends map
		$_options['plugins']['friends_map']['enabled']=$_POST['bp_maps_plugins_friends'];
		//group maps
		$_options['plugins']['groups_maps_members']['enabled']=$_POST['bp_maps_plugins_groups_maps_members'];
		
		$_options['plugins']['profiles_maps']['group_id']=$_POST['bp_maps_profile_group_id'];
		$_options['plugins']['profiles_maps']['map_field_id']=$_POST['bp_maps_profile_map_field_id'];

		$group_datas = bp_maps_profile_create_groupdata($_options['plugins']['profiles_maps']['group_id'],$_options['plugins']['profiles_maps']['map_field_id']);

		$_options['plugins']['profiles_maps']['group_id']=$group_datas['group_id'];
		$_options['plugins']['profiles_maps']['map_field_id']=$group_datas['map_field_id'];

		if ($_POST['bp_maps_profile_fields'])
			$_options['plugins']['profiles_maps']['fields']=$_POST['bp_maps_profile_fields'];
			
		if ($_POST['bp_maps_profile_fields_req'])
			$_options['plugins']['profiles_maps']['fields_req']=$_POST['bp_maps_profile_fields_req'];

		
		break;
	}

	
	$options = wp_parse_args( $_options, $options );
	
	if (update_site_option( 'bp_maps_options', $options )) {
		$message= __( 'Settings saved.', 'buddypress' );
		$type='updated';
		$bp->maps->options=$options;
	}


}


function bp_maps_profile_admin_plugins_div() {
	global $bp;

	$options=$bp->maps->options;
	?>


	<div id="profile-map">
		<?php 
		
		//GET GROUP&FIELDS
		$groups_args = array(
			'user_id'=>false,
			'fetch_fields'=>false,
		);

		if ($options['plugins']['profiles_maps']['group_id']) $groups_args['fetch_fields']=true;
		$groups = BP_XProfile_Group::get($groups_args);

		?>
		<?php if ( isset( $message ) ) {;?>
			<div id="message" class="<?php //TO FIX : hidden when $type is uncommented //echo $type ?> fade">
				<p><?php echo $message ?></p>
			</div>
		<?php } ?>
		<form action="#plugins" name="bp-maps-profile-options" id="bp-maps-profile-options" method="post">	
			<h3><?php _e('Profiles maps', 'bp-maps') ;?></h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="bp_maps_plugins_profiles"><?php _e('Enable Profiles Maps', 'bp-maps') ?></label></th>
					<td>
					<?php
					if (bp_maps_profile_is_enabled()) $profiles_maps_checked=" CHECKED";
					if (!bp_maps_profile_is_setup()) $profiles_maps_disabled=" DISABLED";
					echo'<input type="checkbox" value="1" name="bp_maps_plugins_profiles"'.$profiles_maps_checked.$profiles_maps_disabled.'>';
					?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="bp_maps_plugins_members"><?php _e('Enable Members Map', 'bp-maps') ?></label></th>
					<td>
					<?php
					
					 if (!bp_maps_profile_is_enabled())
						$bp_maps_profile_disabled=" DISABLED";

					if ($options['plugins']['members_map']['enabled']) $members_map_checked=" CHECKED";
					echo'<input type="checkbox" value="1" name="bp_maps_plugins_members"'.$members_map_checked.$bp_maps_profile_disabled.'>';

					?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="bp_maps_plugins_friends"><?php _e('Enable Friends Map', 'bp-maps') ?></label></th>
					<td>
					<?php

					if ($options['plugins']['friends_map']['enabled']) $friends_map_checked=" CHECKED";
					echo'<input type="checkbox" value="1" name="bp_maps_plugins_friends"'.$friends_map_checked.$bp_maps_profile_disabled.'>';

					?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="bp_maps_plugins_groups"><?php _e('Groups Maps', 'bp-maps') ?></label></th>
					<td>
					<?php
					if ($options['plugins']['groups_maps_members']['enabled']) $groups_maps_members_checked=" CHECKED";
					?><input type="checkbox" value="1" name="bp_maps_plugins_groups_maps_members"<?php echo $groups_maps_members_checked.$bp_maps_profile_disabled;?>>
					<?php _e('Include members location on the groups maps','bp-maps');?>
					</td>
				</tr>
			</table>
			<h4><?php _e('Install') ;?></h4>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="bp_maps_profile_install"><?php _e('Location Group', 'bp-maps') ?></label></th>
					<td>
						<input class="required" type="radio" value="new" name="bp_maps_profile_group_id"<?php if (!$options['plugins']['profiles_maps']['group_id'])echo" CHECKED";?>><strong><?php _e('Create New','bp-maps');?></strong>
						<?php
						foreach ($groups as $group) {
							if ($group->id==$options['plugins']['profiles_maps']['group_id']) $checked=" CHECKED";
							
							echo'<input class="required" type="radio" value="'.$group->id.'" name="bp_maps_profile_group_id"'.$checked.'>'.$group->name;
							
							unset($checked);
						}
						?>
					</td>
				</tr>
				<?php if (bp_maps_profile_get_group_id()) {

				?>
					<tr valign="top">
						<th scope="row"><label for="bp_maps_profile_install"><?php _e('Location Marker Field', 'bp-maps') ?></label></th>
						<td>
							<input class="required" type="checkbox" value="new" name="bp_maps_profile_map_field_id"<?php if (!$options['plugins']['profiles_maps']['map_field_id'])echo" CHECKED";?>><strong><?php _e('Create New','bp-maps');?></strong>
							<?php
							foreach ($groups as $group) {

								echo "<div>";
								if ($group->id!=$options['plugins']['profiles_maps']['group_id']) continue;

								if (!$group->fields) continue;

								foreach ($group->fields as $field) {

									if ($field->parent_id) continue;
									
									if ($field->type!='radio') continue;
									
									if ($field->id==$options['plugins']['profiles_maps']['map_field_id']) $is_checked=true;

									?>
									<input type="radio" value="<?php echo $field->id;?>" name="bp_maps_profile_map_field_id"<?php if ($is_checked) echo" CHECKED";?>>
									
									<?php echo $field->name;
									
									unset($is_checked);

								}
								
								echo "<div>";
							}
							?>


						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="bp_maps_profile_fields"><?php _e('Fields used to geocode location', 'bp-maps') ?></label></th>
						<td>
						<?php
						foreach ($groups as $group) {
						
							
						
							echo "<div>";
							if ($group->id!=$options['plugins']['profiles_maps']['group_id']) continue;
							

							if (!$group->fields) continue;
							

							foreach ($group->fields as $field) {
							
								if ($field->id==$options['plugins']['profiles_maps']['map_field_id']) continue;
								
								if (($options['plugins']['profiles_maps']['map_field_id']) && ($field->parent_id==$options['plugins']['profiles_maps']['map_field_id'])) continue;

								if ($options['plugins']['profiles_maps']['fields']) {
									if (in_array($field->id,$options['plugins']['profiles_maps']['fields'])) $is_checked=true;
								}else {
									$is_checked=true;
								}
								?>
								<input type="checkbox" value="<?php echo $field->id;?>" name="bp_maps_profile_fields[]"<?php if ($is_checked) echo" CHECKED";?>>
								
								<?php echo $field->name;
								
								unset($is_checked);

							}
							
							echo "<div>";
						}
						?>
						<small><?php _e('You may want to uncheck the box, the zipcode...','bp-maps');?></small>
						</td>
					</tr>
					<?php if ($options['plugins']['profiles_maps']['fields']) {?>
						<tr valign="top">
							<th scope="row"><label for="bp_maps_profile_fields"><?php _e('Fields required for auto/batch geocode address', 'bp-maps') ?></label></th>
							<td>
							<?php
							foreach ($groups as $group) {
							
								
							
								echo "<div>";
								if ($group->id!=$options['plugins']['profiles_maps']['group_id']) continue;
								

								if (!$group->fields) continue;
								

								foreach ($group->fields as $field) {

									if (!in_array($field->id,$options['plugins']['profiles_maps']['fields'])) continue;

									if ($options['plugins']['profiles_maps']['fields_req']) {
										if (in_array($field->id,$options['plugins']['profiles_maps']['fields_req'])) $is_checked=true;
									}elseif($field->is_required) {
										$is_checked=true;
									}
									?>
									<input type="checkbox" value="<?php echo $field->id;?>" name="bp_maps_profile_fields_req[]"<?php if ($is_checked) echo" CHECKED";?>>
									
									<?php echo $field->name;
									
									unset($is_checked);

								}
								
								echo "<div>";
							}
							?>
							<small><?php _e('The minimum fields required to geocode addresses - eg. city & country','bp-maps');?></small>
							</td>
						</tr>
					<?php }
				}?>
			</table>
			<p id="bp-maps-profile-submit" class="submit">
				<input type="hidden" name="action" value="bp-maps-profile" />
				<input class="button-primary" type="submit" name="submit" value="<?php _e('Save Settings', 'buddypress'); ?>"/>
			</p>
			<?php wp_nonce_field('maps-plugins-profile') ?>
		</form>
	</div>
	<?php
}


///ADMIN | END ///

function bp_maps_profile_edit_no_marker() {

	if (!bp_maps_profile_geolocation_is_active()) return false;

	$marker_id = bp_maps_profile_locate_member();

	if (!$marker_id)
		bp_core_add_message( __( 'Your location was not found.  Please try again by pinning your marker manually.','bp-maps' ), 'error' );

}

function bp_maps_profile_edit_field($value) {
	global $field;
	global $bp;
	
	$options = $bp->maps->options['plugins']['profiles_maps'];

	if ($field->id!=$options['map_field_id']) return $value;
	if (!bp_maps_profile_geolocation_is_active($member_id)) return $value;

	//SHOW MAPS
	echo'<label>'.__('Map Location','bp-maps').'</label>';
	bp_maps_profile_display_user_map(true);
}

function bp_maps_profile_get_the_profile_field_value($value) {
	global $field;
	global $bp;
	
	$options = $bp->maps->options['plugins']['profiles_maps'];

	if ($field->id!=$options['map_field_id']) return $value;
	if (!bp_maps_profile_geolocation_is_active($member_id)) {
		echo'<em>'.__('No marker set','bp-maps').'</em>';
		return false;
	}

	bp_maps_profile_display_user_map();
}

function bp_maps_profile_display_user_map($editable=false,$user_id=false) {
	global $bp;
	
	if (!$user_id);
		$user_id=$bp->displayed_user->id;
	
	//THIS IS FOR FETCHING THE MARKERS
	$marker_args[] = array(
		'type' => 'member_profile',
		'editable'	=>$editable,
		'user_id'	=> $user_id,
		'name'=>__('Profile Marker','bp-maps'),
		'markers_list'	=>false,
		'enable_desc' =>false
	);
	
	//THOSE ARE THE MAP PARAMS
	$map_args = array(
		'groups_args'	=>$marker_args,
		'slug' => 'profile'
	);

	$bp->maps->current_map = new Bp_Map($map_args);
	
	bp_maps_locate_template( array( 'maps/map.php' ), true );
	
}


function bp_maps_profile_add_map_js($js,$map) {
	global $bp;

	$options=$bp->maps->options['plugins']['profiles_maps'];

	if ($options['fields']) {
		$js[]="var map_profile_fields=new Array(".implode(',',$options['fields']).");";
	}
	$js[]="var map_profile_checkbox=jQuery('input[name=\"field_".$options['map_field_id']."\"]');";
	$js[]="var map_profile_confirm_msg='".__('Do you want to update the location marker with the location fields data ?','bp-maps')."';";
	
	return $js;
}


///DISPLAY USER'S INFORMATION AS INFOWINDOW
function bp_maps_profile_marker_title($title,$marker) {

	if ($marker->type!='member_profile') return $title;
	
	$user_id = $marker->user_id;
	return bp_core_get_username($user_id);
}

function bp_maps_profile_infobulle_content($content,$marker) {

	if ($marker->type!='member_profile') return $content;

	$user_id = $marker->user_id;

	$content=bp_core_fetch_avatar( array( 'item_id' => $user_id) );
	
	$content.='<h3 class="fn"><a href="'.bp_core_get_user_domain($user_id).'">'.bp_core_get_username($user_id).'</a> <span class="highlight">@'.bp_core_get_username($user_id,true).'</span></h3>';
	
	
	$content.='<span class="activity">'.bp_last_activity( $user_id,false).'</span>';
	
	//TO FIX
	if ( function_exists( 'bp_activity_latest_update' ) ) : 
		$content.= '<div id="latest-update">'.bp_get_activity_latest_update( $user_id ).'</div>';
	endif;
	
	return $content;
	
}



function bp_maps_profile_print_scripts($js) {
	global $bp;
	
	if (empty($bp->maps->options['plugins']['profiles_maps']['fields'])) return false;

	//fields
	wp_enqueue_script('bp-maps-profile',BP_MAPS_PLUGIN_URL . '/_inc/js/bp-maps-profile.js', array('jquery'),BP_MAPS_VERSION);
}



function bp_maps_profile_is_map_group() {
	if (!bp_is_user_profile()) return false;
	
	global $bp;

	if ($bp->action_variables[1]==$bp->maps->options['plugins']['profiles_maps']['group_id']) 
		return true;
		
	return false;
}

function bp_maps_profile_field_content() {

	global $profile_template;
	global $bp;
	
	$group_id =$bp->maps->options['plugins']['profiles_maps']['group_id'];
	$map_field = $bp->maps->options['plugins']['profiles_maps']['map_field_id'];

	$groups = $profile_template->groups;
	

	//TO FIX : $profile_template->current_group should be $group_id ?
	
	foreach ($groups as $group) {
		if ($group->id!=$group_id) continue;
		
		if (!$group->fields) continue;

		foreach ($group->fields as $field) {
			if ($field->id!=$map_field) continue;


			$data=serialize(
				array(
					__('Geolocate this address','bp-maps')
				)
			);
			
			$field->data->value=$data;

		}

	}
	
	$profile_template->groups=$groups;


	
}

//check checkbox value and update marker (add/delete);
function bp_maps_profile_updated_profile($user_id,$posted_fields_ids,$errors) {
	global $bp;
	
//	return false;

	$map_field=$bp->maps->options['plugins']['profiles_maps']['map_field_id'];
	$geolocate = bp_maps_profile_geolocation_is_active($user_id);

	$user_id=$bp->displayed_user->id;
	$marker_id = bp_maps_profile_locate_member();

	//checkbox unchecked
	if (!$geolocate) {

		//delete marker
		if ($marker_id) {
			$marker = new BP_Maps_Map_Marker($marker_id);
			$marker->delete();
		}
	}else {
		if (!$marker_id) {
			bp_core_add_message( __( 'Your location was not found.  Please try again by pinning your marker manually.','bp-maps' ), 'error' );
			bp_core_redirect( $bp->displayed_user->domain . BP_XPROFILE_SLUG . '/edit/group/' . $bp->action_variables[1] . '/' );
		}
	}

}


function bp_maps_profile_init() {
	global $bp;
	
		//backend options
		add_action( 'bp_maps_admin_plugins_div','bp_maps_profile_admin_plugins_div');
	
	if (!bp_maps_profile_is_enabled()) return false;
	

	
		//change infobulle content to set user's info
		add_filter('bp_maps_marker_infobulle_content','bp_maps_profile_infobulle_content',10,2);
		//add_filter('bp_maps_get_markerslist_marker_content','bp_maps_profile_marker_content',10,2);
		
		//change infobulle content to set user's info
		add_filter('bp_maps_get_marker_title','bp_maps_profile_marker_title',10,2);
		add_filter('bp_maps_get_marker_list_title','bp_maps_profile_marker_title',10,2);


	if ((bp_is_profile_edit()) && (bp_maps_profile_is_map_group())) {//edition
		add_action('wp_print_scripts','bp_maps_profile_print_scripts'); //edition scripts (address autocomplete)
		add_filter('bp_maps_get_map_js','bp_maps_profile_add_map_js',10,2); //array to contain the field ids
		add_action('wp','bp_maps_profile_edit_no_marker');
		
		//TO FIX : useless ?
		//add_filter('bp_maps_get_new_marker_address','bp_maps_profile_get_new_marker_address');
		
		do_action('bp_maps_profile_edit_map_screen');
		
	}
	


	if (
	((bp_is_profile_edit()) && (bp_maps_profile_is_map_group())) || //profile edition
	((bp_is_user_profile()) && (!bp_is_profile_edit())) //profile display
	) {

		bp_maps_head_init(); //load maps JS

		
	}

}

function bp_maps_profile_geolocation_is_active($user_id=false) {
	global $bp;
	
	$map_field_id = $bp->maps->options['plugins']['profiles_maps']['map_field_id'];

	if (!$user_id)
		$user_id=$bp->displayed_user->id;

	$data=xprofile_get_field_data($map_field_id,$user_id);

	if ($data!=__('Disable')) return true;
	
	return false;

}
function bp_maps_profile_marker_icon() {
	echo bp_maps_profile_get_marker_icon();
}

function bp_maps_profile_get_marker_icon() {
	
	if (!bp_maps_profile_is_enabled()) return false;
	
	global $members_template;
	global $bp;
	$member_id=$members_template->member->id;
	
	
	

	if (!bp_maps_profile_geolocation_is_active($member_id)) return false;
	
	$marker_id = BP_Maps_Map_Marker::get_markers( false, false, $member_id, false, 'member_profile');
	
	$marker_id = $marker_id['markers'][0];

	if (!$marker_id) return false;

	$icon = apply_filters('bp_maps_enqueue_url',get_stylesheet_directory_uri() . '/maps/_inc/images/marker.png');
	$link = bp_get_member_permalink().$bp->profile->slug;
	
	return apply_filters('bp_maps_profile_get_marker_icon','<a href="'.$link.'"><img src="'.$icon.'"></a>');
}

add_action( 'bp_maps_init', 'bp_maps_profile_init');
add_action('bp_custom_profile_edit_fields','bp_maps_profile_edit_field'); //profile display (EDIT)
add_action('xprofile_updated_profile','bp_maps_profile_updated_profile',10,3);
add_action('bp_maps_admin_screen_save','bp_maps_profile_admin_screen_save');
add_action( 'bp_directory_members_item','bp_maps_profile_marker_icon');

add_filter('bp_get_the_profile_field_value','bp_maps_profile_get_the_profile_field_value'); //profile display



?>
