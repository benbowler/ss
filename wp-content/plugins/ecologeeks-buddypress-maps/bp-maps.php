<?php

//FIX markers pagination

if ( file_exists( BP_MAPS_PLUGIN_DIR . '/languages/' . get_locale() . '.mo' ) )
	load_textdomain( 'bp-maps', BP_MAPS_PLUGIN_DIR . '/languages/' . get_locale() . '.mo' );
	
if ( file_exists( BP_MAPS_PLUGIN_DIR . '/languages/' . get_locale() . '-slugs.mo' ) )
	load_textdomain( 'bp-maps-slugs', BP_MAPS_PLUGIN_DIR . '/languages/' . get_locale() . '-slugs.mo' );

require ( BP_MAPS_PLUGIN_DIR . '/bp-maps-classes.php' );
require ( BP_MAPS_PLUGIN_DIR . '/bp-maps-templatetags.php' );
require ( BP_MAPS_PLUGIN_DIR . '/bp-maps-ajax.php' );
require ( BP_MAPS_PLUGIN_DIR . '/bp-maps-filters.php' );
require ( BP_MAPS_PLUGIN_DIR . '/bp-maps-admin.php' );
//PLUGZ
require_once ( BP_MAPS_PLUGIN_DIR . '/bp-maps-profile.php' );
require_once ( BP_MAPS_PLUGIN_DIR . '/bp-maps-groups-maps.php' );
require_once ( BP_MAPS_PLUGIN_DIR . '/bp-maps-members-map.php' );
require_once ( BP_MAPS_PLUGIN_DIR . '/bp-maps-friends-map.php' );
require_once ( BP_MAPS_PLUGIN_DIR . '/bp-maps-custom-markers.php' );



function bp_maps_install() {
echo("bp_maps_install()<br>");
	global $wpdb, $bp;

	if ( !empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		
	$sql[] = "CREATE TABLE IF NOT EXISTS {$bp->maps->table_name_markers} (
		`id` bigint(20) unsigned NOT NULL auto_increment,
		`user_id` bigint(20) unsigned NOT NULL default '0',
		`secondary_id` bigint(20) unsigned NOT NULL default '0',
		`type` varchar(20) NOT NULL,
		`lat` varchar(64) NOT NULL,
		`lng` varchar(64) NOT NULL,
		`title` text NOT NULL,
		`address` text NOT NULL,
		`content` text NOT NULL,
		`privacy` text NOT NULL,
		`date_created` datetime NOT NULL,
		`date_updated` datetime NOT NULL,
		PRIMARY KEY  (`id`)
	 	   ) {$charset_collate};";

		   
	if (BP_GROUPS_DB_VERSION<1100) {
		$sql[] = "ALTER TABLE `{$bp->maps->table_name_markers} ADD `secondary_id` BIGINT( 20 ) NOT NULL AFTER `user_id`";
	}

	require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
	dbDelta($sql);
	
	do_action( 'bp_maps_install' );

	update_site_option( 'bp-maps-db-version', BP_GROUPS_DB_VERSION );
}

function maps_check_installed() {	
	global $wpdb, $bp;
echo("maps_check_installed()<br>");
	//Need to check db tables exist, activate hook no-worky in mu-plugins folder.
	if ( get_site_option('bp-maps-db-version') < BP_MAPS_DB_VERSION )
	{
		echo("version de db en place < version du plugin, lancement de l'install<br>");
		bp_maps_install();
	}
	else
	{
		echo("version de db en place < version du plugin, rien besoin de faire<br>");
	}
}
add_action( 'admin_menu', 'maps_check_installed' );

function bp_maps_is_setup() {
echo("bp_maps_is_setup()<br>");
	//
	return true;

	$maps_options = get_site_option( 'bp_maps_options');
	
	if ($maps_options['blog_id']) {
		return true;
	}
}

