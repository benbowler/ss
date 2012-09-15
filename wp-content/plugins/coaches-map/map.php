<?php

global $wpdb;

$users = get_users(array(
  'role' => 'administrator'
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

 <script type="text/javascript"
      src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCQ-07W-Dl4MYDwj921fpfQNiokVNLeNoE&sensor=true">
    </script>
  <script type="text/javascript">
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
  </script>



<?php


/*

  $user->city = $wpdb->get_results("SELECT * FROM wp_bp_xprofile_data WHERE user_id = '" . $user->ID . "' AND field_id = 9");
  $user->country = $wpdb->get_results("SELECT * FROM wp_bp_xprofile_data WHERE user_id = '" . $user->ID . "' AND field_id = 10");

*/

/*

  <?php 

<?php do_action( 'bp_before_members_loop' ); ?>

<?php if ( bp_has_members( bp_ajax_querystring( 'members' ) ) ) : ?>

  <div id="pag-top" class="pagination">

    <div class="pag-count" id="member-dir-count-top">

      <?php bp_members_pagination_count(); ?>

    </div>

    <div class="pagination-links" id="member-dir-pag-top">

      <?php bp_members_pagination_links(); ?>

    </div>

  </div>

  <?php do_action( 'bp_before_directory_members_list' ); ?>

  <ul id="members-list" class="item-list" role="main">

  <?php while ( bp_members() ) : bp_the_member(); ?>

    <li>
      <div class="item-avatar">
        <a href="<?php bp_member_permalink(); ?>"><?php bp_member_avatar(); ?></a>
      </div>

      <div class="item">
        <div class="item-title">
          <a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>

          <?php if ( bp_get_member_latest_update() ) : ?>

            <span class="update"> <?php bp_member_latest_update(); ?></span>

          <?php endif; ?>

        </div>

        <div class="item-meta"><span class="activity"><?php bp_member_last_active(); ?></span></div>

        <?php do_action( 'bp_directory_members_item' ); ?>

        <?php
         /***
          * If you want to show specific profile fields here you can,
          * but it'll add an extra query for each member in the loop
          * (only one regardless of the number of fields you show):
          *
          * bp_member_profile_data( 'field=the field name' );
          *
        ?>
      </div>

      <div class="action">

        <?php do_action( 'bp_directory_members_actions' ); ?>

      </div>

      <div class="clear"></div>
    </li>

  <?php endwhile; ?>

  </ul>

  <?php do_action( 'bp_after_directory_members_list' ); ?>

  <?php bp_member_hidden_fields(); ?>

  <div id="pag-bottom" class="pagination">

    <div class="pag-count" id="member-dir-count-bottom">

      <?php bp_members_pagination_count(); ?>

    </div>

    <div class="pagination-links" id="member-dir-pag-bottom">

      <?php bp_members_pagination_links(); ?>

    </div>

  </div>

<?php else: ?>

  <div id="message" class="info">
    <p><?php _e( "Sorry, no members were found.", 'buddypress' ); ?></p>
  </div>

<?php endif; ?>

<?php do_action( 'bp_after_members_loop' ); ?>*/

