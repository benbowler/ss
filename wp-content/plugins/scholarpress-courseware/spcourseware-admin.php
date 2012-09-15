<?php 
/*
Plugin Name: ScholarPress Courseware
Plugin URI: http://scholarpress.net/courseware/
Description: All-in-one course management for WordPress
Version: 1.1.2
Author: Jeremy Boggs, Dave Lester, Zac Gordon, and Sean Takats
Author URI: http://scholarpress.net/
*/

/*
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// Plugin Version
$spcourseware_version = "1.1.2";

// Courseware Path
$spcourseware_path = ABSPATH . PLUGINDIR . DIRECTORY_SEPARATOR . 'scholarpress-courseware/';

// Include necessary files
include_once 'spcourseware-public.php';
include_once 'spcourseware-bibliography.php';
include_once 'spcourseware-schedule.php';

if (isset($_GET['activate']) && $_GET['activate'] == 'true')
{
	add_action('init', 'courseware_install');
}

// Insert sinks into the plugin hook list for 'admin_menu'
add_action('admin_menu', 'courseware_admin_menu');

// Filter the bibliography and projects pages
add_filter('the_content', 'bib_page', 10);
add_filter('the_content', 'project_page',10);

// Page Delimiters 
define('SP_BIBLIOGRAPHY_PAGE', '<spbibliography />');
define('SP_SCHEDULE_PAGE', '<spschedule />');
define('SP_PROJECTS_PAGE', '<spprojects />');
define('SP_COURSEINFO_PAGE', '<spcourseinfo />');

// Misc functions
//Adapted from PHP.net: http://us.php.net/manual/en/function.nl2br.php#73479
function spcourseware_nls2p($str)
{
  return str_replace('<p></p>', '', '<p>'
        . preg_replace('#([\r\n]\s*?[\r\n]){2,}#', '</p>$0<p>', $str)
        . '</p>');
}

function spcourseware_get_admin_options() {
	$spcoursewareOptions = get_option('SpCoursewareAdminOptions');
	if (!empty($spcoursewareOptions)) {
		foreach ($spcoursewareOptions as $key => $option)
			$spcoursewareAdminOptions[$key] = $option;
		}
	return $spcoursewareAdminOptions;
}

function spcourseware_set_admin_options($course_title, $course_number, $course_section, $course_timestart, $course_timeend, $course_location, $course_timedays, $instructor_firstname, $instructor_lastname, $instructor_email, $instructor_telephone, $instructor_office, $course_description, $instructor_hours) 
{
		$spcoursewareAdminOptions = array('course_title' => $course_title,
		'course_number' => $course_number,
		'course_section' => $course_section,
		'course_timestart' => $course_timestart,
		'course_timeend' => $course_timeend,
		'course_location' => $course_location,
		'course_timedays' => $course_timedays,
		'instructor_firstname' => $instructor_firstname,
		'instructor_lastname' => $instructor_lastname,
		'instructor_email' => $instructor_email,
		'instructor_telephone' => $instructor_telephone,
		'instructor_office' => $instructor_office,
		'course_description' => $course_description,
		'instructor_hours' => $instructor_hours);
    
    $courseware_admin_options = 'SpCoursewareAdminOptions';
    
	if (get_option($courseware_admin_options) ) {
    	update_option($courseware_admin_options, $spcoursewareAdminOptions);
    } else {
        $deprecated=' ';
        $autoload='no';
        add_option($courseware_admin_options, $spcoursewareAdminOptions, $deprecated, $autoload);
    }
}

// Install the courseware
function courseware_install() {

	global $wpdb, $user_level, $spcourseware_version;

	// Check user-level
	get_currentuserinfo();
	if ($user_level < 8) { 
	    return;
	}
	
    $courseware_option_name = 'spcourseware_version'; 
    
    if ( get_option($courseware_option_name) ) {
        update_option($courseware_option_name, $spcourseware_version);
    } else {
        $deprecated=' ';
        $autoload='no';
        add_option($courseware_option_name, $spcourseware_version, $deprecated, $autoload);
    }
		
	// table names
	$assignments_table_name = $wpdb->prefix . "assignments";
	$bib_table_name = $wpdb->prefix . "bibliography";
	$schedule_table_name = $wpdb->prefix . "schedule";
	$projects_table_name = $wpdb->prefix . "projects";
	$assignment2project_table_name = $wpdb->prefix . "assignment2project";
	$units_table_name = $wpdb->prefix . "units";
	$schedule2unit_table_name = $wpdb->prefix . "schedule2unit";
	 
	// First-Run-Only parameters: Check if assignments table exists:
	if($wpdb->get_var("SHOW TABLES LIKE '$assignments_table_name'") != $assignments_table_name) 
	{
		// It doesn't exist, create the table
		$sql = "CREATE TABLE " . $assignments_table_name . " (
	     	 `assignmentID` INT(11) NOT NULL AUTO_INCREMENT,
			 `assignments_title` TEXT NOT NULL,
			 `assignments_scheduleID` INT NOT NULL,
			 `assignments_bibliographyID` INT NOT NULL,
			 `assignments_assignedScheduleID` INT NOT NULL,
			 `assignments_pages` VARCHAR(255) NOT NULL,
			 `assignments_description` TEXT NOT NULL,
			 `assignments_type` ENUM('reading','writing','presentation','groupwork','research','discussion', 'creative') NOT NULL,
			 PRIMARY KEY (`assignmentID`)
			 )"; 

	    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	    dbDelta($sql);
		
	}
	
	// First-Run-Only parameters: Check if bibliography table exists:
	if($wpdb->get_var("SHOW TABLES LIKE '$bib_table_name'") != $bib_table_name) 
	{
		 // It doesn't exist, create the table
		$sql = "CREATE TABLE " . $bib_table_name . " (
	     	 `entryID` INT(11) NOT NULL AUTO_INCREMENT,
	    	 `author_last` TEXT NOT NULL,
	     	 `author_first` TEXT NOT NULL,
			`author_two_last` TEXT NOT NULL,
			`author_two_first` TEXT NOT NULL,
			 `title` TEXT NOT NULL,
			`short_title` TEXT NOT NULL,
			 `journal` TEXT NOT NULL,
			 `volume_title` TEXT NOT NULL,
			 `volume_editors` TEXT NOT NULL,
			 `website_title` TEXT NOT NULL,	
			 `pub_location` TEXT NOT NULL,
			 `publisher` TEXT NOT NULL,
			 `date` TEXT NOT NULL,
			 `dateaccessed` TEXT NOT NULL,
			 `url` VARCHAR(255) NOT NULL,
			 `volume` VARCHAR(255) NOT NULL,
			 `issue` VARCHAR(255) NOT NULL,
			 `pages` VARCHAR(255) NOT NULL,
			 `description` TEXT NOT NULL,
			 `type` ENUM('monograph','textbook','article','volumechapter','unpublished','website','webpage','audio','video') NOT NULL,
			 PRIMARY KEY (`entryID`)
			 )"; 

	    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	    dbDelta($sql);
	}
	
	// First-Run-Only parameters: Check if schedule table exists:
	if($wpdb->get_var("SHOW TABLES LIKE '$schedule_table_name'") != $schedule_table_name) 
	{
		// It doesn't exist, create the table
		$sql = "CREATE TABLE " . $schedule_table_name . " (
	     	 `scheduleID` INT(11) NOT NULL AUTO_INCREMENT,
			 `schedule_title` tinytext NOT NULL,
			 `schedule_date` DATE NOT NULL,
			 `schedule_timestart` TIME NOT NULL,
			 `schedule_timestop` TIME NOT NULL,
			 `schedule_description` TEXT NOT NULL,
			 PRIMARY KEY (`scheduleID`)
			 )"; 

	    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	    dbDelta($sql);
	}
	
	// First-Run-Only parameters: Check if table exists:
	if($wpdb->get_var("SHOW TABLES LIKE '$projects_table_name'") != $projects_table_name) 
	{
		 // It doesn't exist, create the table
	   $sql = "CREATE TABLE " . $projects_table_name . " (
	     	 `projectID` INT(11) NOT NULL AUTO_INCREMENT,
			 `title` TEXT NOT NULL,
			`date` DATE NOT NULL,
			 `description` TEXT NOT NULL,
			 PRIMARY KEY (`projectID`)
			 )"; 

	    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	    dbDelta($sql);
	}
	
	// First-Run-Only parameters: Check if table exists:
	if($wpdb->get_var("SHOW TABLES LIKE '$assignment2project_table_name'") != $assignment2project_table_name) 
	{
		 // It doesn't exist, create the table
	   $sql = "CREATE TABLE " . $assignment2project_table_name . " (
	     	 `assignment2projectID` INT(11) NOT NULL AUTO_INCREMENT,
			 `assignmentID` INT NOT NULL,
			 `projectID` INT NOT NULL,
			 `modified` TIMESTAMP NOT NULL,
			 PRIMARY KEY (`assignment2projectID`)
			 )"; 

	    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	    dbDelta($sql);
	}
	
	// First-Run-Only parameters: Check if table exists:
	if($wpdb->get_var("SHOW TABLES LIKE '$units_table_name'") != $units_table_name) 
	{
		 // It doesn't exist, create the table
	   $sql = "CREATE TABLE " . $units_table_name . " (
	     	 `unitID` INT(11) NOT NULL AUTO_INCREMENT,
			 `title` TEXT NOT NULL,
			 `description` TEXT NOT NULL,
			 PRIMARY KEY (`unitID`)
			 )"; 

	    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	    dbDelta($sql);
	}
	
	// First-Run-Only parameters: Check if table exists:
	if($wpdb->get_var("SHOW TABLES LIKE '$schedule2unit_table_name'") != $schedule2unit_table_name) 
	{
		 // It doesn't exist, create the table
	   $sql = "CREATE TABLE " . $schedule2unit_table_name . " (
	     	 `schedule2unitID` INT(11) NOT NULL AUTO_INCREMENT,
			 `scheduleID` INT NOT NULL,
			 `unitID` INT NOT NULL,
			 `modified` TIMESTAMP NOT NULL,
			 PRIMARY KEY (`schedule2unitID`)
			 )"; 

	    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	    dbDelta($sql);
	}
	
	// Add course info stuff to the options table. You know, for course information.
	$spcoursewareOptions = get_option('SpCoursewareAdminOptions');
	
	if(empty($spcoursewareOptions)) {
	    spcourseware_set_admin_options(null, null, null, null, null, null, null, null, null, null, null, null, null, null);
	}
	
	/// POPULATE DB WITH BIBLIOGRAPHY, SCHEDULE, PROJECTS, PAGES IF NOT ALREADY CREATED
	$now = time();
	$now_gmt = time();
	$parent_id = 1; // Uncategorized default
	$post_modified = $now;
	$post_modified_gmt = $now_gmt;
	
	if (!$wpdb->get_row("SELECT * FROM $wpdb->posts WHERE post_title='Bibliography'", OBJECT))
	{

		$bibliography_title = "Bibliography";
		$bibliography_content = "<div id=\"spbibliography\"><spbibliography /></div>";
		$bibliography_excerpt = "";
		$bibliography_status = "publish";
		$bibliography_name = "bibliography";

		wp_insert_post(array(
		'post_author'		=> '1',
		'post_date'			=> $post_dt,
		'post_date_gmt'		=> $post_dt,
		'post_modified'		=> $post_modified_gmt,
		'post_modified_gmt'	=> $post_modified_gmt,
		'post_title'		=> $bibliography_title,
		'post_content'		=> $bibliography_content,
		'post_excerpt'		=> $bibliography_excerpt,
		'post_status'		=> $bibliography_status,
		'post_name'			=> $bibliography_name,
		'post_type' 		=> 'page')			
		);
	}
	
	if (!$wpdb->get_row("SELECT * FROM $wpdb->posts WHERE post_title='Schedule'", OBJECT)) 
	{
		$schedule_title = "Schedule";
		$schedule_content = "<div id=\"spschedule\"><spschedule /></div>";
		$schedule_excerpt = "";
		$schedule_status = "publish";
		$schedule_name = "schedule";
		wp_insert_post(array(
		'post_author'		=> '1',
		'post_date'		=> $post_dt,
		'post_date_gmt'		=> $post_dt,
		'post_modified'		=> $post_modified_gmt,
		'post_modified_gmt'	=> $post_modified_gmt,
		'post_title'		=> $schedule_title,
		'post_content'		=> $schedule_content,
		'post_excerpt'		=> $schedule_excerpt,
		'post_status'		=> $schedule_status,
		'post_name'		=> $schedule_name,
		'post_type' => 'page')			
		);
	}
}

// Add management pages to the administration panel; sink function for 'admin_menu' hook
function courseware_admin_menu()
{
	$coursewareManage = add_menu_page('SP Courseware','SP Courseware',8,'scholarpress-courseware','spcourseware_manage');
	$schedule = add_submenu_page('scholarpress-courseware','SP Courseware | Schedule', 'Schedule', 8, 'schedule', 'schedule_manage');
	$bibliography = add_submenu_page('scholarpress-courseware','SP Courseware | Bibliography', 'Bibliography', 8, 'bibliography', 'bibliography_manage');
	$assignments = add_submenu_page('scholarpress-courseware','SP Courseware | Assignments', 'Assignments', 8, 'assignments', 'assignments_manage');
	$courseInfo = add_submenu_page('scholarpress-courseware','SP Courseware | Course Information', 'Course Information', 8, 'courseinfo', 'courseinfo_manage');
	$importSources = add_submenu_page('scholarpress-courseware','SP Courseware | Import Bibliographic Sources', 'Import Bibliographic Sources', 8, 'importsources', 'sp_courseware_insert_sources');
	$coursewarePages = array($coursewareManage, $schedule, $bibliography, $assignments, $courseInfo, $importSources);
	
	foreach($coursewarePages as $page) {
	    add_action('admin_head-'.$page, 'courseware_admin_style');
        add_action('admin_head-'.$page, 'courseware_admin_scripts');
    }
}

/* ======== Backend management pages ========*/

