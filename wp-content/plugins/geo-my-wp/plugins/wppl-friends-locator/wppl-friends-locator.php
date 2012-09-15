<?php

$plugins_options = get_option('wppl_plugins');

if ($plugins_options['friends_locator_on'] == 1) {

function addLocation(){
	global $wpdb, $bp;
	
	$map_icon = ($_POST['map_icon']) ? $_POST['map_icon'] : '_default.png';
	
	if ( empty($_POST['wppl_address']) &&  empty($_POST['wppl_lat']) &&  empty($_POST['wppl_long']) ) {
		$wpdb->query(
			$wpdb->prepare( 
				"DELETE FROM " . $wpdb->prefix . "wppl_friends_locator WHERE member_id=%d",$bp->loggedin_user->id
				)
			);
	
	} elseif ($wpdb->replace( $wpdb->prefix . 'wppl_friends_locator', array( 
				'member_id'		=> $bp->loggedin_user->id,	
				'address'		=> $_POST['wppl_address'],
				'zipcode'		=> $_POST['wppl_zipcode'],
				'city' 			=> $_POST['wppl_city'],
				'state' 		=> $_POST['wppl_state'], 
				'country' 		=> $_POST['wppl_country'],
				'lat'			=> $_POST['wppl_lat'],
				'long'			=> $_POST['wppl_long'],
				'map_icon'		=> $map_icon,		
			))===FALSE) {
			 
	echo "Error";
 
	}
	else {	
		echo "Data successfully saved!"; 
	}
	die();
}

add_action('wp_ajax_addLocation', 'addLocation');
add_action('bp_init', 'add_bp_location_tab');

function add_bp_location_tab () {
	global $bp;
	if (bp_is_home()) {$func = 'my_location_page'; } else {$func = 'user_location_page';}

	bp_core_new_nav_item(
   	array(
        'name' => __('Location', 'buddypress'),
        'slug' => 'wppl-location',
        'position' => 50,
        'show_for_displayed_user' => true,
        'screen_function' => $func,
        //'default_subnav_slug' => $who,
        'item_css_id' => 'wppl-my-location'
    ));
    }
    	 
    function my_location_page () {
   	 	add_action( 'bp_template_title', 'my_location_page_title' );
   		add_action( 'bp_template_content', 'my_location_page_content' );
    	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}
	
	function user_location_page () {
   	 	add_action( 'bp_template_title', 'user_location_page_title' );
   		add_action( 'bp_template_content', 'user_location_page_content' );
    	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}
	
	function my_location_page_title() {
    	global $bp;
        echo 'Your location';
    }
			
    function my_location_page_content() { 
   
	global $post, $bp, $wpdb;
	$wppl_options = get_option('wppl_fields');

	$sql ="
		SELECT * 
			FROM " . $wpdb->prefix . "wppl_friends_locator  	
    		WHERE 
    		(1 = 1)
    		AND member_id =". $bp->loggedin_user->id. "
			ORDER BY member_id" ;
    
    $member_info = $wpdb->get_results($sql, ARRAY_A);
    echo '<script type="text/javascript">'; 
	echo 	'savedLat= '.json_encode($member_info[0]['lat']),';'; 
	echo 	'savedLong= '.json_encode($member_info[0]['long']),';'; 
	echo '</script>';
	
    include 'my-location-tab.php';
    }
        	
    function user_location_page_title() {
    	global $bp;
        echo $bp->displayed_user->fullname . '&#39;s Location';
    }
    
    function user_location_page_content() { 
    	include 'user-location-tab.php';
    }
} // friends locator on

?>