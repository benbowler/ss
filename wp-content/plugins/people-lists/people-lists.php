<?php
/** 
 * Plugin Name: People Lists
 * Plugin URI: http://www.ctlt.ubc.ca 
 * Description: Plugin providing a rich text editor on the profile page for easy modifications of specific user profile    
 *				information that can be displayed on any page using the [people-lists list=example-list] shortcode. Users 
 *				will also be able to add custom fields to their user profile and these fields can be displayed on any page 
 * 				using the People Lists template (which can be styled using HTML) that provides codes for every field that is 
 *				desired to be displayed.     
 * Author: Gagan Sandhu , Enej Bajgoric , CTLT DEV, UBC 
 * Version: 1.3.10
 * Author URI: http://www.ctlt.ubc.ca 
 *  
 * GNU General Public License, Free Software Foundation <http://creativecommons.org/licenses/GPL/2.0/>
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */ 
 
# now you can define to for people list to not include the visual editor for 
# there are other plugins that do that quite well 
# like http://wordpress.org/extend/plugins/visual-biography-editor/



if(!defined("PEOPLE_LIST_VISUAL_EDITOR"));
	define("PEOPLE_LIST_VISUAL_EDITOR", true);

add_action('admin_print_styles-settings_page_people_lists', 'people_list_options_init_style' );
add_action('admin_print_scripts-settings_page_people_lists', 'people_list_options_init_script' );

if(PEOPLE_LIST_VISUAL_EDITOR):
	add_action('admin_print_scripts-user-edit.php', 'people_lists_tinymce_init_script');
	add_action('admin_print_scripts-profile.php', 'people_lists_tinymce_init_script');
	
	add_action('admin_print_styles-user-edit.php', 'people_lists_tinymce_init_style');
	add_action('admin_print_styles-profile.php', 'people_lists_tinymce_init_style');
	
	// add wysiwig textarea to  to 
	add_action('show_user_profile', 'people_list_edit_user_profile_bio_field', 10, 1);
	add_action('edit_user_profile', 'people_list_edit_user_profile_bio_field', 10 ,1);
	
	add_action("admin_init",'people_list_edit_user_profile_bio_init');
	add_filter( 'get_the_author_description', 'people_list_edit_user_profile_bio_filter' );
	
	
	// for the media buttons - I think this could probably be removed
	add_action('admin_footer-user-edit.php', 'people_lists_media_buttons', 40 );
	add_action('admin_footer-profile.php', 'people_lists_media_buttons', 40 );

endif;

add_action('admin_footer-user-edit.php', 'wp_preload_dialogs', 30 );
add_action('admin_footer-profile.php', 'wp_preload_dialogs', 30 );



add_action('admin_init', 'people_list_register_options_init');
add_action('admin_menu', 'people_list_options_add_page');
add_action('wp_ajax_people_list_save', 'people_list_saving_list');
add_action('wp_ajax_people_settings_save', 'people_list_saving_settings');
add_action('media_buttons_context', 'people_lists_overlay_button');
add_action('admin_footer', 'people_lists_overlay_popup_form');

add_shortcode('people-lists', 'people_lists_shortcode');

add_filter('widget_text', 'do_shortcode');
add_filter('user_contactmethods','people_lists_user_fields_filter');






function people_list_edit_user_profile_bio_field($user) { 
	$news_user = new WP_User( $user->ID );
?>
	<table class="form-table">
	<tr>
		<th><label for="description"><?php _e('General Information'); ?></label></th>
		<td><?php wp_editor($news_user->description, "description-1", array('textarea_name'=>'description')); ?>
		<span class="description"><?php _e('Share a little biographical information to fill out your profile. This may be shown publicly.'); ?></span></td>
	</tr>
	</table>
	<?php 
}
// a better way to filter the description
// remove_filter('pre_user_description', 'wp_filter_kses');
// add_filter( 'pre_user_description', 'wp_filter_post_kses' );



function people_list_edit_user_profile_bio_init() {
	remove_all_filters('pre_user_description');
}
function people_list_edit_user_profile_bio_filter($bio){
	return apply_filters('the_content', $bio);
}

/**
 * people_lists_user_fields_filter function.
 * Description: Saves the list of fields added by user to Wordpress default fields in array user_contactmethods for 	
 *				displaying in the Your Profile section.
 * @access public
 * @param mixed $user_fields
 * @return void
 */