// Set up admin stylesheet

function courseware_admin_style() 
{
    global $wp_version;
	
    $url = WP_PLUGIN_URL;
    $url = $url . '/scholarpress-courseware/spadmin.css';
    if ($wp_version < 2.7) {
	echo '<link rel="stylesheet" type="text/css" href="' . $url . '" />';
}
}

function courseware_admin_scripts() 
{
    $url = WP_PLUGIN_URL;
    $spcourseware_js = $url . '/scholarpress-courseware/spcourseware.js';
	$datepicker_url = $url . '/scholarpress-courseware/datepicker/';
	echo '<script src="'.$datepicker_url.'jquery.ui.all.js" type="text/javascript" charset="utf-8"></script>';
	echo '<link rel="stylesheet" type="text/css" href="'.$datepicker_url.'base.css" />';
	echo '<link rel="stylesheet" type="text/css" href="'.$datepicker_url.'datepicker.css" />';
}

// Set up the public stylesheet

function courseware_public_style() 
{
    $url = WP_PLUGIN_URL;
    $url = $url . '/scholarpress-courseware/spcourseware.css';
	echo '<link rel="stylesheet" type="text/css" href="' . $url . '" />';
}

add_action('wp_head', 'courseware_public_style');

function spcourseware_manage() {
	?>

    <div class="wrap">
        <div id="icon-index" class="icon32"><br /></div>
        <h2>SP Courseware</h2>
        <div id="dashboard-widgets-wrap">
            <div id='dashboard-widgets' class='metabox-holder has-right-sidebar'>
                <div id="post-body" class="has-sidebar has-right-sidebar">
                    <div id="dashboard-widgets-main-content" class="has-sidebar-content has-right-sidebar">
                        <div id="normal-sortables" class="meta-box-sortables has-right-sidebar">
                            <div id="dashboard_upcoming_schedule" class="postbox">
                                <div class='handlediv'><br /></div>
                                <h3 class='handle'><span>Welcome!</span></h3>
                                <div class="inside" style="padding:12px;">
                                    <h4>Course Schedule</h4>
                                     <?php $entries = sp_courseware_schedule_get_upcoming_entries(1); if(!empty($entries)): ?>
                                    <p>Your next schedule entry is:</p>
                                    <?php foreach ($entries as $entry): ?>
                                        <?php //print_r($entry); ?>
                                        <?php
                                        $startTime = strtotime($entry->schedule_date.' '.$entry->schedule_timestart);
                                    	$endTime = strtotime($entry->schedule_date.' '.$entry->schedule_timestop);
                                        ?>
                                        <small>
                                			<abbr class="dtstart" title="<?php echo date('Y-m-d\TH:i:s\Z', $startTime); ?>">
                                				<?php echo date('F d, Y, g:i a', $startTime); ?>
                                			</abbr>
                                			&ndash;
                                			<abbr class="dtend" title="<?php echo date('Y-m-d\T-H:i:s\Z', $endTime); ?>">
                                				<?php echo date('g:i a', $endTime); ?>
                                			</abbr>
                                		</small>
                                        <h4><?php echo $entry->schedule_title; ?></h4>

                                        
                                        <?php echo spcourseware_nls2p($entry->schedule_description); ?>
                                    <?php endforeach; else: ?>
                                    <p>You have no upcoming schedule entries. <a href="?page=schedule">Want to add one?</a></p>
                                    <?php endif; ?>
                                    <p><a href="?page=schedule" class="button">Edit Schedule Entries</a></p>
                                </div> <?php // Closes class="inside" ?>
                                
                                <div class="inside" style="padding:0 12px;">
                                    <h4>Course Information</h4>
                                    
                                    <?php courseinfo_printfull(); ?>
                                    <p><a href="?page=courseinfo" class="button">Edit Course Information</a></p>
                                    <br />
                                </div> <?php // Closes class="inside" ?>
                            </div> <?php // Closes class="postbox" ?>
                        </div> <?php // Closes class="meta-box-sortables" ?>
                    </div> <?php // Closes doasbhard-widgets-main-content ?>
                </div>  <?php // Closes post-body with has-sidebar class ?>
                
            </div> <?php // Closes #dashboard-widgets ?>
            <div class="clear">
            </div>
        </div><!-- dashboard-widgets-wrap -->
    </div><!-- wrap -->	
	<?php
}

