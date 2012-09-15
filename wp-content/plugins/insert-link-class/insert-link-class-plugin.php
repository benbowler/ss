<?php
/**
* Plugin Name: Insert Link Class Plugin
* Plugin URI: http://www.n7studios.co.uk/2010/03/07/wordpress-insert-link-class-plugin/
* Version: 1.31
* Author: <a href="http://www.n7studios.co.uk/">Tim Carr</a>
* Description: Allows custom class names to be added to the Insert / edit link functionality in the Wordpress Page and Post Editor.
*/

/**
* Insert Link Class Plugin Class
* 
* @package Wordpress
* @subpackage Insert Link Class Plugin
* @author Tim Carr
* @version 1.31
* @copyright n7 Studios
*/
class InsertLinkClassPlugin {
    /**
    * Constructor.  Initiates plugin hooks and filters.
    */
    function InsertLinkClassPlugin() {                     
        if (is_admin()) {
            // Plugin programmatic name (folder) and MySQL table name
            $this->plugin->name = 'insert-link-class'; // Must match folder this plugin resides in under /wp-content/plugins
            $this->plugin->table = 'insert_link_classes'; // Do not change

            // Install & Uninstall Routines
            register_activation_hook(__FILE__, array(&$this, 'Install'));
            register_deactivation_hook(__FILE__, array(&$this, 'Uninstall'));
            
            add_action('admin_menu', array(&$this, 'AddAdminPanels')); // Add admin panels to Wordpress Admin
            
            // TinyMCE Admin
            add_filter('mce_buttons_2', array(&$this, 'TinyMCEButtons'), 999);
            add_filter('tiny_mce_before_init', array(&$this, 'TinyMCEInit'));
            
            wp_enqueue_script('jquery'); // jQuery
        }
    }
    
    /**
    * Installation routine
    */
    function Install() {
        global $wpdb;
        
        $wpdb->query("  CREATE TABLE IF NOT EXISTS ".$wpdb->prefix.$this->plugin->table." (
                            classID int(10) NOT NULL AUTO_INCREMENT,
                            name varchar(200) NOT NULL,
                            css varchar(200) NOT NULL,
                            PRIMARY KEY (`classID`)
                        ) 
                        ENGINE=MyISAM
                        DEFAULT CHARSET=utf8
                        AUTO_INCREMENT=1");
    }
    
    /**
    * Uninstallation routine
    */
    function Uninstall() {
        global $wpdb;
        
        $wpdb->query("  DROP TABLE IF EXISTS ".$wpdb->prefix.$this->plugin->table);
    }
    
    /**
    * Creates menu and submenu entries in Wordpress Admin.
    */
    function AddAdminPanels() {
        add_menu_page('Link Classes', 'Link Classes', 9, $this->plugin->name, array(&$this, 'AdminPanel'));
        add_submenu_page($this->plugin->name, 'Settings', 'Settings', 9, $this->plugin->name, array(&$this, 'AdminPanel'));        
    }
    
    /**
    * Outputs the plugin Admin Panel in Wordpress Admin
    */
    function AdminPanel() {
        switch ($_GET['cmd']) {
            case 'add':
            case 'edit':
                // Save form
                if (isset($_POST['classID'])) {
                    // Save & display list of current records
                    $this->SaveRecord($_GET['pKey'], $_POST);
                    $this->data = $this->GetAllRecords();
                    $this->successMessage = 'Record Saved';
                    include_once(WP_PLUGIN_DIR.'/'.$this->plugin->name.'/list.php');    
                } else {
                    // Display form
                    $this->data = $this->GetRecord($_GET['pKey']);
                    include_once(WP_PLUGIN_DIR.'/'.$this->plugin->name.'/form.php');
                }
                break;
            case 'save':
                // Delete & display list of all current records
                if ($_POST['doAction']) {
                    foreach ($_POST['classID'] as $classID=>$delete) {
                        if ($delete) $this->DeleteRecord($classID);
                    }
                }
                $this->data = $this->GetAllRecords();
                $this->successMessage = 'Record(s) Deleted';
                include_once(ABSPATH.'wp-content/plugins/'.$this->plugin->name.'/list.php');
                break;
            default:
                // Display list of current records
                $this->data = $this->GetAllRecords();
                include_once(ABSPATH.'wp-content/plugins/'.$this->plugin->name.'/list.php');
                break;    
        }        
    }
    
    /**
    * Adds style selector option to TinyMCE
    * 
    * @param mixed $orig
    * @return mixed
    */
    function TinyMCEButtons($orig) {
        return array_merge($orig, array('styleselect'));
    }
    
    /**
    * Adds CSS classes to the TinyMCE editor Style Selector
    * 
    * @param mixed $initArray
    */
    function TinyMCEInit($initArray) {
        global $wpdb;
        
        // Default Wordpress classes
        $cssClasses = array('aligncenter' => 'aligncenter',
                            'alignleft' => 'alignleft',
                            'alignright' => 'alignright',
                            'wp-caption' => 'wp-caption',
                            'wp-caption-dd' => 'wp-caption-dd',
                            'wpGallery' => 'wpGallery',
                            'wp-oembed' => 'wp-oembed');
        
        // Custom classes
        $customClasses = $this->GetAllRecords();
        
        // Build array
        foreach($cssClasses as $css=>$name) {
            $initArray['theme_advanced_styles'] .= $name.'='.$css.';';
        }
        foreach($customClasses as $key=>$customClass) {
            $initArray['theme_advanced_styles'] .= $customClass->name.'='.$customClass->css.';';
        } 
        $initArray['theme_advanced_styles'] = rtrim($initArray['theme_advanced_styles'], ';'); // Remove final semicolon from list
        
        return $initArray;
    }
    
    /**
    * Adds or updates a record
    */
    function SaveRecord($pKey = '', $data) {
        global $wpdb;
        
        if ($pKey == '') {
            // Add new record
            $wpdb->query("  INSERT INTO ".$wpdb->prefix.$this->plugin->table." (name, css)
                            VALUES ('".htmlentities($data['name'])."', '".htmlentities($data['css'])."')");
        } else {
            // Edit existing record
            $wpdb->query("  UPDATE ".$wpdb->prefix.$this->plugin->table." SET
                            name = '".htmlentities($data['name'])."', 
                            css = '".htmlentities($data['css'])."'
                            WHERE classID = ".mysql_real_escape_string($pKey)."
                            LIMIT 1");
        }
        
        return true;
    }
    
    /**
    * Deletes the specified record by primary key
    * 
    * @param int $pKey Primary Key
    * @return bool Success
    */
    function DeleteRecord($pKey) {
        global $wpdb;
        
        $wpdb->query("  DELETE FROM ".$wpdb->prefix.$this->plugin->table."
                        WHERE classID = ".mysql_real_escape_string($pKey)."
                        LIMIT 1");
        
        return true;
    }
    
    /**
    * Gets specific record from the table by primary key
    * 
    * @return array Record
    */
    function GetRecord($pKey) {
        global $wpdb;
        
        $results = $wpdb->get_results(" SELECT *
                                        FROM ".$wpdb->prefix.$this->plugin->table."
                                        WHERE classID = ".mysql_real_escape_string($pKey)."
                                        LIMIT 1");
        return $results[0];
    }
    
    /**
    * Gets all records from the table
    * 
    * @return array Records
    */
    function GetAllRecords() {
        global $wpdb;
        
        return $wpdb->get_results(" SELECT *
                                    FROM ".$wpdb->prefix.$this->plugin->table."
                                    ORDER BY name ASC");
    }
}

$ilcp = new InsertLinkClassPlugin(); // Initialise class
?>