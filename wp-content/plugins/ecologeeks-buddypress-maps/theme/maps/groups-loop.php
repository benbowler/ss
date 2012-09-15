<?php /* Querystring is set via AJAX in _inc/ajax.php - bp_dtheme_object_filter() */ ?>

<?php do_action( 'bp_maps_groups_before_loop' ) ?>

<?php //if ( bp_map_has_markers( bp_ajax_querystring( 'markers' ) ) ) : ?>


	<div id="groups-list" class="item-list">
	<?php 
	while ( bp_maps_groups() ) : bp_maps_the_groups();
		include(BP_MAPS_PLUGIN_DIR . "/theme/maps/markers-loop.php");
	endwhile; ?>
	</div>

	<?php do_action( 'bp_maps_groups_after_loop' ) ?>

<?php /*else: ?>

	<div id="message" class="info">
		<p><?php _e( 'There were no markers found.', 'buddypress' ) ?></p>
	</div>

<?php endif;*/ ?>