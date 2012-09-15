<?php

function bp_maps_admin_js($hook_suffix) {

	if ($hook_suffix!='buddypress_page_bp-maps-setup') return false;
	wp_enqueue_script('jquery-ui-tabs');
	
	wp_enqueue_style( 'bp-maps-tabs', BP_MAPS_PLUGIN_URL . '/_inc/css/jquery.ui.tabs.css' );
	wp_enqueue_script( 'bp-maps-admin', BP_MAPS_PLUGIN_URL . '/_inc/js/admin-maps.js' );	
	//wp_enqueue_style( 'bp-maps-admin', BP_MAPS_PLUGIN_URL . '/css/admin.css' );

}
add_action('admin_enqueue_scripts', 'bp_maps_admin_js',1);

function bp_maps_admin() {
	global $bp;
	global $current_site;
	global $errors;
	global $wpdb;

	$options=$bp->maps->options;
	


	if ( isset( $_POST['submit'] ) ) {
		$_options=array();
		
		if (!$errors)
			$errors = new WP_Error();


		switch ( $_POST['action'] ) {
			
			/*
			case 'system-install':
			break;
			*/
		
			case 'options':
			
				check_admin_referer('maps-options');
				//api key
				$_options['api_key']= trim($_POST['bp_maps_api_key']);
				
				if ((!$_options['api_key']) && ($options['map']['options']['display']!='dynamic'))
					$_POST['bp_maps_map_display']='dynamic';
				
				//MAPS OPTIONS
				//size

				foreach ($options['map']['sizes'] as $size=>$value) {

					if (
						(($_POST['bp_maps_map_size_'.$size.'_width'])&&(is_numeric($_POST['bp_maps_map_size_'.$size.'_width']))) 
						&& (($_POST['bp_maps_map_size_'.$size.'_height']) &&((is_numeric($_POST['bp_maps_map_size_'.$size.'_width']))))
					) {
						
						
						$_options['map']['sizes'][$size]=array('width'=>$_POST['bp_maps_map_size_'.$size.'_width'],'height'=>$_POST['bp_maps_map_size_'.$size.'_height']);
					}else {
						$errors->add('map_size_'.$size,sprintf(__( 'Please specify a valid size for the %s maps', 'bp-maps' ),$size));
					}
				}

				//default
				$_options['map']['options']['size'] = $_POST['bp_maps_map_size_default'];
				
				//Display
				if (!empty($_POST['bp_maps_map_display']))
					$_options['map']['options']['display'] = $_POST['bp_maps_map_display'];

				//zoom
				if (($_POST['bp_maps_map_zoom']) && (is_numeric($_POST['bp_maps_map_zoom']))) {
					$_options['map']['options']['zoom'] = $_POST['bp_maps_map_zoom'];
				}else {
					$errors->add('zoom',__( 'Zoom must be a number', 'bp-maps' ));
				}
				
				//DEFAULT LOCATION
				
				//geoIP
				$geoIPfilepath = trim(stripslashes($_POST['bp_maps_map_geoIPfile']));
				
				if ($geoIPfilepath!=ABSPATH) {
					$extension = bp_maps_get_file_extension($geoIPfilepath);
					if ($extension=='dat') {
						if ( file_exists($geoIPfilepath))
							$_options['geoIPfile']=$geoIPfilepath;
					}
				}
				
				if (!$_options['geoIPfile']) {
					$_POST['bp_maps_map_default_loc']=1;
				}
				
				//center
				if ($_POST['bp_maps_map_center']) {
					$location=explode(',',$_POST['bp_maps_map_center']);
					if ((is_array( $location)) && ((is_numeric($location[0])) && (is_numeric($location[1])))) {
						$_options['map']['options']['center'] = implode(',',$location);
					}else{
						$errors->add('zoom',__( 'Latitude/Longitude must be numbers', 'bp-maps' ));
					}
				}
				$_options['map']['options']['default_loc']=$_POST['bp_maps_map_default_loc'];

				//mapTypeControlOptions
				if (!empty($_POST['bp_maps_map_TypeControlOptions']))
					$_options['map']['options']['mapTypeControlOptions'] = $_POST['bp_maps_map_TypeControlOptions'];
				//navigationControlOptions
				if (!empty($_POST['bp_maps_map_navigationControlOptions']))
					$_options['map']['options']['navigationControlOptions'] = $_POST['bp_maps_map_navigationControlOptions'];
				//mapTypeId
				$_options['map']['options']['mapTypeId'] = $_POST['bp_maps_map_mapTypeId'];
				
			break;
			case 'plugins':
				check_admin_referer('maps-plugins');

				//custom markers
				$_options['plugins']['custom_markers']['enabled']=$_POST['bp_maps_plugins_custom_markers'];
					
				//group maps
				$_options['plugins']['groups_maps_custom']['enabled']=$_POST['bp_maps_plugins_groups_maps_custom'];

			break;
		
			
			case 'r-c':
				check_admin_referer('maps-settings-r-c');
				
				$roles_names = bp_maps_get_roles_names();
				
				if ($_POST['reset-r-c']) {
					foreach ($roles_names as $key=>$name) {
						if ($key=='administrator') continue;

						if (!delete_site_option('maps_r&c_'.$key)) {
							$error=true;
						}
					}
					if ($error) {
						$errors->add('r_c_error_reset',__( 'Error while trying to reset capabilities.', 'bp-maps' ));
					}
				}else {
					
					//TO FIX : error when nothing is checked
				
					$capabilities = $bp->maps->capabilities;
					
					foreach($roles_names as $role => $name)
					{
						$role_capabilities = $_POST['cap'][$role];
						
						if ($role=='administrator') continue;
						
						if (update_site_option('maps_r&c_'.$role, $role_capabilities)) {
							$r = get_role($role);
							
							if ($role!='default') {
							
								foreach ($capabilities as $capability => $v)
								{
									if (isset($role_capabilities[$capability])) {
										$r->add_cap($capability);
									}else {
										$r->remove_cap($capability);
									}
								}
								
							}
						}else {
							$error=true;
						}
					}

					if ($error) 
						$errors->add('r_c_error_save',__( 'Error while trying to save capabilities.', 'bp-maps' ));

				}
				$message = __( 'Settings saved.', 'buddypress' );
				$type='updated';
			
			break;
			case 'system-options':

				check_admin_referer('bp-maps-system-options');

				//TO FIX : do not save options
				
				//DEBUG
				$_options['enable_debug'] = (bool) $_POST['enable_debug'];
				
				//RESET OPTIONS

				if ($_POST['reset-options']) {
				
					do_action('bp_maps_options_reset');
					if (!delete_site_option( 'bp_maps_options')) {
						$errors->add('reset_options',__( 'Error while trying to reset options', 'bp-maps' ));
					}else {
						$_options = bp_maps_default_options();
					}
				}
				//CLEAR DATA
				if ($_POST['clear-data']) {
					$wpdb->query("TRUNCATE TABLE {$bp->maps->table_name_markers}");
				}
				//UNINSTALL PLUGIN
				if ($_POST['uninstall-plugin']) {
					//TO FIX	
				}
			break;
		}
		/*
		echo"option:<br/>";
		print_r($options);
		echo"<br><br>";
		echo"_option:<br/>";
		print_r($_options);
		echo"<br><br>";
		*/
		//TO FIX to CHECK
		//without this first line, plugins options args are not parsed !!!
		$_options['plugins'] = wp_parse_args( $_options['plugins'], $options['plugins'] );
		$options = wp_parse_args( $_options, $options ); //changes full array options with updated values
	
		/*
		echo"new option:<br/>";
		print_r($options);
		echo"<br><br>";
		exit;
		*/

		if (update_site_option( 'bp_maps_options', $options )) {
			$message= __( 'Settings saved.', 'buddypress' );
			$type='updated';
			$bp->maps->options=$options;
		}
		
 		do_action( 'bp_maps_admin_screen_save');

		
	//ADMIN MSG
	if ($errors->get_error_message($code)) {
		$message = $errors->get_error_message($code);
		$type='error';
	}
	if ( isset( $message ) ) {;?>
		<div class="<?php //TO FIX : hidden when $type is uncommented //echo $type ?>">
			<p><?php echo $message ?></p>
		</div>
	<?php }
	}
	?>

	<div id="slider" class="wrap">
		<ul id="tabs">
			<?php
			
			$donations_tab='<li><a href="#donations">'.__("Support & Donations", "bp-maps").'</a></li>';
			?>
			
			<li><a href="#options"><?php _e( 'Maps options', 'bp-maps' ) ?></a></li>
			<li><a href="#plugins"><?php _e( 'Plugins' ) ?></a></li>
			<?php do_action( 'bp_maps_admin_tabs' );?>
			<li><a href="#system"><?php _e('System', 'support') ;?></a></li>
			<!--
			<li><a href="#r-c"><?php _e( 'Roles & Capabilities', 'bp-maps' ) ?></a></li>-->
			<?php
			if (bp_maps_is_setup()) {
					echo $donations_tab;
			} ?>
			
			
		</ul>

		<br />
		<div id="options">
			<h2><?php _e( 'Maps Options', 'bp-maps' ) ?></h2>
			
			<form action="#options" name="bp-maps-options" id="bp-maps-options" method="post">				
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><label for="bp_maps_api_key"><?php _e('Api Key','bp-maps'); ?></label></th>
						<td>
							<input class="required" name="bp_maps_api_key" type="text" id="bp_maps_api_key" size="50" value="<?php echo $options['api_key'];?>"/> - <small><?php _e('Needed only if you plan to use static maps.');?> [<?php printf(__('Get it %s','bp-maps'),'<a target="_blank" href="http://code.google.com/apis/maps/signup.html">'.__('here','bp-maps').'</a>');?>]</small>
						</td>
					</tr>
				</table>
			<h3><?php _e( 'Maps Sizes', 'bp-maps' ) ?></h3>
				<table class="form-table">
					<?php
					foreach ($options['map']['sizes'] as $size=>$value) {
					?>
					<tr valign="top">
						<th scope="row"><label for="bp_maps_map_size_<?php echo $size;?>"><?php echo $size; ?></label></th>
						<td>
							<input class="required" name="bp_maps_map_size_<?php echo $size;?>_width" type="text" id="bp_maps_map_size_<?php echo $size;?>_width" size="5" value="<?php echo $options['map']['sizes'][$size]['width'];?>"/>x
							<input class="required" name="bp_maps_map_size_<?php echo $size;?>_height" type="text" id="bp_maps_map_size_<?php echo $size;?>_height" size="5" value="<?php echo $options['map']['sizes'][$size]['height'];?>"/>
							<small> - px</small>
						</td>
					</tr>
					<?php
					}
					?>

					<tr valign="top">
						<th scope="row"><label for="bp_maps_map_size_default"><?php _e('Default Map Size', 'bp-maps') ?></label></th>
						<td>
							<select name="bp_maps_map_size_default"> 
								<?php
								foreach ($options['map']['sizes'] as $size=>$value) {
								?>
									<option value="<?php echo $size;?>"<?php if ($options['map']['options']['size']==$size)echo" SELECTED";?>><?php echo $size;?>
								<?php
								}
								?>
							</select>
						</td>
					</tr>
				</table>

				<h3><?php _e( 'Maps Default Options', 'bp-maps' ) ?></h3>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><label for="bp_maps_map_TypeControlOptions"><?php _e('Display', 'bp-maps') ?></label></th>
						<td>
							<select name="bp_maps_map_display"<?php if (!$options['api_key'])echo" DISABLED";?>>
								<option value="both"<?php if (!$options['map']['options']['display']=='both')echo" SELECTED";?>><?php _e('Static map first, then dynamic map at user\'s click', 'bp-maps') ?>
								<option value="static"<?php if ($options['map']['options']['display']=='static')echo" SELECTED";?>><?php _e('Static map', 'bp-maps') ?>
								<option value="dynamic"<?php if ($options['map']['options']['display']=='dynamic')echo" SELECTED";?>><?php _e('Dynamic map', 'bp-maps') ?>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="bp_maps_map_zoom"><?php _e('Zoom', 'bp-maps') ?></label></th>
						<td>
							<input class="required" name="bp_maps_map_zoom" type="text" id="bp_maps_map_zoom" size="2" value="<?php echo $options['map']['options']['zoom'];?>"/>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="bp_maps_map_default_loc"><?php _e('Default Location', 'bp-maps') ?></label></th>
						<td>
							<p>
							<?php 
								$geoIPfile_value=$options['geoIPfile'];

								$default_loc_1_check=true;
								
								if (!$geoIPfile_value) {
									$default_loc_2_disabled=true;
									$geoIPfile_value=ABSPATH;
								}
								
								if ($options['map']['options']['default_loc']==2) {
									$default_loc_2_check=true;
									$default_loc_1_check=false;
								}
							
							?>
								<input class="required" name="bp_maps_map_default_loc" type="radio" id="bp_maps_map_default_loc" value="1"<?php if ($default_loc_1_check)echo" CHECKED";?>/>
								<input class="required" name="bp_maps_map_center" type="text" id="bp_maps_map_center" size="20" value="<?php echo $options['map']['options']['center'];?>"/>
								<small><?php _e('Latitude', 'bp-maps') ?>,<?php _e('Longitude', 'bp-maps') ?></small>
							</p>
							<p>
								<input class="required" name="bp_maps_map_default_loc" type="radio" id="bp_maps_map_default_loc" value="2"<?php if ($default_loc_2_check)echo" CHECKED";if ($default_loc_2_disabled) echo" DISABLED";?>/>
								<?php _e('Guess it from user\'s IP', 'bp-maps') ?><br><br>
								<small><?php printf(__('To make this work, you have to upload the %s or the %s file from %s, then set its absolute path here', 'bp-maps'),'<a target="_blank" href="http://www.maxmind.com/app/city">GeoIP City</a>','<a target="_blank" href="http://www.maxmind.com/app/geolitecity">GeoLite City</a> (free)','<a target="_blank" href="http://www.maxmind.com">MaxMind</a>') ?></small>
								<input class="required" name="bp_maps_map_geoIPfile" type="text" id="bp_maps_map_geoIPfile" size="100" value="<?php echo $geoIPfile_value;?>"/>								
							</p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="bp_maps_map_TypeControlOptions"><?php _e('Control Options', 'bp-maps') ?></label></th>
						<td>
							<select name="bp_maps_map_TypeControlOptions">
								<option value=""<?php if (!$options['map']['options']['mapTypeControlOptions'])echo" SELECTED";?>><?php _e('none', 'bp-maps') ?>
								<option value="DEFAULT"<?php if ($options['map']['options']['mapTypeControlOptions']=='DEFAULT')echo" SELECTED";?>><?php _e('DEFAULT', 'bp-maps') ?>
								<option value="HORIZONTAL_BAR"<?php if ($options['map']['options']['mapTypeControlOptions']=='HORIZONTAL_BAR')echo" SELECTED";?>><?php _e('HORIZONTAL_BAR', 'bp-maps') ?>
								<option value="DROPDOWN_MENU"<?php if ($options['map']['options']['mapTypeControlOptions']=='DROPDOWN_MENU')echo" SELECTED";?>><?php _e('DROPDOWN_MENU', 'bp-maps') ?>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="bp_maps_map_navigationControlOptions"><?php _e('Navigation Control Options', 'bp-maps') ?></label></th>
						<td>
							<select name="bp_maps_map_navigationControlOptions">  
								<option value=""<?php if (!$options['map']['options']['navigationControlOptions'])echo" SELECTED";?>><?php _e('none', 'bp-maps') ?>
								<option value="DEFAULT"<?php if ($options['map']['options']['navigationControlOptions']=='DEFAULT')echo" SELECTED";?>><?php _e('DEFAULT', 'bp-maps') ?>
								<option value="ZOOM_PAN"<?php if ($options['map']['options']['navigationControlOptions']=='ZOOM_PAN')echo" SELECTED";?>><?php _e('ZOOM_PAN', 'bp-maps') ?>
								<option value="SMALL"<?php if ($options['map']['options']['navigationControlOptions']=='SMALL')echo" SELECTED";?>><?php _e('SMALL', 'bp-maps') ?>
								<option value="ANDROID"<?php if ($options['map']['options']['navigationControlOptions']=='ANDROID')echo" SELECTED";?>><?php _e('ANDROID', 'bp-maps') ?>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="bp_maps_map_mapTypeId"><?php _e('map Type', 'bp-maps') ?></label></th>
						<td>
							<select name="bp_maps_map_mapTypeId">  
								<option value="ROADMAP"<?php if ($options['map']['options']['mapTypeId']=='ROADMAP')echo" SELECTED";?>><?php _e('ROADMAP', 'bp-maps') ?>
								<option value="SATELLITE"<?php if ($options['map']['options']['mapTypeId']=='SATELLITE')echo" SELECTED";?>><?php _e('SATELLITE', 'bp-maps') ?>
								<option value="HYBRID"<?php if ($options['map']['options']['mapTypeId']=='HYBRID')echo" SELECTED";?>><?php _e('HYBRID', 'bp-maps') ?>
								<option value="TERRAIN"<?php if ($options['map']['options']['mapTypeId']=='TERRAIN')echo" SELECTED";?>><?php _e('TERRAIN', 'bp-maps') ?>
							</select>
						</td>
					</tr>
				</table>
				<?php do_action( 'bp_maps_admin_options' );?>
				
				<br />
				<p class="submit">
					<input type="hidden" name="action" value="options" />
					<input class="button-primary" type="submit" name="submit" value="<?php _e('Save Settings', 'buddypress') ?>"/>
				</p>
				<?php wp_nonce_field('maps-options') ?>
			</form>
		</div>
		<div id="plugins">
		<div id="message" class="info">
			<p><font color="red">
			Some strange stuff occurs when saving the options.  After having saved them; please reload the page (click on the Maps link under the BuddyPress menu) to check the settings have been saved correctly.<br>
			We are working at fixing this.</font>
			</p>
		</div>
		<h2><?php _e('Plugins', 'bp-maps') ;?></h2>
		<form action="#plugins" name="bp-maps-plugins" id="bp-maps-plugins" method="post">
			<table class="form-table">


				<tr valign="top">
					<th scope="row"><label for="bp_maps_plugins_custom_markers"><?php _e('Enable Custom Markers', 'bp-maps') ?></label></th>
					<td>
					<?php
					if ($options['plugins']['custom_markers']['enabled']) $custom_markers_checked=" CHECKED";
					echo'<input type="checkbox" value="1" name="bp_maps_plugins_custom_markers"'.$custom_markers_checked.'>';
					?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="bp_maps_plugins_groups"><?php _e('Groups Maps', 'bp-maps') ?></label></th>
					<td>
					<?php

					if ($options['plugins']['groups_maps_custom']['enabled']) $groups_maps_custom_checked=" CHECKED";
					?><input type="checkbox" value="1" name="bp_maps_plugins_groups_maps_custom"<?php echo $groups_maps_custom_checked;?>>
					<?php _e('Enable custom group markers creation for groups admins','bp-maps');?>
					<br>
					</td>
				</tr>
				<?php do_action( 'bp_maps_admin_plugins_enable_table' );?>
			</table>
				
			<br />
			<p class="submit">
				<input type="hidden" name="action" value="plugins" />
				<input class="button-primary" type="submit" name="submit" value="<?php _e('Save Settings', 'buddypress') ?>"/>
			</p>
			<?php wp_nonce_field('maps-plugins') ?>
		</form>
		<?php do_action( 'bp_maps_admin_plugins_div' );?>
		</div>
		
		
		<div id="system">
			<h2><?php _e('System', 'bp-maps') ;?></h2>
			<form action="#system" name="bp-maps-options" id="bp-maps-options" method="post">	
				<h3><?php _e('Reset Options', 'bp-maps') ;?></h3>
				<p>
					<input name="reset-options" type="checkbox" id="reset-options" value="1"/>
					<?php _e( 'Reset all plugin options', 'bp-maps' ); ?>
					 - <small><?php _e( 'This will not affect the markers datas', 'bp-maps' ); ?></small>
					</p>
				</p>
				<p>
					<input name="clear-data" type="checkbox" id="clear-data" value="1"/>
					<?php _e( 'Clear all maps datas', 'bp-maps' ); ?>
					</p>
				</p>
				<?php do_action( 'bp_maps_admin_system_div_reset' );?>
				<p id="submit-types" class="submit">
					<input type="hidden" name="action" value="system-options" />
					<input class="button-primary" type="submit" name="submit" value="<?php _e('Save system Options', 'bp-maps') ?>"/>
				</p>
				<?php wp_nonce_field('bp-maps-system-options') ?>
			</form>
			<?php do_action( 'bp_maps_admin_system_div' );?>
		</div>
		<!--
		<div id='r-c'>
			<h2><?php _e( 'Roles & Capabilities', 'bp-maps' ) ?></h2>
			<form action="#r-c" name="bp-maps-r-c" id="bp-maps-r-c" method="post">
				<table class='form-table'>
					<?php
					$capabilities = $bp->maps->capabilities;

					
					$capability_groups = array(
						'markers' => __('View Markers', 'bp-maps'),
						'my-markers' => __('Create Markers', 'bp-maps'),
						'my-markers-hide' => __('At marker creation, allow user to hide it for', 'bp-maps'),
						'my-markers-show-approx' => __('At marker creation, allow user to choose to show approximate location for', 'bp-maps'),
						
					);
					$grouping_cap = array();
					foreach ($capabilities as $capability => $v)	$grouping_cap[$v['group']] [$capability] = null;

					$roles_names=bp_maps_get_roles_names();

					$counter_tr=0;
					foreach($roles_names as $role => $name)
					{
					
					
					
						if ('administrator' == $role) continue;
						$name = translate_with_context($name);
						$rcs = get_site_option('maps_r&c_' . $role);
						
						//TR CLASSES

						unset($tr_classes);
						unset($tr_classes_str);
						
						$tr_classes[]='mp_sep';
						$tr_classes[]=$role;
						if (empty($rcs))	$tr_classes[]='no_data';
						if (($counter_tr % 2))	$tr_classes[]='alt';
						
						if ($tr_classes)
							$tr_classes_str=' class="'.implode(' ',$tr_classes).'"';
						
					?>
								<tr<?php echo $tr_classes_str;?>>
									<th scope='row' style='width:100px;'><strong><?php echo $name; ?></strong></th>
									<td style='padding:0;'>
										<table>
											<?php
												$col = 6;
												$counter=0;
												foreach ($capability_groups as $group => $groupname)
												{

													if (($role=='default') && ($group=='admin')) continue;

													if (!isset($grouping_cap[$group])) continue;
													
													echo "<tr><td class='capacity name' colspan='" . ($col+1) . "'><i>$groupname</i></td></tr>";
													$i = 0;
													foreach ($grouping_cap[$group] as $capability => $v)
													{
														$capname = $capabilities[$capability]['name'];
																	
														if (0 == $i)	echo "<tr class='".$capability."'><td class='capacity' style='width:10px'></td>\n";
														
														unset($checked);
														if ($rcs) {
															if ($rcs[$capability]) $checked=true;
														}else {
															$checked = $capabilities[$capability]['default'];
														}
														
														
											?>
																		<td class='capacity'>
																			<label for='<?php echo "check_" . $role . "_" . $capability; ?>'>
																				<input class="<?php echo $capability; ?>" id='<?php echo "check_" . $role . "_" . $capability; ?>' name='cap[<?php echo $role; ?>][<?php echo $capability; ?>]' type='checkbox'<?php echo (isset($checked)) ? " checked='checked'" : ''; ?>/>
																				<span id='<?php echo $role . "_" . $capability; ?>' class='<?php echo (isset($checked)) ? 'crok' : 'crko'; ?>'><?php echo $capname; ?></span>
																			</label>
																		</td>
											<?php
														$i++;
														if (intval($i/$col) == ( $i/$col)) {$i = 0; echo "</tr>\n"; }
													}
													$tr = false;
													while (intval($i/$col) != ($i/$col)) { echo "<td style='border-bottom:none;'></td>\n"; $tr = true; $i++;}
													if ($tr) echo "</tr>\n";
												}
										?></table><?php
						$counter_tr++;
					}
					?>
				</table>
				<p id="submit-r-c" class="submit">
					<?php 
						if (!bp_maps_plugin_is_active('buddypress-maps/bp-maps-roles-and-capabilities.php')) {
							echo'<p>';
							printf(__('if you want precise control on each Wordpress role, enable the plugin %s'),'<em>BuddyPress Maps Roles & Capabilities</em>');
							echo'</p>';
						}?>
					<input type="hidden" name="action" value="r-c" />
					<input class="button-primary" type="submit" name="submit" value="<?php _e('Save Settings', 'buddypress') ?>"/>
					<input id="reset-r-c" name="reset-r-c" type="checkbox" value="1"/><?php _e('Reset Capabilities', 'bp-maps') ?>
				</p>
				<?php wp_nonce_field('maps-settings-r-c') ?>
			</form>
		</div>
		-->
		<?php do_action( 'bp_maps_admin_div' );?>
		<div id="donations">
			<h2><?php _e('Support & Donations', 'bp-maps') ;?></h2>
			<h3><?php _e('Donations', 'bp-maps') ;?></h3>
			<p><?php printf(__('Coding this plugin was an long, long way.  If you like it, if you use it, please %s !','bp-maps'),'<a href="http://dev.benoitgreant.be">'.__('Make a donation','bp-maps').'</a>');?>
			<h3><?php _e('Support', 'bp-maps') ;?></h3>
			<p><?php printf(__('You can find installation instructions %s','bp-maps'),'<a href="http://wordpress.org/extend/plugins/buddypress-maps/installation/">'.__('here','bp-maps').'</a>');?>.</p>
			<p><?php printf(__('Bugs, ideas, ... can be reported into the %s','bp-maps'),'<a href="http://dev.benoitgreant.be/bbpress/forum/buddypress-maps">'.__('support forum','bp-maps').'</a>');?>.</p>
		</div>
	</div>
<?php 
}

function bp_maps_plugin_is_active($file,$sitewide=true) {

	$plugins = get_site_option( 'active_sitewide_plugins' );

	if ($sitewide){
		if ( array_key_exists( $file , $plugins ) ) return true;
	}else{
		if ( in_array( $file , $plugins ) ) return true;
	}
}
function bp_maps_get_file_extension ($filename){
	$filename = strtolower($filename) ;
	$exts = split("[/\\.]", $filename) ;
	$n = count($exts)-1;
	$exts = $exts[$n];
	return $exts;
} 




?>