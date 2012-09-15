<?php
   //// SHORTCODE TO DISPLAY FORM AND RESULTS ////

function wppl_shortcode($params) {
	global $post_types, $wppl_options, $wppl;
	$post_types = array();

	// get and extract shortcode settings //
	$options_r = get_option('wppl_shortcode'); 
	$wppl = $options_r[$params[form]];
    extract(shortcode_atts($wppl, $params)); 
    
    ob_start(); 
    
    if(!$friends_search) {
   		$ptc = count($post_types);
    	$pti = implode( " ",$post_types); 
		($_GET["wppl_post"] == $pti ) ? $pti_all = 'selected="selected"' : $pti_all = "";
	}
	
	$miles = explode(",", $distance_values);
	
	$results_page = ($friends_search) ? $wppl_options['friends_results_page'] : $wppl_options['results_page'];
	
	if($params[form_only] == 1) { 	
		$widget = "widget-"; $faction = get_bloginfo('wpurl') . "/" . $results_page;
	} elseif ($params[form_only] == "y") { 
		$faction = get_bloginfo('wpurl') . "/" . $results_page; $widget = "";
	} elseif ( (empty($params[form_only])) ){ 
		$faction = ""; $widget = "";
	} 
	?>
	<!-- DISPLAY SEARCH FORM -->	
	<div class="wppl-<?php echo $widget; ?>form-wrapper">
		<form class="wppl-<?php echo $widget; ?>form" name="wppl_form" action="<?php echo $faction; ?>" method="get">
			<input type="hidden" name="pagen" value="0" />
				
				<?php if ( $friends_search && ($proflie_fields || $profile_fields_date) )  bp_fields_dropdown($wppl); ?>
				
				<?php if (!$friends_search) { ?>
				
					<!-- only if more then one post type entered display them in dropdown menu otherwise no dropdown needed -->
					<?php if($ptc>1) { ?>
						<div class="wppl-post-type-wrapper">
							<label for="wppl-post-type">What are you looking for: </label>				
								<!-- post types dropdown -->
							<select name="wppl_post" id="wppl-post-type">
								<option value="0"<?php echo $pti_all; ?>> -- Search Site -- </option>
								<?php foreach ($post_types as $post_type) { ?>
								<?php if( $_GET["wppl_post"] == $post_type ) {$pti_post = 'selected="selected"';} else {$pti_post = "";} ?>
								<option value=<?php echo '"' .$post_type .'"' .$pti_post; ?>><?php echo get_post_type_object($post_type)->labels->name; ?></option>			
								<?php } ?>
							</select>
						</div> <!-- post type wrapper --> 
					<?php } else { ?>
						<input type="hidden" name="wppl_post" value="<?php echo implode(' ',$post_types);  ?>" />
					<?php } 
					
					/* post category disply categories only when one post type chooses 
				 	we cant switch categories between the post types if more then one */ 
					if ($ptc<2 && (isset($taxonomies))) { ?>	
						<div class="wppl-category-wrapper">			
							<?php // output dropdown for each taxonomy //
							foreach ($taxonomies as $tax) { ?>
								<div id="<?php echo $tax . '_cat'; ?>">
									<label for="wppl-category-id"><?php echo get_taxonomy($tax)->labels->singular_name; ?>: </label>
									<?php custom_taxonomy_dropdown($tax); ?>				
								</div>
							<?php } /* end foreach */ ?>			
						</div><!-- category-wrapper -->
					<?php } /* end taxonomies dropdown */ ?>
				
				<?php } ?>
				
				<div class="wppl-address-wrapper">        		
					<?php if(!$address_title_within) echo '<label for="wppl-address">' .$address_title. '</label>'; ?>
					<div class="wppl-address-field-wrapper">
						<input type="text" name="wppl_address" class="wppl-address" value="<?php echo $_GET['wppl_address']; ?>" size="35" <?php echo($address_title_within) ? 'placeholder="'. $address_title . '"' : ""; ?> />
						<button type="button" value="<?php echo $params[form]; ?>" onClick="getLocation();formId=this.value;" class="wppl-locate-me-btn"><img src="<?php echo plugins_url('images/locator-images/'.$wppl_options['locator_icon'], __FILE__); ?>" /></button>
					</div>
					<?php if($wppl_options['show_locator_icon']) { ?>
						
					<?php } ?>
				</div><!-- end address wrapper -->
		
				<!--distance values dropdown	-->
				<div class="wppl-distance-units-wrapper">
					<?php if ($display_radius) { ?>
						<div class="wppl-distance-wrapper">
							<select name="wppl_distance" class="wppl-distance" id="wppl-distance">
								<option value="<?php echo end($miles); ?>"><?php if ($display_units) echo  '- Within -'; else echo ($units_name == "imperial") ? "- Miles -" : "- Kilometers -"; ?> </option>
								<?php foreach ($miles as $mile) { ?>
									<?php if( $_GET["wppl_distance"] == $mile ) $mile_s = 'selected="selected"'; else $mile_s = ""; ?>
									<option value=<?php echo '"' .$mile .'"' .$mile_s; ?>><?php echo $mile; ?></option>
								<?php } ?>
							</select>
						</div>
					<?php } else { ?>
						<div class="wppl-distance-wrapper">
							<input type="hidden" name="wppl_distance" value="<?php echo end($miles); ?>" />
						</div>
					<?php } ?>
				
	
					<?php if ($display_units) { ?>
						<div class="wppl-units-wrapper">			
							<?php if ($_GET['wppl_units'] == 'metric')  $unit_m = 'selected="selected"'; else $unit_i = 'selected="selected"'; ?>
							<select name="wppl_units" class="wppl-units">
								<option value="imperial" <?php echo $unit_i; ?>>Miles</option>
								<option value="metric" <?php echo $unit_m; ?>>Kilometers</option>
							</select>
						</div>
					<?php } else { ?>
						<div class="wppl-units-wrapper">	
							<input type="hidden" name="wppl_units" value="<?php echo $units_name; ?>" />
						</div>
					<?php } ?>
				</div><!-- distance unit wrapper -->
			<div class="wppl-submit">
				<input name="submit" type="submit" id="wppl-submit-<?php echo $params[form]; ?>" value="Submit" />
				<input type="hidden" name="wppl_form" value="<?php echo $params[form]; ?>" />
				<input type="hidden" name="action" value="wppl_post" />
			</div>	
		</form>
	</div><!--form wrapper -->	
<?php 	
	if (empty($params[form_only])) {
		if ($friends_search) 
			wppl_bp_query_results ($params, $wppl, $wppl_options);
		else 
			wppl_get_results($params, $wppl, $wppl_options);	
    }
    
    $output_string=ob_get_contents();
	ob_end_clean();

	return $output_string;
} //end of shortcode
add_shortcode( 'wppl' , 'wppl_shortcode' );

