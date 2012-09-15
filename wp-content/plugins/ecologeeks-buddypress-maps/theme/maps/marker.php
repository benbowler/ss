<li>
	<?php //bp_maps_map_marker_js();?>
	<div id="<?php bp_maps_marker_name();?>" rel="<?php bp_maps_marker_index();?>" class="<?php bp_maps_marker_classes();?>">
		<form class="standard-form">
			<div class="item-title">
				<span class="bp_maps_group_icon"><?php bp_map_the_group_icon();?></span>
				<span><?php bp_maps_markerslist_marker_title();?></span>
				<?php if ((bp_map_group_enable_desc()) && (bp_map_marker_editable())) {?>
					<label for="<?php bp_maps_marker_name();?>_title"><?php _e('Title','bp-maps');?></label>
					<input class="input" type="text" name="<?php bp_maps_marker_name();?>_title" id="<?php bp_maps_marker_name();?>_title" value="<?php bp_maps_marker_title();?>">
				<?php }?>
			</div>
			<?php if (bp_map_marker_editable()) {?>
			<?php
			/*
			$map_privacy_levels=bp_maps_map_privacy_levels();
			if ($map_privacy_levels) {?>
			<div class="privacy">
				<label for="<?php bp_maps_get_marker_name();?>'_address"><?php _e('Privacy','bp-maps');?></label>
				<small><?php _e('if you are concerned about privacy, check here which users will see an approximate location and which ones will not be able to see your marker','bp-maps');?></small>
				
				<?php
				foreach ($map_privacy_levels as $mlevel=>$mlsettings) {
					$row_title.='<th>'.$mlsettings['name'].'</th>';
					$cols[]=$mlsettings['name'];
					$rows[]=$mlsettings['can_view'];
						
				}?>
				<?php
				$row_title='<tr>'.$row_title.'</tr>';
				?>
				<table>
					<?php echo $row_title;?>

				</table>
			</div>
			<?php }	
			*/
			
			}
			?>
		
			<?php if (bp_map_group_enable_desc()){?>
			<div class="content">
				<span><?php bp_maps_markerslist_marker_content();?></span>
				<?php if (bp_map_marker_editable()) {?>
					<label for="<?php bp_maps_marker_name();?>_content"/><?php _e('Description','bp-maps');?></label>
					<input type="text" name="<?php bp_maps_marker_name();?>_content" id="<?php bp_maps_marker_name();?>_content" value="<?php bp_maps_marker_content();?>">
				<?php }?>
			</div>
			<?php }?>
			<div class="address">
				<span><?php bp_maps_markerslist_marker_address();?></span>
				<?php if (bp_map_marker_editable()) {?>
					<label for="<?php bp_maps_marker_name();?>_address"><?php _e('Address','bp-maps');?></label>
					<input class="input" type="text" name="<?php bp_maps_marker_name();?>_address" id="<?php bp_maps_marker_name();?>_address" value="<?php bp_maps_marker_address();?>">
				<?php }?>
			</div>
			<a class="button save" href="#"><?php _e('Save Marker','bp-maps');?></a>
			<a class="button delete" href="#"><?php _e('Delete Marker','bp-maps');?></a>
			<div id="message" class="info">
				<p>
					<?php 
					if (bp_map_group_enable_desc()){
						$message_info=__('Enter a title, a description and the adress to locate; then click "save marker".','bp-maps');
					}else {
						$message_info=__('Enter the adress to locate; then click "save marker".','bp-maps');
					}
					echo $message_info;
					?>
				</p>
			</div>
			<?php if (bp_map_marker_editable()) {?>
				<div class="action">
					<?php echo wp_nonce_field(bp_maps_get_map_slug().'_marker'.bp_maps_get_marker_index()."_save",bp_maps_get_map_slug().'_marker'.bp_maps_get_marker_index()."_nonce",true,false);?>
					<input type="hidden" name="latitude" value="">
					<input type="hidden" name="longitude" value="">
					<a class="button edit" href="#"><?php _e('Edit');?></a>
					<a class="button close" href="#"><?php _e('Close');?></a>
				</div>
			<?php }?>
		</form>
		<div class="item-meta">
			<span class="author"><?php bp_maps_marker_author()?></span>
			<span class="activity"><?php bp_maps_marker_last_active() ?></span>
		</div>
		<?php do_action( 'bp_maps_markerslist_marker_item' ) ?>
	</div>
</li>