function bp_maps_default_options() {
	$options['map']['options'] =(array(
			'center'=>'21.3069444,-157.8583333',
			'display'=>'dynamic',
			'zoom'=>8,
			'size'=>'MEDIUM',
			'mapTypeControlOptions'=>'DROPDOWN_MENU',
			'navigationControlOptions'=>'SMALL','ANDROID',
			'mapTypeId'=>'ROADMAP'
		));
	
	$options['map']['sizes'] = array (
							'SMALL' => array('width' => '300', 'height' => '225'),
							'MEDIUM' => array('width' => '400', 'height' => '300'),
							'LARGE' => array('width' => '640', 'height' => '480') 
							);
							
	//PRIVACY
	//action=>privacy level
	//by order of importance
	$options['marker']['privacy']['marker_view'] = false;
	$options['marker']['privacy']['marker_view_approx'] = 0;
	$options['marker']['privacy']['marker_view_full'] = 1;
	
	$options['plugins'] = array(
		'profiles_maps'=>array('enabled'=>false),
		'members_map'=>array('enabled'=>true),
		'friends_map'=>array('enabled'=>true),
		'custom_markers'=>array('enabled'=>true),
		'groups_maps_custom'=>array('enabled'=>false),
		'groups_maps_members'=>array('enabled'=>true)
		
	);

	return apply_filters('bp_maps_default_options',$options);
}


function bp_maps_setup_globals() {
	global $bp, $wpdb;

	/* For internal identification */
	$bp->maps->id = 'maps';
	
	/* Register this in the active components array */
	//$bp->maps->format_notification_function = 'maps_format_notifications';
	$bp->maps->slug = BP_MAPS_SLUG;

	/* Register this in the active components array */
	$bp->active_components[$bp->maps->slug] = $bp->maps->id;


	$custom_options = get_site_option( 'bp_maps_options');
	$options_defaults = bp_maps_default_options();

	$bp->maps->options = wp_parse_args($custom_options, $options_defaults );
	
	$bp->maps->format_notification_function = 'bp_maps_format_notifications';
	
	$bp->maps->table_name_markers = $wpdb->base_prefix . 'bp_maps_markers';
	

	
	//level=>name of the filtering check function
	//by order of importance !
	$bp->maps->privacy['levels'] = array (
		'visitor'=>'bp_maps_check_is_visitor',
		'member'=>'bp_maps_check_is_member',
		'friend'=>'bp_maps_check_is_friend'
	);	

	//CAPABILITIES
	$capabilities	= array(
		'maps_view_markers' => array(	
			'name'	=> __('View Markers','bp-maps'),
			'group'	=> 'markers',
			'default'	=>true,
		),
		'maps_view_markers_approximate' => array(	
			'name'	=> __('View Approximate locations only','bp-maps'),
			'group'	=> 'markers'
		),
		'maps_create_markers' => array(	
			'name'	=> __('Create Markers','bp-maps'),
			'group'	=> 'my-markers',
			'default'	=>true,
		),
		'maps_marker_creation_visitors_hide' => array(	'name'	=> __('Visitors','bp-maps'),
			'group'	=> 'my-markers-hide',
			'default'	=>true,
		),
		'maps_marker_creation_members_hide' => array(	'name'	=> __('Members','bp-maps'),
			'group'	=> 'my-markers-hide',
			'default'	=>true,
		),
		'maps_marker_creation_friends_hide' => array(	'name'	=> __('Friends','bp-maps'),
			'group'	=> 'my-markers-hide',
			'default'	=>true,
		),
		'maps_marker_creation_visitors_show_approx' => array(	'name'	=> __('Visitors','bp-maps'),
			'group'	=> 'my-markers-show-approx',
			'default'	=>true,
		),
		'maps_marker_creation_members_show_approx' => array(	'name'	=> __('Members','bp-maps'),
			'group'	=> 'my-markers-show-approx',
			'default'	=>true,
		),
		'maps_marker_creation_friends_show_approx' => array(	'name'	=> __('Friends','bp-maps'),
			'group'	=> 'my-markers-show-approx',
			'default'	=>true,
		),

	);
	$bp->maps->capabilities = apply_filters( 'maps_capabilities',$capabilities);
	
	//activity
	apply_filters( 'bp_activity_mini_activity_types', array(
		'marker_created') );

	do_action('bp_maps_init');
	
}
add_action( 'plugins_loaded', 'bp_maps_setup_globals', 5 );
add_action( 'admin_menu', 'bp_maps_setup_globals', 2 );

function bp_maps_setup_root_component() {
	/* Register 'maps' as a root component */

	bp_core_add_root_component( BP_MAPS_SLUG );

}
add_action( 'plugins_loaded', 'bp_maps_setup_root_component', 2 );