// Handles the assignments management page
function assignments_manage()
{
	global $wpdb;

	$updateaction = !empty($_REQUEST['updateaction']) ? $_REQUEST['updateaction'] : '';
	$assignmentID = !empty($_REQUEST['assignmentID']) ? $_REQUEST['assignmentID'] : '';
	
	if (isset($_REQUEST['action']) ):
		if ($_REQUEST['action'] == 'delete_assignment') 
		{
			$assignmentID = intval($_GET['assignmentID']);
			if (empty($assignmentID))
			{
				?><div class="error"><p><strong>Failure:</strong> No assignments ID given. I guess I deleted nothing successfully.</p></div><?php
			}
			else
			{
				$wpdb->query("DELETE FROM " . $wpdb->prefix . "assignments WHERE assignmentID = '" . $assignmentID . "'");
				$sql = "SELECT assignmentID FROM " . $wpdb->prefix . "assignments WHERE assignmentID = '" . $assignmentID . "'";
				$check = $wpdb->get_results($sql);
				if ( empty($check) || empty($check[0]->assignmentID) )
				{
					?><div class="updated"><p>Reading Entry <?php echo $assignmentID; ?> deleted successfully.</p></div><?php
				}
				else
				{
					?><div class="error"><p><strong>Failure:</strong></p></div><?php
				}
			}
		} // end delete_assignment block
	endif;
	
	if ( $updateaction == 'update_assignment' )
	{
		$title = !empty($_REQUEST['assignment_title']) ? $_REQUEST['assignment_title'] : '';
		$scheduleID = !empty($_REQUEST['assignment_scheduleID']) ? $_REQUEST['assignment_scheduleID'] : '';
		$bibliographyID = !empty($_REQUEST['assignment_bibliographyID']) ? $_REQUEST['assignment_bibliographyID'] : '';
		$type = !empty($_REQUEST['assignment_type']) ? $_REQUEST['assignment_type'] : '';
		$pages = !empty($_REQUEST['assignment_pages']) ? $_REQUEST['assignment_pages'] : '';
		$description = !empty($_REQUEST['assignment_description']) ? $_REQUEST['assignment_description'] : '';
		
		if ( empty($assignmentID) )
		{
			?><div class="error"><p><strong>Failure:</strong> No reading-id given. Can't save nothing. Giving up...</p></div><?php
		}
		else
		{
			$sql = "UPDATE " . $wpdb->prefix . "assignments SET assignments_title = '" . $title . "', assignments_scheduleID = '" . $scheduleID . "',  assignments_bibliographyID = '" . $bibliographyID . "', assignments_type = '" . $type . "', assignments_pages = '" . $pages . "', assignments_description = '" . $description . "' WHERE assignmentID = '" . $assignmentID . "'";
			$wpdb->get_results($sql);
			$sql = "SELECT assignmentID FROM " . $wpdb->prefix . "assignments WHERE assignments_title = '" . $title . "' and assignments_scheduleID = '" . $scheduleID . "' and assignments_type = '" . $type . "' and assignments_pages = '" . $pages . "' and assignments_description = '" . $description . "' LIMIT 1";
			$check = $wpdb->get_results($sql);
			if ( empty($check) || empty($check[0]->assignmentID) )
			{
				?><div class="error"><p><strong>Failure:</strong> I couldn't update your entry. Try again?</p></div><?php
			}
			else
			{
				?><div class="updated"><p>Assignment <?php echo $assignmentID; ?> updated successfully.</p></div><?php
			}
		}
	} // end update_assignment block
	elseif ( $updateaction == 'add_assignment' )
	{
		$title = !empty($_REQUEST['assignment_title']) ? $_REQUEST['assignment_title'] : '';
		$scheduleID = !empty($_REQUEST['assignment_scheduleID']) ? $_REQUEST['assignment_scheduleID'] : '';
		$bibliographyID = !empty($_REQUEST['assignment_bibliographyID']) ? $_REQUEST['assignment_bibliographyID'] : '';
		$type = !empty($_REQUEST['assignment_type']) ? $_REQUEST['assignment_type'] : '';
		$pages = !empty($_REQUEST['assignment_pages']) ? $_REQUEST['assignment_pages'] : '';
		$description = !empty($_REQUEST['assignment_description']) ? $_REQUEST['assignment_description'] : '';
		
		$sql = "INSERT INTO " . $wpdb->prefix . "assignments SET assignments_title = '" . $title . "', assignments_scheduleID = '" . $scheduleID . "', assignments_bibliographyID = '" . $bibliographyID . "',  assignments_type = '" . $type . "', assignments_pages = '" . $pages . "', assignments_description = '" . $description . "'";
		$wpdb->get_results($sql);
		$sql = "SELECT assignmentID FROM " . $wpdb->prefix . "assignments WHERE assignments_title = '" . $title . "' and assignments_scheduleID = '" . $scheduleID . "' and assignments_type = '" . $type . "' and assignments_pages = '" . $pages . "' and assignments_description = '" . $description . "'";
		$check = $wpdb->get_results($sql);
		if ( empty($check) || empty($check[0]->assignmentID) )
		{
			?><div class="error"><p><strong>Failure:</strong> Try again? </p></div><?php
		}
		else
		{
			?><div class="updated"><p>Yeah! Assignment <?php echo $check[0]->assignmentID;?> added successfully.</p></div><?php
		}
	} // end add_assignment block
	?>

	<div class="wrap">
	<?php
	if ( $_REQUEST['action'] == 'edit_assignment' )
	{
		?>
		<h2><?php _e('Edit Assignment'); ?></h2>
		<?php
		if ( empty($assignmentID) )
		{
			echo "<div class=\"error\"><p>I didn't get an entry identifier from the query string. Giving up...</p></div>";
		}
		else
		{
			assignments_editform('update_assignment', $assignmentID);
		}	
	}
	else
	{
		?>
		<h2><?php _e('Add Assignment'); ?></h2>
		<?php assignments_editform(); ?>
	
		<h2><?php _e('Manage Assignments'); ?></h2>
		<?php
			assignments_displaylist();
	}
	?>
	</div><?php
}