function people_lists_user_fields_filter($user_fields)
{
	$option_name = 'people-lists'; 
	$people_list_option = get_option($option_name);
	
	if( is_array($people_list_option['settings']) ):
		foreach ($people_list_option['settings'] as $index => $list){
			 $user_field_added[$index] = stripslashes($list);
		}
	endif;
	
	if(empty($people_list_option['settings'])){
		return $user_fields;
	} 	
	else{
		return array_merge($user_field_added,$user_fields);
	}
}

/**
 * people_list_saving_settings function.
 * Description: Storing the new fields information in the database and saving sorting of that list.
 * @access public
 * @return void
 */
function people_list_saving_settings() {
	$option_name = 'people-lists'; 
	$people_list = get_option($option_name);
	
	wp_parse_str( urldecode($_POST['field_info']), $field_info );
	
	$final_field_array = array();
	$count = 0;
	
	if( is_array($field_info['field_slug_list']) ):	
		foreach($field_info['field_slug_list'] as $slug)
		{
			$final_field_array[$slug] = $field_info['field_name_list'][$count];
			$count++;
		}
	endif;
	
	$people_list['settings'] = $final_field_array;
	
    update_option( $option_name, $people_list);
}

/**
 * people_list_saving_list function.
 * Description: Storing list information in the database for new and updated lists to track any sorting of the lists.
 * @access public
 * @return void
 */
function people_list_saving_list() {
	$option_name = 'people-lists'; 
	$people_list['lists'] = array();
	$people_list = get_option($option_name);
	
	wp_parse_str( $_POST['list'], $list );
	
	
	wp_parse_str($_POST['form'], $data);
	
	// set the list to be something nice	
	$data['list'] = $list; 

	if(!is_numeric($data['avatar_size']))
		$data['avatar_size'] = 96;
	
	if( is_numeric( $data['list_id'] ) ||  is_array( $people_list['lists'][$data['list_id']] )):
		// make sure that the slug is beeing passed on
		$data['slug'] = $people_list['lists'][$data['list_id']]['slug'];
		$people_list['lists'][$data['list_id']] = $data;
		echo 'update';
	else:
		$slug = people_lists_slug( $data['title'] );
		// Check if the slug exists 
		$counter = 1;
		while( people_lists_slug_exists($slug,$people_list['lists']) )
		{
			$slug = people_lists_slug($data['title'])."-".$counter;
			$counter += 1;
		}
		$data['slug'] = $slug;
		// Saving for the first time
		$people_list['lists'][] = $data;
		echo 'new';
	endif;
	
    update_option( $option_name, $people_list);
 	die(); // thats it
}


/**
 * people_lists_slug function.
 * Description: Creating a slug (shortcode list name) using the list name provided by the user.
 * @access public
 * @param mixed $str
 * @return void
 */
function people_lists_slug($str)
{
	$str = strtolower(trim($str));
	$str = preg_replace('/[^a-z0-9-]/', '-', $str);
	$str = preg_replace('/-+/', "-", $str);
	return $str;
}

/**
 * people_lists_slug_exists function.
 * Description: Check if a slug (shortcode list name) exists. 
 * @access public
 * @param mixed $slug
 * @param mixed $people_list['lists']
 * @return void
 */
function people_lists_slug_exists($slug,$people_list)
{	
	if( is_array($people_list) ):
		foreach($people_list as $list):
			if($list['slug'] == $slug)
				return $list;		
		endforeach;
	endif;
	return false;
}

/**
 * people_lists_field_slug_exists function.
 * Description: Check if a field slug with that name exists. 
 * @access public
 * @param mixed $slug
 * @param mixed $people_list['lists']
 * @return void
 */
function people_lists_field_slug_exists($field_slug,$people_list)
{
	if( is_array($people_list) ):
		foreach($people_list as $fields):
			if($fields['field_slug'] == $field_slug)
				return $fields;	
		endforeach;
	endif;	
	
	return false;
}


/**
 * people_lists_media_buttons function.
 * add media buttons if they need to be there 
 * @access public
 * @return void
 */
function people_lists_media_buttons() {
        if ( !current_user_can( 'upload_files' ) ) return;
        echo '<div id="media-buttons-shell"><span id="media-buttons" class="hide-if-no-js" style="display:none">';
        do_action( 'media_buttons' );
        echo '</span></div>';
}

