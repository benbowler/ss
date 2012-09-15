<?php
if ( !defined('WP_UNINSTALL_PLUGIN') ) {
    exit();
}
if ('uninstall.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die ('<h2>Direct File Access Prohibited</h2>');
/**
 * The plugin uninstallation script
 * 1. Remove the tables
 * 2. Remove options
 * Done
 */

/**
 * Set Global variable
 */
global $wpdb;

/** Remove databases */
$prefix = '';
if(is_multisite()) {
    global $blog_id;
    $prefix = $wpdb->base_prefix . $blog_id;
} else {
    $prefix = $wpdb->prefix;
}
if ($wpdb->get_var("show tables like '" . $prefix . "sur_feed'")) {
    //delete it
    $wpdb->query("DROP TABLE IF EXISTS " . $prefix . "sur_feed");
}

/** Remove capabilities */
/**
 * @global WP_Roles
 */
global $wp_roles;
//remove capability to admin
$wp_roles->remove_cap('administrator', 'manage_feedback');
$wp_roles->remove_cap('administrator', 'view_feedback');

//remove capability to editor
$wp_roles->remove_cap('editor', 'manage_feedback');
$wp_roles->remove_cap('editor', 'view_feedback');

//remove capability to author
$wp_roles->remove_cap('author', 'view_feedback');

/** Delete options */
delete_option('wp_feedback_info');
delete_option('wp_feedback_global');
delete_option('wp_feedback_survey');
delete_option('wp_feedback_feedback');