// Displays the list of assignments entries
function assignments_displaylist() 
{
	global $wpdb;
	
	$assignments = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."assignments LEFT JOIN ".$wpdb->prefix."bibliography ON ".$wpdb->prefix."assignments.assignments_bibliographyID = ".$wpdb->prefix."bibliography.entryID LEFT JOIN ".$wpdb->prefix."schedule ON ".$wpdb->prefix."assignments.assignments_scheduleID = ".$wpdb->prefix."schedule.scheduleID ORDER BY schedule_date, assignmentID ASC");
	
	if ( !empty($assignments) )
	{
		?>
			<table width="100%" cellpadding="3" cellspacing="3" class="widefat post">
			<thead>
			<tr>
				<th scope="col"><?php _e('Date') ?></th>
				<th scope="col"><?php _e('Type') ?></th>
				<th scope="col"><?php _e('Title') ?></th>
				<th scope="col"><?php _e('Description') ?></th>
				<th scope="col"><?php _e('Edit') ?></th>
				<th scope="col"><?php _e('Delete') ?></th>
			</tr>
			</thead>
		<?php
		$class = '';
		echo '<tbody>';
		
		foreach ( $assignments as $assignment )
		{
			$class = ($class == 'alternate') ? '' : 'alternate';
			?>
			<tr class="<?php echo $class; ?>">
				<td><?php echo $assignment->schedule_date ? $assignment->schedule_date : 'No Date'; ?></td>
				<td><?php echo $assignment->assignments_type; ?></td>
				
				<td><?php echo $assignment->assignments_title ? $assignment->assignments_title : 'Untitled'; ?></td>
				<td><?php echo $assignment->assignments_description; ?></td>
				<td><a href="admin.php?page=<?php echo $_GET['page']; ?>&amp;action=edit_assignment&amp;assignmentID=<?php echo $assignment->assignmentID;?>" class="edit"><?php echo __('Edit'); ?></a></td>
				<td><a href="admin.php?page=<?php echo $_GET['page']; ?>&amp;action=delete_assignment&amp;assignmentID=<?php echo $assignment->assignmentID;?>" class="delete" onclick="return confirm('Are you sure you want to delete this entry?')"><?php echo __('Delete'); ?></a></td>
			</tr>
			<?php
		}
		?>
		</table>
		<?php
	}
	else
	{
		?>
		<p><?php _e("You haven't entered any assignments yet.") ?></p>
		<?php	
	}
}


