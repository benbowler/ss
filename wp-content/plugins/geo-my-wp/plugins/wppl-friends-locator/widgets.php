<?php 
//// REGISTER BUDDYPRESS MEMBER'S LOCATION WIDGET ////
class wppl_widget_bp_member_location extends WP_Widget {
		function wppl_widget_bp_member_location() {
			$widget_location_ops = array( 'classname' => 'wppl_widget_bp_member_location', 'description' => 'Displays BP Member&#39;s Location' ); // Widget Settings
			$control_location_ops = array( 'id_base' => 'wppl_widget_bp_member_location' ); // Widget Control Settings
			$this->WP_Widget( 'wppl_widget_bp_member_location', 'WPPL BP member&#39;s Location', $widget_location_ops, $control_location_ops ); // Create the widget
		}
		
		function widget($args, $instance) {
			if(bp_is_member()) {
				global $bp;
				
				extract( $args );
				//$title 		= $instance['title']; // the widget title
				$map_height 			= $instance['map_height'];
				$map_width 				= $instance['map_width'];
				$directions				= $instance['directions'];
				$map_type				= $instance['map_type'];
				$address				= $instance['address'];
				$no_location			= $instance['no_location'];
			
				echo $before_widget;

				echo $before_title . $bp->displayed_user->fullname . '&#39;s Location' . $after_title;

        		echo do_shortcode('[wppl_member_location map_width="'.$map_width.'" map_height="'.$map_height.'" address="'.$address.'" map_type="'.$map_type.'" directions="'.$directions.'" no_location="'.$no_location.'"]');

				echo $after_widget;
			}
		}

	// Update Settings //
		function update($new_instance, $old_instance) {
			//$instance['title'] 			= strip_tags($new_instance['title']);
			$instance['map_height'] 			= $new_instance['map_height'];
			$instance['map_width']				= $new_instance['map_width'];
			$instance['directions']				= $new_instance['directions'];
			$instance['map_type']				= $new_instance['map_type'];
			$instance['address']				= $new_instance['address'];
			$instance['no_location']			= $new_instance['no_location'];
			
			return $instance;
		}

	// Widget Control Panel //
		function form($instance) {
			$defaults = array( 'title' => 'Location');
			$instance = wp_parse_args( (array) $instance, $defaults ); 
			?>
			<p style="margin-bottom:10px; float:left;">
		    	<lable style="width:100%;float:left;"><?php echo  esc_attr( __( 'Map Width:' ) ); ?></label>
		    	<span style="float:left;width:100%;">
					<input type="text" name="<?php echo $this->get_field_name('map_width'); ?>" value="<?php echo $instance['map_width']; ?>" size="5" />px
				</span>
			</p>
			<p style="margin-bottom:10px; float:left;">
		    	<lable style="width:100%;float:left;"><?php echo  esc_attr( __( 'Map Height:' ) ); ?></lable>
				<span style="float:left;width:100%;">
					<input type="text" name="<?php echo $this->get_field_name('map_height'); ?>" value="<?php echo $instance['map_height']; ?>" size="5" style="float: left;width:"/>px
				</span>
			</p>
			<div class="clear"></div>
			<p style="margin-bottom:10px; float:left;width:100%">
		    	<?php echo '<input type="checkbox" value="1" name="'. $this->get_field_name('directions').'"'; echo ($instance["directions"] == "1" ) ? "checked=checked" : ""; echo 'width="25" style="float: left;margin-right:10px;"/>'; ?>
		    	<lable><?php echo  esc_attr( __( 'Show Directions Link.' ) ); ?></lable>
			</p>
			<p style="margin-bottom:10px; float:left;width:100%">
		    	<?php echo '<input type="checkbox" value="1" name="'. $this->get_field_name('address').'"'; echo ($instance["address"] == "1" ) ? "checked=checked" : ""; echo 'width="25" style="float: left;margin-right:10px;"/>'; ?>
		    	<lable><?php echo  esc_attr( __( 'Show Address.' ) ); ?></lable>
			</p>
			<p style="margin-bottom:10px; float:left;width:100%">
		    	<?php echo '<input type="checkbox" value="1" name="'. $this->get_field_name('no_location').'"'; echo ($instance["no_location"] == "1" ) ? "checked=checked" : ""; echo 'width="25" style="float: left;margin-right:10px;"/>'; ?>
		    	<lable><?php echo  esc_attr( __( 'Show "No  location" message.' ) ); ?></lable>
			</p>
			<p>
			<lable><?php echo esc_attr( __( 'Map Type:' ) ); ?></lable>
				<select name="<?php echo $this->get_field_name('map_type'); ?>" style="float: left;width: 100%;">
					<?php echo '<option value="ROADMAP"'; echo ($instance['map_type'] == "ROADMAP" ) ?'selected="selected"' : ""; echo '>ROADMAP</options>'; ?>
					<?php echo '<option value="SATELLITE"'; echo ($instance['map_type'] == "SATELLITE" ) ?'selected="selected"' : ""; echo '>SATELLITE</options>'; ?>
					<?php echo '<option value="HYBRID"'; echo ($instance['map_type'] == "HYBRID" ) ?'selected="selected"' : ""; echo '>HYBRID</options>'; ?>
					<?php echo '<option value="TERRAIN"'; echo ($instance['map_type'] == "TERRAIN" ) ?'selected="selected"' : ""; echo '>TERRAIN</options>'; ?>
				</select>
			</p>
	<?php } 
}
add_action('widgets_init', create_function('', 'return register_widget("wppl_widget_bp_member_location");'));
?>