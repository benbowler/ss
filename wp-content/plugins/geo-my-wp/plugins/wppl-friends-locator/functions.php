<?php
//////////////////////////////////////////////////////////////////////////////////////////
/// SOME OF THE CODE IN THIS PAGE WAS INSPIRED BY THE CODE WRITTEN BY ANDREA TARANTI ////
/// 				THE CREATOR OF BP PROFILE SEARCH -- THANK YOU 					////
///////////////////////////////////////////////////////////////////////////////////////

global $mmc, $member_info, $map_types;
$member_info = 0;
$mmc = 0;
$map_types = array();

///// MEMBER'S LOCATION MAP SHORTCODE ////////
function wppl_member_location($member) {
	wp_enqueue_style('wppl-bp-style');
	extract(shortcode_atts(array(
		'directions' 	=> '1',
		'map_height' 	=> '250',
		'map_width' 	=> '250',
		'map_type' 		=> 'ROADMAP',
		'zoom_level' 	=> 13,
		'address' 		=> 1,
		'no_location' 	=> 0	
	),$member));
	
    global $bp, $wpdb, $mmc, $member_info, $map_types;
    $mmc++;
    if(!$member_info != 0) {
    	$sql ="
		SELECT * 
			FROM " . $wpdb->prefix . "wppl_friends_locator  	
    		WHERE 
    		(1 = 1)
    		AND member_id =". $bp->displayed_user->id. "
			ORDER BY member_id" ;
			
		$member_info = $wpdb->get_results($sql, ARRAY_A);
	}
	if ($member_info && $member_info !=0) {
   		$get_loc = array(
    		$member_info[0]['lat'],
    		$member_info[0]['long'],
    		$member_info[0]['address']
    	);
    	
    	$map_types[$mmc] = $map_type;
    	echo '<script type="text/javascript">'; 
		echo 	'singleLocation= '.json_encode($get_loc),';'; 
		echo 	'memberMapType= '.json_encode($map_types),';'; 
		echo 	'mapId= '.json_encode($mmc),';'; 
		echo  '</script>';
		wp_enqueue_script( 'wppl-member-map', true); ?>
		
    	<?php	
    	$member_map .='';	
    	$member_map .=	'<div class="wppl-single-member-wrapper">';
    	if($address == 1) {
    		$member_map .=		'<div class="wppl-single-member-address"><span>Address: </span>' .$member_info[0]['address'].'</div>';
		}
		$member_map .=		'<div class="wppl-single-member-map-wrapper" style="position:relative">';
		$member_map .=			'<div id="member-map-'.$mmc.'" style="width:'.$map_width.'px; height:'.$map_height.'px;"></div>';
		$member_map .= 			'<div class="wppl-single-member-direction-wrapper" style="width:'.($map_width - 10).'px">';
    	$member_map .= 				'<div class="wppl-single-member-direction" style="display:none;">';
		$member_map .=					'<form action="http://maps.google.com/maps" method="get" target="_blank">';
		$member_map .= 						'<input type="text" name="saddr" />';
		$member_map .= 						'<input type="hidden" name="daddr" value="'. $member_info[0]['address'].'" /><br />';
		$member_map .= 						'<input type="submit" class="button" value="GO" />';
		$member_map .= 					'</form>'; 
		$member_map .= 				'</div>';
		if ($directions == 1) {
    		$member_map .= 				'<span><a href="#" class="show-directions">Get Directions</a></span>';
    	}
    	$member_map .= 			'</div>';
    	$member_map .=		'</div>';// map wrapper //
    	$member_map .=	'</div>';// map wrapper //
    
    	echo $member_map;
	} else {
		if ($no_location == 1) echo $bp->displayed_user->fullname . ' has not added a location yet.';
	}
}
add_shortcode( 'wppl_member_location' , 'wppl_member_location' );

