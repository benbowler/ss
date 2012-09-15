<?php
/*
Plugin Name: Scoped
Plugin URI: http://pre.vu
Description: Adds a shortcode for Pages/Posts to pull in content from the Scoped engine
Version: 0.2.1
Author: Paul Arterburn
Author URI: http://goscoped.com
License: GPLv2
*/

/**
 * Adds the Scoped Widgets
 */
class scoped_widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'scoped_widget', // Base ID
			'Scoped Widget', // Name
			array( 'description' => __( 'Scoped Dynamic Content Widget', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'scoped_title', $instance['title'] );
		$scoped_id = apply_filters( 'scoped_scoped_id', $instance['scoped_id'] );
		$default_content = apply_filters( 'scoped_default_content', $instance['default_content'] );


		echo $before_widget;
		if ( ! empty( $title ) ) echo $before_title . $title . $after_title;
		echo "<div id='".$scoped_id."'>".html_entity_decode($default_content)."</div>";
		
		//powered by Scoped link was breaking Wordpress rules
		//echo "<span style='font-size:90%;'>&#080;&#111;&#119;&#101;&#114;&#101;&#100;&#032;&#098;&#121; <a href='http://&#103;&#111;&#115;&#099;&#111;&#112;&#101;&#100;&#046;&#099;&#111;&#109;'>Scoped</a>";
		
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['scoped_id'] = strip_tags( $new_instance['scoped_id'] );
		$instance['default_content'] = htmlentities($new_instance['default_content']);

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( $instance ) {
			$title = esc_attr( $instance[ 'title' ] );
			$scoped_id = esc_attr( $instance[ 'scoped_id' ] );
			$default_content = esc_attr( $instance[ 'default_content' ] );
		}
		else {
			$title = __( 'Scoped Content', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p><p><label for="<?php echo $this->get_field_id( 'scoped_id' ); ?>"><?php _e( 'Scoped ID:' ); ?></label> 
		<input size="15" class="widefat" id="<?php echo $this->get_field_id( 'scoped_id' ); ?>" name="<?php echo $this->get_field_name( 'scoped_id' ); ?>" type="text" value="<?php echo $scoped_id; ?>" />
		</p><p><label for="<?php echo $this->get_field_id( 'default_content' ); ?>"><?php _e( 'Default Content:' ); ?></label> 
		<textarea class="widefat" id="<?php echo $this->get_field_id( 'default_content' ); ?>" rows="6" type="text" name="<?php echo $this->get_field_name( 'default_content' ); ?>"><?php echo html_entity_decode($default_content); ?></textarea>
		</p><p>Adjust these content zones in the<br/><a href="admin.php?page=scoped-menu">Scoped Admin Portal</a>.</p>

		<?php 
	}

} // class scoped_widget

// register scoped_widget widget
add_action( 'widgets_init', create_function( '', 'register_widget( "scoped_widget" );' ) );	

/*
function my_scripts_method() {

}    
*/

function scoped_shortcode( $atts, $content = null)	{
	// this will display our message before the content of the shortcode
	return "<div id='".$atts['id']."'>".$content."</div>";
 
}

add_shortcode('scoped', 'scoped_shortcode');
add_shortcode('Scoped', 'scoped_shortcode');
add_shortcode('SCOPED', 'scoped_shortcode');

function add_scoped_button() {
   // Don't bother doing this stuff if the current user lacks permissions
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
     return;
 
   // Add only in Rich Editor mode
   if ( get_user_option('rich_editing') == 'true') {
     add_filter("mce_external_plugins", "add_scoped_tinymce_plugin");
     add_filter('mce_buttons', 'register_scoped_button');
   }
}
 
function register_scoped_button($buttons) {
   array_push($buttons, "|", "yourscoped");
   return $buttons;
}
 
// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
function add_scoped_tinymce_plugin($plugin_array) {
   
   $plugin_array['yourscoped'] = plugins_url( 'editor_plugin.js' , __FILE__ );
   return $plugin_array;
}
 
function my_refresh_mce($ver) {
  $ver += 3;
  return $ver;
}

// init process for button control
add_filter( 'tiny_mce_version', 'my_refresh_mce');
add_action('init', 'add_scoped_button');


if (!class_exists("ScopedWordpressPlugin")) {
	class ScopedWordpressPlugin {
		var $adminOptionsName = "ScopedWordpressPluginAdminOptions";
		function ScopedWordpressPlugin() { //constructor
			
		}
		function init() {
			$this->getAdminOptions();
		}
		//Returns an array of admin options
		function getAdminOptions() {
			$devloungeAdminOptions = array('scoped_api_key' => '');
			$devOptions = get_option($this->adminOptionsName);
			if (!empty($devOptions)) {
				foreach ($devOptions as $key => $option)
					$devloungeAdminOptions[$key] = $option;
			}				
			update_option($this->adminOptionsName, $devloungeAdminOptions);
			return $devloungeAdminOptions;
		}
		

		//Prints out the admin page
		function printAdminPage() {
					$devOptions = $this->getAdminOptions();
										
					if (isset($_POST['update_ScopedWordpressPluginSettings'])) { 
						if (isset($_POST['scoped_api_key'])) {
							$devOptions['scoped_api_key'] = apply_filters('scoped_api_key', $_POST['scoped_api_key']);
						}
						update_option($this->adminOptionsName, $devOptions);
						
						?>
<div class="updated"><p><strong><?php _e("Settings Updated.", "ScopedWordpressPlugin");?></strong></p></div>
					<?php
					} ?>
<div class=wrap>
<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
<h2>Scoped Setup</h2>
<h3>Scoped API Key <span style="font-size:70%;">(get yours <a href="http://goscoped.com/sign-up.php" target="_blank">here</a>)</span></h3>
<input type="text" name="scoped_api_key" size="50" value="<?php _e(apply_filters('format_to_edit',$devOptions['scoped_api_key']), 'ScopedWordpressPlugin') ?>">
<div class="submit">
<input type="submit" name="update_ScopedWordpressPluginSettings" value="<?php _e('Update Settings', 'ScopedWordpressPlugin') ?>" /></div>
</form>
 </div>
					<?php
				}//End function printAdminPage()


		function Main() {
			    if (!current_user_can('manage_options'))  {
			        wp_die( __('You do not have sufficient permissions to access this page.') );
			    }

			    echo '<div class="wrap">';
			   	echo '<h2>Scoped</h2>';
			   	echo '<p><br/><br/><br/>Scoped is not quite ready for the masses just yet please subscribe to our beta tester list to get early access!</p>';

			   	?>
              
              <br/><br?><form onsubmit="return ConversionCount()" action="http://goscoped.us4.list-manage.com/subscribe/post?u=de91cb0b6333bf5bb9e1c0fa9&amp;id=6ae7f3c617" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="form-inline" target="_blank">
                <span class="add-on"><i class="icon-envelope"></i></span><input type="email" value="" name="EMAIL" id="mce-EMAIL2" placeholder="email address" required> <button type="submit" class="btn"><i class="icon-check"></i>Subscribe</button>
                <!-- <input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="btn"> -->
              </form>

			   	<?php
			  	//echo '<iframe src="http://goscoped.com/index.php?template=wordpress" style="width: 100%; height: 80%; min-height: 600px;"></iframe>';
			    echo '</div>';
			}

		function ConfigureMenu() {
			add_menu_page("Scoped", "Scoped", 6, basename(__FILE__), array(&$dl_pluginSeries,'Main'));
			add_submenu_page( "scoped-menu", "Setup", "Setup", 6, basename(__FILE__),  array(&$dl_pluginSeries,'printAdminPage') );
		}			

		function add_settings_link($links, $file) {
		static $this_plugin;
		if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);
		 
		if ($file == $this_plugin){
			$settings_link = '<a href="admin.php?page=scoped-sub-menu">'.__("Setup", "scoped-wordpress-plugin").'</a>';
			 array_unshift($links, $settings_link);
		}
			return $links;
		 }
	
	}

} //End Class ScopedWordpressPlugin

