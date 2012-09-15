<?php
global $wppl_options, $plugins_options, $wppl_on;

if ( $wppl_on['featured_posts'] ) include_once 'plugins/featured-posts/featured-posts-admin.php';
	
if ( $wppl_on['per_post_icon'] ) include_once 'plugins/featured-map-icon/featured-map-icons.php';

$prefix = '_wppl_';
$wppl_meta_box = array(
    'id' => 'wppl-meta-box',
    'title' => 'WP Places Locator - Please enter the full address or the latitude / longitude of the location.',
    'pages' => $wppl_options['address_fields'],
    'context' => 'normal',
    'priority' => 'high',
    'fields' => array(
        array(
            'name' => 'Street',
            'desc' => '',
            'id' => $prefix . 'street',
            'type' => 'text',
            'std' => ''
        ),
        array(
            'name' => 'Apt/Suite',
            'desc' => '',
            'id' => $prefix . 'apt',
            'type' => 'text',
            'std' => ''
        ),
        array(
            'name' => 'City',
            'desc' => '',
            'id' => $prefix . 'city',
            'type' => 'text',
            'std' => ''
        ),
        array(
            'name' => 'State',
            'desc' => '',
            'id' => $prefix . 'state',
            'type' => 'text',
            'std' => ''
        ),
        array(
            'name' => 'Zip Code',
            'desc' => '',
            'id' => $prefix . 'zipcode',
            'type' => 'text',
            'std' => ''
        ),
        array(
            'name' => 'Country',
            'desc' => '',
            'id' => $prefix . 'country',
            'type' => 'text',
            'std' => ''
        ),
        
        array(
            'name' => 'Phone Number',
            'desc' => '',
            'id' => $prefix . 'phone',
            'type' => 'text',
            'std' => ''
        ),
        
        array(
            'name' => 'Fax Number',
            'desc' => '',
            'id' => $prefix . 'fax',
            'type' => 'text',
            'std' => ''
        ),
        
        array(
            'name' => 'Email Address',
            'desc' => '',
            'id' => $prefix . 'email',
            'type' => 'text',
            'std' => ''
        ),
        
        array(
            'name' => 'Website Address',
            'desc' => 'Ex: www.website.com',
            'id' => $prefix . 'website',
            'type' => 'text',
            'std' => ''
        ),
        
       
        array(
            'name' => 'Latitude',
            'desc' => '',
            'id' => $prefix . 'enter_lat',
            'type' => 'text-right',
            'std' => ''
        ),
        
         array(
            'name' => 'Longitude',
            'desc' => '',
            'id' => $prefix . 'enter_long',
            'type' => 'text-right',
            'std' => ''
        ),
         array(
            'name' => 'Latitude',
            'desc' => '',
            'id' => $prefix . 'lat',
            'type' => 'text-disable',
            'std' => ''
        ),
         array(
            'name' => 'longitude',
            'desc' => '',
            'id' => $prefix . 'long',
            'type' => 'text-disable',
            'std' => ''
        ),
        array(
            'name' => 'Full Address',
            'desc' => '',
            'id' => $prefix . 'address',
            'type' => 'text-disable',
            'std' => ''
        ),
         array(
            'name' => 'Days & Hours',
            'desc' => '',
            'id' => $prefix . 'days_hours',
            'type' => 'text',
            'std' => ''
        )
    )
);

// Add meta box //
function wppl_add_box() {
	global $wppl_options;
    global $wppl_meta_box;
    	if ($wppl_options['address_fields']) {
 		foreach ($wppl_meta_box['pages'] as $page) {
        	add_meta_box($wppl_meta_box['id'], $wppl_meta_box['title'], 'wppl_show_box', $page, $wppl_meta_box['context'], $wppl_meta_box['priority']);
   		}
    }
}
add_action('admin_menu', 'wppl_add_box');