/**
 * people_lists_tinymce_init_script function.
 * Description: Calling the appropriate JS files to initialize tinyMCE on profile page.
 * @access public
 * @return void
 */
function people_lists_tinymce_init_script(){
	// wp_enqueue_script('tiny_mce');
	// add_action('admin_print_footer_scripts', 'wp_tiny_mce', 25 );
	
	wp_enqueue_script('people-lists-tinymce', plugins_url('/people-lists/js/people-lists-tinymce.js'),'jquery');
	
	// add_filter( 'tiny_mce_before_init', 'people_lists_tiny_filter_remove_fullscreen');
}

/**
 * people_lists_tinymce_init_style function.
 * Description: Calling the appropriate CSS files to initialize tinyMCE on profile page.
 * @access public
 * @return void
 */
function people_lists_tinymce_init_style(){
	// wp_enqueue_style('people-lists-tinymce', plugins_url('/people-lists/css/people-lists-tinymce.css'),'css');
}
/**
 * people_list_register_options_init function.
 * Description: Registering People Lists Settings Page
 * @access public
 * @return void
 */
function people_list_register_options_init(){
	register_setting( 'people_lists_options', 'people_lists', 'people_list_validate_admin_page' );
	
	// set the language 
	load_plugin_textdomain( 'people-list', false , basename( dirname( __FILE__ ) ) . '/languages' );
	
}

/**
 * people_list_options_init_style function.
 * Description: Calling the appropriate CSS files to initialize functionality on the People Lists Settings page.
 * @access public
 * @return void
 */
function people_list_options_init_style(){	
	wp_enqueue_style('people-lists-style',  plugins_url('/people-lists/css/people-lists.css'),'css');
}
/**
 * people_list_options_init_script function.
 * Description: Calling the appropriate JS files to initialize functionality on the People Lists Settings page.
 * @access public
 * @return void
 */
function people_list_options_init_script(){	
	wp_enqueue_script('people-lists-jquery-sortable', plugins_url('/people-lists/js/jquery-ui.min.js'), array('jquery','jquery-ui-tabs','jquery-ui-sortable'));
	wp_enqueue_script('people-lists', plugins_url('/people-lists/js/people-lists.js'), array('jquery','jquery-ui-tabs','jquery-ui-sortable'));
	}


/**
 * people_list_options_add_page function.
 * Description: Initialize People Lists Option page.
 * @access public
 * @return void
 */
function people_list_options_add_page() {
	$page = add_options_page('People Lists', 'People Lists', 'manage_options', 'people_lists', 'people_list_admin_page');
	 add_action('admin_print_styles-' . $page,'people_lists_admin_styles');
}

/**
 * people_lists_admin_styles function.
 * Description: JQuery Sortable initialization call.
 * @access public
 * @return void
 */
function people_lists_admin_styles() {
	 wp_enqueue_script('people-lists-jquery-sortable');
	 wp_enqueue_script('people-lists');
	 wp_enqueue_style( 'people-lists-style');
	 wp_enqueue_script('jquery-ui-tabs');
}


/**
 * people_lists_overlay_button function.
 * Description: For adding the People Lists "Insert List" button to the pages & posts editing screens.
 * @access public
 * @param mixed $context
 * @return void
 */
function people_lists_overlay_button($context){
    $people_lists_overlay_image_button = plugins_url('/people-lists/img/form-button.png');
    $output_link = '<a href="#TB_inline?width=450&inlineId=people_lists_select_list_form" class="thickbox" id="add-people-list-button" title="' . __("Add People List", 'people-lists') . '"><img src="'.$people_lists_overlay_image_button.'" alt="' . __("Add People List", 'people-lists') . '" /></a>';
    return $context.$output_link;
}

/**
 * people_lists_overlay_popup_form function.
 * Description: For displaying the overlay to insert a People List to the pages & posts editing screens.
 * @access public
 * @return void
 */