// Displays the add/edit form
function assignments_editform($mode='add_assignment', $assignmentID=false)
{
	global $wpdb;
	$data = false;
	
	if ( $assignmentID !== false )
	{
		if ( intval($assignmentID) != $assignmentID )
		{
			echo "<div class=\"error\">Not a valid ID!</div>";
			return;
		}
		else
		{
			$data = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "assignments WHERE assignmentID = '" . $assignmentID . " LIMIT 1'");
			if ( empty($data) )
			{
				echo "<div class=\"error\"><p>I couldn't find an assignment with that ID.</p></div>";
				return;
			}
			$data = $data[0];
		}	
	}
	
	?>
	<!-- Beginning of Assignment Adding Page -->

	<form name="readingform" id="readingform" class="wrap" method="post" action="">
		<input type="hidden" name="updateaction" value="<?php echo $mode?>">
		<input type="hidden" name="assignmentID" value="<?php echo $assignmentID?>">
		<div id="poststuff" class="metabox-holder has-right-sidebar">
    	
		<div id="side-info-column" class="inner-sidebar">
		<div id="datetimediv" class="postbox">
			<h3 class='hndle'><span>Type of Assignment</span></h3>
                <div class="inside withlabels biblio_options">
                    <p>Select the type of assignment from the list below.</p>							
                    <p>
                    <label for="assignment_reading">
                    <input type="radio" id="assignment_reading" name="assignment_type" class="input" value="reading" 
                    <?php if ( empty($data) || $data->assignments_type=='reading' ) echo "checked" ?>/>
                    Reading</label>
                    </p>

                    <p>
                    <label for="assignment_writing">
                    <input id="assignment_writing" type="radio" name="assignment_type" name="assignment_type" class="input" value="writing" 
                    <?php if ( !empty($data) && $data->assignments_type=='writing' ) echo "checked" ?>/>  
                    Writing</label>
                    </p>
                    
                    <p>
                    <label for="assignment_presentation">
                    <input type="radio" id="assignment_presentation" name="assignment_type" class="input" value="presentation" 
                    <?php if ( !empty($data) && $data->assignments_type=='presentation' ) echo "checked" ?>/>  
                    Presentation</label>
                    </p>
                    
                    <p>
                    <label for="assignment_group">
                    <input type="radio" id="assignment_group" name="assignment_type" class="input" value="groupwork" 
                    <?php if ( !empty($data) && $data->assignments_type=='groupwork' ) echo "checked" ?>/> 
                    Group Work </label>
                    </p>
            
                    <p>
                    <label for="assignment_research">
                    <input type="radio" id="assignment_research" name="assignment_type" class="input" value="research" 
                    <?php if ( !empty($data) && $data->assignments_type=='research' ) echo "checked" ?>/>  
                    Research</label>
                    </p>

                    <p>
                    <label for="assignment_discussion">
                    <input type="radio" id="assignment_discussion" name="assignment_type" class="input" value="discussion" 
                    <?php if ( !empty($data) && $data->assignments_type=='discussion' ) echo "checked" ?>/> 
                    Discussion</label>	
                    </p>
                    
                    <p>
                    <label for="assignment_creative">
                    <input type="radio" id="assignment_creative" name="assignment_type" class="input" value="creative" 
                    <?php if ( !empty($data) && $data->assignments_type=='creative' ) echo "checked" ?>/>  
                    Creative</label>		
                    </p>
				</div>
		</div>
        

			<div id="datetimediv" class="postbox">
			<h3 class='hndle'><span>Due Date</span></h3>
				<div class="inside">
					<p><label for="assignment_scheduleID"><?php _e('Date Due'); ?></label></p>
					<select name="assignment_scheduleID" id="assignment_scheduleID">
						<option value=""></option>
						<?php 
							// Get schedule events from DB
							$SQL = 'SELECT * from '.$wpdb->prefix.'schedule ORDER BY schedule_date, schedule_timestart';
							$dates = $wpdb->get_results($SQL, OBJECT);
							foreach ($dates as $date) {
						?>
						<option value="<?php echo $date->scheduleID; ?>"<?php if ($date->scheduleID==$data->assignments_scheduleID) echo " selected"; ?>><?php echo date('F d, Y', strtotime($date->schedule_date)); ?>: <?php echo $date->schedule_title; ?></option>
						<?php } ?>
					</select>
					<p><label for="assignment_assignedScheduleID"><?php _e('Date Assigned (optional)'); ?></label></p>
					<select name="assignment_assignedScheduleID" id="assignment_assignedScheduleID">
						<option value=""></option>
						<?php 
							// Get schedule events from DB
							$SQL = 'SELECT * from '.$wpdb->prefix.'schedule ORDER BY schedule_date, schedule_timestart';
							$dates = $wpdb->get_results($SQL, OBJECT);
							foreach ($dates as $date) {
						?>
						<option value="<?php echo $date->scheduleID; ?>"<?php if ($date->scheduleID==$data->assignments_assignedScheduleID) echo " selected"; ?>><?php echo date('F d, Y', strtotime($date->schedule_date)); ?>: <?php echo $date->schedule_title; ?></option>
						<?php } ?>
					</select>
				</div>

			</div>
	</div><!-- End side info column-->
		
		<!-- Start Main Body -->
		<div id="post-body" class="has-sidebar">
		    <div id="post-body-content" class="has-sidebar-content">
			
    			<div id="normal-sortables" class="meta-box-sortables">

					<div id="titlediv">
					    <label for="assignment_title"><?php _e('Title'); ?></label>
						
						<div id="titlewrap">
							<input type="text" id="title" name="assignment_title" class="input" size="45" value="<?php if ( !empty($data) ) echo htmlspecialchars($data->assignments_title); ?>" />
						</div>
					</div><!-- End #titlediv -->
				
					<div class="postbox" id="bibfield">
						<h3 class='hndle'><span>Assignment Information</span></h3>
						<div class="inside">
						<p><label for="assignment_bibliographyID">Select the bibliography for the reading</label></p>
										<?php
										// Get bibliography events from DB
    									$SQL = 'SELECT * from '.$wpdb->prefix.'bibliography ORDER BY author_last, title';
    									$bibs = $wpdb->get_results($SQL, OBJECT); ?>
							<select name="assignment_bibliographyID" <?php if(empty($bibs)): ?>disabled="disabled"<?php endif;?>>
							    <?php 
					
									if(!empty($bibs)): ?>
								<option value="">Select a Bibliography Entry</option>

								<?php foreach ($bibs as $bib): ?>
								<option value="<?php echo $bib->entryID; ?>"<?php if ($bib->entryID==$data->assignments_bibliographyID) echo " selected"; ?>><?php echo $bib->author_last; ?>: <?php echo $bib->title; ?></option>
								<?php endforeach; else: ?>
								    <option value="">No Biblography Entries!</option>
								    <?php endif;?>
						</select>
						<p><label for="assignment_pages">Enter in the pages for the assignment:</label></p>
						
						<input type="text" name="assignment_pages" class="input" value="<?php if ( !empty($data) ) echo htmlspecialchars($data->assignments_pages); ?>" />												
						</div> <?php // Closes .inside ?>
					</div> <?php // Closes postbox ?>


					<div class="postbox" id="descriptionfield">
						<h3 class='hndle'><span>Description</span></h3>
						<div class="inside">
							<p><label for="assignment_description">Enter in a description of the assignment below.</label></p>
							<textarea name="assignment_description" class="mceEditor input" rows="10" cols="60"><?php if ( !empty($data) ) echo htmlspecialchars($data->assignments_description); ?></textarea>
				</fieldset>
						</div> <?php // closes inside ?>
					</div> <?php // closes postbox ?>
                </div>
			</div> <?php // closes normal-sortables ?>
		</div> <?php // closes post-body ?>
		
		<!-- Beginning of side info column -->
			
		<div class="clear">				
		</div>
		<p class="submit">
			<input type="submit" name="save" class="button-primary" value="Save Assignment &raquo;" />
		</p>
		</div>
	</form>
	<?php
}

