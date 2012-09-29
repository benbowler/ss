<?php
/*
Plugin Name: Coaches Map
Plugin URI: http://benbowler.com
Description: Geocode and display coaches locations in map.
Version: 0.1 BETA
Author: Ben Bowler
Author URI: http://benbowler.com
*/

/*
Coaches Map (Wordpress Plugin)
*/

//tell wordpress to register the demolistposts shortcode
add_shortcode("coaches-map", "coaches_map_handler");

function coaches_map_handler() {
  //run function that actually does the work of the plugin
  $demolph_output = coaches_map_function();
  //send back text to replace shortcode in post
  return $demolph_output;
}

function coaches_map_function() {
  return require_once("map.php");
}

/*
 *  Shedule to geocode users locations
 *
 */
add_action('generate_coords', 'coaches_map_generate_coords');

function my_activation() {
  if ( !wp_next_scheduled( 'generate_coords' ) ) {
    wp_schedule_event( current_time( 'timestamp' ), 'daily', 'generate_coords');
  }
}
add_action('wp', 'my_activation');

/*
 * Function to geocode users locations
 *
 */
function coaches_map_generate_coords() {
  global $wpdb;

  $users = get_users();

  // Change the line below to your timezone!
  //date_default_timezone_set('Australia/Melbourne');
  $date = date('m/d/Y h:i:s a', time());
  $return = "Geocoding: " . $date . " \n";

  foreach ($users as &$user) {

    // print_r($user->display_name);
    $user->data = get_userdata($user->ID);

    $city = $wpdb->get_results("SELECT * FROM wp_bp_xprofile_data WHERE user_id = '" . $user->ID . "' AND field_id = 5");
    $country = $wpdb->get_results("SELECT * FROM wp_bp_xprofile_data WHERE user_id = '" . $user->ID . "' AND field_id = 2");

    $user->city = $city[0]->value;
    $user->country = $country[0]->value;

    if($user->city) {

      $location = "{$user->city}, {$user->country}";
      $url = 'http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($location) . '&sensor=true';

      /* curl */
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      $response = curl_exec($ch);
      curl_close($ch);
      $geocode = json_decode($response);

      $lat = $geocode->results[0]->geometry->location->lat;
      $long = $geocode->results[0]->geometry->location->lng;

      // Delete
      $delete = $wpdb->query("DELETE FROM wp_bp_xprofile_data WHERE user_id = '" . $user->ID . "' AND (field_id = 9 OR field_id = 10)");
      // Update lat lon in db
      $insert_lat = $wpdb->query("INSERT INTO wp_bp_xprofile_data (user_id,field_id,value) VALUES ('" . $user->ID . "',9,'" . $lat . "')");
      $insert_long = $wpdb->query("INSERT INTO wp_bp_xprofile_data (user_id,field_id,value) VALUES ('" . $user->ID . "',10,'" . $long . "')");

      $return .= "{$user->display_name} : $lat $long \n ";
    }
  }

  wp_mail('cron@benbowler.com', 'YSS User location generated', $return);
}



