<?php
//TO FIX
//bp_maps_profile_batch_locate_format_notifications | notification do not show

function bp_maps_profile_batch_locate_array_trim($a){
	$j = 0;
	$b = null;
	for($i = 0; $i < count($a); $i++){
		if($a[$i] != ""){
			$b[$j++] = trim($a[$i]);
		}
	}
	return $b;
}

function bp_maps_profile_batch_locate_format_adress_for_url($address_array) {

	$address_array = bp_maps_profile_batch_locate_array_trim($address_array);


	foreach ($address_array as $key=>$string) {
		$string = strip_tags($string);
		$string = htmlentities($string);
		$string_arr=explode(' ',$string);
		$string_arr=bp_maps_profile_batch_locate_array_trim($string_arr);

		$string_new=implode('+',$string_arr);
		$address_array[$key]=$string_new;
	
	}

	$address = implode(',',$address_array);

	
	return $address;


}

function bp_maps_profile_batch_locate_adress_to_location($address_array) {

	$address = bp_maps_profile_batch_locate_format_adress_for_url($address_array);

	$url = 'http://maps.google.com/maps/api/geocode/json?sensor=false&address='.$address;

	$geocode=file_get_contents($url);
	
	

	$output= json_decode($geocode);

	$lat = $output->results[0]->geometry->location->lat;
	$long = $output->results[0]->geometry->location->lng;
	
	if (($lat) && ($long))
		return array($lat,$long);
	
}

function bp_maps_profile_batch_locate_get_members($page=false) {


	$usersearch = isset($_GET['usersearch']) ? $_GET['usersearch'] : null;
	$userspage = isset($_GET['userspage']) ? $_GET['userspage'] : null;
	$role = isset($_GET['role']) ? $_GET['role'] : null;

	// Query the user IDs for this page
	$wp_user_search = new WP_User_Search($usersearch, $userspage, $role);
	return $wp_user_search->results;

}

function bp_maps_profile_batch_locate_get_non_located_members($members_ids,$with_address=false) {

	if (!$members_ids) return false;
	
	global $bp;
	$options=$bp->maps->options['plugins']['profiles_maps'];

	foreach ($members_ids as $user_id) {

		//check if the marker exists
		$marker_id = bp_maps_profile_get_members_markers_ids('member_profile','include='.$user_id);
		
		if ($with_address) {

			$address = bp_maps_profile_get_member_location($user_id,$options['fields_req']);
			if (!$address)
				continue;			
		}

		if (!$marker_id) {
			$fetch_members[]=$user_id;
		}

	}


	return $fetch_members;
}

function bp_maps_profile_locate_member($user_id=false,$name=false) {
	global $bp;
	
	$options = $bp->maps->options['plugins']['profiles_maps'];
	
	if (!$user_id)
		$user_id = $bp->displayed_user->id;

	global $bp_maps_profile_batch_locate_msgs;

	$messages['name'] .= '<strong>user #'.$user_id.'</strong> ';
	
	if ($name)
		$messages['name'] .='('.$name.') ';
	

	//check if the marker exists
	$marker_id = bp_maps_profile_get_members_markers_ids('member_profile','include='.$user_id);
	$marker_id = $marker_id[0];
	$profile_marker = new BP_Maps_Map_Marker($marker_id);

	//marker exists, skip user
	if ($marker_id) {
		$messages[] .='already has the marker #'.$marker_id.', <font color="red">skip user</font>';
		$result=true;
	} else {

		$address = bp_maps_profile_get_member_location($user_id,$options['fields_req']);
		
		if (!$address) {
			$messages[] .='no address, <font color="red">skip user</font>';
		}else {
		
			$address_arr = explode(' ',$address);
			$coords = bp_maps_profile_batch_locate_adress_to_location($address_arr);


			$messages['address'] .='looking for address <em>'.$address.'</em>';

			if (empty($coords)) {
				$messages['address'].=' ...not found';
				$messages[]='<font color="red">skip user</font>';
			}else {

				$messages['address'].=' ...<font color="green">found</font>';

				$point=new BP_Maps_Map_Marker();

				$point->user_id=$user_id;
				$point->Lat=$coords[0];
				$point->Lng=$coords[1];
				$point->address=$address;
				$point->type='member_profile';
				
				$marker_id=$point->save();

				if (!$marker_id) {
					$messages[].='<font color="red">error while trying to save marker</font>';
				}else {
					$messages[].='<font color="green">marker saved</font>';
					
					$xprofile=xprofile_set_field_data($options['map_field_id'],$user_id,__('Enable'));
					
					//only for batch locations
					if (is_admin()) {
						$notification_sent = bp_core_add_notification( $point->ID, $user_id, 'maps', 'profile_batch_marker_set'); // $item_id, $user_id, $component_name, $component_action, $secondary_item_id = false, $date_notified = false
						
						if ($notification_sent) {
							$messages[].='<font color="green">notification sent</font>';
							
						}
					}
					$result=true;
				}
			}
		}
	}

	$bp_maps_profile_batch_locate_msgs[$user_id]=$messages;

	if ($result) return $marker_id;
	
}

function bp_maps_profile_locate_members($members_ids,$required_fields=false) {
	global $bp_maps_profile_batch_locate_msgs;
	
	if (is_array($members_ids))
		$members_ids = implode(',',$members_ids);
		
	if (!$members_ids) return false;

	if ( bp_has_members( 'populate_extras=false&include='.$members_ids ) ) {
	
		while ( bp_members() ) : bp_the_member();

		$member_id=bp_get_member_user_id();

		$members[$member_id]=bp_get_member_name();

		endwhile;

	}

	$messages=array();
	
	if (!$members) return false;
	
	foreach ($members as $id=>$name) {


		if (!bp_maps_profile_locate_member($id,$name))
			$errors=true;

	}
	
	
	if (!$errors) return true;

}

