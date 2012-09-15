<?php
////// TITLE //////
function wppl_title() {
	global $wppl, $post, $org_address;
	echo '<div class="wppl-title-holder">';
		echo '<h3 class="wppl-h3">';
			echo '<a href="'; the_permalink(); echo '/?address='. $org_address .'">'; the_title(); echo '</a>';
		echo '</h3>';
	echo '</div>';
}
	
///////// THUMBNAIL ///////////////
function wppl_thumbnail() {
	global $wppl, $post;
	if($wppl['show_thumb']) {
		echo '<div class="wppl-thumb">';
			$thumbnail_id=get_the_post_thumbnail($post->ID);
			preg_match ('/src="(.*)" class/',$thumbnail_id,$link);
			echo '<a href="'.$link[1] .'" class="thickbox">' . get_the_post_thumbnail($post->ID,array($wppl['thumb_width'],$wppl['thumb_height'])) . '</a>';	
		echo '</div>';
	}	
}

////// DAYS AND HOURS ///////////////	
function wppl_days_hours() {
	global $post;
	echo '<div class="wppl-days-hours-wrapper">';
	$days_hours = get_post_meta($post->ID, '_wppl_days_hours', true);
	$dc = 0;
	if ($days_hours) {
		foreach ($days_hours as $day) {
			if(!in_array('', $day)){
				$dc++;
				echo '<div class="single-days-hours"><span class="single-day">' . $day['days'] . ':</span><span class="single-hour">' . $day['hours'] . '</span></div>';
			}
		}
	}
	if ( (!$days_hours) || ($dc == 0) ) {
		echo '<span class="single-day">Sorry, days & Hours not avaiable</span>'; 
	} 				
	echo '</div>';
}

////////// BY RADIUS ///////////
function wppl_by_radius () {
	global $lat, $long, $single_result, $unit_a;
	if ($lat && $long) echo '<span class="radius-dis">'. $single_result->distance . " " . $unit_a['name'] . '</span>';
	}

///////// TAXES WRAPPER //////////// 
function wppl_taxonomies() {
	global $wppl, $post;
	if ($wppl['custom_taxes']) {
		echo '<div class="wppl-taxes-wrapper">';
    		if($wppl['taxonomies']) {
    			foreach ($wppl['taxonomies'] as $tax) {
    				$terms = get_the_terms($post->ID, $tax);
    		
    				if ( $terms && ! is_wp_error( $terms ) ) :
						$terms_o = array();

						foreach ( $terms as $term ) {
							$terms_o[] = $term->name;
						}
						
						$terms_o = join( ", ", $terms_o );
						$the_tax = get_taxonomy($tax);
						echo '<div class="wppl-taxes"><span>'.$the_tax->labels->singular_name . ':</span> ' . $terms_o.'</div>';
					endif;	 		
				}
			}
		echo '</div>';
	}
}
		
//////// ADDITIONAL INFORMAION  //////////
function wppl_additional_info() { ?>
	<?php global $wppl, $post; ?>
	<?php if($wppl['additional_info']); { ?>
		<div class="wppl-additional-info">	
   			<?php if($wppl['additional_info']['phone']) { ?><div class="wppl-phone"><span>Phone: </span><?php echo (get_post_meta($post->ID,'_wppl_phone',true)) ? get_post_meta($post->ID,'_wppl_phone',true) : 'N/A'; ?></div><?php } ?>
    		<?php if($wppl['additional_info']['fax']) { ?><div class="wppl-fax"><span>Fax: </span><?php echo (get_post_meta($post->ID,'_wppl_fax',true)) ? get_post_meta($post->ID,'_wppl_fax',true) : 'N/A'; ?></div><?php } ?>
    		<?php if($wppl['additional_info']['email']) { ?><div class="wppl-email"><span>Email: </span><?php echo (get_post_meta($post->ID,'_wppl_email',true)) ? get_post_meta($post->ID,'_wppl_email',true) : 'N/A'; ?></div><?php } ?>
    		<?php if($wppl['additional_info']['website']) { ?><div class="wppl-website"><span>Website: </span><?php if (get_post_meta($post->ID,'_wppl_website',true)) { ?><a href="http://<?php echo get_post_meta($post->ID,_wppl_website,true); ?>" target="_blank"><?php echo get_post_meta($post->ID,'_wppl_website',true); } else { echo "N/A"; }?></a></div><?php } ?>
		</div> <!-- additional info -->
	<?php } ?>
<?php } ?>
<?php
///////// EXCERPT //////////////
function wppl_excerpt() {
	global $wppl, $post;
	if ($wppl['show_excerpt']) { 
    	echo '<div class="wppl-excerpt">';
			echo wp_trim_words( $post->post_content,$wppl['words_excerpt']);
		echo '</div>';
	}
}

//////// GET DIRECTIONS ////////////
function wppl_directions() {
	global $wppl, $single_result, $org_address, $unit_a;
	if ($wppl['get_directions']) { 			 	
    	echo '<div class="wppl-get-directions">'; 
    	echo 	'<span><a href="http://maps.google.com/maps?f=d&hl=en&doflg=' . $unit_a['map_units'] . '&geocode=&saddr=' .$org_address . '&daddr=' . str_replace(" ", "+", $single_result->address) . '&ie=UTF8&z=12" target="_blank">Get Directions</a></span>';
		echo '</div>';
	}
}

/////// ADDRESS ///////////
function wppl_address() {
	global $post;
	echo '<div class="wppl-address">';
 	echo 	get_post_meta($post->ID,'_wppl_address',true); 
	echo '</div>';
	} 

	
function distance_between() {
	global $wppl, $single_result, $lat, $long, $pc, $unit_a;
	if ($wppl['by_driving']) { 
	echo '<script>';
	echo		'latitude= '.json_encode($lat),';'; 
    echo		'longitude= '.json_encode($long),';'; 
    echo		'des_lat= '.json_encode($single_result->lat),';'; 
    echo		'des_long= '.json_encode($single_result->long),';';
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
      			jQuery('#wppl-driving-distance-<?php echo $pc; ?>').text('Driving: ' + totalDistance)
      			//directionsDisplay.setPanel(document.getElementById("wppl-directions-panel-<?php echo $pc; ?>"));	
    		}
 		 });	 
 		 //directionsDisplay.setMap(map);
 		 //directionsDisplay.setPanel(document.getElementById("wppl-directions-panel-" + (i + 1) ));    
	</script>
	<?php
	//echo 	'<div id="wppl-directions-panel-'. $pc .'"></div>';
	echo 	'<div class="wppl-driving-distance" id="wppl-driving-distance-' .$pc . '"></div>';
	}
}

/////// MARK FEATURED POSTS ////////
function wppl_featured_posts() {
	global $wppl, $post;
	if ($wppl['featured_posts']) {
		echo '<style>span.wppl-featured-post { position: absolute;top: -10px;right: -40px;display: block;width: 100px;height: 100px;}.wppl-single-result {position:relative;}</style>';	
		if (get_post_meta($post->ID,'_wppl_featured_post',true) ==1 ) { 		 	
			echo '<span class="wppl-featured-post" style="background:url('.plugins_url('plugins/featured-posts/images/'.$wppl['featured_posts_image'],__FILE__).') no-repeat"></span>';
		}
	}
}
   
			
    