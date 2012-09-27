<?php
/*
	Plugin Name: Latest News Widget
	Plugin URI: http://taylorlovett.com/wordpress-plugins
	Description: Insert a "Latest News Widget" in to your sidebar. Very customizable and easy to use! Comes packed with options: choose up to three categories to pull posts from, show the excerpt or the content, optionally show the byline and post title, optionally show "Read More" text, etc.
	Version: 1.0.1
	Author: Taylor Lovett
	Author URI: http://www.taylorlovett.com
*/

/*
	If you have time to translate this plugin in to your native language, please contact me at 
	admin@taylorlovett.com and I will add you as a contributer with your name and website to the
	Wordpress plugin page.
	
	Languages: English

	Copyright (C) 2010-2011 Taylor Lovett, taylorlovett.com (admin@taylorlovett.com)
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 3 of the License, or
	(at your option) any later version.
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
require_once('widgets/class.widget.php');
require_once('class.utils.php');
require_once('class.settings_page.php');
TL_Latest_News_Widget_Utils::define_constants();
if (!class_exists('TL_Latest_News_Widget_Core')) {
	class TL_Latest_News_Widget_Core {
		function TL_Latest_News_Widget_Core() {
			add_action('widgets_init', create_function('', 'register_widget("TL_Latest_News_Widget");'));
			if (is_admin()) {
				$admin_options = TL_Latest_News_Widget_Utils::get_admin_options();
				if ($admin_options['enable_dashboard_widget'] == 1) {
					require_once('widgets/class.dashboard.php');
					$lnw_dashboard = new TL_Latest_News_Widget_Dashboard();
					if ($lnw_dashboard->is_dashboard_page())
						add_action('admin_print_styles', array(&$lnw_dashboard, 'insert_styles'), 1);
					add_action('wp_dashboard_setup', array(&$lnw_dashboard, 'install'));
				}
				add_filter('plugin_action_links', array(&$this, 'redo_plugin_admin_links'), 10, 2);
				add_action('admin_menu', create_function('', "add_options_page(__('Latest News Widget', 'latest-news-widget'), __('Latest News Widget', 'latest-news-widget'), 9, 'latest-news-widget', array('TL_Latest_News_Widget_Core', 'settings_page'));"));
				add_action('admin_print_styles', array('TL_Latest_News_Widget_Settings_Page', 'admin_styles'), 1);
			}
		}
		
		function redo_plugin_admin_links($action_links, $plugin_file) {
			static $link_added = false;
			if (!$link_added && basename($plugin_file) == 'latest-news-widget.php') {
				$new_link = '<a style="font-weight:bold;" href="admin.php?page=latest-news-widget" title="' . __('Manage Latest News Widget', 'latest-news-widget') . '">' . __('Settings', 'latest-news-widget') . '</a>';
				array_unshift($action_links, $new_link);
				$link_added = true;
			}
			return $action_links;
		}
		
		function settings_page() {
			new TL_Latest_News_Widget_Settings_Page();
		}
		
		function is_plugin_page() {
			$pages = array('latest-news-widget');
			return (in_array($GLOBALS['lnw_current_page'], $pages));
		}
	}
}
new TL_Latest_News_Widget_Core();
?>