function bp_maps_profile_batch_locate_div() {
	global $bp;
	global $bp_maps_profile_batch_locate_msgs;
	
	if (!bp_maps_profile_is_enabled()) return false;
	
	$members_step = 20;
	$members_locate_page=(int)$_POST['members_page'];
	


	if (($_POST['action']=='batch_locate') && (check_admin_referer( 'bp_maps_profile_batch_locate' ) ) ) {

		$total_members = bp_maps_profile_batch_locate_get_members();
		

		
		$non_located_members = bp_maps_profile_batch_locate_get_non_located_members($total_members);
		$non_located_members_with_address = bp_maps_profile_batch_locate_get_non_located_members($total_members,true);
		
		$process_members_start=(int)$members_locate_page*$members_step;
		$process_members_end=(int)($members_locate_page+1)*$members_step;

		$process_members = array_slice($non_located_members_with_address, $process_members_start, $process_members_end);
		
		bp_maps_profile_locate_members($process_members);
		
	}


	
	?>
	<h2><?php _e('Batch Locate Members','bp-maps');?></h2>
	<form action="#system" name="bp-maps-batch-locate" id="bp-maps-batch-locate" method="post">
		<p>
		<?php
		_e('If you have a large amount of members, you might want to set their locations markers automatically.','bp-maps');
		echo'<br>';
		_e('If an adress is found, the user will be notified that his marker has been created.','bp-maps');
		
		$total_members = bp_maps_profile_batch_locate_get_members();
		$non_located_members = bp_maps_profile_batch_locate_get_non_located_members($total_members);
		$non_located_members_with_address = bp_maps_profile_batch_locate_get_non_located_members($total_members,true);

		$total_members_count=count($total_members);
		$non_located_members_count=count($non_located_members);
		$located_members_count=$total_members_count-$non_located_members_count;
		$non_located_members_with_address_count=count($non_located_members_with_address);
		

		
		$users_start=$members_step*$members_locate_page;
		$users_end=($members_locate_page+1)*$members_step;
		
		if ($users_end>=$non_located_members_with_address_count)
			$users_end=$non_located_members_with_address_count;
			
			
		?><p><?php
		printf(__('You have %d members on this blog.  %d already have set a location marker.','bp-maps'),$total_members_count,$located_members_count);
		?><br/>
		<?php
		
		if ($located_members_count>0)
			$users_left=sprintf(__('Among the %d members left','bp-maps'),$non_located_members_count).', ';
		
		printf(__('%s%d have filled at least one location field from which a location marker can be set.','bp-maps'),$users_left,$non_located_members_with_address_count);
		?></p>
		<?php
		if ($users_start<$non_located_members_with_address_count) {?>

			<p id="submit-types" class="submit">
				<input type="hidden" name="action" value="batch_locate"/>
				<input type="hidden" name="members_page" value="<?php echo $members_locate_page;?>"/>
				<input class="button-primary" type="submit" name="submit" value="<?php printf(__('Batch Locate users %d to %d','bp-maps'),$users_start,$users_end); ?>"/>
			</p>
			<?php wp_nonce_field('bp_maps_profile_batch_locate') ?>
			
		<?php }else {
			echo"<strong>";
			_e('No more users to locate','bp-maps');
			echo"</strong>";
		}
		?>
	</form>
	<p>
	<?php
	
	if ($bp_maps_profile_batch_locate_msgs) {
	
		foreach ($bp_maps_profile_batch_locate_msgs as $message) {
			echo "<p>";
			$debug = implode('<br>',$message);
			echo $debug;
			echo "</p>";
		
		}
		
	}
	
	?>
	</p>
	<?php
}

//NOTIFICATIONS

function bp_maps_profile_batch_locate_format_notifications ($action, $item_id, $secondary_item_id, $total_items ) {
	global $bp;

	switch ( $action ) {
	
		case 'profile_batch_marker_set':

		$profile_edition_link=$bp->loggedin_user->domain . $bp->profile->slug.'/edit/group/'.$bp->maps->options['plugins']['profiles_maps']['group_id'];
		
		$link = '<a href="' . $profile_edition_link . '" title="' . __( 'Location Marker Set!', 'bp-maps' ) . '">' . __('A location marker has been set using your profile information.  Please check that its position is correct !  You can also edit or delete it.', 'bp-maps' ) . '</a>';

		return apply_filters( 'bp_maps_single_marker_set_notification',$link);

		break;
	}

	do_action( 'bp_maps_profile_batch_locate_format_notifications', $action, $item_id, $secondary_item_id, $total_items );
	
	return false;
}

function bp_maps_profile_batch_remove_notification() {
	
	if (!bp_maps_profile_is_enabled()) return false;
	
	global $bp;

	bp_core_delete_notifications_for_user_by_type( $bp->loggedin_user->id, $bp->maps->slug, 'marker_set' );

}

add_action('bp_maps_format_notifications','bp_maps_profile_batch_locate_format_notifications',10,4);
add_action('bp_maps_profile_edit_map_screen','bp_maps_profile_batch_remove_notification');
add_action('bp_maps_admin_system_div','bp_maps_profile_batch_locate_div');


?>