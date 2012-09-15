<?php
////// SHORTCODE FOR SINGLE LOCATION MAP///////
function wppl_shortcode_single($single) {
	extract(shortcode_atts(array(
		'map_height' => '250',
		'map_width' => '250',
		'map_type' => 'ROADMAP',
		'zoom_level' => 13,
		'show_info' => 1	
	),$single));
	
	global $post, $wpdb;		
    $get_loc = array(
    	get_post_meta($post->ID,'_wppl_lat',true),
    	get_post_meta($post->ID,'_wppl_long',true),
    	get_the_title($post->ID),
    	get_post_meta($post->ID,'_wppl_address',true),
    	get_post_meta($post->ID,'_wppl_phone',true),
    	get_post_meta($post->ID,'_wppl_fax',true),
    	get_post_meta($post->ID,'_wppl_email',true),
    	get_post_meta($post->ID,'_wppl_website',true),
    	$map_type,
    );
    //wp_enqueue_script( 'wppl-map' ,true);
    echo '<script type="text/javascript">'; 
	echo 	'singleLocation= '.json_encode($get_loc),';'; 
	echo	'zoomLevel= '. $zoom_level,';';
	echo  '</script>';
	wp_enqueue_script( 'wppl-infobox', true);
	wp_enqueue_script( 'wppl-sl-map', true);
		?>
	<script>
	jQuery(function() {
    	jQuery('.show-directions').click(function(event){
    		event.preventDefault();
    		jQuery(".wppl-single-direction").slideToggle(); 
    	}); 
    });
	</script>
	<?php
	
	$single_address = (get_post_meta($post->ID,'_wppl_address',true)) ? get_post_meta($post->ID,'_wppl_address',true) : 'N/A';
	$single_phone 	= (get_post_meta($post->ID,'_wppl_phone',true)) ? get_post_meta($post->ID,'_wppl_phone',true) : 'N/A';
	$single_fax 	= (get_post_meta($post->ID,'_wppl_fax',true)) ? get_post_meta($post->ID,'_wppl_fax',true) : 'N/A';
	$single_email 	= (get_post_meta($post->ID,'_wppl_email',true)) ? get_post_meta($post->ID,'_wppl_email',true) : 'N/A';
	$single_website = (get_post_meta($post->ID,'_wppl_website',true)) ? '<a href="http://' . get_post_meta($post->ID,'_wppl_website',true). '" target="_blank">' .get_post_meta($post->ID,'_wppl_website',true). '</a>' : "N/A";
	
 	$single_map .='';
 	$single_map .=	'<div class="wppl-single-wrapper">';
	$single_map .=		'<div class="wppl-single-map-wrapper" style="position:relative">';
	$single_map .=			'<div id="map_single" style="width:' . $map_width .'px; height:' . $map_height . 'px;"></div>';
	
	$single_map .= 			'<div class="wppl-single-direction-wrapper">';
    $single_map .= 				'<div class="wppl-single-direction" style="display:none;">';
	$single_map .=					'<form action="http://maps.google.com/maps" method="get" target="_blank">';
	$single_map .= 						'<input type="text" size="35" name="saddr" value="'. $_GET['address'].'" />';
	$single_map .= 						'<input type="hidden" name="daddr" value="'. get_post_meta($post->ID,'_wppl_address',true).'" /><br />';
	$single_map .= 						'<input type="submit" class="button" value="GO" />';
	$single_map .= 					'</form>'; 
    $single_map .= 				'</div>';
    $single_map .= 				'<a href="#" class="show-directions">Get Directions</a>';
    $single_map .= 			'</div>';
    $single_map .=		'</div>';// map wrapper //
	
	if ($show_info == 1) {
		$single_map .= '<div class="wppl-additional-info">';
		$single_map .=		'<div class="wppl-address"><span>Address: </span>' . $single_address . '</div>';
    	$single_map .=		'<div class="wppl-phone"><span>Phone: </span>' . $single_phone . '</div>';
    	$single_map .=		'<div class="wppl-fax"><span>Fax: </span>' . $single_fax . '</div>';
    	$single_map .=		'<div class="wppl-email"><span>Email: </span>' . $single_email . '</div>';
   	 	$single_map .=		'<div class="wppl-website"><span>Website: </span>' . $single_website . '</div>';
    	$single_map .= '</div>';
    } 
 
    $single_map .= '</div>';
    return $single_map;	
}
add_shortcode( 'wppl_single' , 'wppl_shortcode_single' );

////// SHORTCODE DISPLAY USER'S LOCATION   ///////
function wppl_location_shortcode($locate) {
	global $wppl_options;
	extract(shortcode_atts(array(
		'display_by' 	=> 'city',
		'title' 		=> 'Your Location:',
		'show_name' 	=> 0
	),$locate));
	
	$location .= '';
	$location .=	'<div class="wppl-your-location-wrapper">';
		
	if ($show_name) {
		global $current_user;
		get_currentuserinfo();
		if($current_user->user_login) {
			$user_name = ', '.$current_user->user_login;
		} else if($current_user->user_firstname) {	
			$user_name = ', '.$current_user->user_firstname;
		} else {
			$user_name = ', '.$current_user->user_lastname;
		}
		if(!is_user_logged_in()) { 
			$name_is = 'Hello, Guest!';
		}else {
			$name_is = 'Hello'.$user_name.'!';
		}
		$location .=		'<span class="wppl-hello-user">'.$name_is.'</span>';
	}
	if($_COOKIE['wppl_' . $display_by]) {
		$location .= '<span class="wppl-your-location-title">' . $title . '</span>';
	} else {
		$location .= '<span class="wppl-your-location-title"><a href="#" id="location-form-btn">Get your current location</a></span>';
	}
	$location .=		'<span class="wppl-your-location-location"><a href="#" id="location-form-btn">' . $_COOKIE['wppl_' . $display_by]. '</a></span>';
	$location .=    	'<div id="wppl-show-location-form"  style="display:none">';
	$location .=			'<form class="wppl-location-form" name="wppl_location_form" onsubmit="return false" action="" method="post">';
	$location .=				'<div>';
	$location .=					'<input type="text" name="wppl_user_address" class="wppl-user-address" value="" placeholder="zipcode or full address..." />';
	$location .= 					'<button type="button"  onClick="getLocationNoSubmit();" class="wppl-locate-me-btn"><img src="' . plugins_url('images/locator-images/'.$wppl_options['locator_icon'], __FILE__) . '"></button>';
	$location .=				'</div>';
	$location .=				'<input type="submit" value="go" />';
	$location .=				'<input type="hidden" name="action" value="wppl_user_location" />';
	$location .=			'</form>';
	$location .= 		'</div>';	
	$location .=	'</div>';
	
	return $location;
	
}
add_shortcode( 'wppl_location' , 'wppl_location_shortcode' );
?>