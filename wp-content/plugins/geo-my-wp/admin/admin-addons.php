<table class="widefat fixed">
			<thead>
				<th>
					<div class="wppl-settings">
						<span>
							<span><h3><?php echo _e('Plugins','wppl'); ?></h3></span>
							<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
						</span>
						<div class="clear"></div>
						<span class="wppl-help-message">
						<p style="font-size: 13px;font-weight: normal;">
							<?php _e('The plugins add some extra features to your site. You can choose to turn on or off any of them. 
							Turn on only the plugins that you are going to use for a better performance.', 'wppl'); ?>
						</p></span>
					</div>
				</th>	
			<th></th>
			</thead>
			<tr>
				<td>
				<div class="wppl-settings">
					<p>
					<span>
						<span><?php echo _e('Friends Finder (<a href="'.$site_url.'">Buddypress</a> must be installed and activated):','wppl'); ?></span>
						<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
					</span>
					<div class="clear"></div>
					<span class="wppl-help-message">
						<?php _e('This feature will allow your members add their location in their profile page 
						and for you to create a members search form. Using that , members will be able to search to other members based on 
						a given address and distance. ', 'wppl'); ?>
					</span>
					</p>
				</div>
				</td>		
				<td>
					<p style="color:brown; font-size:12px;"><input name="wppl_plugins[friends_locator_on] " type="checkbox" value="1" <?php if ($plugins_options['friends_locator_on']) echo "checked"; if (!$wppl_exist['flo']) echo ' disabled="disabled" '; ?>/></p>
				</td>
			</tr>
			<tr>
				<td>
				<div class="wppl-settings">
					<p>
					<span>
						<span><?php echo _e('Near Locations Widget :', 'wppl'); ?></span>
						<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
					</span>
					<div class="clear"></div>
					<span class="wppl-help-message">
						<?php _e('Add a widget that will show posts based on the user location in the sidebar. The widget will look for the user location if exist
						and will display members based on that. If user location is not exist or if no members found within the chosen distance then 
						random members will be displayed.', 'wppl'); ?>
					</span>
					</p>
				</div>
				</td>
				<td>
					<p style="color:brown; font-size:12px;"><input name="wppl_plugins[near_location_on] " type="checkbox" value="1" <?php if ($plugins_options['near_location_on']) echo "checked"; if (!$wppl_exist['nlo']) echo ' disabled="disabled" '; ?>/> <?php if (!$wppl_exist['nlo']) echo $pro_message; ?></p>
				</td>
			</tr>
			<tr>
				<td>
				<div class="wppl-settings">
					<p>
					<span>
						<span><?php echo _e('Featured Posts :'); ?></span>
						<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
					</span>
					<div class="clear"></div>
					<span class="wppl-help-message">
						<?php _e('This feature will add a "featured post" checkbox to the "new/update" post page. This checkbox will be available only through admin access.
						using that you will be able to mark certain posts (maybe from paid members) as featured post. Then when creating a search form
						you will also have two checkboxes available. One that will let you display random featured post on top of the search results. And another checkbox that will let you mark the featured posts within the results. ', 'wppl'); ?>
					</span>
					</p>
				</div>
				</td>
				<td>
					<p style="color:brown; font-size:12px;"><input name="wppl_plugins[featured_posts_on] " type="checkbox" value="1" <?php if ($plugins_options['featured_posts_on']) echo "checked"; if (!$wppl_exist['fpo']) echo ' disabled="disabled" '; ?>/> <?php if (!$wppl_exist['fpo']) echo $pro_message; ?></p>
				</td>
			</tr>
			<tr>
				<td>
				<div class="wppl-settings">
					<p>
					<span>
						<span><?php echo _e('Per Post Map Icon :'); ?></span>
						<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
					</span>
					<div class="clear"></div>
					<span class="wppl-help-message">
						<?php _e('This feature will add the map icons to the "new/update post" page. Using that you can let your members or yourself choose 
						the icon that will display their location on the map. That is instead of having all results using the same icon. ', 'wppl'); ?>
					</span>
					</p>
				</div>
				</td>
				<td>
					<p style="color:brown; font-size:12px;"><input name="wppl_plugins[map_icons_on] " type="checkbox" value="1" <?php if ($plugins_options['map_icons_on']) echo "checked"; if (!$wppl_exist['mio']) echo ' disabled="disabled" '; ?>/> <?php if (!$wppl_exist['mio']) echo $pro_message; ?></p>
				</td>
			</tr>
			<tr>
				<td>
					<p><?php echo _e('Featured Posts Scroller :'); ?></p>
				</td>
				<td>
					<p style="color:brown; font-size:12px;"><input name="wppl_plugins[posts_scroller_on] " type="checkbox" value="1" <?php if ($plugins_options['posts_scroller_on']) echo "checked"; if (!$wppl_exist['fps']) echo  ' disabled="disabled" '; ?>/> <?php if (!$wppl_exist['fps']) echo '<span style="color:brown"> Under development </span>'; ?></p>
				</td>
			</tr>
			<tr>
				<td>
					<p><input name="Submit" class="wppl-save-btn" type="submit" value="<?php _e('Save Changes'); ?>" /></p>
				</td>
				<td></td>
			</tr>
			<thead>
				<th>
					<div class="wppl-settings">
						<span>
							<span><h3><?php echo _e('Themes','wppl'); ?></h3></span>
							<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
						</span>
						<div class="clear"></div>
						<span class="wppl-help-message">
						<p style="font-size: 13px;font-weight: normal;">
							<?php _e('The featured themes will let you choose different styling to the results pages.', 'wppl'); ?>
						</p></span>
					</div>
				</th>	
				<th></th>
			</thead>
			<tr>
				<td>
					<p><?php echo _e('Restaurant Template:'); ?></p>
				</td>
				<td>
					<p style="color:brown; font-size:12px;"><input name="wppl_plugins[restaurant_on] " type="checkbox" value="1" <?php if ($plugins_options['restaurant_on']) echo "checked"; if (!$wppl_exist['rto']) echo ' disabled="disabled" '; ?>/> <?php if (!$wppl_exist['rto']) echo $pro_message; ?></p>
				</td>
			</tr>
			<tr>
				<td>
					<p><?php echo _e('Real Estate Template:'); ?></p>
				</td>
				<td>
					<p style="color:brown; font-size:12px;"><input name="wppl_plugins[estate_on] " type="checkbox" value="1" <?php if ($plugins_options['estate_on']) echo "checked"; if (!$wppl_exist['eto']) echo ' disabled="disabled" '; ?>/> <?php if (!$wppl_exist['eto']) echo $pro_message; ?></p>
				</td>
			</tr>
			<tr>
				<td>
					<p><input name="Submit" class="wppl-save-btn" type="submit" value="<?php _e('Save Changes'); ?>" /></p>
				</td>
				<td></td>
			</tr>
		</table>
	</table>