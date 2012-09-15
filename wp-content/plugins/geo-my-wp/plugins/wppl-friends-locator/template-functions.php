<?php

/////// TITLE ///////////
function wppl_bp_title() {
	global $user_data;
	echo '<div class="wppl-item-title">';
	echo	'<a href="' . $user_data->user_url . '">'. $user_data->fullname . '</a>';
	echo '</div>';
	}
	
///////// THUMBNAIL ///////////////
function wppl_bp_thumbnail() {
	global $user_data, $wppl;
	if($wppl['show_thumb']) {
		echo '<div class="wppl-item-avatar">';
		echo "<style> .wppl-item-avatar img { width:$wppl[thumb_width]px !important; height:$wppl[thumb_height]px !important; } </style>";
			echo '<a href="' . $user_data->user_url . '">'. $user_data->avatar_thumb . '</a>';
		echo '</div>';
	}	
}

////////// ACTION BUTTONS //////
function wppl_bp_action_btn() {
	global $bp, $single_user;
	if($bp->active_components['friends'] == 1) {
		echo '<div class="wppl-action">';
			$friend_status = friends_check_friendship_status( $bp->loggedin_user->userdata->ID,$single_user->member_id); 
			echo bp_get_add_friend_button( $single_user->member_id, $friend_status ); 
		echo '</div>';
   		}
   	}
   	
////////// BY RADIUS ///////////
function wppl_bp_by_radius () {
	global $lat, $long, $single_user, $unit_a;
		if ($lat && $long) echo '<div class="bp-radius-wrapper">' . $single_user->distance .' ' . $unit_a['name'] . '</div>'; 
	}
		
//////// GET DIRECTIONS ////////////
function wppl_bp_directions() {
	global $wppl, $single_user, $org_address, $unit_a;
	if ($wppl['get_directions']) { 			 	
    	echo '<div class="bp-get-directions">'; 
    	echo 	'<span><a href="http://maps.google.com/maps?f=d&hl=en&doflg=' . $unit_a['map_units'] . '&geocode=&saddr=' .$org_address . '&daddr=' . str_replace(" ", "+", $single_user->address) . '&ie=UTF8&z=12" target="_blank">Get Directions</a></span>';
		echo '</div>';
	}
}

/////// ADDRESS ///////////
function wppl_bp_address() {
	global $single_user, $wppl;
	echo '<div class="wppl-bp-address">';
 	echo 	'<span>Address: </span>' . $single_user->address; 
	echo '</div>';
	} 
	
function wppl_bp_last_active() {
	global $user_data;
	echo '<div class="wppl-item-meta">';
		echo '<span class="activity">';
			echo $user_data->last_active; 
		echo '</span>';
	echo '</div>';
	}


//////// DRIVING DISTANCE ///////////

/////// CALCULATE DRIVING DISTANCE ////////
/* function distance_between($des_lat,$des_long) {  			 	
  	global $distance_between, $lat, $long, $directions, $org_address, $unit, $wppl_units;
  	
   $ci = curl_init();
   	curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ci, CURLOPT_USERAGENT, "Mozilla/2.0 (compatible; MSIE 3.02; Update a; AK; Windows 95)");
	curl_setopt($ci, CURLOPT_HTTPGET, true);
	curl_setopt($ci, CURLOPT_URL, 'http://maps.googleapis.com/maps/api/directions/xml?origin=' . $lat . "," . $long . '&destination=' .  $des_lat . "," . $des_long .  '&units=' . $wppl_units . '&sensor=true'  );
	curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 60);
	$got_xml = curl_exec($ci);		
   	$xml_distance = new SimpleXMLElement($got_xml);
   	list($distance_between) = explode(",", $xml_distance->route->leg->distance->text);		
}
function driving_distance($by_driving, $single_result) {
	if ($by_driving == 1) { 
		distance_between($single_result->lat, $single_result->long); ?>
    					<span class="drive-dis">Driving: <?php echo $distance_between; ?></span> 
    				<?php	} ?>
    				
*/
	
function bp_distance_between() {
	global $wppl, $single_user, $lat, $long, $pc, $unit_a;
	if ($wppl['by_driving']) { 
	echo '<script>';
	echo		'latitude= '.json_encode($lat),';'; 
    echo		'longitude= '.json_encode($long),';'; 
    echo		'des_lat= '.json_encode($single_user->lat),';'; 
    echo		'des_long= '.json_encode($single_user->long),';';
    echo		'unit= '.json_encode($unit_a['name']),';';
    echo		'pc= '.json_encode($pc),';'; 
    echo '</script>';
	?>
	<script>    	
    	var directionsDisplay;
		var directionsService = new google.maps.DirectionsService();	
		var directionsDisplay;
        directionsDisplay = new google.maps.DirectionsRenderer();	
  		var start = new google.maps.LatLng(latitude,longitude);
  		var end = new google.maps.LatLng(des_lat,des_long);
  		var request = {
    		origin:start,
    		destination:end,
    		travelMode: google.maps.TravelMode.DRIVING
 		};
 		
  		directionsService.route(request, function(result, status) {
    		if (status == google.maps.DirectionsStatus.OK) {
      			directionsDisplay.setDirections(result);
      			//alert((result.routes[0].legs[0].distance.value * 0.001));
      			if (unit == 'Mi') {
      				totalDistance = (Math.round(result.routes[0].legs[0].distance.value * 0.000621371192 * 10) / 10)+' Mi'
      			} else { 
      				totalDistance = (Math.round(result.routes[0].legs[0].distance.value * 0.01) / 10)+' Km'
      			}	
      			jQuery('#bp-driving-distance-<?php echo $pc; ?>').text('Driving: ' + totalDistance)
      			//directionsDisplay.setPanel(document.getElementById("wppl-directions-panel-<?php echo $pc; ?>"));	
    		}
 		 });	 
 		 //directionsDisplay.setMap(map);
 		 //directionsDisplay.setPanel(document.getElementById("wppl-directions-panel-" + (i + 1) ));    
	</script>
	<?php
	//echo 	'<div id="wppl-directions-panel-'. $pc .'"></div>';
	echo 	'<div class="bp-driving-distance" id="bp-driving-distance-' .$pc . '"></div>';
	}
}