if (class_exists("ScopedWordpressPlugin")) {
	$dl_pluginSeries = new ScopedWordpressPlugin();
}

//Initialize the admin panel
if (!function_exists("ScopedWordpressPlugin_ap")) {
	function ScopedWordpressPlugin_ap() {
		global $dl_pluginSeries;
		if (!isset($dl_pluginSeries)) {
			return;
		}

		add_menu_page("Scoped", "Scoped", 6, "scoped-menu", array(&$dl_pluginSeries,'Main'));
		add_submenu_page( "scoped-menu", "Setup", "Setup", 6, "scoped-sub-menu",  array(&$dl_pluginSeries,'printAdminPage') );

		if (function_exists('add_options_page')) {
			add_options_page('Scoped Setup', 'Scoped Setup', 9, basename(__FILE__), array(&$dl_pluginSeries, 'printAdminPage'));
		}

	}	
}

//Actions and Filters	
if (isset($dl_pluginSeries)) {
	//Actions
	add_action('admin_menu', 'ScopedWordpressPlugin_ap');
	add_action('scoped-wordpress-plugin/scoped.php',  array(&$dl_pluginSeries, 'init'));

			$devOptions = get_option("ScopedWordpressPluginAdminOptions");
			if (!empty($devOptions)) {
				foreach ($devOptions as $key => $option)
					$ScopedAdminOptions[$key] = $option;
			}		

	$scoped_api_key = $ScopedAdminOptions["api_key"];
	
	add_filter('plugin_action_links', array(&$dl_pluginSeries, 'add_settings_link'), 10, 2 );

    wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js');
    wp_enqueue_script( 'jquery' );
    wp_register_script( 'scopedjs', 'https://goscoped.com/api/js/'.$scoped_api_key);
    wp_enqueue_script( 'scopedjs'); 

}


?>