//// paginations for buddypress  /////////////
function bp_pagination_links($pages, $per_page) {
	$page_n = $_GET['pagen'];
	if ($pages > 1) {
		
		if ($page_n > 0) {		
			echo '<a href="' .add_query_arg('pagen', ($page_n - 1)) .'" class="prev page-numbers" onclick="document.wppl_form.submit();">&#171;</a>';
		} 		
		
		for($i=0; $i< $pages; $i++): 
			$num = $i + 1;
    		echo '<a href="' .add_query_arg('pagen',$i) .'" '; echo ( $page_n+1 == $num ) ? 'class="page-numbers current"' : 'class="page-numbers"'; echo ' onclick="document.wppl_form.submit();">' . $num .  '</a>';
		endfor; 
		
		if ($page_n < ($pages -1)) {
			echo '<a href="' .add_query_arg('pagen', ($page_n + 1)) .'" class="next page-numbers" onclick="document.wppl_form.submit();">&#187;</a>';
		}			
	}
}	

///// fields dropdown for search form /////////
function bp_fields_dropdown($wppl) {
	global $bp;
	 
	 //// check buddypress fields ////
    $total_fields = ($wppl['profile_fields'] ) ? $wppl['profile_fields'] : array();
	if($wppl['profile_fields_date'] ) array_unshift($total_fields, $wppl['profile_fields_date']);
  	
	echo '<div class="wppl-checkbox-category-wrapper">';
		
	foreach ($total_fields as $field_id) {
		
		$field_data = new BP_XProfile_Field ($field_id);
		$children = $field_data->get_children ();
	
		switch ($field_data->type) {
			case 'datebox':
				echo "<div class='date'>";
					echo '<label class="wppl-category-id">Age Range (min - max)</label><br />';
					echo '<input size="3" type="text" name="'. $field_id.'" value="' . $_GET[$field_id] . '" placeholder="Min" />';
					echo '&nbsp;-&nbsp;';
					echo '<input size="3" type="text" name="' . $field_id . '_to" value="' . $_GET[$field_id. '_to' ] . '" placeholder="Max" />';
				echo '</div>';
			break;
		
			case 'multiselectbox':
			case 'selectbox':
			case 'radio':
			case 'checkbox': 
				echo '<div class="wppl-category-checkbox">';
					echo '<label class="wppl-category-id">' . $field_data->name. '</label><br />';
					$tt = array();
					if($_GET[$field_id]) { 
						$tt = $_GET[$field_id];
					}
					echo '<div class="wppl-checkboxes-wrapper">';
					foreach ($children as $child) {	
						$child->name = trim ($child->name);
						$checked =  (in_array ($child->name, $tt ) )? "checked='checked'": ""; 
						echo '<div class="wppl-single-checkbox"><input ' .$checked . ' type="checkbox" name="'. $field_id. '[]" value="'.$child->name.'" /><span>'.$child->name.'</span></div>';
					}
					echo '</div>';
				echo '</div>';
			break;
		} // switch
		
	}
	echo '</div>';
}

///////// query profile fields //////////
function wppl_bp_query_fields ($total_fields) {
		global $bp;
		global $wpdb;
		global $and_users;
		$empty_fields = array();
			
		foreach ($total_fields as $field_id) {
			
			$value = $_GET[$field_id];
			$to = $_GET[$field_id. '_to'];
			
			if ($value) array_push($empty_fields, $_GET[$field_id]);
			
			if($value || $to) {
			
				$field_data = new BP_XProfile_Field ($field_id);
				
				switch ($field_data->type) {
				
					case 'select':
					case 'multiselectbox':
					case 'checkbox':
					case 'radio':
						$sql = "SELECT user_id from {$bp->profile->table_name_data}";
						$sql .= " WHERE field_id = $field_id ";
						$like = array ();
						foreach ($value as $curvalue)
							$like[] = "value = '$curvalue' OR value LIKE '%\"$curvalue\"%' ";
						$sql .= ' AND ('. implode (' OR ', $like). ')';	
					break;

					case 'datebox':
					
						$value = (!$value) ? '1': $value;
						$to = (!$to) ? '200' : $to;
						if ($to < $value)  $to = $value;
		
						$time = time ();
						$day = date ("j", $time);
						$month = date ("n", $time);
						$year = date ("Y", $time);
						$ymin = $year - $to - 1;
						$ymax = $year - $value;

						$sql = "SELECT user_id from {$bp->profile->table_name_data}";
						$sql .= " WHERE field_id = $field_id AND value > '$ymin-$month-$day' AND value <= '$ymax-$month-$day'";
					break;
				}//switch //	
		
				$results = $wpdb->get_col ($sql, 0);
				
				if (!is_array ($userids)) $userids = $results; else $userids = array_intersect ($userids, $results); 
									
				echo '<br />';
			} // if value //
		} // for eaech //
		
	if ($userids) return $and_users = "AND member_id IN (" . implode( ',' , $userids) . ")";
	else if (!empty($empty_fields)) return "buba";	
} 