function people_lists_overlay_popup_form(){
	$option_name = 'people-lists'; 
	$people_list_option = get_option($option_name);
    ?>
    <script>
        function people_lists_insert_overlay_form(){
		   	var people_lists_user_selection_list_value = jQuery("#people_lists_dropdown_selection option:selected").attr('value');
			if (people_lists_user_selection_list_value == "dropdown-first-option"){
				alert("<?php _e('Please select a list.','people-list');?>");
				return;
			}
            var win = window.dialogArguments || opener || parent || top;
            win.send_to_editor("[people-lists list=" + people_lists_user_selection_list_value + "]");
        }
    </script>

    <div id="people_lists_select_list_form" style="display:none;">
        <div class="people_lists_select_list_form_wrap">
        	<?php if(empty($people_list_option['lists'] )) : ?>
				<div id="message" class="updated below-h2 clear"><p><?php _e('You currently have no lists. Go ahead and create one! Click','people-list');?> <a href="/wp-admin/options-general.php?page=people_lists"><?php _e('here','people-list');?></a>.</p></div>
            <?php else: ?>
            <div style="padding:15px 15px 0 15px;">
                <h3 style="color:#5A5A5A!important; font-family:Georgia,Times New Roman,Times,serif!important; font-size:1.8em!important; font-weight:normal!important;"><?php _e("Insert A People List",'people-list');?></h3>
                <span>
                    <?php _e("Select a list from the dropdown below to add it to your post or page.",'people-list');?>
                </span>
            </div>
            <div style="padding:15px 15px 0 15px;">
		        <select id="people_lists_dropdown_selection">
		            <option value="dropdown-first-option">  <?php _e("Select a Form",'people-list');?>  </option>
		            <?php if( is_array($people_list_option['lists']) ):
		            	 foreach ($people_list_option['lists'] as $index =>$list_name): ?>
		                    <option value="<?php echo $list_name['slug']; ?>"><?php echo esc_html($list_name['title']); ?></option>
		            <?php endforeach; 
		            endif; ?>
		        </select> <br/>

            </div>
            <div style="padding:15px;">
                <input type="button" class="button-primary" value="<?php _e('Insert People List','people-list');?>" onclick="people_lists_insert_overlay_form();"/>&nbsp;&nbsp;&nbsp;
           		<a class="button" style="color:#bbb;" href="#" onclick="tb_remove(); return false;"><?php _e("Cancel"); ?></a>
            </div>
            
            <?php endif; ?>
            
        </div>
    </div>
    <?php
}

/**
 * people_list_admin_page function.
 * Description: HTML layout creation of the admin page and building/calling different panels (create/edit/manage).
 * @access public
 * @return void
 */
function people_list_admin_page() {
	
	$people_list_option = get_option('people-lists');
	
	if( is_numeric($_GET['delete']) ):
		unset($people_list_option['lists'][$_GET['delete']]);
		update_option( 'people-lists', $people_list_option);
	endif;
	
	if($_GET['delete-all'])
		delete_option('people-lists');
	?>
	<div class="wrap" id="people-list-page">
		<h2 id="people-list-header"><?php _e('People Lists','people-list');?></h2>
		<?php if($_GET['panel']=="create" || empty($people_list_option['lists']) || !isset($_GET['panel']) ):
		
		else: ?>
			<a href="options-general.php?page=people_lists&panel=create" class="button"><?php _e('Add New People List','people-list');?></a>
				
		<?php endif;
		
		if(empty($people_list_option['lists'] )) : ?>
			<div id="message" class="updated below-h2 clear"><p><?php _e('You currently have no lists. Go ahead and create one!','people-list');?></p></div>
			
		<?php else: ?>
			<ul id="people-list-manage">
				<li><a href="options-general.php?page=people_lists&panel=manage"><?php _e('View All Lists','people-list');?></a>
				<ul>
				<?php if( is_array($people_list_option['lists']) ):
					foreach($people_list_option['lists'] as $index =>$list): ?>
					<li>
						<a href="options-general.php?page=people_lists&panel=edit&list_id=<?php echo $index;?>"><?php echo $list['title']; ?></a>
					</li>
				<?php endforeach; 
				endif; ?>
				</ul>
				</li>
			</ul>
		
			<ul id="people-list-settings">
				<li>
					<a href="options-general.php?page=people_lists&panel=settings" id="people-lists-settings-link"><?php _e('Profile Settings','people-list');?></a> <span> <?php _e('add new fields to the user profile','people-list');?></span>
				</li>
			</ul>

		<?php endif; 
		
		if( empty($people_list_option['lists']) ):
			require_once('views/create.php');
		else:
			switch($_GET['panel']) {
				case "create":
					require_once('views/create.php');
				break;
				
				case "edit":
					if( is_numeric( $_GET['list_id'] ) && $people_list_option['lists'][$_GET['list_id']] ):
						$list_id = $_GET['list_id'];
						$list = $people_list_option['lists'][$_GET['list_id']];
						require_once('views/edit.php');
					else:
						require_once('views/create.php');
					endif;			
				break;
				
				case "manage":
					require_once('views/manage.php');
				break;
		
				case "settings":
					require_once('views/settings.php');
				break;
				
				default:
					require_once('views/create.php');
				break;
			}
		endif;
		?>	
	</div>
<?php	
}

