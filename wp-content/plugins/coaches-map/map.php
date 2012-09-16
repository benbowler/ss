<?php

global $wpdb;

$users = get_users(array(
  'role' => 'gold'
  ));

$googleMap = '';

foreach ($users as &$user) {
      $user->data = get_userdata($user->ID);

      //$user->location = $wpdb->get_results("SELECT * FROM wp_bp_xprofile_data WHERE user_id = '" . $user->ID . "'");
      $lat = $wpdb->get_results("SELECT * FROM wp_bp_xprofile_data WHERE user_id = '" . $user->ID . "' AND field_id = 9");
      $long = $wpdb->get_results("SELECT * FROM wp_bp_xprofile_data WHERE user_id = '" . $user->ID . "' AND field_id = 10");

      // $user->coords = $coords;

      $user->lat = $lat[0]->value;
      $user->long = $long[0]->value;

      if($user->lat) {
        $googleMap .= "['{$user->display_name}', {$user->lat}, {$user->long}, {$user->ID}],";
      }
}
//var_dump($users);
var_dump($googleMap);

?>
<div id="map" style="width: 100%; height: 600px;"></div>

 <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCQ-07W-Dl4MYDwj921fpfQNiokVNLeNoE&sensor=true"></script>
  <script type="text/javascript">
  $(document).ready(function() {
    var locations = [
      <?php echo $googleMap; ?>
    ];

    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 2,
      center: new google.maps.LatLng(0, 0),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow();

    var marker, i;

    for (i = 0; i < locations.length; i++) {  
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
        map: map
      });

      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(locations[i][0]);
          infowindow.open(map, marker);
        }
      })(marker, i));
    }
  });
  </script>