// Adds admin menu to WP Dashboard > BuddyPress	
function bp_maps_add_admin_menu() {
echo("bp_maps_add_admin_menu()<br>");
	global $wpdb, $bp;
	

	if ( !is_site_admin() )
	{
		echo("bp_maps_add_admin_menu() : utilisateur pas admin du site<br>");
		return false;
	}
	else
	{
		echo("bp_maps_add_admin_menu() : utilisateur est admin du site<br>");
	}

	/* Add the administration tab under the "Site Admin" tab for site administrators */
	// Buzug : suppression
	// add_submenu_page( 'bp-general-settings', __('Maps','bp-maps'),__('Maps','bp-maps'), 'manage_options', 'bp-maps-setup', "bp_maps_admin" );
	add_options_page( __('Maps','bp-maps'),__('Maps','bp-maps'), 'manage_options', 'bp-maps-setup', "bp_maps_admin" );
}
add_action('admin_menu', 'bp_maps_add_admin_menu');


///HEAD|START///
	/**
	* Scripts for non-admin screens
	*
	*/
	
	function bp_maps_print_head() {
		$js[]="\n<script type='text/javascript'>";
		$js[]="<!--BP-MAPS|START-->";
		$js[]="var geocoder;";
		$js[]="<!--BP-MAPS|END-->";
		$js[]="</script>\n";
		echo implode("\n",$js);
	}
	
	function bp_maps_print_scripts() {
		wp_enqueue_script('bp-maps-unobstrusive',BP_MAPS_PLUGIN_URL . '/_inc/js/unobstrusive-gmaps.js', array('jquery'),BP_MAPS_VERSION);
		wp_enqueue_script( 'json2' );
		wp_enqueue_script('bp-maps',BP_MAPS_PLUGIN_URL . '/_inc/js/bp-maps.js', array('jquery','json2'),BP_MAPS_VERSION);
		wp_enqueue_script('jquery-ui-tabs');
	}

	/**
	* Stylesheets for non-admin pages
	*
	*/
	function bp_maps_print_styles() {
		wp_enqueue_style( 'bp-maps-screen', apply_filters('bp_maps_enqueue_url',get_stylesheet_directory_uri() . '/maps/style.css'));
		wp_enqueue_style( 'bp-maps-tabs', BP_MAPS_PLUGIN_URL . '/_inc/css/jquery.ui.tabs.css' );
	}
///HEAD|END///


function array_flatten($array, $preserve_keys = false)
{
  if (!$preserve_keys) {
    // ensure keys are numeric values to avoid overwritting when array_merge gets called
    $array = array_values($array);
  }

  $flattened_array = array();
  foreach ($array as $k => $v) {
    if (is_array($v)) {
      $flattened_array = array_merge($flattened_array, call_user_func(__FUNCTION__, $v, $preserve_keys));
    } elseif ($preserve_keys) {
      $flattened_array[$k] = $v;
    } else {
      $flattened_array[] = $v;
    }
  }
  return $flattened_array;
}

//CAPABILITIES

function bp_maps_get_roles_names() {
	$roles_names['default']=__('(no role)', 'bp-maps');
	$roles_names=apply_filters('bp_maps_roles_names',$roles_names);
	return $roles_names;
}


function bp_maps_get_user_role($user_id=false) {
	global $bp;
	
	if (!$user_id) {
		global $bp;
		$user_id = $bp->loggedin_user->id;
	}

	//if (!$user_id) return 'visitor';
	
	$user = new WP_User( $user_id );

	if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
		foreach ( $user->roles as $role )
			return $role;
	}

}

function bp_map_user_can_default($action) {
	global $bp;
	$capabilities = $bp->maps->capabilities;
	
	if (
	($capabilities[$action]['group']=='maps') || //visitors
	(($capabilities[$action]['group']=='my-maps') && ($bp->loggedin_user->domain)) //logged user
	)
		return true; 
}

function bp_map_user_can($action) {
	return true;
	//always OK when is site admin
	//if (current_user_can('level_10')) return true;

	$role = bp_maps_get_roles_names();
	
	$default_capabilities = get_site_option('maps_r&c_default');
	
	if (!$default_capabilities) return bp_map_user_can_default($action);

	if ((!$role) || (!bp_maps_plugin_is_active('buddypress-maps/bp-maps-roles-and-capabilities.php'))) {
		return $default_capabilities[$action];
	}else {
		return current_user_can($action);
	}
}

