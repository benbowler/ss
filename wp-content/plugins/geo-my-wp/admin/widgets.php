<?php
////////////////////////////////////////////////////////////
/////// WIDGETS REGISTRATION PAGE //////////
//////////////////////////////////////////////////////////

//// Register Search Form widget ////
class wppl_widget extends WP_Widget {
	// Constructor //
		function wppl_widget() {
			$widget_ops = array( 'classname' => 'wppl_widget', 'description' => 'Displays Places Locator Search form in your sidebar' ); // Widget Settings
			$control_ops = array( 'id_base' => 'wppl_widget' ); // Widget Control Settings
			$this->WP_Widget( 'wppl_widget', 'WPPL Search Form', $widget_ops, $control_ops ); // Create the widget
		}
	// Extract Args //
		function widget($args, $instance) {
			extract( $args );
			$title 		= apply_filters('widget_title', $instance['title']); // the widget title
			$short_code	= $instance['short_code'];

			echo $before_widget;

			if ( $title ) { echo $before_title . $title . $after_title; }

        	echo do_shortcode('[wppl form="'.$short_code.'" form_only="1"]');

			echo $after_widget;
		}

	// Update Settings //
		function update($new_instance, $old_instance) {
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['short_code'] = $new_instance['short_code'];
			//$instance['pages'] = $new_instance['pages'];
			return $instance;
		}

	// Widget Control Panel //
		function form($instance) {
			$w_posts = get_post_types();
			$defaults = array( 'title' => 'Search Places');
			$instance = wp_parse_args( (array) $instance, $defaults ); 
			$shortcodes = get_option('wppl_shortcode');
			?>
  			<p style="margin-bottom:10px; float:left;">
		    	<lable><?php echo  esc_attr( __( 'Title:' ) ); ?></lable>
				<input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" width="25" style="float: left;width: 100%;"/>
			</p>
			<p>
			<lable><?php echo esc_attr( __( 'Choose shortcode to use in the sidebar:' ) ); ?></lable>
				<select name="<?php echo $this->get_field_name('short_code'); ?>" style="float: left;width: 100%;">
				<?php foreach ($shortcodes as $shortcode) {
					echo '<option value="' . $shortcode[form_id] . '"'; echo ($instance['short_code'] == $shortcode[form_id] ) ?'selected="selected"' : ""; echo '>wppl form="' . $shortcode[form_id] . '"</options>';
						} ?>
				</select>
			</p>
	<?php } 
	 }
add_action('widgets_init', create_function('', 'return register_widget("wppl_widget");'));

//// Register User Location widget ////
class wppl_widget_location extends WP_Widget {
	// Constructor //
		function wppl_widget_location() {
			$widget_location_ops = array( 'classname' => 'wppl_widget_location', 'description' => 'Displays user current location' ); // Widget Settings
			$control_location_ops = array( 'id_base' => 'wppl_widget_location' ); // Widget Control Settings
			$this->WP_Widget( 'wppl_widget_location', 'WPPL User Current Location', $widget_location_ops, $control_location_ops ); // Create the widget
		}
	// Extract Args //
		function widget($args, $instance) {
			extract( $args );
			$title_location 		= $instance['title_location']; // the widget title
			$short_code_location	= $instance['short_code_location'];
			$display_by 			= $instance['display_by'];
			$name_guest 			= $instance['name_guest'];
			
			echo $before_widget;

			//if ( $title_location ) { echo $before_title . $title_location . $after_title; }

        	echo do_shortcode('[wppl_location show_name="'.$name_guest.'" display_by="'.$display_by.'" title="'.$title_location.'"]');

			echo $after_widget;
		}

	// Update Settings //
		function update($new_instance, $old_instance) {
			$instance['title_location'] 		= strip_tags($new_instance['title_location']);
			$instance['short_code_location'] 	= $new_instance['short_code_location'];
			$instance['display_by'] 			= $new_instance['display_by'];
			$instance['name_guest'] 			= $new_instance['name_guest'];
			
			return $instance;
		}

	// Widget Control Panel //
		function form($instance) {
			$defaults = array( 'title' => 'WPPL User Location');
			$instance = wp_parse_args( (array) $instance, $defaults ); 
			?>
  			<p style="margin-bottom:10px; float:left;">
		    	<lable><?php echo  esc_attr( __( 'Title: (ex:"Your Location")' ) ); ?></lable>
				<input type="text" name="<?php echo $this->get_field_name('title_location'); ?>" value="<?php echo $instance['title_location']; ?>" width="25" style="float: left;width: 100%;"/>
			</p>
			<p style="margin-bottom:10px; float:left;width:100%">
		    	<?php echo '<input type="checkbox" value="1" name="'. $this->get_field_name('name_guest').'"'; echo ($instance["name_guest"] == "1" ) ? "checked=checked" : ""; echo 'width="25" style="float: left;margin-right:10px;"/>'; ?>
		    	<lable><?php echo  esc_attr( __( 'Display User Name.' ) ); ?></lable>
			</p>
			<p>
			<lable><?php echo esc_attr( __( 'Display location by:' ) ); ?></lable>
				<select name="<?php echo $this->get_field_name('display_by'); ?>" style="float: left;width: 100%;">
					<?php echo '<option value="zipcode"'; echo ($instance['display_by'] == "zipcode" ) ?'selected="selected"' : ""; echo '>Zipcode</options>'; ?>
					<?php echo '<option value="city"'; echo ($instance['display_by'] == "city" ) ?'selected="selected"' : ""; echo '>City, State</options>'; ?>
				</select>
			</p>
	<?php } 
	 }
add_action('widgets_init', create_function('', 'return register_widget("wppl_widget_location");'));