// Callback function to show fields in meta box //
function wppl_show_box() {
    global $wppl_meta_box, $post;
    $wppl_options = get_option('wppl_fields');
    echo	'<script type="text/javascript">';
	echo		'addressMandatory= '.json_encode($wppl_options['mandatory_address']),';'; 
	echo	'</script>';
	
    echo 	'<input type="hidden" name="wppl_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
    
    echo 	'<table class="form-table" style="width:100%;">';
    echo	'<tr><td>';

	echo	'<div class="wppl-admin-map-holder">';
    echo		'<div id="map"></div>';
	echo	'</div>';
	
	echo	'<div id="wppl-lookup-options-holder">';

	echo	'<h1>Use any of the 5 options below to return the address, latitude and longitude.</h1>';
    echo		'<h2>1) Get your current location - <input type="button" class="button-primary" value="Locate Me" onClick="getLocation();" /></h2>';
  	echo		'<h2>2) Use the map to drag and drop the marker to the correct location to get the latitude / longitude and click on the "Get Address" button below</h2>';
  	echo 		'<h2>3) Address Lookup - start typing an adress for autocomplete</h2>';
  	echo		'<input type="text" id="wppl-addresspicker" size="50" value="', get_post_meta($post->ID,$wppl_meta_box['fields'][14]['id'],true) , '" />';
  	echo 		'<h2>4) Enter Address - Click on the "address" tab below, fill up the address fields and click "Get Lat/Long".</h2>';
  	echo 		'<h2>5) Enter Lat/Long - Click on the "Lat/Long" tab below, fill up the Latitude/Longitude fields and click "Get Address".</h2>';
  	echo		'<input type="button" style="float:left; margin-top:10px" class="preview button" value="Delete Address" onClick="removeAddress();" />';
  	echo	'</div>';
  	
   	echo	'<div class="metabox-tabs-div">';
	echo		'<ul class="metabox-tabs" id="metabox-tabs">';
	echo			'<li class="active address-tab"><a class="active" href="javascript:void(null);">Address</a></li>';
	echo			'<li class="lat-long-tab"><a href="javascript:void(null);">Latitude / Longitude</a></li>';
	echo			'<li class="extra-info-tab"><a href="javascript:void(null);">Additional Information</a></li>';
	echo			'<li class="days-hours-tab"><a href="javascript:void(null);">Days & Hours</a></li>';
	echo			'<li id="ajax-loader" style="visibility:hidden; background:none; border:0px;"><img src="'. plugins_url('images/wpspin_light.gif', __FILE__) . '" id="ajax-loader-image" alt="" "> Loading</li>';
	echo		'</ul>';
	
	echo 		'<div style="float:left;width:100%;" class="address-tab">';
 	echo 		'<h4 class="heading">Address</h4>';
  	echo 			'<table class="form-table" style="width:97%;float:left;clear:none;">';				
 	echo 				'<tr>';
 	echo 					'<th style="width:20%"><label for="', $wppl_meta_box['fields'][0]['id'], '">', $wppl_meta_box['fields'][0]['name'], '</label></th>';
    echo					'<td><input type="text" name="',$wppl_meta_box['fields'][0]['id'], '" id="', $wppl_meta_box['fields'][0]['id'], '" value="', get_post_meta($post->ID,$wppl_meta_box['fields'][0]['id'],true), '" size="30" style="width:97%" />', '<br />', $field['desc'] , '</td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th style="width:20%"><label for="', $wppl_meta_box['fields'][1]['id'], '">', $wppl_meta_box['fields'][1]['name'], '</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][1]['id'], '" id="', $wppl_meta_box['fields'][1]['id'], '" value="', get_post_meta($post->ID,$wppl_meta_box['fields'][1]['id'],true), '" size="30" style="width:97%" />', '<br />', $field['desc'] , '</td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th style="width:20%"><label for="', $wppl_meta_box['fields'][2]['id'], '">', $wppl_meta_box['fields'][2]['name'], '</label></th>';
    echo					'<td><input type="text" name="',$wppl_meta_box['fields'][2]['id'], '" id="', $wppl_meta_box['fields'][2]['id'], '" value="', get_post_meta($post->ID,$wppl_meta_box['fields'][2]['id'],true), '" size="30" style="width:97%" />', '<br />', $field['desc'] , '</td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th style="width:20%"><label for="', $wppl_meta_box['fields'][3]['id'], '">', $wppl_meta_box['fields'][3]['name'], '</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][3]['id'], '" id="', $wppl_meta_box['fields'][3]['id'], '" value="', get_post_meta($post->ID,$wppl_meta_box['fields'][3]['id'],true), '" size="30" style="width:97%" />', '<br />', $field['desc'] , '</td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th style="width:20%"><label for="', $wppl_meta_box['fields'][4]['id'], '">', $wppl_meta_box['fields'][4]['name'], '</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][4]['id'], '" id="', $wppl_meta_box['fields'][4]['id'], '" value="', get_post_meta($post->ID,$wppl_meta_box['fields'][4]['id'],true), '" size="30" style="width:97%" />', '<br />', $field['desc'] , '</td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th style="width:20%"><label for="', $wppl_meta_box['fields'][5]['id'], '">', $wppl_meta_box['fields'][5]['name'], '</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][5]['id'], '" id="', $wppl_meta_box['fields'][5]['id'], '" value="', get_post_meta($post->ID,$wppl_meta_box['fields'][5]['id'],true), '" size="30" style="width:97%" />', '<br />', $field['desc'] , '</td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<td><input type="button" class="button-primary" value="Get Lat/Long" onClick="getLatLong();"></td>';
    echo 				'</tr>';
    echo 			'</table>';
    echo 		'</div>'; 
    
    echo 		'<div style="float:left;width:100%;"  class="lat-long-tab">';
 	echo 			'<h4 class="heading">Latitude / Longitude</h4>';
    echo 			'<table class="form-table" style="width:97%;">';
    echo 				'<tr>';
 	echo 					'<th style="width:20%"><label for="', $wppl_meta_box['fields'][10]['id'], '">', $wppl_meta_box['fields'][10]['name'], '</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][10]['id'], '" id="', $wppl_meta_box['fields'][10]['id'], '" value="', get_post_meta($post->ID,$wppl_meta_box['fields'][10]['id'],true), '" size="30" style="width:97%" />', '<br />', $field['desc'] , '</td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th style="width:20%"><label for="', $wppl_meta_box['fields'][11]['id'], '">', $wppl_meta_box['fields'][11]['name'], '</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][11]['id'], '" id="', $wppl_meta_box['fields'][11]['id'], '" value="', get_post_meta($post->ID,$wppl_meta_box['fields'][11]['id'],true), '" size="30" style="width:97%" />', '<br />', $field['desc'] , '</td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo					'<td><input type="button" class="button-primary" value="Get Address" onClick="getAddress();" /></td>';
    echo 				'</tr>'; 
 	echo 			'</table>';
 	echo 		'</div>';
 	
 	echo 		'<div style="float:left;width:100%;" class="extra-info-tab">';
 	echo 			'<h4 class="heading">Extra Information</h4>';
    echo 			'<table class="form-table" style="width:97%;float:left;clear:none;">';    
    echo 				'<tr>';
 	echo 					'<th style="width:25%"><label for="', $wppl_meta_box['fields'][6]['id'], '">', $wppl_meta_box['fields'][6]['name'], '</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][6]['id'], '" id="', $wppl_meta_box['fields'][6]['id'], '" value="', get_post_meta($post->ID,$wppl_meta_box['fields'][6]['id'],true), '" size="30" style="width:97%;" />', '<br />', $field['desc'] , '</td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th style="width:25%"><label for="', $wppl_meta_box['fields'][7]['id'], '">', $wppl_meta_box['fields'][7]['name'], '</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][7]['id'], '" id="', $wppl_meta_box['fields'][7]['id'], '" value="', get_post_meta($post->ID,$wppl_meta_box['fields'][7]['id'],true), '" size="30" style="width:97%;" />', '<br />', $field['desc'] , '</td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th style="width:25%"><label for="', $wppl_meta_box['fields'][8]['id'], '">', $wppl_meta_box['fields'][8]['name'], '</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][8]['id'], '" id="', $wppl_meta_box['fields'][8]['id'], '" value="', get_post_meta($post->ID,$wppl_meta_box['fields'][8]['id'],true), '" size="30" style="width:97%;" />', '<br />', $field['desc'] , '</td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th style="width:25%"><label for="', $wppl_meta_box['fields'][9]['id'], '">', $wppl_meta_box['fields'][9]['name'], '</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][9]['id'], '" id="', $wppl_meta_box['fields'][9]['id'], '" value="', get_post_meta($post->ID,$wppl_meta_box['fields'][9]['id'],true), '" size="30" style="width:97%;" />', '<br />', $field['desc'] , '</td>';
 	echo 				'</tr>';	
 	echo 			'</table>';
 	echo 		'</div>';
 	
 	if(get_post_meta($post->ID,$wppl_meta_box['fields'][15]['id'],true)) { $days_hours = get_post_meta($post->ID,$wppl_meta_box['fields'][15]['id'],true);}
 	
 	echo 		'<div style="float:left;width:100%;" class="days-hours-tab">';
 	echo 			'<h4 class="heading">Days & Hours</h4>';
    echo 			'<table class="form-table" style="width:97%;float:left;clear:none;">';    
  	echo 				'<tr>';
 	echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Days</label></th>';
    echo 					'<td style="width:150px"><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[0][days]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[0][days], '" style="width:150px" />', '<br />', $field['desc'] , '</td>';
    echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Hours</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[0][hours]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[0][hours], '" style="width:150px" />', '<br />', $field['desc'] , '</td>';  
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Days</label></th>';
    echo 					'<td style="width:150px"><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[1][days]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[1][days], '" style="width:150px" />', '<br />', $field['desc'] , '</td>';
    echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Hours</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[1][hours]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[1][hours], '" style="width:150px" />', '<br />', $field['desc'] , '</td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Days</label></th>';
    echo 					'<td style="width:150px"><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[2][days]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[2][days], '" style="width:150px" />', '<br />', $field['desc'] , '</td>';
    echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Hours</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[2][hours]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[2][hours], '" style="width:150px" />', '<br />', $field['desc'] , '</td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Days</label></th>';
    echo 					'<td style="width:150px"><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[3][days]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[3][days], '" style="width:150px" />', '<br />', $field['desc'] , '</td>';
    echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Hours</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[3][hours]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[3][hours], '" style="width:150px" />', '<br />', $field['desc'] , '</td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Days</label></th>';
    echo 					'<td style="width:150px"><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[4][days]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[4][days], '" style="width:150px" />', '<br />', $field['desc'] , '</td>';
    echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Hours</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[4][hours]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[4][hours], '" style="width:150px" />', '<br />', $field['desc'] , '</td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Days</label></th>';
    echo 					'<td style="width:150px"><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[5][days]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[5][days], '" style="width:150px" />', '<br />', $field['desc'] , '</td>';
    echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Hours</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[5][hours]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[5][hours], '" style="width:150px" />', '<br />', $field['desc'] , '</td>';
 	echo 				'</tr>';
 	echo 				'<tr>';
 	echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Days</label></th>';
    echo 					'<td style="width:150px"><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[6][days]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[6][days], '" style="width:150px" />', '<br />', $field['desc'] , '</td>';
    echo 					'<th style="width:30px"><label for="', $wppl_meta_box['fields'][15]['id'], '">Hours</label></th>';
    echo 					'<td><input type="text" name="',$wppl_meta_box['fields'][15]['id'], '[6][hours]" id="', $wppl_meta_box['fields'][15]['id'], '" value="', $days_hours[6][hours], '" style="width:150px" />', '<br />', $field['desc'] , '</td>';
 	echo 				'</tr>';
 	echo 			'</table>';
 	echo 		'</div>';
 	
 	echo 	'</div>';
 	
 	echo 	'<div style="margin-top:38px;float:left;width:100%;" class="postbox">';
 	echo 		'<h3 style="margin:0px">Saved Data - <span style="color:brown;font-size:14px;">This table contains the data that is going to be used. it must have an address, latitude and longitude.</span></h3>';
 	echo 		'<table class="form-table" style="width:97%;float:left;clear:none;" >';
 	echo 			'<tr>';
 	echo 				'<th style="width:20%"><label for="', $wppl_meta_box['fields'][12]['id'], '">', $wppl_meta_box['fields'][12]['name'], '</label></th>';
    echo 				'<td><input type="text" name="',$wppl_meta_box['fields'][12]['id'], '" id="', $wppl_meta_box['fields'][12]['id'], '" class="', $wppl_meta_box['fields'][12]['id'], '" value="', get_post_meta($post->ID,'_wppl_lat',true), '" size="30" style="width:97%;background:lightgray;border:1px solid #aaa" disabled="disabled" />', '<br />', $field['desc'] , '</td>';
 	echo 			'</tr>';
 	echo 			'<tr>';
 	echo 				'<th style="width:20%"><label for="', $wppl_meta_box['fields'][13]['id'], '">', $wppl_meta_box['fields'][13]['name'], '</label></th>';
    echo 				'<td><input type="text" name="',$wppl_meta_box['fields'][13]['id'], '" id="', $wppl_meta_box['fields'][13]['id'], '" class="', $wppl_meta_box['fields'][13]['id'], '" value="', get_post_meta($post->ID,'_wppl_long',true), '" size="30" style="width:97%;background:lightgray;border:1px solid #aaa" disabled="disabled" />', '<br />', $field['desc'] , '</td>';
 	echo 			'</tr>';
 	echo 			'<tr>';
 	echo 				'<th style="width:20%"><label for="', $wppl_meta_box['fields'][14]['id'], '">', $wppl_meta_box['fields'][14]['name'], '</label></th>';
    echo 				'<td><input type="text" name="',$wppl_meta_box['fields'][14]['id'], '" id="', $wppl_meta_box['fields'][14]['id'], '" class="', $wppl_meta_box['fields'][14]['id'], '" value="', get_post_meta($post->ID,'_wppl_address',true), '" size="30" style="width:97%;background:lightgray;border:1px solid #aaa" disabled="disabled" />', '<br />', $field['desc'] , '</td>';
 	echo 			'</tr>';
 	echo 		'</table>';
 	echo 	'</div>';
 	
 	echo '</td></tr></table>';
 	
}