//PRIVACY CHECK FUNCTIONS
function bp_maps_check_is_friend($marker_author_id=false) {
	//TO FIX
	return true;
	global $bp;
	if (BP_Friends_Friendship::check_is_friend( $bp->loggedin_user->id, $marker_author_id )=='is_friend')
		return true;
		
	return false;
}
function bp_maps_check_is_member() {
	if (is_user_logged_in())
		return true;
		
	return false;
}
function bp_maps_check_is_visitor() {
	if (!is_user_logged_in())
		return true;
		
	return false;
}

//PRIVACY FUNCTIONS
function bp_maps_get_user_privacy_level($user_id=false) {
	global $bp;
	
	if (!$user_id) $user_id = $bp->loggedin_user->id;
	
	if (!$user_id) return false;
	
	$index=-1;
	foreach ($bp->maps->privacy['levels'] as $name => $fn) {
		if ($fn()) $index++;
	}
	return $index;
}

function bp_maps_marker_user_can_view ($marker) {

	//if (current_user_can('level_10')) return true;

	//if ($marker->user_id=$bp->loggedin_user->id) return true;
	
	$needed_level = $marker->privacy['marker_view'];
	
	if (!$needed_level) return true;
	
	if ($user_level >= bp_maps_get_user_privacy_level()) return true;
	
	return false;
	
}

function bp_maps_marker_user_can_view_approx ($marker) {

	//if (current_user_can('level_10')) return true;

	//if ($marker->user_id=$bp->loggedin_user->id) return true;
	
	$needed_level = $marker->privacy['marker_view_approx'];
	
	if (!$needed_level) return true;
	
	if ($user_level >= bp_maps_get_user_privacy_level()) return true;
	
	return false;
	
}



///GEOIP FUNCTIONS | START///
function bp_maps_geoip() {
	global $bp;
	$geoIPfile=$bp->maps->options['geoIPfile'];
	if (!$geoIPfile) return false;
	if (!file_exists($geoIPfile)) return false;
	
	include_once(BP_MAPS_PLUGIN_DIR . "/_inc/php/geoIP/ip.php");
	include_once(BP_MAPS_PLUGIN_DIR . "/_inc/php/geoIP/geoipcity.inc");
	include_once(BP_MAPS_PLUGIN_DIR . "/_inc/php/geoIP/geoipregionvars.php");

	// uncomment for Shared Memory support
	// geoip_load_shared_mem("/usr/local/share/GeoIP/GeoIPCity.dat");
	// $gi = geoip_open("/usr/local/share/GeoIP/GeoIPCity.dat",GEOIP_SHARED_MEMORY);
	
	$user_ip=bp_maps_get_ip();
	$user_ip="213.49.238.136";

	$gi = geoip_open($geoIPfile, GEOIP_STANDARD);

	return geoip_record_by_addr($gi,$user_ip);
}

///GEOIP FUNCTIONS | END///

function bp_maps_head_init() {
	//WILL NOT RUN MORE THAN ONCE.
	add_action('wp_head','bp_maps_print_head');
	add_action('wp_print_scripts','bp_maps_print_scripts');
	add_action('wp_print_styles','bp_maps_print_styles');
}


/*** Marker Fetching, Filtering & Searching  *************************************/

function bp_maps_get_groups($groups_args_arr ) {
	global $bp;
	
	$defaults = array(
		'user_id' => false, // The ID of the marker creator
		'secondary_id'	=>false, //A second ID (ex. get the markers for the group#4
		'type' => false, // member_profile, ...
		'editable'	=>false,
		'enable_desc'=>true,
		'icon'=>array( //check http://groups.google.com/group/google-chart-api/web/chart-types-for-map-pins
			'img'=>false, 
			'shadow'=>false
		),
		'markers_list'=>true,
		'infobulles'=>true,
		'include_ids'	=> false, //array of markers IDs you want to include in the selection if wanted
		'page' => 1, // The page to return if limiting per page
		'per_page' => 20, // The number of results to return per page
		'markers_max'=>1,
		'search_terms' => false // Limit to markers that match these search terms
	);
		foreach ($groups_args_arr as $group_args) {
		
			unset ($args);
			$args = wp_parse_args( $group_args, $defaults);
			$group=new BP_Maps_Map_Group($args['name'],$args['user_id'], $args['secondary_id'], $args['type'], $args['editable'], $args['enable_desc'], $args['icon'], $args['markers_list'], $args['infobulles'], $args['include_ids'], $args['page'], $args['per_page'], $args['markers_max'], $args['search_terms']);
			
			$markers_count = bp_maps_get_group_markers_count($group);
			
			if (($markers_count) || bp_map_group_editable($group))
				$populated_groups[]=$group;
			
		}
		
		$total=count($populated_groups);


	return array('groups'=>$populated_groups,'total'=>$total);
}

