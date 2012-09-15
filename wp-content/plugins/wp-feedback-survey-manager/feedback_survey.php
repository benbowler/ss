<?php
/*
Plugin Name: WP Feedback & Survey Manager
Plugin URI: http://www.intechgrity.com/wp-plugins/wp-feedback-survey-manager/
Description: A simple plugin to gather feedback and run survey on your WordPress Blog. Stores the gathered data on database
Author: Swashata
Version: 1.0.1
Author URI: http://www.swashata.com
License: GPLv3
Text Domain: fbsr
 */

/**
 * Copyright Swashata Ghosh, 2012
 * My plugins are created for WordPress, an open source software
    released under the GNU public license
    <http://www.gnu.org/licenses/gpl.html>. Therefore any part of
    my plugins which constitute a derivitive work of WordPress are also
    licensed under the GPL 3.0. My plugins are comprised of several
    different file types, including: php, cascading style sheets,
    javascript, as well as several image types including GIF, JPEG, and
    PNG. All PHP and JS files are released under the GPL 3.0 unless
    specified otherwised within the file itself. If specified as
    otherwise the files are licesned or dual licensed (as stated in
    the file) under the MIT <http://www.opensource.org/licenses/mit-license.php>,
    a compatible GPL license.
 */

include_once dirname(__FILE__) . '/classes/loader.php';
include_once dirname(__FILE__) . '/classes/form-class.php';

if(is_admin()) {
    include_once dirname(__FILE__) . '/classes/admin-class.php';
} else {

}

/**
 * Holds the plugin information <br />
 * 'version' => The version of this plugin <br />
 * 'feedback_table' => The name of the Ad Unit table with $wpdb->prefix <br />
 *
 * @global array
 */
global $wp_feedback_info;

$wp_feedback = new wp_feedback_loader(__FILE__, 'fbsr', '1.0.1', 'wp_fbsr', 'http://www.intechgrity.com/wp-plugins/wp-feedback-survey-manager/', 'http://wordpress.org/support/plugin/wp-feedback-survey-manager');

$wp_feedback->load();

/**
 * TODO List for version 1.1.0
 * @todo Feedback Report - Fetch all feedback for the selected one - using AJAX
 * @todo Mark Feedbacks as important? Why not... probably output on the frontend.
 */
