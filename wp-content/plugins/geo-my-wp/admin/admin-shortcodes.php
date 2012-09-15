<ul class="wppl-shortcodes-list" style="margin:0;">
	<?php $yy = 0; $hide_not = array(); $hide_yes = array();
		
			if (!empty($options_r)) {
				$tt = array_keys($options_r);
				foreach ($options_r as $option) :
					$e_id = $tt[$yy]; // element id //
		
					if ($option['friends_search']) {
						array_push($hide_not, $e_id);
						array_push($hide_yes, '0');
						echo '<script type="text/javascript">'; 
						echo 	'hideNot= '.json_encode($hide_not),';';
						echo 	'hideYes= '.json_encode($hide_yes),';'; 
						echo '</script>';		
					} else {
						array_push($hide_yes, $e_id);
						array_push($hide_not, '0');
						echo '<script type="text/javascript">'; 
						echo 	'hideYes= '.json_encode($hide_yes),';'; 
						echo 	'hideNot= '.json_encode($hide_not),';'; 
						echo '</script>';	
					} ?>
				
			<li class="wppl-shortcode-info-holder" id="wppl-shortcode-info-holder-<?php echo $e_id; ?>">
			
			<table class="widefat" style="margin-bottom: -2px;" id="shortcode-header-table-<?php echo $e_id; ?>">
				<input type="hidden" name="<?php echo 'wppl_shortcode['.$e_id.'][form_id]'; ?>" value="<?php echo $e_id;?>">
				<thead>
					<th>
						<div class="wppl-settings">
							<span>
								<span><h4><?php echo _e('Copy this shortcode and paste it into any page to display the search form and results','wppl'); ?></h4></span>
								<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
							</span>
							<div class="clear"></div>
							<span class="wppl-help-message">
							<p style="font-size: 13px;font-weight: normal;"><?php _e('This is the main shortcode that displays the search form and the results. 
									Click on the "Edit" button to choose the setting of the shortcode and when you done click "Save". 
									copy the shortcode and paste in the page where you want the search form and the results to be displayed. 
									you can also choose this shortcode using the search form widget to display the search form in the sidebar.
									if you want to display only the search form in one page and have the results being displayed in a different page 
									you need to use the shortcode like "[wppl form="'.$e_id.'" form_only="y"] and you need to choose the results page in the main setting page.', 'wppl'); ?>
							</p></span>
						</div>	
					</th>
					<th><?php if ($option['friends_search']) { ?>
							<img src="<?php echo plugins_url('/geo-my-wp/images/bp-icon.png'); ?>" width="40px" height="40px" style="float:left;" /> 
						<?php } else { ?>
							<img src="<?php echo plugins_url('/geo-my-wp/images/wp-icon.png'); ?>" width="40px" height="40px" style="float:left;" /> 
						<?php }?>
						<p style="float:left;margin-top:10px;margin-left:10px;color: #21759B;">[wppl form="<?php echo $e_id; ?>"]</p></th>
				</thead>
				<tr>
					<td>
						<p><input type="button" class="wppl-edit-btn" onclick="jQuery('.edit-shortcode-<?php echo $e_id; ?>').slideToggle('slow');" value="<?php _e('Edit'); ?>">
						<input type="button" class="wppl-remove-btn" onclick="removeElement(jQuery(this).closest('.wppl-shortcode-info-holder'));" value="<?php _e('Remove'); ?>"></p>
			
					</td>
					<td></td>
				</tr>
			</table>
			
			<div class="edit-shortcode-<?php echo $e_id; ?>" style="display:none">
				<table class="widefat" id="shortcode-table-<?php echo $e_id; ?>">
					<tr style=" height:40px;">
						<td>
						<div class="wppl-settings">
							<p>
							<span>
							<img class="wppl-bp-icon" src="<?php echo plugins_url('/geo-my-wp/images/bp-icon.png'); ?>" width="40px" height="40px" style="float:left;" />
							<label for="label-post-types-<?php echo $e_id; ?>" style="float:left;margin-top:10px;margin-left:10px;">
							<strong style="font-size:12px;"><?php echo _e('This is Buddypress search form:','wppl'); ?></strong></label></span>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<?php echo (!$wppl_on['friends']) ? '<span class="wppl-turnon-message"> (Turn on "Friends Finder" feature in the Add-ons page in order to use this feature)</span>' : ''; ?> 
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Check this checkbox if you are creating a Buddypress search form. Otherwise leave it empty for a post type search form. ', 'wppl'); ?>
								</span>
							</p>
						</div>
						</td>
						<td>
							<p><input type="checkbox" name="<?php echo 'wppl_shortcode[' .$e_id .'][friends_search]'; ?>" value="1" style="margin-left: 10px;" class="friends-check-btn" id="<?php echo 'shortcode-table-'.$e_id; ?>"  <?php echo ($option['friends_search']) ? " checked=checked " : ""; echo( !$wppl_on['friends'] ) ? "disabled" : "";?>></p>
						</td>
					</tr>
					<th><?php echo _e('Search form:' , 'wppl'); ?></th>
					<th></th>		

					<tr style="height:40px;" class="friends-not">
						<td>
							<p><label for="label-post-types-<?php echo $e_id; ?>"><?php echo _e('Post types:','wppl'); ?></label></p>
						</td>
						<td id="posts-checkboxes-<?php echo $e_id; ?>" <?php if (!$option['post_types']) { echo 'style="background: #FAA0A0"' ;}; ?>>
							<?php foreach ($posts as $post) { ?>
							<p><input type="checkbox" name="<?php echo 'wppl_shortcode[' .$e_id .'][post_types][]'; ?>" value="<?php echo $post; ?>" style="margin-left: 10px;" id="<?php echo $e_id; ?>" onchange="change_it(this.name,this.id);" <?php if ($option['post_types']) { echo (in_array($post, $option['post_types'])) ? ' checked=checked' : '';} ?>>&nbsp;&nbsp;<?php echo get_post_type_object($post)->labels->name; ?></p>
							<?php } ?>
						</td>
					</tr>
			
					<tr style=" height:40px;" class="friends-not" >
					 	<td>
							<p><label for="label-taxonomies-<?php echo $e_id; ?>"><?php echo _e('Taxonomies: (no taxonomies for multiple post types):','wppl'); ?></label></p>
						</td>
						<td class="taxes-<?php echo $e_id; ?>" style=" padding: 8px;">
							<?php foreach ($posts as $post) {
									$taxes = get_object_taxonomies($post);
									echo '<div id="' . $post . '_cat_' . $e_id . '" '; echo ((count($option['post_types']) == 1) && (in_array($post, $option['post_types']))) ? 'style="display: block; " ' : 'style="display: none;"'; echo '>';
									foreach ($taxes as $tax) { 
				 						if (is_taxonomy_hierarchical($tax)) { 
											echo '<p><input type="checkbox" name="wppl_shortcode[' .$e_id .'][taxonomies][]" value="' . $tax .'" '; if($option['taxonomies']) { echo (in_array($tax , $option['taxonomies'])) ? "checked=checked" : "";}; echo  ' style="margin-left: 10px; " />&nbsp;&nbsp;' . get_taxonomy($tax)->labels->singular_name . '</p>';
										}
									}
									echo '</div>';
								} ?>
						</td>
					</tr>
					
					<?php if ($wppl_on['friends']) { ?>
					<tr style="height:40px;" class="friends-yes">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<span><?php echo _e('Profile fields:','wppl'); ?></span>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('The profile fields you choose here will be displayed in the search form. They will be used to filter results based on the profile fields the user chooses. Only checkbox, select, multiselect, and radio buttons types fields will show here - no text input type fields. Also you can choose one of your date fields to work as an "Age range" field.', 'wppl'); ?>
								</span>
							</p>
						</div>		
						</td>
						<td id="profile-fields-checkboxes-<?php echo $e_id; ?>">
							<p><?php wppl_bp_admin_profile_fields($e_id, $option, 'profile_fields'); ?></p>	
						</td>
					</tr>
					<?php } ?>
					
			
					<tr style=" height:40px;">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-address-title-<?php echo $e_id; ?>"><?php echo  _e("Address Fields Title"); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Type the title for the address field of the search form. for example "Enter your address". this title wll be displayed either next to the address input field or within if you check the checkbox for it. ', 'wppl'); ?>
								</span>
							</p>
						</div>		
						
						</td>
						<td>
							<p>
								<?php echo _e('Field title:','wppl'); ?>
								<input name="<?php echo 'wppl_shortcode[' .$e_id .'][address_title]'; ?>" size="40" type="text" value="<?php echo ($option[address_title]) ? $option[address_title] : 'zipcode, city & state or full address...' ; ?>" />
								<input type="checkbox" value="1" name="<?php echo 'wppl_shortcode[' .$e_id .'][address_title_within]'; ?>" <?php echo (isset($option['address_title_within'])) ? " checked=checked " : ""; ?>>	
								<?php echo _e('Within the input field','wppl'); ?>
							</p>
						</td>
					</tr>
			
					<tr style=" height:40px;">
						<td>
							<div class="wppl-settings">
							<p>
								<span>
									<label for="label-distance-<?php echo $e_id; ?>"><?php echo _e('Radius values:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Check the "multiple" checkbox if you want to have a select dropdown menu of multiple radius values in the search form. then enter the values you in the input box comma separated. If the checkbox in unchecked the last value in the values list will be the default radius value.', 'wppl'); ?>
								</span>
							</p>
						</div>	
						</td>
						<td>
							<p>
							<input type="checkbox"  value="1" name="<?php echo 'wppl_shortcode[' .$e_id .'][display_radius]'; ?>" <?php echo (isset($option['display_radius'])) ? " checked=checked " : ""; ?>>
							<?php echo _e('Multiple:','wppl'); ?>&nbsp;
							&nbsp;&nbsp;&#124;&nbsp;&nbsp;
							<?php echo _e('Values (comma separated):','wppl'); ?>&nbsp;
							<input type="text" name="<?php echo 'wppl_shortcode[' .$e_id .'][distance_values]'; ?>" size="20" <?php echo ($option['distance_values']) ? ' value="' . $option['distance_values'] . '"' : ' value="5,10,25,50"'; ?>></p>				
						</td>
					</tr>
			
					<tr style=" height:40px;">
						<td> 
							<div class="wppl-settings">
							<p>
								<span>
									<label for="label-distance-<?php $e_id; ?>"><?php echo _e('Units:' , 'wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Check the "both" checkbox to have a select dropdown list of both Miles and Kilometer in the search form. Otherwise, leave the box unchecked and choose the default units you want to use.', 'wppl'); ?>
								</span>
							</p>
						</div>	
						</td>
						<td>
							<p>
							<input type="checkbox" value="1" name="<?php echo 'wppl_shortcode[' .$e_id .'][display_units]'; ?>" <?php echo (isset($option['display_units'])) ? "checked=checked" : ""; ?>> 
							<?php echo _e('Both','wppl'); ?>
							&nbsp;&nbsp;&#124;&nbsp;&nbsp;
							<?php echo _e('or choose one:','wppl'); ?>
							<select name="<?php echo 'wppl_shortcode[' .$e_id .'][units_name]'; ?>">
								<option value="imperial" <?php echo ($option['units_name'] == "imperial" ) ?'selected="selected"' : ""; ?>>Miles</option>
								<option value="metric" <?php echo ($option['units_name'] == "metric" ) ?'selected="selected"' : ""; ?>>Kilometers</option>
							</select>			
						</td>
					</tr>
			
					<th><?php echo _e('Results','wppl'); ?></th>
					<th></th>
					<tr style=" height:40px;" class="friends-not">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-styling-<?php echo $e_id; ?>"><?php echo _e('Results template:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Choose the look of the results page. you can always modify the themes by adding your own styling. "custom" theme is a very plain and simple theme for you to modify it the way you want. ', 'wppl'); ?>
								</span>
							</p>
						</div>	
						
						</td>
						<td>
							<p> 
							<select name="<?php echo 'wppl_shortcode[' .$e_id .'][results_template]'; ?>">
								<option value="custom" <?php echo ($option['results_template'] == "custom" ) ? 'selected="selected"' : ""; ?>>Custom</option>			
								<option value="default" <?php echo ($option['results_template'] == "default" ) ? 'selected="selected"' : ""; ?>>Default</option>						
								<option value="restaurants" <?php echo ($option['results_template'] == "restaurants" ) ?'selected="selected"' : ""; if ($plugins_options['restaurant_on'] != 1) {echo 'disabled';} ?>>Restaurant</option>
								<option value="real-estate" <?php echo ($option['results_template'] == "real-estate" ) ?'selected="selected"' : ""; if ($plugins_options['estate_on'] != 1) {echo 'disabled';} ?>>Real Estate</option>
							</select>
							</p>
						</td>
					</tr>
					
					<?php if ($wppl_on['friends']) { ?>
					<tr style=" height:40px;" class="friends-yes">
						<td>
							<p><label for="label-styling-<?php echo $e_id; ?>"><?php echo _e('Results template:','wppl'); ?></label></p>
						</td>
						<td>
							<p> 
							<select name="<?php echo 'wppl_shortcode[' .$e_id .'][results_template]'; ?>">
								<option value="default" <?php echo ($option['results_template'] == "default" ) ? 'selected="selected"' : ""; ?>>Default</option>						
							</select>
							</p>
						</td>
					</tr>
					<?php } ?>
			
					<tr style=" height:40px;">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-styling-<?php echo $e_id; ?>"><?php echo _e('Main wrapper width:','wppl');?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('This is the DIV tag that wraps the map and the results. you can choose the exact size for it or leave it empty for 100% to cover the content area.. ', 'wppl'); ?>
								</span>
							</p>
						</div>	
						</td>
						<td>
							<p>
								<input type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')" size="2" name="<?php echo 'wppl_shortcode[' .$e_id .'][main_wrapper_width]'; ?>" <?php echo ($option['main_wrapper_width']) ? 'value="' . $option['main_wrapper_width'] . '"' : 'value=""' ?>>px&nbsp;&nbsp;<?php echo _e('(100% if left empty)','wppl'); ?>
							</p>
						</td>
					</tr>
			
					<tr style=" height:40px;">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-display-results-<?php echo $e_id; ?>"><?php echo _e('Results output:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Choose if you want to show the results using the map only , posts only or both map and the results below it.', 'wppl'); ?>
								</span>
							</p>
						</div>	
						</td>
						<td>
							<p><input type="radio" name="<?php echo 'wppl_shortcode[' .$e_id .'][results_type]'; ?>" value="both" <?php echo ($option['results_type'] == "both") ? "checked=checked" : ""; ?> />&nbsp;&nbsp;<?php echo _e('Both posts and map','wppl'); ?>
							<p><input type="radio" name="<?php echo 'wppl_shortcode[' .$e_id .'][results_type]'; ?>"  value="posts"  <?php echo ($option['results_type'] == "posts") ? "checked=checked" : ""; ?>/>&nbsp;&nbsp;<?php echo _e('Posts only','wppl'); ?>
							<p><input type="radio" name="<?php echo 'wppl_shortcode[' .$e_id .'][results_type]'; ?>" value="map"  <?php echo ($option['results_type'] == "map") ? "checked=checked" : ""; ?>/>&nbsp;&nbsp;<?php echo _e('Map only','wppl'); ?>
						</td>
					</tr>
					
					<tr style=" height:40px;">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-display-results-<?php echo $e_id; ?>"><?php echo _e('Google Map:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Settings for the main google map. Define its height and width in PX otherwise it will by 500 X 500. Choose the map type from the dropdown menu . check the "auto zoom" checkbox if you want all the markers to fit in the map or you can choose a custom zoom level from the dropdown menu.', 'wppl'); ?>
								</span>
							</p>
						</div>	
						</td>
						<td>
							<p style="line-height: 40px;">
							<?php echo _e('Width:','wppl'); ?>
							&nbsp;<input type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')" name="<?php echo 'wppl_shortcode[' .$e_id .'][map_width]'; ?>" value="<?php echo $option['map_width']; ?>" size="2">px
							&nbsp;&nbsp;&#124;&nbsp;&nbsp;
							<?php echo _e('height:','wppl'); ?>
							&nbsp;<input type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')" name="<?php echo 'wppl_shortcode[' .$e_id .'][map_height]'; ?>" value="<?php echo $option['map_height']; ?>" size="2">px
							&nbsp;&nbsp;&#124;&nbsp;&nbsp;
							<?php echo _e('Map Type:','wppl'); ?>
							<?php echo 				
							'<select name="wppl_shortcode[' .$e_id .'][map_type]">
								<option value="ROADMAP" '; echo ($option['map_type'] == "ROADMAP" ) ?'selected="selected"' : ""; echo '>ROADMAP</option>
								<option value="SATELLITE" '; echo ($option['map_type'] == "SATELLITE" ) ?'selected="selected"' : ""; echo '>SATELLITE</option>
								<option value="HYBRID" '; echo ($option['map_type'] == "HYBRID" ) ?'selected="selected"' : ""; echo '>HYBRID</option>
								<option value="TERRAIN" '; echo ($option['map_type'] == "TERRAIN" ) ?'selected="selected"' : ""; echo '>TERRAIN</option>
							</select>'
							?>
							&nbsp;&nbsp;&#124;&nbsp;&nbsp;
							<input type="checkbox" value="1" name="<?php echo 'wppl_shortcode[' .$e_id .'][auto_zoom]'; ?>" <?php echo (isset($option['auto_zoom'])) ? "checked=checked" : ""; ?>>
							<?php echo _e('Auto zoom:','wppl'); ?>&nbsp;
							&nbsp;&nbsp;&#124;&nbsp;&nbsp;
							<?php echo _e('Or zoom lever:','wppl'); ?>&nbsp; 
							<select name="<?php echo 'wppl_shortcode[' .$e_id .'][zoom_level]'; ?>">
							<?php for ($r=1; $r< 18 ; $r++) { 			
								echo '<option value="' .$r. '"'; echo ($option['zoom_level'] == $r ) ? 'selected="selected"' : ""; echo '>'.$r.'</option>';
								} ?>						
							</select><?php echo _e('(will not count if auto zoom is checked)','wppl'); ?></p>
						</td>
					</tr>
			
					<tr style=" height:40px;">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-display-results-<?php echo $e_id; ?>"><?php _e('Google map icon:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
									<span class="wppl-premium-message"></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Choose the global icon for google map. All results will use this icon to show its location on the map unless you check the "per post icon" checkbox below.', 'wppl'); ?>
								</span>
							</p>
						</div>	
						</td>
						<td class="wppl-premium-version-only">
							<p style="float:left;width:500px;">
							<?php $map_icons = glob(plugin_dir_path(dirname(__FILE__)) . 'map-icons/main-icons/*.png');
								$display_icon = plugins_url('/geo-my-wp/map-icons/main-icons/');
								foreach ($map_icons as $map_icon) {
									echo '<span style="float:left;"><input type="radio" name="wppl_shortcode[' .$e_id .'][map_icon]" value="'.basename($map_icon).'"'; echo ($option['map_icon'] == basename($map_icon) ) ? "checked" : ""; echo ' />
									<img src="'.$display_icon.basename($map_icon).'" height="40px" width="35px"/></span>';
								} ?>
							</p>	
						</td>
					</tr>
					
					<tr style=" height:40px;">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-display-results-<?php echo $e_id; ?>"><?php _e('"Your location" icon:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
									<span class="wppl-premium-message"></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Choose the icon that will show the user location on the map.', 'wppl'); ?>
								</span>
							</p>
						</div>	
						</td>
						<td class="wppl-premium-version-only">
							<p style="float:left;width:500px;">
							<?php $yl_icons = glob(plugin_dir_path(dirname(__FILE__)) . 'map-icons/your-location-icons/*.png');
								$display_yl_icon = plugins_url('/geo-my-wp/map-icons/your-location-icons/');
								foreach ($yl_icons as $yl_icon) {
									echo '<span style="float:left;"><input type="radio" name="wppl_shortcode[' .$e_id .'][your_location_icon]" value="'.basename($yl_icon).'"'; echo ($option['your_location_icon'] == basename($yl_icon) ) ? "checked" : ""; echo ' />
									<img src="'.$display_yl_icon.basename($yl_icon).'" height="40px" width="35px"/></span>';
								} ?>
							</p>	
						</td>
					</tr>
			
					<tr style=" height:40px;" class="friends-not">
						<td>
							<div class="wppl-settings">
							<p>
								<span>
									<label for="label-per-post-icon-<?php echo $e_id; ?>"><?php echo _e('Per post map&#39;s icon:', 'wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
									<span class="wppl-premium-message"></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Check this checkbox if you want each post to use its own google map icon. ', 'wppl'); ?>
								</span>
							</p>
						</div>
						</td>
						<td class="wppl-premium-version-only">
							<p>
							<input type="checkbox" name="<?php echo 'wppl_shortcode[' .$e_id .'][per_post_icon]'; ?>" <?php echo ( isset($option['per_post_icon']) ) ? " checked=checked " : ""; echo ' value="1" '; echo( !$wppl_on['per_post_icon'] ) ? "disabled" : ""; ?>>
							<?php echo _e('Yes','wppl'); ?>
							</p>
						</td>
					</tr>
			
					<?php if ($wppl_on['friends']) { ?>
					<tr style="height:40px;" class="friends-yes">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-per-post-icon-<?php echo $e_id; ?>"><?php echo _e('Per member map&#39;s icon:', 'wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
									<span class="wppl-premium-message"></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Check this checkbox if you want each member to use its own google map icon. ', 'wppl'); ?>
								</span>
							</p>
						</div>
						</td>
						<td class="wppl-premium-version-only">
							<p>
								<input type="checkbox" name="<?php echo 'wppl_shortcode[' .$e_id .'][per_member_icon]'; ?>" <?php echo ( isset($option['per_member_icon']) ) ? " checked=checked " : ""; echo ' value="1" '; echo(!$wppl_on['friends']) ? "disabled" : ""; ?>>
								<?php echo _e('Yes','wppl'); ?>
							</p>
						</td>
					</tr>
					<?php } ?>
					
					<tr style=" height:40px;" >
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-styling-<?php echo $e_id; ?>"><?php echo _e('Single result styling:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('This control the height and width of each result within the results page. Define its width and height in PX otherwise it will be uses based on the stylesheet.', 'wppl'); ?>
								</span>
							</p>
						</div>
						</td>
						<td>
							<p> 
								<?php echo _e('Width:','wppl'); ?>&nbsp;&nbsp;<input type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')" size="2" name="<?php echo 'wppl_shortcode[' .$e_id .'][single_result_width]'; ?>" <?php echo ($option['single_result_width']) ? 'value="' . $option['single_result_width'] . '"' : 'value=""'; ?>>px
								&nbsp;&nbsp;&#124;&nbsp;&nbsp;
								<?php echo _e('Height:','wppl'); ?>&nbsp;&nbsp;<input type="text" size="2" onkeyup="this.value=this.value.replace(/[^\d]/,'')" name="<?php echo 'wppl_shortcode[' .$e_id .'][single_result_height]'; ?>" <?php echo ($option['single_result_height']) ? 'value="' . $option['single_result_height'] . '"' : 'value=""'; ?>>px
								&nbsp;&nbsp;<?php echo _e('(stylesheet if left empty)','wppl'); ?>
							</p>
						</td>
					</tr>
				
					<tr style=" height:40px;" class="friends-not">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-posts-scroller-<?php echo $e_id; ?>"><?php echo _e('Random Featured posts :','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
									<span class="wppl-premium-message"></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('will display number of random featured posts below the map and above the list of results. This way you can mark certain posts as "featured posts" in the admin section and those posts would always be displayed on top. The width of each post would be resized based on the number of posts you choose to display so choose the number of posts based on the width of your content area.  ', 'wppl'); ?>
								</span>
							</p>
						</div>
						</td>
						<td class="wppl-premium-version-only">
							<p>
							<input type="checkbox" name="<?php echo 'wppl_shortcode[' .$e_id .'][random_featured_posts]'; ?>" <?php echo (isset($option['random_featured_posts'])) ? "checked=checked" : ""; echo ' value="1" '; echo( !$wppl_on['featured_posts']) ? "disabled" : "";?>>
							<?php echo _e('Show','wppl'); ?>
							&nbsp;&nbsp;&#124;&nbsp;&nbsp;
							<?php echo _e('width:','wppl'); ?>
							&nbsp;<input type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')" size="1" name="<?php echo 'wppl_shortcode[' .$e_id .'][random_featured_width]'; ?>" <?php echo ($option['random_featured_width']) ? 'value="' . $option['random_featured_width'] . '"' : 'value=""'; echo( !$wppl_on['featured_posts'] ) ? "disabled" : "";?>>px
							&nbsp;&nbsp;&#124;&nbsp;&nbsp;
							<?php echo _e('Number of posts:','wppl'); ?>
							&nbsp;<input type="text" size="1" onkeyup="this.value=this.value.replace(/[^\d]/,'')" name="<?php echo 'wppl_shortcode[' .$e_id .'][random_featured_count]'; ?>" <?php echo ($option['random_featured_count']) ? 'value="' . $option['random_featured_count'] . '"' : 'value="3"'; echo( !$wppl_on['featured_posts'] ) ? "disabled" : "";?>>
							</p>
						</td>
					</tr>
			
					<tr style=" height:40px;" class="friends-not">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-show-thumb-<?php echo $e_id; ?>"><?php echo _e('Mark featured posts:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
									<span class="wppl-premium-message"></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Use this feature to mark featured post within the results list. It will display the image that you choose from the dropdown list on top of each featured post. ', 'wppl'); ?>
								</span>
							</p>
						</div>
						</td>
						<td class="wppl-premium-version-only">
							<p style="float:left;width:500px;">
								<span style="float:left">
									<input type="checkbox" name="<?php echo 'wppl_shortcode[' .$e_id .'][featured_posts]'; ?>" <?php echo (isset($option['featured_posts'])) ? "checked=checked" : ""; echo ' value="1" '; echo( !$wppl_on['featured_posts'] ) ? "disabled" : ""; ?>>
									<?php echo _e('Yes','wppl'); ?>
									&nbsp;&nbsp;&#124;&nbsp;&nbsp;
									<?php echo _e('Image:','wppl'); ?> 
								</span>	
								<span style="float:left;">
									<?php $map_icons = glob(plugin_dir_path(dirname(__FILE__)) . 'plugins/featured-posts/images/*.png');
									$display_icon = plugins_url('/geo-my-wp/plugins/featured-posts/images/');
									foreach ($map_icons as $map_icon) {
										echo '<span style="float:left;"><input type="radio" name="wppl_shortcode[' .$e_id .'][featured_posts_image]" value="'.basename($map_icon).'"'; echo ($option['featured_posts_image'] == basename($map_icon) ) ? "checked" : ""; echo ' />
										<img src="'.$display_icon.basename($map_icon).'" height="40px" width="35px"/></span>';
									} ?>
								</span>
							</p>	
						</td>
					</tr>
		
					<tr style=" height:40px;" class="friends-not">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-show-thumb-<?php echo $e_id; ?>"><?php echo _e('Per result google map:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
									<span class="wppl-premium-message"></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('This featured let you add a single map to each result in the results page with marker shows the location and marker shows the user location. This could be used instead or in addition to the main map. ', 'wppl'); ?>
								</span>
							</p>
						</div>
						</td>
						<td class="wppl-premium-version-only">
							<p>
							<input type="checkbox" value="1" name="<?php echo 'wppl_shortcode[' .$e_id .'][single_map]'; ?>" <?php echo (isset($option['single_map'])) ? "checked=checked" : ""; ?>>
							<?php echo _e('Yes','wppl'); ?>
							&nbsp;&nbsp;&#124;&nbsp;&nbsp;
							<?php echo _e('Height:','wppl'); ?>
							&nbsp;<input type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')" name="<?php echo 'wppl_shortcode[' .$e_id .'][single_map_height]'; ?>" value="<?php echo $option['single_map_height']; ?>" size="2">px
							&nbsp;&nbsp;&#124;&nbsp;&nbsp;
							<?php echo _e('Width:','wppl'); ?>
							&nbsp;<input type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')" name="<?php echo 'wppl_shortcode[' .$e_id .'][single_map_width]'; ?>" value="<?php echo $option['single_map_width']; ?>" size="2">px
							&nbsp;&nbsp;&#124;&nbsp;&nbsp;
							<?php echo _e('Map Type:','wppl'); ?>&nbsp;				
							<select name="<?php echo 'wppl_shortcode[' .$e_id .'][single_map_type]'; ?>">
								<option value="ROADMAP" <?php echo ($option['single_map_type'] == "ROADMAP" ) ?'selected="selected"' : ""; ?>>ROADMAP</option>
								<option value="SATELLITE" <?php echo ($option['single_map_type'] == "SATELLITE" ) ?'selected="selected"' : ""; ?>>SATELLITE</option>
								<option value="HYBRID" <?php echo ($option['single_map_type'] == "HYBRID" ) ?'selected="selected"' : ""; ?>>HYBRID</option>
								<option value="TERRAIN" <?php echo ($option['single_map_type'] == "TERRAIN" ) ?'selected="selected"' : ""; ?>>TERRAIN</option>
							</select>
							</p>
						</td>
					</tr>
			
					<tr style=" height:40px;" class="friends-not">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-show-thumb-<?php echo $e_id; ?>"><?php echo _e('Featured image:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Display featured image and define its width and height in PX. ', 'wppl'); ?>
								</span>
							</p>
						</div>
						</td>
						<td>
							<p>
								<input type="checkbox" value="1" name="<?php echo 'wppl_shortcode[' .$e_id .'][show_thumb]'; ?>" <?php echo (isset($option['show_thumb'])) ? "checked=checked" : ""; ?>>
								<?php echo _e('Yes','wppl'); ?>
								&nbsp;&nbsp;&#124;&nbsp;&nbsp;
								<?php echo _e('Height:','wppl'); ?>
								&nbsp;<input type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')"  size="2" name="<?php echo 'wppl_shortcode[' .$e_id .'][thumb_height]'; ?>" <?php echo ($option['thumb_height']) ? 'value="' . $option['thumb_height'] . '"' : 'value="200"'; ?>>px
								&nbsp;&nbsp;&#124;&nbsp;&nbsp;
								<?php echo _e('Width:','wppl'); ?>
								&nbsp;<input type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')"  size="2" name="<?php echo 'wppl_shortcode[' .$e_id .'][thumb_width]'; ?>" <?php echo ($option['thumb_width']) ? 'value="' . $option['thumb_width'] . '"' : 'value="200"'; ?>>px
							</p>
						</td>
					</tr>
					
					<tr style=" height:40px;" class="friends-yes">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-show-thumb-<?php echo $e_id; ?>"><?php echo _e('Avatar:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Display members avatar and define its width and height in PX. ', 'wppl'); ?>
								</span>
							</p>
						</div>
						</td>
						<td>
							<p>
								<input type="checkbox" value="1" name="<?php echo 'wppl_shortcode[' .$e_id .'][show_thumb]'; ?>" <?php echo (isset($option['show_thumb'])) ? "checked=checked" : ""; ?>>
								<?php echo _e('Yes','wppl'); ?>
								&nbsp;&nbsp;&#124;&nbsp;&nbsp;
								<?php echo _e('Height:','wppl'); ?>
								&nbsp;<input type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')"  size="2" name="<?php echo 'wppl_shortcode[' .$e_id .'][thumb_height]'; ?>" <?php echo ($option['thumb_height']) ? 'value="' . $option['thumb_height'] . '"' : 'value="200"'; ?>>px
								&nbsp;&nbsp;&#124;&nbsp;&nbsp;
								<?php echo _e('Width:','wppl'); ?>
								&nbsp;<input type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')"  size="2" name="<?php echo 'wppl_shortcode[' .$e_id .'][thumb_width]'; ?>" <?php echo ($option['thumb_width']) ? 'value="' . $option['thumb_width'] . '"' : 'value="200"'; ?>>px
							</p>
						</td>
					</tr>
					<tr style=" height:40px;" class="friends-not">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-show-thumb-<?php echo $e_id; ?>"><?php echo _e('Additional information:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Show the fields of the additional information you want to display for each result. ', 'wppl'); ?>
								</span>
							</p>
						</div>	
						</td>
						<td>
							<p>
								<input type="checkbox" name="<?php echo 'wppl_shortcode[' .$e_id .'][additional_info][phone]'; ?>" value="1" <?php echo (isset($option['additional_info']['phone'])) ? "checked=checked" : ""; ?>>&nbsp;<?php echo _e('Phone','wppl'); ?>&nbsp;&nbsp;
								<input type="checkbox" name="<?php echo 'wppl_shortcode[' .$e_id .'][additional_info][fax]'; ?>" value="1" <?php echo (isset($option['additional_info']['fax'])) ? "checked=checked" : ""; ?>>&nbsp;<?php echo _e('Fax','wppl'); ?>&nbsp;&nbsp;
								<input type="checkbox" name="<?php echo 'wppl_shortcode[' .$e_id .'][additional_info][email]'; ?>" value="1" <?php echo (isset($option['additional_info']['email'])) ? "checked=checked" : ""; ?>>&nbsp;<?php echo _e('Email','wppl'); ?>&nbsp;&nbsp;
								<input type="checkbox" name="<?php echo 'wppl_shortcode[' .$e_id .'][additional_info][website]'; ?>" value="1" <?php echo (isset($option['additional_info']['website'])) ? "checked=checked" : ""; ?>>&nbsp;<?php echo _e('Website','wppl'); ?>&nbsp;&nbsp;
							</p>
						</td>
					</tr>
			
					<tr style=" height:40px;" class="friends-not">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-excerpt-<?php echo $e_id; ?>"><?php echo _e('Excerpt:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('This featured will grab the number of words that you choose from the post content and display it in the resut. You can give it a very high number (ex. 99999) to display the entire content. ', 'wppl'); ?>
								</span>
							</p>
						</div>
						</td>
						<td>
							<p>
								<input type="checkbox"  value="1" name="<?php echo 'wppl_shortcode[' .$e_id .'][show_excerpt]'; ?>" <?php echo (isset($option['show_excerpt'])) ? "checked=checked" : ""; ?>>
								<?php echo _e('Yes','wppl'); ?>&nbsp;
								&nbsp;&nbsp;&#124;&nbsp;&nbsp;
								<?php echo _e('Words count:','wppl'); ?>
								<input type="text" name="<?php echo 'wppl_shortcode[' .$e_id .'][words_excerpt]'; ?>" value="<?php echo $option['words_excerpt']; ?>" size="5"></p>
						</td>
					</tr>
					
					<tr style=" height:40px;" class="friends-not">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-extra-info-<?php echo $e_id; ?>"><?php echo _e('Show Categories','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Using this feature you can display in each result the categories attached to its post.', 'wppl'); ?>
								</span>
							</p>
						</div>
						</td>
						<td>
							<p>
							<input type="checkbox"  value="1" name="<?php echo 'wppl_shortcode[' .$e_id .'][custom_taxes]'; ?>" <?php echo (isset($option['custom_taxes'])) ? "checked=checked" : ""; ?>>
							<?php echo _e('Yes','wppl'); ?>
							</p>
						</td>
					</tr>
					
					<tr style=" height:40px;" class="friends-yes">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-profile-fields-<?php echo $e_id; ?>"><?php echo _e('Show Profile fields','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
									<span class="wppl-premium-message"></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Choose the profile fields that you want to display in each result.', 'wppl'); ?>
								</span>
							</p>
						</div>
						</td>
						<td class="wppl-premium-version-only">
							<p>
							<?php if ($wppl_on['friends'])  wppl_bp_admin_profile_fields($e_id, $option, 'results_profile_fields'); ?>	
							</p>
						</td>
					</tr>
				<!--	
					<tr style=" height:40px;" class="friends-not">
						<td>
							<p><label for="label-extra-info-<?php echo $e_id; ?>"><?php echo _e('"Extra Information" link:','wppl'); ?></label></p>
						</td>
						<td>
							<p>
							<input type="checkbox"  value="1" name="<?php echo 'wppl_shortcode[' .$e_id .'][extra_info]'; ?>" <?php echo (isset($option['extra_info'])) ? "checked=checked" : ""; ?>>
							<?php echo _e('Yes','wppl'); ?>
							</p>
						</td>
					</tr>
			-->
					<tr style=" height:40px;">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-per-page-<?php echo $e_id; ?>"><?php echo _e('Results per page:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Choose the number of results per page.', 'wppl'); ?>
								</span>
							</p>
						</div>
						</td>
						<td>
							<p><input type="text" name="<?php echo 'wppl_shortcode[' .$e_id .'][per_page]'; ?>" value="<?php echo ($option['per_page']) ? $option['per_page'] : '5'; ?>" size="3"></p>
						</td>
					</tr>
			
					<tr style=" height:40px;">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-driving-distance-<?php echo $e_id; ?>"><?php echo _e('Driving distance:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('While the results showing the radius distance from the user to the location this feature let you display the exact driving distance. Please note that each driving distance request counts with google API when you can have 2500 requests per day (without a Google API key). also this featured could cause the results being displayed a little slower.', 'wppl'); ?>
								</span>
							</p>
						</div>
						</td>
						<td>
							<p>
							<input type="checkbox"  value="1" name="<?php echo 'wppl_shortcode[' .$e_id .'][by_driving]'; ?>" <?php echo (isset($option['by_driving'])) ? "checked=checked" : ""; ?>>
							<?php echo _e('Yes','wppl'); ?>
							</p>
						</td>
					</tr>
			
					<tr style=" height:40px;">
						<td>
						<div class="wppl-settings">
							<p>
								<span>
									<label for="label-get-directions-<?php echo $e_id; ?>"><?php echo _e('Show "get directions" link:','wppl'); ?></label>
									<span><a href="#" class="wppl-help-btn"><img src="<?php echo plugins_url('/geo-my-wp/images/help-btn.png'); ?>" width="25px" height="25px" style="float:left;" /></a></span>
								</span>
								<div class="clear"></div>
								<span class="wppl-help-message">
									<?php _e('Display "get directions" link that will open a new window with google map that show the exact driving direction from the user to the location..', 'wppl'); ?>
								</span>
							</p>
						</div>
						</td>
						<td>
							<p>
							<input type="checkbox"  value="1"  name="<?php echo 'wppl_shortcode[' .$e_id .'][get_directions]'; ?>" <?php echo (isset($option['get_directions'])) ? "checked=checked" : ""; ?>>
							<?php echo _e('Yes','wppl'); ?>
							</p>
						</td>
					</tr>
			
					<tr style=" height:40px;">
						<td>
							<p><input type="submit" name="Submit" class="wppl-save-btn" value="<?php echo _e('Save Changes','wppl'); ?>" /></p>
						</td>
						<td></td>
					</tr>
			
				</table>
			</div>

			</li>
			<?php $yy++;
			endforeach;		
			} ?>
		
    		<p><input type="button" id="create-new-shortcode" value="<?php echo _e('Create new shortcode'); ?>">
   			<input type="submit" name="Submit" class="wppl-save-btn" value="<?php echo _e('Save Changes'); ?>" /></p>
    
    		<?php $next_id = (isset($tt)) ? (max($tt) + 1) : "1"; ?>
  			<input type="hidden" name="element-id" value="<?php echo next_id; ?>" />
		
 			<div style="display:none;" id="wppl-new-shortcode-fields">
				<input type="hidden" name="<?php echo 'wppl_shortcode['.$next_id.'][form_id]'; ?>" value="<?php echo $next_id; ?>" disabled/>
				<input type="hidden" name="<?php echo 'wppl_shortcode['.$next_id.'][address_title]'; ?>" value="Zipcode or full address..." disabled />
				<input type="hidden" name="<?php echo 'wppl_shortcode['.$next_id.'][display_radius]'; ?>" value="1" disabled />
				<input type="hidden" name="<?php echo 'wppl_shortcode['.$next_id.'][results_type]'; ?>" value="both" disabled />
				<input type="hidden" name="<?php echo 'wppl_shortcode['.$next_id.'][auto_zoom]'; ?>" value="1" disabled />
				<input type="hidden" name="<?php echo 'wppl_shortcode['.$next_id.'][map_icon]'; ?>" value="_default.png" disabled />
				<input type="hidden" name="<?php echo 'wppl_shortcode['.$next_id.'][your_location_icon]'; ?>" value="blue-dot.png" disabled />
			</div>
		</ul>