function markers_get_markers($args = '' ) {
	global $bp;

	$defaults = array(
		'type' => false, // member_profile, ...
		'user_id' => false, // The ID of the marker creator
		'secondary_id'	=>false, //A second ID (ex. get the markers for the classified#4
		'search_terms' => false, // Limit to markers that match these search terms
		'include_ids'	=> false, //array of markers IDs you want to include in the selection if wanted
		'per_page' => 20, // The number of results to return per page
		'page' => 1, // The page to return if limiting per page
		
		'populate_extras' => true, // Fetch meta such as is_banned and is_member
	);

	$params = wp_parse_args( $args, $defaults );
	extract( $params, EXTR_SKIP );

	$markers = BP_Maps_Map_Marker::get_markers($per_page,$page,$user_id,$secondary_id,$type,$search_terms,$include_ids);



	return apply_filters( 'markers_get_markers', $markers, &$params );
}

function markers_get_total_marker_count() {
	if ( !$count = wp_cache_get( 'bp_total_marker_count', 'bp' ) ) {
		$count = BP_Maps_Map_Marker::get_total_marker_count();
		wp_cache_set( 'bp_total_marker_count', $count, 'bp' );
	}

	return $count;
}

function markers_get_user_markers( $user_id = false, $pag_num = false, $pag_page = false ) {
	global $bp;

	if ( !$user_id )
		$user_id = $bp->displayed_user->id;

	return BP_Markers_Member::get_marker_ids( $user_id, $pag_num, $pag_page );
}

function markers_total_markers_for_user( $user_id = false ) {
	global $bp;

	if ( !$user_id )
		$user_id = ( $bp->displayed_user->id ) ? $bp->displayed_user->id : $bp->loggedin_user->id;

	if ( !$count = wp_cache_get( 'bp_total_markers_for_user_' . $user_id, 'bp' ) ) {
		$count = BP_Markers_Member::total_marker_count( $user_id );
		wp_cache_set( 'bp_total_markers_for_user_' . $user_id, $count, 'bp' );
	}

	return $count;
}

function bp_maps_format_notifications( $action, $item_id, $secondary_item_id, $total_items ) {
	global $bp;

	switch ( $action ) {

	}

	return do_action( 'bp_maps_format_notifications', $action, $item_id, $secondary_item_id, $total_items );


}


function bp_maps_map_get_ajax_marker_html($map_slug,$marker_index,$enable_desc){
	global $bp;
	global $map_marker;
	global $map_group;

	
	
	//we fake the object vars so we can get the right marker template
	$map=$bp->maps->current_map->slug=$map_slug;
	$map_group->markers_template->current_marker=$marker_index;
	$map_group->enable_desc=$enable_desc;
	$map_group->editable=true;
	//$map_marker->open=true;
	
	$html=bp_maps_map_get_marker_html();
	return $html;
	
}
	

function bp_maps_map_get_marker_html() {

	$html=get_include_contents(BP_MAPS_PLUGIN_DIR . "/theme/maps/marker.php");
	
	return $html;
}

/*** Markers Cleanup Functions ****************************************************/

function bp_maps_remove_data_for_user( $user_id ) {
	BP_Maps_Map_Marker::delete_all_for_user($user_id);

	do_action( 'bp_maps_remove_data_for_user', $user_id );
}
add_action( 'wpmu_delete_user', 'bp_maps_remove_data_for_user', 1 );
add_action( 'delete_user', 'bp_maps_remove_data_for_user', 1 );
add_action( 'make_spam_user', 'bp_maps_remove_data_for_user', 1 );


/********************************************************************************/



?>