/**
 * people_list_validate_admin_page function.
 * Description: Sanitizes and validates an inputted array.
 * @access public
 * @param mixed $input
 * @return void
 */
function people_list_validate_admin_page($input) {
	return $input;
}

/**
 * people_list_form function.
 * Description: Building of form users interact with, including a name field, a modifiable people lists profile display 	                                
 *              template and jQuery Sortable lists that allow for dragging and dropping of users for a specific list. 
 * @access public
 * @param bool $list_id. (default: false)
 * @param bool $list. (default: false)
 * @return void
 */
function people_list_form($list_id=false,$list=false)
{	
	$users_of_blog = get_users_of_blog();
	$option_name = 'people-lists'; 
	$people_list_option = get_option($option_name);
	
	if(empty($list['list']['uid']))
		$list['list']['uid'] = array();
	
	?>
	<div id="people-list-form-shell">
		<form method="post" id="people-list-form" action="options.php">
		<label for="title"><?php _e('Name','people-list');?></label><br />
		<input type="text" value="<?php echo $list['title'];?>" name="title" id="title" size="50" />
		<p><?php _e('The name helps identify which list you are editing.','people-list');?></p>
	
		<a id="template-link" href="#"><?php _e('Template Info','people-list');?></a>
		<div class="template-info" id="template-info" >
			<label id="avatar_size_label"><?php _e('User Picture size:','people-list');?> <input type="text" id="avatar_size" value="<?php echo $list['avatar_size'];?>" name="avatar_size" size="3" />px <span><?php _e('the size of the image that is created when %thumbnail% is called in the template.','people-list');?></span></label>	

			<div class="template-tabbed">
				
				<div class="template-area">
					<ul class="template-tabs">
						<li><a href="#default_codes" title="Default Codes" class="tabbed"><?php _e('Default Codes','people-list');?></a></li>
						<li><a href="#added_fields" title="Added Fields" class="tabbed" ><?php _e('Added Fields Codes','people-list');?></a></li>
						<li><a href="#before_after" title="Added Fields" class="tabbed" ><?php _e('Before & After','people-list');?></a></li>
					</ul>
					<div id="default_codes" class="template-content">
				  		<strong><?php _e('Default codes you can use are:','people-list');?> </strong>
				  		<ul>		  		
					  		<li>%nickname% - <?php _e('To display nickname','people-list');?> </li>
							<li>%email%    - <?php _e('To display email','people-list');?> </li>
							<li>%bio%      - <?php _e('To display user rich-text info from profile','people-list');?> </li>
							<li>%firstname%     - <?php _e('To display first name','people-list');?> </li>
							<li>%lastname%      - <?php _e('To display last name','people-list');?> </li>
							<li>%username%      - <?php _e('To display username','people-list');?> </li>
							<li>%thumbnail% - <?php _e('To display user\'s thumbnail photo','people-list');?> </li>
							<li>%website% - <?php _e('To display user\'s website','people-list');?> </li>
							<li>%aim% - <?php _e('To display user\'s website','people-list');?> </li>
							<li>%yahooim% - <?php _e('To display user\'s website','people-list');?> </li>
							<li>%jabbergoogle% - <?php _e('To display user\'s website','people-list');?> </li>
							<li>%id% - <?php _e('To display the user id','people-list');?></li>
							<li>%authorurl% <?php _e('To display the link url to this users posts','people-list');?></li>
						</ul>
					</div>
					<div id="added_fields" class="template-content">
						<strong><?php _e('Codes that you have added are:','people-list');?></strong>
						<ul>
						<?php
						 if( is_array($people_list_option['settings']) ): 
							foreach ($people_list_option['settings'] as $index => $field_slug): ?>
								<li class="template-code-list">%<?php echo $index; ?>%</li>
						<?php endforeach; 
							$filter_array = array();
							$filter_array =  apply_filters('people_list_custom_fields',$filter_array);
							
							foreach ( $filter_array as $item):
								echo '<li class="template-code-list">'.$item.'</li>';
							endforeach;
							
						endif; ?>
						</ul>
					</div>
					<div id="before_after">
						<ul>
							<li id="header-before-after"><span >before</span><span class='after-item'>after</span></li>
							<?php 
							foreach(array('nickname','email','bio','firstname','lastname','username','thumbnail','website','aim','yahooim','jabbergoogle','id','authorurl') as $item): 
								$before = ( isset($list['before'][$item]) ? $list['before'][$item]: '' );
								$after  = ( isset($list['after'][$item]) ? $list['after'][$item]: '' );
								people_lists_before_after_item($item,$list['before'][$item],$list['after'][$item],$item); 
							endforeach; 
							$item = null;
							foreach ($people_list_option['settings'] as $item => $field_slug):
								$before = ( isset($list['before'][$item]) ? $list['before'][$item]: '' );
								$after  = ( isset($list['after'][$item]) ? $list['after'][$item]: '' );
								people_lists_before_after_item( $field_slug, $list['before'][$item], $list['after'][$item], $item );
							endforeach; 
							$item = null;
							$filter_array = array();
							$filter_array =  apply_filters('people_list_custom_fields',$filter_array);
							if(is_array($filter_array)):
								foreach ( $filter_array as $item):
									$before = ( isset($list['before'][$item]) ? $list['before'][$item]: '' );
									$after  = ( isset($list['after'][$item]) ? $list['after'][$item]: '' );
									people_lists_before_after_item( $item, $list['before'][$item], $list['after'][$item], $item ); 
								endforeach; 
							endif;
							?>
						</ul>
					
					</div>
				</div>
			</div>
			<textarea name="template" class="template-text" id="template-text"><?php 
				if( !empty($list['template']) )
					echo  stripslashes(trim($list['template']));	
				else 
					echo people_lists_default_template(); ?>
			</textarea><br />
			
		</div>
	
		<div id="availableList" class="listDiv"> 
			<h4><?php _e('Available People','people-list');?></h4>
			<p><?php _e('List of users that have not been selected to be in your list. 
			Drag and drop the a person into the selected people area.','people-list');?></p>	
			<ul id="sortable1" class='droptrue'>
			<?php if( is_array($users_of_blog) ):
				foreach($users_of_blog as $person): 
					if(!in_array($person->ID, $list['list']['uid'])): ?>
						<li class="ui-state-default ui-state-default-list" id="uid_<?php echo $person->ID; ?>">
						<?php echo get_avatar($person->ID, 32); ?>
						<?php echo $person->display_name; ?><span><?php echo $person->user_email; ?></span></li>
					<?php else: 
						$selected_people[$person->ID] = $person;
						endif;
				endforeach;
			endif; ?>
			</ul>
		</div>
		
		<div id="selectedUserList" class="listDiv">
			<h4><?php _e('Selected People','people-list');?></h4> <a href="#" id="selected-lock"><?php _e('Pin','people-list');?></a>
			<p><?php _e('List of users that are have been selected to be in your list. Drag and drop a person into the available people area to remove them.','people-list');?></p>
			<ul id="sortable2" class='droptrue clear'>
			<?php if( is_array($list['list']['uid']) ):
				foreach(  $list['list']['uid'] as $person_id ): ?>
					<li class="ui-state-default ui-state-default-list" id="uid_<?php echo $selected_people[$person_id]->ID; ?>">
					<?php echo get_avatar($selected_people[$person_id]->ID, 32); ?>
					<?php echo $selected_people[$person_id]->display_name; ?><span><?php echo $selected_people[$person_id]->user_email; ?></span></li>
			<?php endforeach; 
			endif;?>
			</ul>
		</div>
		
		<p class="submit clear">
		<?php if(is_numeric($list_id)): ?>
			<input type="hidden" value="<?php echo esc_attr($list_id);?>" name="list_id" id="list-id" />
			<input id="submit-people-list" type="submit" class="button-primary" value="<?php _e('Update Changes','people-list');?>" /> 
		<?php else: ?>
			<input id="submit-people-list" type="submit" class="button-primary" value="<?php _e('Add List','people-list');?>" /> 
		<?php endif; ?>
			<img src="<?php bloginfo('url'); ?>/wp-admin/images/wpspin_light.gif" id="ajax-response" />
		</p>
		</form>
	</div>
<?php 
}

/**
 * people_list_field_form function.
 * Description: Building of form users interact with to add fields that are inserted into a jQuery Sortable list that allows 
 * 				for sorting and lists the field name, the template code that goes along with that field and an option for 
 * 				deletion of added fields.
 * @access public
 * @return void
 */
function people_list_field_form(){
	$option_name = 'people-lists'; 
	$people_list_option = get_option($option_name);
		
?>
	<div id="contact-info-shell">
		<label for="contact-info-field"><?php _e('Name of new field','people-list');?></label><br />
		<input type="text" name="contact-info-field" id="contact-info-field" size="30" />
	
		<p class="submit">
			<input type="submit" id="add-field-button" class="button-secondary" value="<?php _e('Add Field') ?>" /> 
			<img src="<?php bloginfo('url'); ?>/wp-admin/images/wpspin_light.gif" id="ajax-response2" />
		</p>
	
		<p><?php _e('This name should be a one or two word description of your new field. (eg. Position, Location, etc.)','people-list');?></p><br />
		<p><?php _e('List of fields that are being added to contact info section in your profile. <br/> Drag & Drop to change the order of their display in Your Profile.','people-list');?></p><br />
		<form id="profile-field-form">
			<ul id="sortable-profile-field">	
			<?php if( is_array($people_list_option['settings']) ): 
				foreach ($people_list_option['settings'] as $index => $field_slug):?>
					<li class="ui-state-default ui-state-default-list" ><?php echo stripslashes($field_slug); ?><span><?php _e('Template Code','people-list');?>: %<?php echo $index; ?>%<br /><a href="#" class="delete-field-link"><?php _e('Delete','people-list');?></a></span>
						<input type="hidden" value="<?php echo $index; ?>" name="field_slug_list[]" />
						<input type="hidden" value="<?php echo stripslashes($field_slug); ?>" name="field_name_list[]" />
					</li>
			<?php endforeach; 
			endif; ?>			
			</ul>
		</form>
	</div>
<?php
}

function people_lists_before_after_item($item = null, $before_value=null,$after_value=null,$id=null)
{
	if( isset($item) ):
		
	?>
	<li class="before-after-item">
		<input type="text" name="before[<?php echo $id; ?>]" value="<?php echo esc_attr($before_value); ?>" class="before-item" /> 
		<span class="before-after-item-label"><?php echo $item; ?></span>
		<input type="text"  name="after[<?php echo $id; ?>]" value="<?php echo esc_attr($after_value); ?>" class="after-item" />
	</li>
	<?php endif; 
}
/**
 * people_lists_default_template function.
 * Description: Display the default template, which includes a thumbnail, nickname, email and user's bio from their "Your 
 * 				Profile" tab.
 * @access public
 * @return void
 */
function people_lists_default_template()
{
	$html  =	"<div class ='user-thumbnail'>%thumbnail%</div> \n";
	$html .=	"<div class ='user-info'>%nickname%</div> \n";
	$html .=	"<div class ='user-info'>%email%</div> \n";
	$html .=	"<div class ='user-bio'>%bio%</div> \n";

	return $html;
}

/**
 * people_lists_shortcode function.
 * Description: Creation of the [people-lists list=example-list] shortcode and conversion of the template codes into the 
 *  			selected display option selected by the user.
 * @access public
 * @param mixed $atts
 * @return void
 */
function people_lists_shortcode($atts) {

	$option_name = 'people-lists'; 
	$people_list_option = get_option($option_name);
	
	extract(shortcode_atts(array(
		'list' => null,
			), $atts));
	if( !isset($list) )
		return "Empty list - Please remove the [people-lists] code.";
	
	
	// $people_lists = get_option('people-lists');	
	$found_people_list = people_lists_slug_exists($list,$people_list_option['lists']);
	
	if(!$found_people_list)
		return "This list is non-existent - Please remove the [people-lists list=".$list."] code.";

	$users_of_blog = get_users_of_blog();	
	$input_template = array();

	$input_template[0] = "%nickname%";
	$input_template[1] = "%email%";
	$input_template[2] = "%bio%";	
	$input_template[3] = "%firstname%";
	$input_template[4] = "%lastname%";
	$input_template[5] = "%username%";
	$input_template[6] = "%thumbnail%";
	$input_template[7] = "%website%";
	$input_template[8] = "%aim%";
	$input_template[9] = "%yahooim%";
	$input_template[10] = "%jabbergoogle%";
	$input_template[11] = "%id%";
	$input_template[12] = "%authorurl%";
		
	$counter = 13;
	if( is_array($people_list_option['settings']) ): 
		foreach($people_list_option['settings'] as $index => $field_slug):
			$input_template[$counter] = "%".$index."%";
			$counter++; 
		endforeach;
	endif;
	$input_template = apply_filters('people_list_custom_fields',$input_template);
	if( is_array($found_people_list['list']['uid']) ): 
		foreach($found_people_list['list']['uid'] as $id):
			$replacements = array();
			$user_data = get_userdata($id);
			
			$replacements[0] = ( !empty($user_data->nickname) ? 	$found_people_list['before']['nickname']. $user_data->nickname. 		$found_people_list['after']['nickname']:"");
			$replacements[1] = ( !empty($user_data->user_email) ? 	$found_people_list['before']['email'].	 $user_data->user_email. 	$found_people_list['after']['email']:"");
			$replacements[2] = ( !empty($user_data->description) ? 	$found_people_list['before']['bio'].		 $user_data->description. 	$found_people_list['after']['bio']:"");
			$replacements[3] = ( !empty($user_data->first_name) ? 	$found_people_list['before']['firstname'].$user_data->first_name. 	$found_people_list['after']['firstname']:"");
			$replacements[4] = ( !empty($user_data->last_name) ? 	$found_people_list['before']['lastname']. $user_data->last_name. 	$found_people_list['after']['lastname']:"");
			$replacements[5] = ( !empty($user_data->user_login) ? 	$found_people_list['before']['username']. $user_data->user_login. 	$found_people_list['after']['username']:"");
			$replacements[6] = $found_people_list['before']['thumbnail'].get_avatar($id,$found_people_list['avatar_size']). $found_people_list['after']['thumbnail'];	
			$replacements[7] = ( !empty($user_data->user_url) ? 	$found_people_list['before']['website'].	 $user_data->user_url. 		$found_people_list['after']['website']:"");
			$replacements[8] = ( !empty($user_data->aim) ? 			$found_people_list['before']['aim'].		 $user_data->aim. 			$found_people_list['after']['aim']:"");
			$replacements[9] = ( !empty($user_data->yim) ? 			$found_people_list['before']['yahooim'].  $user_data->yim. 			$found_people_list['after']['yahooim']:"");
			$replacements[10] = ( !empty($user_data->jabber) ? 		$found_people_list['before']['jabbergoogle'].$user_data->jabber. 	$found_people_list['after']['jabbergoogle']:"");									
			$replacements[11] = $found_people_list['before']['id'].		 $id. $found_people_list['after']['id'];
			$replacements[12] = $found_people_list['before']['authorurl'].get_author_posts_url($id).$found_people_list['after']['authorurl'];	
			$counter = 13;
			
			
			if( is_array($people_list_option['settings']) ): 
				foreach($people_list_option['settings'] as $index => $field_slug):
					$replacements[$counter] =  ( !empty( $user_data->$index) ? $found_people_list['before'][$index]. $user_data->$index.$found_people_list['after'][$index]: ""); 
					$counter++; 
				endforeach;	
			endif;
			$replacements = apply_filters('people_list_fields_display',$replacements, $user_data, $found_people_list );
			$html = '<div class="person">';
			$html .= stripslashes($found_people_list['template']);
			$html .= '</div>';
			$html2 .= apply_filters("people_list_shortcode", str_replace($input_template, $replacements, $html));
					
		endforeach;
	endif;
	
	return $html2;
}


if ( function_exists('register_uninstall_hook') )
    register_uninstall_hook(__FILE__, 'people_lists_uninstall_hook');
/**
 * people_lists_uninstall_hook function.
 * Delete options once the plugin is disabled
 * @access public
 * @return void
 */
function people_lists_uninstall_hook()
{
	$option_name = 'people-lists'; 
    delete_option($option_name);
}

/**
 * people_lists_tiny_filter_remove_fullscreen function.
 * 
 * @access public
 * @param mixed $initArray
 * @return void
 */
function people_lists_tiny_filter_remove_fullscreen($initArray){
	$initArray["theme_advanced_buttons1"] = str_replace(',fullscreen', '', $initArray["theme_advanced_buttons1"]);
	return $initArray;
	
}

/* --- End of File --- */