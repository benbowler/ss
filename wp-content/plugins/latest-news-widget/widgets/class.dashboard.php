<?php
if (!class_exists('TL_Latest_News_Widget_Dashboard')) {
	class TL_Latest_News_Widget_Dashboard {
		function install() {
			wp_add_dashboard_widget('latest-news-widget-dashboard', __('WP Blogging Tips, Downloads, SEO Tricks & Exclusive Tutorials', 'latest-news-widget'), array(&$this, 'display'));	
		}
		
		function is_dashboard_page() {
			return (is_admin() && preg_match('/((index\.php)|(wp-admin\/?))$/', $_SERVER['REQUEST_URI']));
		}
		
		function insert_styles() {
			wp_register_style('lnw-dashboard', plugins_url() . '/latest-news-widget/css/latest-news-widget-dashboard.css');
            wp_enqueue_style('lnw-dashboard');
		}
		
		
		function display() {
			?>
			<div id="lnw-dashboard">
				<form action="http://www.aweber.com/scripts/addlead.pl" method="post"> 
				<input type="hidden" name="meta_web_form_id" value="313313020" />
				<input type="hidden" name="meta_split_id" value="" />
				<input type="hidden" name="listname" value="lnw-plugin" />
				<input type="hidden" name="redirect" value="http://www.taylorlovett.com/wordpress-plugins/tutorials-offers-tips/" id="redirect_e986c7209386fa3d07e66a01a3062d0b" />

				<input type="hidden" name="meta_adtracking" value="LNW_-_Wordpress_In-Plugin" />
				<input type="hidden" name="meta_message" value="1" />
				<input type="hidden" name="meta_required" value="name,email" />

				<input type="hidden" name="meta_tooltip" value="" />
				<p>If you are interested in some great, free WordPress stuff like exclusive plugin offers, tutorials, tips, and more, then sign up. Usually this kind of great content sosts money, but we are sick and tired of seeing WordPress websites die out because of improper plugin usage and lack of website knowledge. Signup now, you won't regret it.</p>
				<ul>
					<li><label for="name"><?php _e('Your Name:', 'latest-news-widget'); ?></label> <input name="name" type="text" /></li>
					<li><label for="email"><?php _e('Your Email:', 'latest-news-widget'); ?></label> <input name="email" type="text" /></li>
					<li><input type="submit" value="<?php _e('Sign Up for Free', 'latest-news-widget'); ?>" /></li>
				</ul>
				</form>
			</div>
			<?php
		}
	}
}
?>