//////////////  EVERY NEW POST OR WHEN POST IS BEING UPDATED ////////////////////
//// CREATE MAP, LATITUDE, LONGITUDE AND SAVE DATA INTO OUR LOCATIONS TABLE ////
///  DATA SAVED - POST ID, POST TYPE, POST STATUS , POST TITLE , LATITUDE, LONGITUDE AND ADDRESS     ////
//////////////////////////////////////////////////////////////////////////////

add_action( 'save_post' , 'wppl_save_data'); 

function wppl_save_data($post_id) {
    global $wppl_meta_box, $wpdb, $wppl_feature_meta_box, $post;
    $plugins_options = get_option('wppl_plugins');
    $wppl_options = get_option('wppl_fields'); 
   
    // verify nonce //
    if (!wp_verify_nonce($_POST['wppl_meta_box_nonce'], basename(__FILE__))) {
        return;
    }
 
    // check autosave //
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
      
    // Check permissions //
  	if (in_array($_POST['post_type'], $wppl_options['address_fields'])) {
        if (!current_user_can('edit_page', $post->ID)) {
            return;
        }
    } else { 
    	if (!current_user_can('edit_post', $post->ID)) {
        return;
    	}
 	}
 	
 	if (!in_array($_POST['post_type'], $wppl_options['address_fields']))
	return;
	
	if ($plugins_options['featured_posts_on'] == 1) {
    	update_post_meta($post->ID,'_wppl_featured_post' , $_POST['_wppl_featured_post']);
    }
    if ($plugins_options['map_icons_on'] == 1) {
    	update_post_meta($post->ID,'_wppl_map_icon' , $_POST['_wppl_map_icon']);
    }  
    
    foreach ($wppl_meta_box['fields'] as $field) {	
        $old = get_post_meta($post->ID, $field['id'], true);
        $new = $_POST[$field['id']];
 
        if ($new && $new != $old) {
            update_post_meta($post->ID, $field['id'], $new);
        } elseif ('' == $new && $old) {
            delete_post_meta($post->ID, $field['id'], $old);
        }
    }
    
    if ( get_post_meta($post->ID,'_wppl_address',true) && get_post_meta($post->ID,'_wppl_long',true) && get_post_meta($post->ID,'_wppl_lat',true) ) {
    $wpdb->replace( $wpdb->prefix . 'places_locator', 
		array( 
				'post_id'		=> $post->ID, 
				'feature'  		=> $_POST['_wppl_featured_post'],
				'post_type' 	=> $_POST['post_type'],
				'post_title' 	=> $_POST['post_title'], 
				'post_status'	=> $_POST['post_status'], 
				'city' 			=> $_POST[$wppl_meta_box['fields'][2]['id']],
				'state' 		=> $_POST[$wppl_meta_box['fields'][3]['id']], 
				'zipcode' 		=> $_POST[$wppl_meta_box['fields'][4]['id']], 
				'country' 		=> $_POST[$wppl_meta_box['fields'][5]['id']],
				'phone' 		=> $_POST[$wppl_meta_box['fields'][6]['id']], 
				'fax' 			=> $_POST[$wppl_meta_box['fields'][7]['id']], 
				'email' 		=> $_POST[$wppl_meta_box['fields'][8]['id']], 
				'website' 		=> $_POST[$wppl_meta_box['fields'][9]['id']],
				'lat' 			=> $_POST[$wppl_meta_box['fields'][12]['id']], 
				'long' 			=> $_POST[$wppl_meta_box['fields'][13]['id']],	
				'address' 		=> $_POST[$wppl_meta_box['fields'][14]['id']],
				'map_icon'  	=> $_POST['_wppl_map_icon'],			
			)
		);
		
	} else {
		$wpdb->query(
		$wpdb->prepare( 
			"DELETE FROM " . $wpdb->prefix . "places_locator WHERE post_id=%d",$post->ID
			)
		);
	}
		
      
}
?>