/////// query buddypress results /////////
function wppl_bp_query_results ($params, $wppl, $wppl_options) {
	
	global $wpdb, $wppl, $and_users, $org_address, $unit_a, $pc, $total_fields, $total_results_fields, $lat, $long; ;

	if(!empty( $_GET['action'] ) &&  $_GET['action'] == "wppl_post") {	
		
		if (empty($params[form])) {
			$options_r = get_option('wppl_shortcode'); 
			$wppl = $options_r[$_GET['wppl_form']];
			ob_start();	
  	 	}
   	
   		extract(shortcode_atts($wppl, $get_results));
   	
   		$total_fields = ($profile_fields ) ? $profile_fields : array();
   		
		if($profile_fields_date ) array_unshift($total_fields, $profile_fields_date);
		
   		if ($total_fields ) if (wppl_bp_query_fields($total_fields) == "buba") {
   			wppl_bp_no_members();
   			if (empty($params[form])) {
   				$output_results=ob_get_contents();
				ob_end_clean();
				return $output_results;
			} else return;
   		}
   		
   		// when we need to display profile fields in results //
		if ($results_profile_fields || $results_profile_fields_date ) {
			$total_results_fields = ($results_profile_fields ) ? $results_profile_fields : array(); 		
			if($results_profile_fields_date ) array_unshift($total_results_fields, $results_profile_fields_date);
		}
   	   		
		echo '<link rel="stylesheet" type="text/css" media="all" href="' . plugins_url('themes/'.$results_template.'/css/style.css', (__FILE__))  .'">'; 
   		   			
		if ($_GET['wppl_units'] == "imperial") { 
			$unit_a = array('radius' => 3959, 'name' => "Mi", 'map_units' => "ptm", 'units'	=> 'imperial');
		} else { 
			$unit_a = array('radius' => 6371, 'name' => "Km", 'map_units' => 'ptk', 'units' => "metric");
		}
		
 		$radius = $_GET['wppl_distance'];	
		$from_page = $_GET['pagen'];
	
		$org_address = str_replace(" ", "+", $_GET['wppl_address']);

		/////// CONVERT ORIGINAL ADDRESS TO LATITUDE/LONGITUDE ////	
		$do_it = ConvertToCoords($org_address);
			
		(!empty($org_address)) ? $your_loc = array("Your Location", $lat,$long) : $your_loc = "0";
	
		$order_by = "members.member_id";
	
		if (!empty($org_address)) {
			$calculate = ", ROUND(".$unit_a['radius']. " * acos( cos( radians( $lat ) ) * cos( radians( members.lat ) ) * cos( radians( members.long ) - radians( $long ) ) + sin( radians( $lat ) ) * sin( radians( members.lat) ) ),1 )  AS distance";
			$having = "HAVING distance <= $radius OR distance IS NULL";
			$order_by = "distance";
			$showing = 'within ' . $radius . ' ' . $unit_a['name'] . ' from ' . $_GET['wppl_address'];
		}	
		$get_them = 1;	
		
	
	} elseif ( ($_COOKIE['wppl_lat']) && ($_COOKIE['wppl_long']) && ($params[form]) && ($wppl_options['auto_search'] == 1) ) {
	
		global $wpdb, $wppl, $and_users, $org_address, $unit_a, $pc, $total_fields, $total_results_fields, $lat, $long; ;
		
		extract(shortcode_atts($wppl, $get_results));
		
		$org_address = str_replace(" ", "+", $_COOKIE['wppl_city']);
   		
   		if ($results_profile_fields || $results_profile_fields_date) {
			$total_results_fields = ($results_profile_fields ) ? $results_profile_fields : array(); 		
			if($results_profile_fields_date ) array_unshift($total_results_fields, $results_profile_fields_date);
		} 		
   		
   		echo '<link rel="stylesheet" type="text/css" media="all" href="' . plugins_url('themes/'.$results_template.'/css/style.css', (__FILE__))  .'">'; 
    	
		if ($wppl_options['auto_units'] == "imperial") 
			$unit_a = array('radius' => 3959, 'name' => "Mi", 'map_units' => "ptm", 'units'	=> 'imperial');
		else 
			$unit_a = array('radius' => 6371, 'name' => "Km", 'map_units' => 'ptk', 'units' => "metric");
		
		$radius = $wppl_options['auto_radius'];
		$from_page  = ($_GET['pagen']) ? $_GET['pagen'] : 0;
		$lat = $_COOKIE['wppl_lat'];
		$long = $_COOKIE['wppl_long'];
		$your_loc = array("Your Location", $lat,$long);
		$having = "HAVING distance <= $radius OR distance IS NULL";
		$order_by = "distance";
		$calculate = ", ROUND(" . $unit_a['radius'] . "* acos( cos( radians( $lat ) ) * cos( radians( members.lat ) ) * cos( radians( members.long ) - radians( $long ) ) + sin( radians( $lat ) ) * sin( radians( members.lat) ) ),1 )  AS distance";
		$showing = 'within ' . $radius . ' ' . $unit_a['name'] . ' Near your location';
			
		$auto_se = 1;
		$get_them = 1;			
	}
		
	if($get_them) {
		
		$sql ="
			SELECT members.* {$calculate}
			FROM " . $wpdb->prefix . "wppl_friends_locator  members	
    		WHERE (1=1)
    		{$and_users}
			{$having} ORDER BY {$order_by}" ;
					
		$total_ids = $wpdb->get_results ($sql);
		
		$user_ids = array_chunk($total_ids,$per_page);
		/// count number of pages ///
		$pages = count($user_ids);
		/// posts to display ///
		$user_ids = $user_ids[$from_page];
		
		if ($user_ids) { 
		
			if( ($results_type == "both") || ($results_type == "map") ) { $map_yes = 1; } ?>
	
			<div id="wppl-output-wrapper" style="width:<?php echo ($main_wrapper_width) ? $main_wrapper_width.'px' : '100%'; ?>">
			
				<div id="pag-top" class="pagination">
					<?php if ($map_yes == 1) echo '<div id="show-hide-btn-wrapper"><a href="#" class="map-show-hide-btn"><img src="'.plugins_url('geo-my-wp/images/show-map-icon.png',basename( __FILE__) ).'" /></a></div>'; ?>
					<div class="pag-count" id="member-dir-count-top" <?php echo ($map_yes) ? 'style="margin-left:20px;"' : ''; ?>>
						Viewing member <?php echo (($from_page * $per_page) +1) ; ?> to <?php echo ($from_page * $per_page) + count($user_ids); ?> (of <?php echo count($total_ids); ?> active members) <?php echo $showing; ?>
					</div>	
					<div class="pagination-links" id="member-dir-pag-bottom">
						<?php bp_pagination_links($pages, $per_page); ?>
					</div>
				</div>
				
				<?php 
		
				///// DISPLAY MAP  /////		
				if ($map_yes == 1) {
					echo '<div id="wppl-hide-map" style="float:left;">';
					echo	'<div class="wppl-map-wrapper" style="position:relative">';
					echo		'<div id="map" style="width:'; echo (!empty($map_width)) ? $map_width : 500 ; echo 'px; height:'; echo (!empty($map_height)) ? $map_height : 500; echo 'px;"></div>';
					echo		'<img class="map-loader" src="'.plugins_url('geo-my-wp/images/map-loader.gif', basename(__file__) ).'" style="position:absolute;top:45%;left:33%;"/>';
					echo	'</div>';// map wrapper //
					echo '</div>'; // show hide map //				
				};	
				echo	'<div class="clear"></div>';	
				
				if( ($results_type == "both") || ($results_type == "posts") ) {
					$pc = ($from_page * $per_page) +1;
					include(plugin_dir_path(__FILE__) . 'themes/default/results.php'); ?>
				
					<div id="pag-bottom" class="pagination">
						<div class="pag-count" id="member-dir-count-bottom"> 
							Viewing member <?php echo (($from_page * $per_page) +1) ; ?> to <?php echo ($from_page * $per_page) + count($user_ids); ?> (of <?php echo count($total_ids); ?> active members) <?php echo $showing; ?>
						</div>	
						<div class="pagination-links" id="member-dir-pag-bottom">
							<?php bp_pagination_links($pages, $per_page); ?>
						</div>
					</div>
				
				<?php } ?>
				<div class="clear"></div>
				
				<?php 
				///// DISPLAY MAP  /////		
				if ($map_yes == 1) {
					echo 		'<script type="text/javascript">'; 
					echo			'avatar= '.json_encode($avatars),';'; 
					echo 			'locations= '.json_encode($user_ids),';'; 
					echo 			'your_location= '.json_encode($your_loc),';'; 
					echo			'page= '.json_encode($from_page * $per_page),';'; 
					echo 			'mapType= '.json_encode($map_type),';';
					echo 			'additionalInfo= '.json_encode($additional_info),';';
					echo			'zoomLevel= '. $zoom_level,';';
					echo			'autoZoom= '.json_encode($auto_zoom),';';
					echo 			'friendsSearch="1";';
					echo 			'perMemberIcon= '.json_encode($per_member_icon),';'; 	
					echo 			'mainIconsFolder= '.json_encode(plugins_url('geo-my-wp/map-icons/',basename(__file__) ) ),';';
					echo 			'bpIconsFolder= '.json_encode(plugins_url('map-icons/',__file__ ) ),';';
					echo			'mainIcon= '.json_encode($map_icon),';';
					echo			'ylIcon= '.json_encode($your_location_icon),';';
					echo			'units= '.json_encode($unit_a),';';
					echo 		'</script>';
					echo '<script src="'.plugins_url('geo-my-wp/js/infobox.js',basename(__file__) ). '" type="text/javascript"></script>';		
				};	
				
				wp_enqueue_script( 'wppl-friends-map', true);
		
			echo '</div>';
				
		} else {
			echo '<div id="message" class="info" style="float:left; width:100%;">';
				echo '<p>'; echo _e( "Sorry, no members were found.", 'buddypress' );echo ($wppl_options['wider_search']) ? '  <a href="'. add_query_arg('wppl_distance', $wppl_options['wider_search_value']). '" onclick="document.wppl_form.submit();">click here</a> to search wihtin a greater range or <a href="'. add_query_arg('wppl_address', ''). '" onclick="document.wppl_form.submit();">click here</a> to see all results. </p>' : ''; echo '';
			echo '</div>';
		}
		
		if (empty($params[form])) {
   			$output_results=ob_get_contents();
			ob_end_clean();
			return $output_results;
		}
	}
} 
add_shortcode( 'wppl_friends_results' , 'wppl_bp_query_results' );

function wppl_bp_no_members() {
	global $wppl_options;
	echo '<div id="message" class="info" style="float:left; width:100%;">';
		echo '<p>'; echo _e( "Sorry, no members were found.", 'buddypress' );
	echo '</div>';
	}
?>