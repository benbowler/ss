
<?php
if ( bp_maps_map_is_visible() ) : 
	
	//JS for the Map
	bp_maps_map_js();

?>
	<div rel="<?php bp_maps_map_slug();?>" id="<?php bp_maps_map_slug();?>" class="<?php bp_maps_map_classes();?>">
	
		<div id="<?php bp_maps_map_slug();?>_content" style="width:<?php bp_maps_map_width();?>;height:<?php bp_maps_map_height();?>" class="<?php bp_maps_map_content_classes();?>"/>
			<?php bp_maps_map_static_img();?>
		</div>

	<div id="markers_tabs">
		<ul id="tabs">
		<?php 
		while ( bp_map_groups() ) :
		bp_map_the_group();
		if (bp_map_group_markers_list()) {
			?>
			<li><?php bp_map_the_group_checkbox();?><a href="#<?php bp_map_the_group_slug();?>"><?php bp_maps_group_name();?> (<?php bp_maps_group_saved_markers_count();?>)</a></li>
			<?php 
		}
		endwhile; ?>
		</ul>
		<?php 
		
		while ( bp_map_groups() ) : ?>
		
		<?php bp_map_the_group();
		if (bp_map_group_markers_list()) {?>
			<div id="<?php bp_map_the_group_slug();?>" class="<?php bp_maps_group_classes();?>" rel="<?php bp_map_the_group_index();?>">
			<?php if (bp_map_group_editable()) {?>
				
				<span class="bp_maps_map_add_marker<?php if(!bp_maps_group_can_add_marker())echo" strike";?>"><a href="#"><?php _e('Add Marker','bp-maps');?></a>
					<?php //bp_map_print();?>
					<?php 
					if (bp_map_get_group_max_markers()>1) {
						echo'(';
						printf(__('max: %d','bp-maps'),bp_map_get_group_max_markers());
						echo')';
					}
					?>
				</span>
			<?php }
				
				do_action( 'bp_maps_groups_before_loop' );?>

				<div id="groups-list" class="item-list">
				<?php include(BP_MAPS_PLUGIN_DIR . "/theme/maps/markers-loop.php");?>
				
				</div>

				<?php do_action( 'bp_maps_groups_after_loop' );?>
			</div>
		<?php }
		endwhile; ?>
			<?php if (bp_maps_map_privacy()) _e('Some markers on this map may not be accurate (privacy settings)');?>
		</div>
	</div>
<?php
	

else:
?>
	<div id="message" class="info">
		<p><?php _e( 'This map is not visible', 'buddypress' ) ?></p>
	</div>
<?php
endif; ?>