///// SHORTCODE TO DISPLAY RESULTS  ////
function wppl_get_results($params, $wppl, $wppl_options) {

	if(!empty( $_GET['action'] ) &&  $_GET['action'] == "wppl_post") {		
 		
 		global $wppl, $wpdb, $post, $org_address, $wppl_options, $lat , $long, $distance_between, $post_types, $unit_a, $wppl_options,$per_page, $posts_within, $total_rows, $from_page, $wppl_units; 
    	
		$ptc = count(explode( " ",$_GET['wppl_post']));
	   		
   		if (empty($params[form])) {	
   			$options_r = get_option('wppl_shortcode'); 
			$wppl = $options_r[$_GET['wppl_form']];
   			ob_start();	
   		}
   		
   		// shortcode attributed //	
   		extract(shortcode_atts($wppl, $get_results)); 
   		
   		echo '<link rel="stylesheet" type="text/css" media="all" href="' . plugins_url('themes/'.$results_template.'/css/style.css', __FILE__) .'">';
   		
		if ($friends_search) 
			{ ( $total_fields ) ? bp_query_fields($total_fields) : bp_query_results();}
   		   		
 		// distance units //
		if ($_GET['wppl_units'] == "imperial") 
			$unit_a = array('radius' => 3959, 'name' => "Mi", 'map_units' => "ptm", 'units'	=> 'imperial');
		else 
			$unit_a = array('radius' => 6371, 'name' => "Km", 'map_units' => 'ptk', 'units' => "metric");
		
   		$org_address = str_replace(" ", "+", $_GET['wppl_address']);
   			
    	// radius value //
 		$radius = $_GET['wppl_distance'];	
		$from_page = $_GET['pagen'];
		
		/////// CONVERT ORIGINAL ADDRESS TO LATITUDE/LONGITUDE ////	
		$do_it = ConvertToCoords($org_address);
	
		// marker for "your location" //
		(!empty($org_address)) ? $your_loc = array("Your Location", $lat,$long) : $your_loc = "0";
	
		if ($_GET['wppl_post'] != "0") $wppl_post = $_GET['wppl_post']; else $wppl_post = $post_types; 
		
		$order_by = "wposts.post_title";
	
		if (!empty($org_address)) {
			$having = "HAVING distance <= $radius OR distance IS NULL";
			$order_by = "distance";
			$calculate = ", ROUND(" . $unit_a['radius'] . "* acos( cos( radians( $lat ) ) * cos( radians( wposts.lat ) ) * cos( radians( wposts.long ) - radians( $long ) ) + sin( radians( $lat ) ) * sin( radians( wposts.lat) ) ),1 )  AS distance";
			$showing = 'within ' . $radius . ' ' . $unit_a['name'] . ' from ' . $_GET['wppl_address'];
		}	
		
		$get_them = 1;
	
	} elseif ( ($_COOKIE['wppl_lat']) && ($_COOKIE['wppl_long']) && ($params[form]) && ($wppl_options['auto_search'] == 1) ) {
	
		global $wpdb, $post, $distance_between, $post_types, $lat , $long, $org_address, $unit_a, $wppl_options,$per_page, $posts_within, $total_rows, $from_page, $wppl_units; 
		
		$options_r = get_option('wppl_shortcode'); 
		
		$org_address = str_replace(" ", "+", $_COOKIE['wppl_city']);
   		
   		extract(shortcode_atts($options_r[$params[form]], $get_results)); 		
   		
   		echo '<link rel="stylesheet" type="text/css" media="all" href="' . plugins_url('themes/'.$results_template.'/css/style.css', __FILE__) .'">';
    	
		if ($wppl_options['auto_units'] == "imperial") 
			$unit_a = array('radius' => 3959, 'name' => "Mi", 'map_units' => "ptm", 'units'	=> 'imperial');
		else 
			$unit_a = array('radius' => 6371, 'name' => "Km", 'map_units' => 'ptk', 'units' => "metric");
		
		$radius = $wppl_options['auto_radius'];
		$from_page  = ($_GET['pagen']) ? $_GET['pagen'] : 0;
		$lat = $_COOKIE['wppl_lat'];
		$long = $_COOKIE['wppl_long'];
		$wppl_post = $post_types;
		$your_loc = array("Your Location", $lat,$long);
		$having = "HAVING distance <= $radius OR distance IS NULL";
		$order_by = "distance";
		$calculate = ", ROUND(" . $unit_a['radius'] . "* acos( cos( radians( $lat ) ) * cos( radians( wposts.lat ) ) * cos( radians( wposts.long ) - radians( $long ) ) + sin( radians( $lat ) ) * sin( radians( wposts.lat) ) ),1 )  AS distance";
		$showing = 'within ' . $radius . ' ' . $unit_a['name'] . ' Near your location';
			
		$auto_se = 1;
		$get_them = 1;			
	}
	
	if($get_them == 1) {	
	//// CREATE TAXONOMIES VARIATIONS TO EXECUTE WITH SQL QUERY ////
		if ($ptc<2 && (isset($taxonomies))) {
			// count that we have at leaset one category choosen //
			//otherwise no need to run the first wp_query//
			$rr = 0; 
			$args = array('relation' => 'AND');
			foreach ($taxonomies as $tax) { 
				if ($_GET[$tax] != 0) {
					$rr++;
					$args[] = array(
						'taxonomy' => $tax,
						'field' => 'id',
						'terms' => array($_GET[$tax])
					);
				}		
			}
			if($rr == 0) $args = '';	
		}
			
		$query = new WP_Query( array('post_type' => $wppl_post, 'post_status' => array('publish'), 'tax_query' => $args, 'fields' => 'ids', 'posts_per_page'=>-1 ));		
	
		$sql ="
		SELECT wposts.* {$calculate}
			FROM " . $wpdb->prefix . "places_locator  wposts	
    		WHERE 
    		(1 = 1)
    		AND wposts.post_id IN (" . implode( ',' , $query->posts) . ")	
			{$having} ORDER BY {$order_by}" ;
		
		/// total results within the radius requested ///
		$total_results = $wpdb->get_results($sql);
		
		/// divide results to pages ///
		$posts_within = array_chunk($total_results,$per_page);
		
		/// count number of pages ///
		$pages = count($posts_within);
		
		/// posts to display ///
		$posts_within = $posts_within[$from_page];
		
		//// CKECK IF WE GOT AT LEAST ONE RESULT TO DISPLAY   ////
		//// OTHERWISE NO NEED TO RUN THE FUNCTION BELOW ////
		if (!empty($posts_within)) { 
			global $pc, $mc;
			//////// DISPLAY OUR POSTS LOOP - YAAAAAAYYY  /////////	
			$mc = 1; /* single maps counter */ 
			$pc = ($from_page * $per_page) +1; /* post count, to display number of post */
		
			if( ($results_type == "both") || ($results_type == "map") ) $map_yes = 1;
	
			echo '<div id="wppl-output-wrapper" style="width:'; echo ($main_wrapper_width) ? $main_wrapper_width.'px' : '100%'; echo '">';
				if ($map_yes == 1)echo '<div id="show-hide-btn-wrapper"><a href="#" class="map-show-hide-btn"><img src="'.plugins_url('images/show-map-icon.png', __FILE__).'" /></a></div>';
			
				echo '<div class="wppl-pagination-wrapper">';
					echo '<h2 class="wppl-h2"> Showing ' . count($posts_within) . ' out of ' . count($total_results) . ' results ' . $showing . '</h2>';
					
					pagination_links($pages, $per_page);
				
				echo '</div>'; // pagination wrapper//
				
				///// DISPLAY MAP  /////		
				if ($map_yes == 1) {
					echo '<div id="wppl-hide-map" style="float:left;">';
					echo	'<div class="wppl-map-wrapper" style="position:relative">';
					echo		'<div id="map" style="width:'; echo (!empty($map_width)) ? $map_width : 500 ; echo 'px; height:'; echo (!empty($map_height)) ? $map_height : 500; echo 'px;"></div>';
					echo		'<img class="map-loader" src="'.plugins_url('images/map-loader.gif', __FILE__).'" style="position:absolute;top:45%;left:33%;"/>';
					echo	'</div>';// map wrapper //
					echo '</div>'; // show hide map //	
					
				};	
				echo	'<div class="clear"></div>';	
	
				/*if($featured_posts_scroller == 9) {
					//$wid = (620/$scroller_posts)-5; $sc = 1;
					//echo '<script>'.'imgLinks= '.json_encode( plugins_url('plugins/featured-posts-scroller/images/',__FILE__) ),';'; echo '</script>';
						
					$featured_posts_within = array_random_assoc($total_results, $random_featured_count);
					
					include_once 'plugins/featured-posts/results.php';
					//wp_enqueue_script( 'wppl-scroller');
				} */
				
				if($random_featured_posts == 1) {
					$wid = (100/$random_featured_count)-3;			
					$featured_posts_within = array_random_assoc($total_results, $random_featured_count);
					include_once 'plugins/featured-posts/results.php';
				}
			
				//////  DISPLAY POSTS ///////
				if( ($results_type == "both") || ($results_type == "posts") ) {
					echo '<div class="wppl-results-wrapper">';
						if( file_exists( STYLESHEETPATH. 'themes/'.$results_template .'/results.php' ) ) {
    						include(STYLESHEETPATH . 'themes/'.$results_template .'/results.php');
						} else {	
							include('themes/'.$results_template .'/results.php');	
						}
					echo '</div>'; // results wrapper //
			
					echo 	'<div id="wppl-go-top">Top</div>';
					echo	'<div class="wppl-pagination-wrapper">';
					echo		'<h2 class="wppl-h2"> Showing ' . count($posts_within) . ' out of ' . count($total_results) . ' results ' . $showing . '</h2>';	
									pagination_links($pages, $per_page);
					echo	'</div>'; // pagination wrapper//
				}
				echo	'</div>'; // output wrapper //
				
				if ( ($map_yes == 1) || ($single_map == 1) ) {
				echo '<script type="text/javascript">'; 
					echo 'yourLocationIcon= '.json_encode('http://maps.google.com/mapfiles/ms/icons/blue-dot.png'),';';
					echo 'singleMapType= '.json_encode($single_map_type),';';
					echo 'locations= '.json_encode($posts_within),';'; 
					echo 'your_location= '.json_encode($your_loc),';'; 
					echo 'page= '.json_encode($from_page * $per_page),';'; 
					echo 'mapType= '.json_encode($map_type),';';
					echo 'additionalInfo= '.json_encode($additional_info),';';
					echo 'zoomLevel= '. $zoom_level,';';
					echo 'autoZoom= '.json_encode($auto_zoom),';';
					echo 'perPostIcon= '.json_encode($per_post_icon),';'; 	
					echo 'mainIconsFolder= '.json_encode(plugins_url('/map-icons/',__file__) ),';';
					echo 'mainIcon= '.json_encode($map_icon),';';
					echo 'ylIcon= '.json_encode($your_location_icon),';';
					echo 'units= '.json_encode($unit_a),';';
				echo '</script>';
				wp_enqueue_script( 'wppl-infobox', true);
				}
				
				if ($map_yes == 1) wp_enqueue_script( 'wppl-map', true);
				if ($single_map == 1) wp_enqueue_script( 'wppl-small-maps', true);
				
    		// DONE - GOOD JOB //	
    	// IF NO RESULTS //
    	} else { 					
    		echo '<h2 class="wppl-h2">' . $wppl_options['no_results']. '</h2>'; echo ($wppl_options['wider_search']) ? ' <p>please, <a href="'. add_query_arg('wppl_distance', $wppl_options['wider_search_value']). '" onclick="document.wppl_form.submit();">click here</a> to search within a greater range or <a href="'. add_query_arg('wppl_address', ''). '" onclick="document.wppl_form.submit();">click here</a> to see all results. </p>' : ''; echo '';
   		} // end function //
    }	 
    
    if (empty($params[form])) {
   		$output_results=ob_get_contents();
		ob_end_clean();
		return $output_results;
	}
      
}
add_shortcode( 'wppl_results' , 'wppl_get_results' );		
?>