// Handles the schedule management page
function schedule_manage()
{
	global $wpdb;
    $spcoursewareAdminOptions = spcourseware_get_admin_options();

    $defaultStart = !empty($spcoursewareAdminOptions['course_timestart']) ? $spcoursewareAdminOptions['course_timestart'] : date('H:i:s');
    $defaultStop = !empty($spcoursewareAdminOptions['course_timeend']) ? $spcoursewareAdminOptions['course_timeend'] : date('H:i:s');
    
    
	$updateaction = !empty($_REQUEST['updateaction']) ? $_REQUEST['updateaction'] : '';
	$scheduleID = !empty($_REQUEST['scheduleID']) ? $_REQUEST['scheduleID'] : '';
	$title = !empty($_REQUEST['schedule_title']) ? $_REQUEST['schedule_title'] : '';
	$date = !empty($_REQUEST['schedule_date']) ? $_REQUEST['schedule_date'] : date('Y-m-d');
	$description = !empty($_REQUEST['schedule_description']) ? $_REQUEST['schedule_description'] : '';	
	$timestart = !empty($_REQUEST['schedule_timestart']) ? date('H:i:s', strtotime($_REQUEST['schedule_timestart'])) : $defaultStart;
	$timestop = !empty($_REQUEST['schedule_timestop']) ? date('H:i:s', strtotime($_REQUEST['schedule_timestop'])) : $defaultStop;	
	
    
	if (isset($_REQUEST['action']) ):
		if ($_REQUEST['action'] == 'delete_schedule') 
		{
			$scheduleID = intval($_GET['scheduleID']);
			if (empty($scheduleID))
			{
				?><div class="error"><p><strong>Failure:</strong> No schedule ID given. I guess I deleted nothing successfully.</p></div><?php
			}
			else
			{
				$wpdb->query("DELETE FROM " . $wpdb->prefix . "schedule WHERE scheduleID = '" . $scheduleID . "'");
				$sql = "SELECT scheduleID FROM " . $wpdb->prefix . "schedule WHERE scheduleID = '" . $scheduleID . "'";
				$check = $wpdb->get_results($sql);
				if ( empty($check) || empty($check[0]->scheduleID) )
				{
					?><div class="updated"><p>Schedule Entry <?php echo $scheduleID; ?> deleted successfully.</p></div><?php
				}
				else
				{
					?><div class="error"><p><strong>Failure:</strong> Could not delete <?php echo $scheduleID; ?>.</p></div><?php
				}
			}
		} // end delete_schedule block
	endif;
	
	if ( $updateaction == 'update_schedule' )
	{
		
		if ( empty($scheduleID) )
		{
			?><div class="error"><p><strong>Failure:</strong> No schedule ID given.</p></div><?php
		}
		else
		{
			$sql = "UPDATE " . $wpdb->prefix . "schedule SET schedule_title = '" . $title . "', schedule_date = '" . $date . "', schedule_timestart = '" . $timestart . "', schedule_timestop = '" . $timestop . "', schedule_description = '" . $description . "'  WHERE scheduleID = '" . $scheduleID . "'";
			$wpdb->get_results($sql);
			$sql = "SELECT scheduleID FROM " . $wpdb->prefix . "schedule WHERE schedule_title = '" . $title . "' and schedule_date = '" . $date . "' and schedule_description = '" . $description . "'  LIMIT 1";
			$check = $wpdb->get_results($sql);
			if ( empty($check) || empty($check[0]->scheduleID) )
			{
				?><div class="error"><p><strong>Failure:</strong> Try again?</p></div><?php
			}
			else
			{
				?><div class="updated"><p>schedule <?php echo $scheduleID; ?> updated successfully.</p></div><?php
			}
		}
	} // end update_schedule block
	elseif ( $updateaction == 'add_schedule' )
	{
		$sql = "INSERT INTO " . $wpdb->prefix . "schedule SET schedule_title = '" . $title . "', schedule_date = '" . $date . "', schedule_timestart = '" . $timestart . "', schedule_timestop = '" . $timestop . "', schedule_description = '" . $description . "'";
		$wpdb->get_results($sql);
		$sqlres = "SELECT scheduleID FROM " . $wpdb->prefix . "schedule WHERE schedule_title = '" . $title . "' and schedule_date = '" . $date . "' and schedule_description = '" . $description . "'";
		$check = $wpdb->get_results($sqlres);

		if ( empty($check) || empty($check[0]->scheduleID) )
		{
			?><div class="error"><p><strong>Failure:</strong> Try again? <?php if(empty($_REQUEST['schedule_date'])) echo 'Date for schedule entry is required.'; else echo $sqlres; ?></p></div><?php
		}
		else
		{
			?><div class="updated"><p>Writing up a storm! Schedule id <?php echo $check[0]->scheduleID;?> added successfully.</p></div><?php
		}
	} // end add_schedule block
	?>

	<div class=wrap>
	<?php
	if ( $_REQUEST['action'] == 'edit_schedule' )
	{
		?>
		<h2><?php _e('Edit Schedule Entry'); ?></h2>
		<?php
		if ( empty($scheduleID) )
		{
			echo "<div class=\"error\"><p>I didn't get an entry identifier from the query string. Giving up...</p></div>";
		}
		else
		{
			schedule_editform('update_schedule', $scheduleID);
		}	
	}
	else
	{
		?>
		<h2><?php _e('Add Schedule Entry'); ?></h2>
		<?php schedule_editform(); ?>
	
		<h2><?php _e('Manage Schedule'); ?></h2>
		<?php schedule_displaylist();
	}
	?>
	</div><?php
}

