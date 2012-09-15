<?php global $single_result;
/////// POSTS LOOP - DEFAULT /////////
foreach ($posts_within as $single_result) {
	$post = get_post($single_result->post_id); ?>
	
	<div class="wppl-single-result" <?php echo 'style="width:'.$single_result_width.'px; height:'.$single_result_height.'px"'; ?>>
	
		<div class="wppl-title-holder">
			<h3 class="wppl-h3">
				<a href="<?php the_permalink(); ?>/?address=<?php echo $org_address; ?>"><?php echo $pc; ?>) <?php the_title(); ?></a>
				<?php if ($lat && $long) { ?><span class="radius-dis">(<?php echo $single_result->distance . " " . $unit_a['name']; ?>)</span> <?php } ?>
			</h3>
		</div><!-- title holder -->
		
		<?php wppl_thumbnail(); ?>
		
		<?php wppl_excerpt(); ?>
		
		<?php wppl_taxonomies(); ?>
		
    	<div class="clear"></div>
    	<div class="wppl-info">
    		<div class="wppl-info-left">
    			<?php wppl_additional_info(); ?>	
    		</div><!-- info left -->
    		<div class="wppl-info-right">
    			<?php wppl_address(); ?>
    		
    			<?php distance_between(); ?>
    			
    			<?php wppl_directions(); ?>
    		
    		</div><!-- info right -->
    	</div> <!-- info -->
    </div> <!--  single- wrapper -->
    <div class="clear"></div>     
<?php $mc++; $pc++; } ?>
	
