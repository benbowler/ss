<?php global $single_result;
/////// POSTS LOOP - DEFAULT /////////
foreach ($posts_within as $single_result) {
	$post = get_post($single_result->post_id); ?>
	
	<div class="wppl-single-result" <?php echo 'style="width:'.$single_result_width.'px; height:'.$single_result_height.'px"'; ?>>
	
		<?php wppl_title(); ?>
		
		<?php wppl_by_radius () ?>
		
		<?php wppl_thumbnail(); ?>
		
		<?php wppl_excerpt(); ?>
		
		<?php wppl_taxonomies(); ?>
		
    	<div class="clear"></div>
    	<div class="wppl-info">
    		
    			<?php wppl_additional_info(); ?>	
    		
    			<?php wppl_address(); ?>
    		
    			<?php distance_between(); ?>
    			
    			<?php wppl_directions(); ?>
    		
    		
    	</div> <!-- info -->
    </div> <!--  single- wrapper -->
    <div class="clear"></div>     
<?php $mc++; $pc++; } ?>
	
