<?php
/*
Plugin Name: GEO my WP
Plugin URI: http://www.geomywp.com 
Description: Add location to any post types, pages or members (using Buddypress) and create an advance proximity search forms.
Author: Eyal Fitoussi
Version: 1.5.2
Author URI: http://www.geomywp.com 

*/

/*
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//// CREATE DATABASE'S TABLE ////
global $wppl_on, $wppl_exist, $wppl_options, $plugins_options;
$plugins_options = get_option('wppl_plugins');
$wppl_options = get_option('wppl_fields');

//// LOAD ADMIN PAGES ONLY ////
if (is_admin() ) {
	include_once 'admin/admin-settings.php';
	//// UPLOAD METABOXES ONLY ON NECESSARY PAGES ////
	if (in_array( $pagenow, array( 'post-new.php', 'post.php','page.php','page-new' ) ) ) {
		include_once 'admin/metaboxes.php'; 
	}

	if ($wppl_on['friends']) include_once 'plugins/wppl-friends-locator/admin-functions.php';
} 
	
if ($plugins_options['friends_locator_on']) include_once 'plugins/wppl-friends-locator/wppl-friends-locator.php';
	
//// LOAD FILES ONLY ON FRONT END IF BUDDYPRESS ON ////
if (!is_admin() && $plugins_options['friends_locator_on'] ) {
	include_once 'plugins/wppl-friends-locator/functions.php';
	include_once 'plugins/wppl-friends-locator/template-functions.php';
}
	
//// INCLUDE WIDGETS ////	
include_once 'admin/widgets.php';
if ($plugins_options['friends_locator_on']) include_once 'plugins/wppl-friends-locator/widgets.php';

//// LOAD FILES ONLY ON FRONT END ////
if (!is_admin()) {
	include_once 'get-places.php';
	include_once 'template-functions.php';
	include_once 'functions.php';
	include_once 'shortcodes.php';
}
 
global $places_locator_db_version;
$places_locator_db_version = "1.0";

function places_locator_install() {
   global $wpdb;
   global $places_locator_db_version;

   $table_name = $wpdb->prefix . "places_locator";
      
   $sql = "CREATE TABLE $table_name (
  `post_id` 	bigint(30) NOT NULL,
  `feature`		varchar(255) NOT NULL ,
  `post_status` varchar(255) NOT NULL ,
  `post_type` 	varchar(255) NOT NULL , 
  `lat` 		varchar(255) NOT NULL ,
  `long` 		varchar(255) NOT NULL ,
  `post_title` 	varchar(255) NOT NULL ,
  `city` 		varchar(255) NOT NULL ,
  `state` 		varchar(255) NOT NULL ,
  `zipcode` 	varchar(255) NOT NULL ,
  `country` 	varchar(255) NOT NULL ,
  `address` 	varchar(255) NOT NULL ,
  `phone` 		varchar(255) NOT NULL ,
  `fax` 		varchar(255) NOT NULL ,
  `email` 		varchar(255) NOT NULL ,
  `website` 	varchar(255) NOT NULL ,
  `map_icon`	varchar(255) NOT NULL ,
  UNIQUE KEY id (post_id)
    );";
    
   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql);
   add_option("places_locator_db_version", $places_locator_db_version);
}
register_activation_hook(__FILE__,'places_locator_install');

	
//// CREATE MEMBERS'S TABLE ////
global $wppl_friends_locator_db_version;
$wppl_friends_locator_db_version = "1.0";

function wppl_friends_locator_install() {
	global $wpdb;
 	global $wppl_friends_locator_db_version;
	
	$table_name = $wpdb->prefix . "wppl_friends_locator";
      
 	  $sql = "CREATE TABLE $table_name (
  	`member_id` 	bigint(30) NOT NULL,
  	`lat` 			varchar(255) NOT NULL ,
  	`long` 			varchar(255) NOT NULL ,
  	`city` 			varchar(255) NOT NULL ,
  	`state` 		varchar(255) NOT NULL ,
  	`zipcode` 		varchar(255) NOT NULL ,
  	`country` 		varchar(255) NOT NULL ,
  	`address` 		varchar(255) NOT NULL ,
  	`map_icon` 		varchar(255) NOT NULL ,
  	UNIQUE KEY id (member_id)
    );";
    
   	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   	dbDelta($sql);
   	add_option("wppl_friends_locator_db_version", $wppl_friends_locator_db_version);
}
register_activation_hook(__FILE__,'wppl_friends_locator_install');

// REGISTER STYLESHEET AND JAVASCRIPTS IN THE FRONT END//
function wppl_register_scripts() {
	global $wppl_options, $plugins_options;
	
	wp_enqueue_style( 'wppl-style', plugins_url('css/style.css', __FILE__) );
	wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css');
	wp_enqueue_script( 'wppl-google-api-key', 'http://maps.googleapis.com/maps/api/js?key='.$wppl_options['google_api'].'&sensor=false&region='.$wppl_options['country_code'],array(),false); 
    wp_register_script( 'wppl-map',  plugins_url('js/map.js', __FILE__),array(),false,true );
   //wp_register_script( 'wppl-small-maps',  plugins_url('js/single-maps.js', __FILE__),array(),false,true );
    wp_register_script( 'wppl-infobox',  plugins_url('js/infobox.js', __FILE__),array(),false,true );
    wp_register_script( 'wppl-sl-map',  plugins_url('js/single-location-map.js', __FILE__),array(),false,true );
    wp_enqueue_script( 'wppl-locate',  plugins_url('js/locate.js', __FILE__),array(),false,true );
    wp_enqueue_script('thickbox', null,  array('jquery'));    	
	
	/*if ($options['front_autocomplete']) {
		wp_enqueue_script( 'jquery');
    	wp_enqueue_script( 'jquery-ui-autocomplete');
   	 	wp_enqueue_script( 'wppl-autocomplete',  plugins_url('js/autocomplete-front.js', __FILE__),array(),true );
   	 } */
   	
   	/// friends locator map ////
   	if ($plugins_options['friends_locator_on']) {
   		wp_register_script( 'wppl-friends-map',  plugins_url('plugins/wppl-friends-locator/js/main-map.js', __FILE__),array(),false,true );
   		wp_register_script( 'wppl-member-map',  plugins_url('plugins/wppl-friends-locator/js/single-member-map.js', __FILE__),array(),false,true );
   		wp_register_style( 'wppl-bp-style',  plugins_url('plugins/wppl-friends-locator/css/bp-style.css', __FILE__),array(),false,false );
   	}
}
add_action( 'wp_enqueue_scripts', 'wppl_register_scripts' );