// Displays the list of schedule entries
function schedule_displaylist() 
{
	global $wpdb;
	
	$schedule = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "schedule ORDER BY scheduleID DESC");
	
	if ( !empty($schedule) )
	{
		?>
			<table width="100%" cellpadding="3" cellspacing="3" class="widefat post">
			<thead>
			<tr>
				<th scope="col"><?php _e('ID') ?></th>
				<th scope="col"><?php _e('Title') ?></th>
				<th scope="col"><?php _e('Description') ?></th>
				<th scope="col"><?php _e('Date') ?></th>
				<th scope="col"><?php _e('Edit') ?></th>
				<th scope="col"><?php _e('Delete') ?></th>
			</tr>
			</thead>
		<?php
		$class = '';
		foreach ( $schedule as $schedule )
		{
			$class = ($class == 'alternate') ? '' : 'alternate';
			?>
			<tbody>
			<tr class="<?php echo $class; ?>">
				<th scope="row"><?php echo $schedule->scheduleID; ?></th>
				<td><?php echo $schedule->schedule_title ?></td>
				<td><?php echo $schedule->schedule_description ?></td>
				<td><?php echo $schedule->schedule_date ?></td>
				<td><a href="admin.php?page=<?php echo $_GET['page']; ?>&amp;action=edit_schedule&amp;scheduleID=<?php echo $schedule->scheduleID;?>" class="edit"><?php echo __('Edit'); ?></a></td>
				<td><a href="admin.php?page=<?php echo $_GET['page']; ?>&amp;action=delete_schedule&amp;scheduleID=<?php echo $schedule->scheduleID;?>" class="delete" onclick="return confirm('Are you sure you want to delete this entry?')"><?php echo __('Delete'); ?></a></td>
			</tr>
			</tbody>
			<?php
		}
		?>
		</table>
		<?php
	}
	else
	{
		?>
		<p><?php _e("You haven't entered any schedule entries yet.") ?></p>
		<?php	
	}
}


// Displays the add/edit form
function schedule_editform($mode='add_schedule', $scheduleID=false)
{
	global $wpdb;
	$data = false;
	
	if ( $scheduleID !== false )
	{
		// this next line makes me about 200 times cooler than you.
		if ( intval($scheduleID) != $scheduleID )
		{
			echo "<div class=\"error\"><p>Bad Monkey! No banana!</p></div>";
			return;
		}
		else
		{
			$data = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "schedule WHERE scheduleID = '" . $scheduleID . " LIMIT 1'");
			if ( empty($data) )
			{
				echo "<div class=\"error\"><p>I couldn't find a quote linked up with that identifier. Giving up...</p></div>";
				return;
			}
			$data = $data[0];
		}	
	}
	
	spcourseware_get_admin_options();
    
	?>

    <!-- Beginning of Add Schedule Entry -->
	<form name="scheduleform" id="scheduleform" class="wrap" method="post" action="">
	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div id="side-info-column" class="inner-sidebar">
			<div id="datetimediv" class="postbox">
				<h3 class='hndle'><span>Date &amp; Time</span></h3>
				<div class="inside">
							<p><label><?php _e('Date'); ?> (YYYY-MM-DD)</label></p>
							<input type="text" name="schedule_date" id="schedule_date" class="format-y-m-d divider-dash split-date date" value="<?php if ( !empty($data) ) echo htmlspecialchars($data->schedule_date); ?>" />			
							<?php $url = WP_PLUGIN_URL."/scholarpress-courseware/datepicker/images/calendar.gif"; ?>		
							<script type="text/javascript" charset="utf-8">
							jQuery("#schedule_date").datepicker({ 
							    dateFormat: jQuery.datepicker.W3C, 
								showOn: "button", //change to button once button works 
							    buttonImage: "<?php echo $url;?>",
							    buttonImageOnly: true 
							});
							</script>							

							<?php $spcoursewareAdminOptions = spcourseware_get_admin_options(); ?>
							<p><label for="schedule_timestart"><?php _e('TimeStart'); ?> (12:00pm)</label></p>
							<?php ?><input type="text" name="schedule_timestart" class="date"  value="<?php if ( !empty($data) ) {echo date('g:ia',strtotime($data->schedule_timestart));} else {echo date('g:ia', strtotime($spcoursewareAdminOptions['course_timestart']));} ?>" /> <?php ?>
							
							<p><label for="schedule_timestop"><?php _e('TimeStop'); ?> (1:00pm)</label></p>
							<input type="text" name="schedule_timestop" class="date"  value="<?php if ( !empty($data) ){ echo date('g:ia', strtotime($data->schedule_timestop));} else {echo date('g:ia', strtotime($spcoursewareAdminOptions['course_timeend']));} ?>" />													

				</div>
			</div>
		</div><!-- End side info column-->


		<input type="hidden" name="updateaction" value="<?php echo $mode?>">
		<input type="hidden" name="scheduleID" value="<?php echo $scheduleID?>">

		<!-- Start Main Body -->
		<div id="post-body" class="has-sidebar">
			<div id="post-body-content" class="has-sidebar-content">
				<div id="titlediv">
						<h3><?php _e('Title'); ?></h3>
						<div id="titlewrap">
						<input type="text" id="title" name="schedule_title" class="input" size="45" value="<?php if ( !empty($data) ) echo htmlspecialchars($data->schedule_title); ?>" />
					</div>
				</div><!-- End #titlediv -->

			<div id="normal-sortables" class="meta-box-sortables">
				<div id="postexcerpt" class="postbox">
					<h3 class='hndle'><span>Description</span></h3>
					<div class="inside">
						<p><label for="description">Enter in a description of what will go on in this class.</label></p>
						<textarea name="schedule_description" id="description" cols="45" rows="7"><?php if ( !empty($data) ) echo htmlspecialchars($data->schedule_description); ?></textarea>
					</div>

				</div>
			</div>

    				<p class="submit">
    					<input type="submit" name="save" class="button-primary" value="Publish Schedule Entry &raquo;" />
    				</p>
        </div>
	</div>
	</div>
	</form>
	<div class="clear"></div>
	
	<?php
}

