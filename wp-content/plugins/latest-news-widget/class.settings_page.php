<?php
if (!class_exists('TL_Latest_News_Widget_Settings_Page')) {
	class TL_Latest_News_Widget_Settings_Page {
		var $action_complete = '';
		
		function admin_styles() {
			wp_register_style('lnw-admin', plugins_url() . '/latest-news-widget/css/latest-news-widget-admin.css');
			wp_enqueue_style('lnw-admin');
		}
	
		function TL_Latest_News_Widget_Settings_Page() {
			$admin_options = TL_Latest_News_Widget_Utils::get_admin_options();
			if (!empty($_POST['settings'])) {
				$_POST['settings'] = array_map(array('TL_Latest_News_Widget_Utils', 'encode_option'), $_POST['settings']);
				$admin_options = array_merge($admin_options, $_POST['settings']);
				$this->action_complete = __('Your settings have been successfully saved!', 'latest-news-widget');
				update_option(LATEST_NEWS_WIDGET_OPTION_NAME, $admin_options);
			}
			?>
			<div id="latest-news-widget-admin">
				<h2><?php _e('Latest News Widget', 'latest-news-widget'); ?></h2>
				<a class="genesis" href="http://www.shareasale.com/r.cfm?b=241369&u=481196&m=28169&urllink=&afftrack="><?php _e('Latest News Widget works best with any of the 20+ ', 'custom-contact-forms'); ?><span><?php _e('Genesis', 'custom-contact-forms'); ?></span> <?php _e('Wordpress child themes. The', 'custom-contact-forms'); ?> <span><?php _e('Genesis Framework', 'custom-contact-forms'); ?></span> <?php _e('empowers you to quickly and easily build incredible websites with WordPress.', 'custom-contact-forms'); ?></a>
				
				<form class="blog-horizontal-form" method="post" action="http://www.aweber.com/scripts/addlead.pl">
					<input type="hidden" name="meta_web_form_id" value="313313020" />
					<input type="hidden" name="meta_split_id" value="" />
					<input type="hidden" name="listname" value="lnw-plugin" />
					<input type="hidden" name="redirect" value="http://www.taylorlovett.com/wordpress-plugins/tutorials-offers-tips/" id="redirect_e986c7209386fa3d07e66a01a3062d0b" />

					<input type="hidden" name="meta_adtracking" value="LNW_-_Wordpress_In-Plugin" />
					<input type="hidden" name="meta_message" value="1" />
					<input type="hidden" name="meta_required" value="name,email" />

					<input type="hidden" name="meta_tooltip" value="" />
					<span><?php _e("WP Blogging Tips, Downloads, SEO Tricks & Exclusive Tutorials", 'latest-news-widget'); ?></span>
					<input type="text" name="name" value="Your Name" onclick="value=''" />
					<input type="text" name="email" value="Your Email" onclick="value=''" />
					<input type="submit" value="<?php _e('Sign Up for Free', 'latest-news-widget'); ?>" />
				</form>
				<?php if (!empty($this->action_complete)) { ?>
				<div class="action-complete">
					<?php echo $this->action_complete; ?>
				</div>
				<?php } ?>
				<div id="general-settings" class="postbox">
					<h3 class="hndle"><span>
					  <?php _e("General Settings", 'latest-news-widget'); ?>
					  </span></h3>
					<div class="inside">
						<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
						<ul>
							<li><?php _e("Not only does Latest News Widget allow you to add new posts to your sidebar, but it adds a cool widget to your dashboard that shows Wordpress blogging tips, free downloads, tutorials, SEO tips, etc. You can enable or disable the widget by toggling the following setting.", 'latest-news-widget'); ?></li>
							<li>
								<label><?php _e('Dashboard Widget:', 'latest-news-widget'); ?></label> 
								<select name="settings[enable_dashboard_widget]">
									<option value="1"><?php _e("Enabled", 'latest-news-widget'); ?></option>
									<option <?php if ($admin_options['enable_dashboard_widget'] != 1) echo 'selected="selected"'; ?> value="0"><?php _e("Disabled", 'latest-news-widget'); ?></option>
								</select>
							</li>
							<li><input type="submit" value="<?php _e('Save', 'latest-news-widget'); ?>" />
						</ul>
						</form>
					</div>
				</div>
			</div>
			<?php
		}
	}
}
?>