//// Include javascripts, jquery and styles only in choosen post types admin area ////
function wppl_admin_page() {
	global $wppl_options, $post_type;
    if( ( $wppl_options['address_fields']) && (in_array($post_type, $wppl_options['address_fields']) ) ) {
    	wp_enqueue_style( 'wppl-admin-style', plugins_url('admin/css/style-admin.css', __FILE__),array(),false,false);
    	wp_enqueue_script( 'wppl-google-api-key', 'http://maps.googleapis.com/maps/api/js?key='.$wppl_options['google_api'].'&sensor=false&region='.$wppl_options['country_code'],array(),false); 
    	wp_enqueue_script( 'wppl-locate-admin',plugins_url('js/locate-admin.js', __FILE__),array(),false,true);
    	wp_enqueue_script( 'jquery');
    	wp_enqueue_script( 'jquery-ui-autocomplete');
    	wp_enqueue_script( 'jquery-address-picker', plugins_url('js/jquery.ui.addresspicker.js', __FILE__),array(),false,true);	
	}
}
add_action( 'admin_print_scripts-post-new.php', 'wppl_admin_page', 11 );
add_action( 'admin_print_scripts-post.php', 'wppl_admin_page', 11 );

function wppl_theme_color() {
	global $wppl_options;
    if($wppl_options['use_theme_color']) {
    	$color = $wppl_options['theme_color'];
   		echo "<style> #wppl-output-wrapper .wppl-pagination-wrapper h2.wppl-h2 { color: $color; } </style>"; 
    	echo "<style> .wppl-single-result .wppl-h3 a:link, .wppl-single-result .wppl-h3 a:visited { color: $color; } </style>";
    	echo "<style> .wppl-single-result .wppl-address { color: $color; } </style>";
    	echo "<style> .wppl-single-result  .wppl-get-directions a:link, .wppl-single-result .wppl-get-directions a:visited { color: $color; } </style>";
    	echo "<style>  #wppl-output-wrapper .wppl-website a:link, #wppl-output-wrapper .wppl-website a:visited  { color: $color; } </style>";
    	echo "<style> .near-title-holder a:link, .near-title-holder a:visited  { color: $color; } </style>";
    	echo "<style> #wppl-output-wrapper .wppl-pagination a:link, #wppl-output-wrapper .wppl-pagination a:visited { color: $color; } </style>";
    	echo "<style> .wppl-excerpt a:link, .wppl-excerpt a:visited { color: $color; } </style>";
    	echo "<style> .wppl-single-wrapper div span { color: $color; } </style>";
    	echo "<style> .wppl-extra-info-wrapper a:link, .wppl-extra-info-wrapper a:visited { color: $color; } </style>";
    	echo "<style> .popup-additional-info a:link, .popup-additional-info a:visited { color: $color; } </style>";
    	echo "<style> .wppl-slider-single-result .wppl-title-holder a:link, .wppl-slider-single-result .wppl-title-holder a:visited { color: $color; } </style>";
    	echo "<style> .wppl-featured-single-result .wppl-title-holder a:link, .wppl-featured-single-result .wppl-title-holder a:visited { color: $color; } </style>";
	}
	echo '<script> autoLocate= '.json_encode($wppl_options['auto_locate']),'; </script>';
	echo '<script> locateMessage= '.json_encode($wppl_options['locate_message']),'; </script>';
}
add_action( 'wp_head', 'wppl_theme_color' );

?>