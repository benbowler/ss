<?php

global $wpdb;

$users = get_users(); //array( 'role' => 'gold', 'number' => 2 )

$googleMap = '';

foreach ($users as &$user) {
      $user->data = get_userdata($user->ID);

      //print_r(var_dump($user->data));

      $city = $wpdb->get_results("SELECT * FROM wp_bp_xprofile_data WHERE user_id = '" . $user->ID . "' AND field_id = 5");
      $user->city = $city[0]->value;

      $lat = $wpdb->get_results("SELECT * FROM wp_bp_xprofile_data WHERE user_id = '" . $user->ID . "' AND field_id = 9");
      $long = $wpdb->get_results("SELECT * FROM wp_bp_xprofile_data WHERE user_id = '" . $user->ID . "' AND field_id = 10");

      $user->lat = $lat[0]->value;
      $user->long = $long[0]->value;

      $user->role = $user->data->roles[0];

      if($user->lat && ($user->role == 'gold' || $user->role == 'silver' || $user->role == 'ysc_trainee')) {

        $user->avatar = get_avatar( $user->ID, 64 );
        //$avatar = addslashes($user->avatar);

        $popup = " {$user->avatar}<p style=\"display:block;float:right;height:80px;width:140px;\"><strong>{$user->display_name}</strong><br />{$user->city}<br /><a href=\"" . get_bloginfo('siteurl') . "/members/{$user->user_login}\">Contact Coach.</a><p>";

        $googleMap .= "['$popup', {$user->lat}, {$user->long}, {$user->ID}, '{$user->role}'],";
      }
}
//var_dump($users);
//var_dump($googleMap);

?>
<div id="map_canvas" style="width: 100%; height: 600px; display: block;"></div>

 <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCQ-07W-Dl4MYDwj921fpfQNiokVNLeNoE&sensor=true"></script>
  <script type="text/javascript">
  $(document).ready(function() {
    var locations = [
      <?php echo $googleMap; ?>
    ];

    var map = new google.maps.Map(document.getElementById('map_canvas'), {
      zoom: 2,
      center: new google.maps.LatLng(0, 0),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow();

    var marker, i;

    for (i = 0; i < locations.length; i++) {  
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
        map: map,
        icon: '<?php echo get_bloginfo('template_directory'); ?>/images/pins/' + locations[i][4] + '.png'
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