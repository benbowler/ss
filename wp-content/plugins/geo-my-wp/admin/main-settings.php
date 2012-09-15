<table class="widefat fixed" style="margin-bottom:10px;">
		<thead>
				<th><h3><?php _e('I hope you will find this plugin useful! any donation would be much appreciated! thank you :)', 'wppl'); ?></h3></th>
				<th><a href="https://www.paypal.com/us/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FTXHJQAS523D4" border="0" alt="PayPal - The safer, easier way to pay online!" target="_blank"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" /></a></th>
			</thead>
	</table>
	<table class="widefat fixed">
		<thead>
			<th><h3><?php echo _e('General Settings','wppl'); ?></h3></th>
			<th></th>
		</thead>
		<tr>
			<td>
				<div class="wppl-settings">
					<p>
					<span>
						<label><?php _e('Google Maps API V3 Key:', 'wppl'); ?></label>
						<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
					</span>
					<div class="clear"></div>
					<span class="wppl-help-message"><?php _e('this is optional but will let you track your API requests. you can obtain your free Goole API key <a href="https://code.google.com/apis/console/" target="_blank">here</a>.', 'wppl'); ?></span>
					</p>
				</div>
			</td>
			<td>
				<p><input id="api_key" name="wppl_fields[google_api]" size="40" type="text" value="<?php echo $wppl_options[google_api]; ?>" /></p>
			</td>
		</tr> 
		<tr>
			<td>
				<div class="wppl-settings">
					<p>
					<span>
						<span><?php _e('Country code:', 'wppl'); ?></span>
						<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
					</span>
					<div class="clear"></div>
					<span class="wppl-help-message"><?php _e('Enter you country code for example for United States enter US. you can find your country code <a href="http://www.geomywp.com/wp-content/country-code.php" target="blank">here</a>', 'wppl'); ?></span>
					</p>
				</div>
			</td>
			<td>
				<p><input id="country-code" name="wppl_fields[country_code]" size="5" type="text" value="<?php echo $wppl_options[country_code]; ?>" /></p>
			</td>
		</tr> 
		<tr>
			<td>
			<div class="wppl-settings">
				<p>
				<span>
					<span><?php _e('Auto Locate:', 'wppl'); ?> </span>
					<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
				</span>
				<div class="clear"></div>
					<span class="wppl-help-message"><?php _e('this feature will automatically try to get the user current location when first visiting the website. Check the message checked box to display a message while getting the current location.', 'wppl'); ?></span>
				</p>
			</div>
			</td>
			<td>
				<p>
				<input id="wppl-locate-message" name="wppl_fields[auto_locate] " type="checkbox" value="1" <?php if ($wppl_options['auto_locate'] == 1) {echo "checked";}; ?>/>
				<?php echo _e('Yes','wppl'); ?>
				&nbsp;&nbsp;&#124;&nbsp;&nbsp;
				<input id="wppl-locate-message" name="wppl_fields[locate_message] " type="checkbox" value="1" <?php if ($wppl_options['locate_message'] == 1) {echo "checked";}; ?>/>
				<?php echo _e('Display message','wppl'); ?>
				</p>
			</td>
		</tr>
		
		<tr>
			<td>
			<div class="wppl-settings">
				<p>
				<span>
					<span><?php _e('Auto Results:', 'wppl'); ?> </span>
					<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
				</span>
				<div class="clear"></div>
					<span class="wppl-help-message"><?php _e('Will automatically do initial search and display results based on the user location when first goes to a search page. You need to define the radius of this initial search and the units.', 'wppl'); ?></span>
				</p>
			</div>
			</td>
			<td>
				<p>
				<input id="wppl-auto-search" name="wppl_fields[auto_search] " type="checkbox" value="1" <?php if ($wppl_options['auto_search'] == 1) {echo "checked";}; ?>/>
				<?php echo _e('Yes','wppl'); ?>
				&nbsp;&nbsp;&#124;&nbsp;&nbsp;
				<?php echo _e('Radius','wppl'); ?>		
				<input type="text" id="wppl-auto-radius" name="wppl_fields[auto_radius]" SIZE="1" onkeyup="this.value=this.value.replace(/[^\d]/,'')" value="<?php echo ($wppl_options['auto_radius']) ? $wppl_options['auto_radius'] : "50"; ?>" />	
				&nbsp;&nbsp;&#124;&nbsp;&nbsp;
				<select id="wppl-auto-units" name="wppl_fields[auto_units]">
					<option value="imperial" <?php echo ($wppl_options['auto_units'] == "imperial") ? "selected" : ""; ?>>Miles</option>
					<option value="metric" <?php echo ($wppl_options['auto_units'] == "metric") ? "selected" : ""; ?>>Kilometers</option>
				</select>
				</p>
			</td>
		</tr>
		<tr>
			<td>
			<div class="wppl-settings">
				<p>
				<span>
					<span><?php _e('Locator icon:', 'wppl'); ?></span>
					<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
				</span>
				<div class="clear"></div>
					<span class="wppl-help-message"><?php _e('Choose if to display the locator button in the search form. This button will get the user&#39;s current location and submit the search form based of that. you can choose one of the default icons or you can add icon of your own. ', 'wppl'); ?></span>
				</p>
			</div>
			</td>
			<td>
				<p>
				<span style="float:left"><input name="wppl_fields[show_locator_icon] " type="checkbox" value="1" <?php if ($wppl_options['show_locator_icon'] == 1) {echo "checked";}; ?>/>
					<?php echo _e('Yes','wppl'); ?>
					&nbsp;&nbsp;&#124;&nbsp;&nbsp;
				</span>
				<span style="width:365px;float:left;margin-left:10px;">	
					<?php $locator_icons = glob(plugin_dir_path(dirname(__FILE__)) . 'images/locator-images/*.png');
					$display_icon = plugins_url('/geo-my-wp/images/locator-images/');
					foreach ($locator_icons as $locator_icon) { ?>
					<span style="float:left;">
						<input type="radio" name="wppl_fields[locator_icon]" value="<?php echo basename($locator_icon); ?>" <?php echo ($wppl_options['locator_icon'] == basename($locator_icon) ) ? "checked" : ""; ?> />
						<img src="<?php echo $display_icon.basename($locator_icon); ?>" height="30px" width="30px"/>
						&nbsp;&nbsp;&#124;&nbsp;&nbsp;
					</span>
					<?php } ?>
				</span>
				</p>	
			</td>
		</tr>
		<tr>
			<td>
			<div class="wppl-settings">
				<p>
				<span>
					<span><?php echo _e('"Greater range search" Link: ','wppl'); ?></span>
					<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
				</span>
				<div class="clear"></div>
					<span class="wppl-help-message">
						<?php echo _e('When no results found a message with two links will be displayed. One link will run the search form again with a greater radius range (the one you enter in the input box)
								and anotehr link that will display all existing posts.', 'wppl'); ?>
					</span>
				</p>
			</div>
			</td>
			<td>
				<p>
				<input name="wppl_fields[wider_search] " type="checkbox" value="1" <?php if ($wppl_options['wider_search'] == 1) {echo "checked";}; ?>/>
				<?php echo _e('Yes','wppl'); ?>
				&nbsp;&nbsp;&#124;&nbsp;&nbsp;
				<?php echo _e('Distance value: ','wppl'); ?>
				<input id="wider-search" name="wppl_fields[wider_search_value]" size="5" type="text" value="<?php echo $wppl_options[wider_search_value]; ?>" /></p>
			</td>
		</tr>
		
		<tr>
			<td>
				<p><?php _e('Autocomplete when typing an address in front end:', 'wppl'); ?> </p>
			</td>
			<td>
				<p>
				<input name="wppl_fields[front_autocomplete] " type="checkbox" value="1" <?php if ($wppl_options['front_autocomplete'] == 1) {echo "checked";} echo "disabled" ?>/>
				<?php echo _e('Yes','wppl'); ?><span style="color:brown;">   (this feature is under development)<span></p> 
			</td>
		</tr>
		<tr>
			<td>
				<p><input name="Submit" class="wppl-save-btn" type="submit" value="<?php echo esc_attr_e('Save Changes'); ?>" /></p>
			</td>
			<td></td>
		</tr>
		<thead>
			<th><h3><?php echo _e('Post types Settings','wppl'); ?></h3></th>
			<th></th>
		</thead>
		<tr>
			<td>
			<div class="wppl-settings">
				<p>
				<span>
					<span><?php _e('Post types:', 'wppl'); ?></span>
					<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
				</span>
				<div class="clear"></div>
					<span class="wppl-help-message">
						<?php _e('Choose the post types where you want the address fields to appear. choose only the post types which you want to add a location too.', 'wppl'); ?>
					</span>
				</p>
			</div>	
			</td>
			<td>			
			<?php foreach ($posts as $post) { 
				echo '<input id="address_fields" type="checkbox" name="wppl_fields[address_fields][]" value="' . $post . '" '; if ($wppl_options['address_fields']) { echo (in_array($post, $wppl_options['address_fields'])) ? "checked=checked" : "";}; echo  ' style="margin-left: 10px;">' .get_post_type_object($post)->labels->name ;
					}	
			?>
			</td>
		</tr>
		<tr>
			<td>
			<div class="wppl-settings">
				<p>
				<span>
					<span><?php _e('Mandatory address fields: ', 'wppl'); ?></span>
					<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
				</span>
				<div class="clear"></div>
					<span class="wppl-help-message">
						<?php _e('Check this box if you want to make sure that users add location to a new post. it will prevent them from saving a post that do not have a location. Otherwise, users will be able to save a post even without a location. This way the post will be published and would show up in Wordpress search results but not in the Proximity search results.  ', 'wppl'); ?>
					</span>
				</p>
			</div>		
			</td>
			<td>
				<p>
				<input name="wppl_fields[mandatory_address] " type="checkbox" value="1" <?php if ($wppl_options['mandatory_address'] == 1) {echo "checked";}; ?>/>
				<?php echo _e('Yes','wppl'); ?>
				</p>
			</td>
		</tr>
		
		<tr>
			<td>
			<div class="wppl-settings">
				<p>
				<span>
					<span><?php _e('Choose results page :' , 'wppl'); ?></span>
					<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
				</span>
				<div class="clear"></div>
					<span class="wppl-help-message">
						<?php _e('This page will display the search results when using the widget or when you want to have the search form in one page and the results showing in a different page. Choose the page from the dropdown menu and paste the shortcode [wppl_results] into it.', 'wppl'); ?>
					</span>
				</p>
			</div>		
			</td>
			<td>
			<select name="wppl_fields[results_page]" style="float:left">
				<?php foreach ($pages_s as $page_s) {
				echo '<option value="'.$page_s->post_name.'"'; echo ($wppl_options[results_page] == $page_s->post_name ) ? 'selected="selected"' : "" ; echo '>'. $page_s->post_title . '</option>';
				}
			?>
			</select>
				<p style="float:left;font-size:12px;margin-left:25px"><?php _e('Paste the shortcode', 'wppl'); ?> <span style="color:brown"> [wppl_results] </span><?php echo esc_attr_e('into the results page'); ?></p>
			</td>
		</tr>
		
		<tr>
			<td>
				<p><?php _e('To display the search form in one page and the results in the "results" page use', 'wppl'); ?><span style="color:brown"> form_only="y" </span><?php _e('in your shortcode:', 'wppl')?></p>
			</td>
			<td>
				<p style="float:left;font-size:12px;"><?php _e('For example you can use:', 'wppl'); ?> <span style="color:brown"> [wppl form="1" form_only="y"] </span><?php _e('in any page to display the search form and the results will show in the results page', 'wppl'); ?></p>
			</td>
		</tr>
		
		<tr>
			<td>
			<div class="wppl-settings">
				<p>
				<span>
					<span><?php _e('Theme color:', 'wppl'); ?></span>
					<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
				</span>
				<div class="clear"></div>
					<span class="wppl-help-message">
						<?php _e('Check the checkbox if you want to use the theme color and choose the color you want. This feature controls the color of the links, address, and the title in the search results. If the checkbox left empty the colors that will be used will be from the stylesheet.', 'wppl'); ?>
					</span>
				</p>
			</div>		
			</td>
			<td>
				<p>
				<input type="checkbox" name="wppl_fields[use_theme_color]" value="1" <?php echo ($wppl_options['use_theme_color']) ? 'checked="checked"' : ''; ?>" />
				<?php echo _e('Yes','wppl'); ?>
				&nbsp;&nbsp;&#124;&nbsp;&nbsp;
				<input type="text" id="wppl-theme-color" name="wppl_fields[theme_color]" value="<?php echo ($wppl_options['theme_color']) ? $wppl_options['theme_color'] : "#2E738F"; ?>" />
				<div id="wppl-theme-color-picker"></div></p>
			</td>
		</tr>
		
		<tr>
			<td>
			<div class="wppl-settings">
				<p>
				<span>
					<span><?php _e('"No Results" title:', 'wppl'); ?></span>
					<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
				</span>
				<div class="clear"></div>
					<span class="wppl-help-message">
						<?php _e('Message to display when no results were found.', 'wppl'); ?>
					</span>
				</p>
			</div>	
			</td>
			<td>
				<p><input  type="text" id="api_key" name="wppl_fields[no_results]" size="40" value="<?php echo ($wppl_options[no_results]) ? $wppl_options[no_results] : "No results were found."; ?>" /></p>
			</td>
		</tr>
		<tr>
			<td>
				<p><input name="Submit" class="wppl-save-btn" type="submit" value="<?php echo esc_attr_e('Save Changes'); ?>" /></p>
			</td>
			<td></td>
		</tr> 
		<thead>
			<th><h3><?php echo _e('Buddypress Settings','wppl'); ?></h3></th>
			<th></th>
		</thead>
		<tr>
			<td>
			<div class="wppl-settings">
				<p>
				<span>
					<span><?php _e('Choose members (Buddypress) results page :' , 'wppl'); ?></span>
					<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
					<?php echo (!$wppl_on['friends']) ? '<span class="wppl-turnon-message"> (Turn on "Friends Finder" feature in the Add-ons page in order to use this feature)</span>' : ''; ?>
				</span>
				<div class="clear"></div>
					<span class="wppl-help-message">
						<?php _e('This page will display the search results when using the widget or when you want to have the search form in one page and the results showing in a different page. Choose the page from the dropdown menu and paste the shortcode [wppl_friends_results] into it.', 'wppl'); ?>
					</span>
				</p>
			</div>		
			</td>
			<td>
			<select name="wppl_fields[friends_results_page]" style="float:left" <?php echo(!$wppl_on['friends']) ? "disabled" : "";?>>
				<?php foreach ($pages_s as $page_s) {
				echo '<option value="'.$page_s->post_name.'"'; echo ($wppl_options[friends_results_page] == $page_s->post_name ) ? 'selected="selected"' : "" ; echo '>'. $page_s->post_title . '</option>';
				}
			?>
			</select>
				<p style="float:left;font-size:12px;margin-left:25px"><?php _e('Paste the shortcode', 'wppl'); ?> <span style="color:brown"> [wppl_friends_results] </span><?php echo esc_attr_e('into the results page'); ?></p>
			</td>
		</tr>
		<tr>
			<td>
			<div class="wppl-settings">
				<p>
				<span>
					<span><?php _e('Allow members to choose map icon: ', 'wppl'); ?> </span>
					<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
					<span class="wppl-premium-message"></span>
				</span>
				<div class="clear"></div>
					<span class="wppl-help-message">
						<?php _e('Checking this box will add a "Map icon" tab to the user "location" tab in his profile page. Use this when you want to let your members to choose their icon that will be displayed on the google map.', 'wppl'); ?>
					</span>
				</p>
			</div>
			</td>
			<td  class="wppl-premium-version-only">
				<p>
				<input name="wppl_fields[per_member_icon] " type="checkbox" value="1" <?php if ($wppl_options['per_member_icon'] == 1) {echo "checked";}; echo( !$wppl_on['friends'] ) ? "disabled" : "";?>/>
				<?php echo _e('Yes','wppl'); ?>
				</p>
			</td>
		</tr>
		<tr>
			<td>
				<p><input name="Submit" class="wppl-save-btn" type="submit" value="<?php echo esc_attr_e('Save Changes'); ?>" /></p>
			</td>
			<td></td>
		</tr>
	</table>