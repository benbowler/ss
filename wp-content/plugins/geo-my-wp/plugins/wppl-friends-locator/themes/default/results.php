 <?php global $bp, $user_data, $single_user, $total_fields; $avatars = array(); ?>
	
	<ul id="wppl-members-list" class="wppl-item-list" role="main">
		<?php foreach($user_ids as $single_user) { 
			$user_data = new BP_Core_User($single_user->member_id); $avatars[$single_user->member_id] = array($user_data->avatar,$user_data->fullname); ?>
	
		<li <?php echo 'style="width:'.$single_result_width.'px; height:'.$single_result_height.'px"'; ?>>
			<?php wppl_bp_thumbnail(); ?>
				
			<div class="wppl-item">
				<?php wppl_bp_by_radius(); ?>
				
				<?php wppl_bp_title(); ?>
				
				<?php wppl_bp_last_active(); ?>
				
			</div>
				
			<?php wppl_bp_action_btn(); ?>
			
			<div class="bp-distance-wrapper">
					<?php wppl_bp_address(); ?>
					<?php wppl_bp_directions(); ?>
					<?php bp_distance_between(); ?>
			</div>
			<div class="clear"></div>
		</li>
	<?php $pc++;  } ?>
	</ul>
	<?php bp_member_hidden_fields(); ?>
	