// Form for the Course Information page.
function courseinfo_manage()
{
	global $wpdb;
	$data = false;

	if ($_POST['save']) {
	    
	    $courseTimeStart = !empty($_REQUEST['course_timestart']) ? date('H:i:s', strtotime($_REQUEST['course_timestart'])) : '';
        $courseTimeEnd = !empty($_REQUEST['course_timeend']) ? date('H:i:s', strtotime($_REQUEST['course_timeend'])) : '';
        
		spcourseware_set_admin_options($_REQUEST['course_title'], $_REQUEST['course_number'], $_REQUEST['course_section'], $courseTimeStart, $courseTimeEnd, $_REQUEST['course_location'], $_REQUEST['course_timedays'], $_REQUEST['instructor_firstname'], $_REQUEST['instructor_lastname'], $_REQUEST['instructor_email'], $_REQUEST['instructor_telephone'], $_REQUEST['instructor_office'],  $_REQUEST['course_description'], $_REQUEST['instructor_hours']);
	
		echo '<div class="updated"><p>Course information saved successfully.</p></div>';
	
	}

	$spcoursewareAdminOptions = spcourseware_get_admin_options();

	?>
	
	<div id="dashb" class="wrap">
	    <h2>Course Information Management</h2>
	    
	<form name="courseinfoform" id="courseinfoform" class="wrap" method="post" action="">

		<input type="hidden" name="updateinfo" value="<?php echo $mode?>" />
		<div id="poststuff" class="metabox-holder">		
		<!-- Start Main Body -->
		<div id="post-body" class="has-sidebar">
			<div id="post-body-content" class="has-sidebar-content">
			    
				<div id="courseinformation" class="postbox">
					<h3 class='hndle'><span>Course Information</span></h3>                    
					<div class="inside withlabels">
					    <p><label for="title"><?php _e('Title'); ?></label></p>
						<input type="text" id="title" name="course_title" class="input" size="45" value="<?php echo $spcoursewareAdminOptions['course_title']; ?>" />
						<p><label for="course_number"><?php _e('Course Number'); ?></label></p>
							<input type="text" name="course_number" class="input" size="45" value="<?php echo $spcoursewareAdminOptions['course_number']; ?>" />
						<p><label for="course_section"><?php _e('Course Section'); ?></label></p>
							<input type="text" name="course_section" class="input" size="45" value="<?php echo $spcoursewareAdminOptions['course_section']; ?>" />
						<p><label for="course_timedays"><?php _e('Course Days'); ?></label></p>
							<input type="text" name="course_timedays" class="input" size="45" value="<?php echo $spcoursewareAdminOptions['course_timedays']; ?>" />
						<p><label for="course_timestart"><?php _e('Course Time Start (e.g. 11:00am)'); ?></label></p>
							<input type="text" name="course_timestart" class="input" size="45" value="<?php echo date('g:ia', strtotime($spcoursewareAdminOptions['course_timestart'])); ?>" />

						<p><label for="course_timeend"><?php _e('Course Time End (e.g. 12:00pm)'); ?></label></p>
							<input type="text" name="course_timeend" class="input" size="45" value="<?php echo date('g:ia', strtotime($spcoursewareAdminOptions['course_timeend'])); ?>" />
						<p><label for="course_location"><?php _e('Course Location'); ?></label></p>
							<input type="text" name="course_location" class="input" size="45" value="<?php echo $spcoursewareAdminOptions['course_location']; ?>" />
						<p><label for="course_description"><?php _e('Course Description'); ?></label></p>
							<textarea name="course_description" class="input" cols="45" rows="7"><?php echo $spcoursewareAdminOptions['course_description']; ?></textarea>
					</div>
					
				</div>

			</div>
		</div>
		<div id="side-info-column" class="inner-sidebar">
			<div id="datetimediv" class="postbox" >
					<h3 class='hndle'><span>Instructor Information</span></h3>
					<div class="inside">
						<p><label for="instructor_firstname"><?php _e('Instructor First Name'); ?></label></p>
							<input type="text" name="instructor_firstname" class="date" value="<?php echo $spcoursewareAdminOptions['instructor_firstname']; ?>" />						

						<p><label for="instructor_lastname"><?php _e('Instructor Last Name'); ?></label></p>
							<input type="text" name="instructor_lastname" class="date" value="<?php echo $spcoursewareAdminOptions['instructor_lastname']; ?>" />					

						<p><label for="instructor_email"><?php _e('Instructor Email'); ?></label></p>
							<input type="text" name="instructor_email" class="date" value="<?php echo $spcoursewareAdminOptions['instructor_email']; ?>" />

						<p><label for="instructor_telephone"><?php _e('Instructor Telephone'); ?></label></p>
							<input type="text" name="instructor_telephone" class="date" value="<?php echo $spcoursewareAdminOptions['instructor_telephone']; ?>" />

						<p><label for="instructor_office"><?php _e('Instructor Office Location'); ?></label></p>
							<input type="text" name="instructor_office" class="date" value="<?php echo $spcoursewareAdminOptions['instructor_office']; ?>" />

						<p><label for="instructor_hours"><?php _e('Instructor Office Hours'); ?></label></p>
							<input type="text" name="instructor_hours" class="date" value="<?php echo $spcoursewareAdminOptions['instructor_hours']; ?>" />
					</div>	

			</div>
		</div><!-- End side info column-->
		<p class="submit clear"><input type="submit" name="save" class="button-primary" value="Save Course Information &raquo;" /></p>
		</div>
</div>
	</form>
	<?php
}