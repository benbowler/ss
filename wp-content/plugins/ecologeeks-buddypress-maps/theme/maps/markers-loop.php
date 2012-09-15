<?php /* Querystring is set via AJAX in _inc/ajax.php - bp_dtheme_object_filter() */ ?>

<?php do_action( 'bp_maps_markers_before_loop' ) ?>

	<div class="pagination">

		<div class="pag-count" id="marker-dir-count">
			<?php //bp_maps_markers_pagination_count() ?>
		</div>

		<div class="pagination-links" id="marker-dir-pag">
			<?php //bp_maps_markers_pagination_links() ?>
		</div>

	</div>

	<ul id="markers-list" class="item-list">
	<?php 
	while ( bp_markers() ) : bp_the_marker();
		include(BP_MAPS_PLUGIN_DIR . "/theme/maps/marker.php");
	endwhile; ?>
	</ul>
	<input class="markers_ids" type="hidden" id="<?php bp_maps_map_slug();?>_markers_ids" name="<?php bp_maps_map_slug();?>_markers_ids" value="<?php echo $input_marker_ids_str;?>">

	<?php do_action( 'bp_maps_markers_after_loop' ) ?>

<?php /*else: ?>

	<div id="message" class="info">
		<p><?php _e( 'There were no markers found.', 'buddypress' ) ?></p>
	</div>

<?php endif;*/ ?>