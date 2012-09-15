<?php
/*
Plugin Name: Coaches Map
Plugin URI: http://www.reallyeffective.co.uk/knowledge-base
Description: DEMO List Posts
Version: 0.1 BETA
Author: Paul McKnight
Author URI: http://www.reallyeffective.co.uk
*/

/*
Coaches Map (Wordpress Plugin)
Copyright (C) 2009 Paul McKnight
Contact me at http://www.reallyeffective.co.uk

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
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
  coaches_map_generate_coords();
  return require_once("map.php");
}

// Cron
//wp_get_schedule(, '');

function coaches_map_generate_coords() {
  global $wpdb;

  $users = get_users(array(
  'role' => 'administrator'
  ));

  foreach ($users as &$user) {
    $user->data = get_userdata($user->ID);

    //$user->location = $wpdb->get_results("SELECT * FROM wp_bp_xprofile_data WHERE user_id = '" . $user->ID . "'");
    $city = $wpdb->get_results("SELECT * FROM wp_bp_xprofile_data WHERE user_id = '" . $user->ID . "' AND field_id = 5");
    $country = $wpdb->get_results("SELECT * FROM wp_bp_xprofile_data WHERE user_id = '" . $user->ID . "' AND field_id = 2");
    //$lat = $wpdb->get_results("SELECT * FROM wp_bp_xprofile_data WHERE user_id = '" . $user->ID . "' AND field_id = 9");
    //$long = $wpdb->get_results("SELECT * FROM wp_bp_xprofile_data WHERE user_id = '" . $user->ID . "' AND field_id = 10");

    // $user->coords = $coords;

    $user->city = $city[0]->value;
    $user->country = $country[0]->value;

    if($user->city) {
      $location = "{$user->city}, {$user->country}";
      echo file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($location) . '&sensor=true');

    }
  }
}
