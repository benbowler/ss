<?php

function bp_map_has_markers($map=false,$groups_args=false) {
	global $bp;
	

	
	if (!$map)
		$map=$bp->maps->current_map;
	
	/***
	 * Set the defaults based on the current page. Any of these will be overridden
	 * if arguments are directly passed into the loop. Custom plugins should always
	 * pass their parameters directly to the loop.
	 */
	$user_id = false;
	$secondary_id = false;
	$type = false;
	$search_terms = false;
	$slug = false;
	
	/* User filtering */
	if ( !empty( $bp->displayed_user->id ) )
		$user_id = $bp->displayed_user->id;

	/* Type */
	if ( 'my-markers' == $bp->current_action ) {
		if ( 'most-popular' == $order )
			$type = 'popular';
		else if ( 'alphabetically' == $order )
			$type = 'alphabetical';
	} else if ( $bp->markers->current_marker->slug ) {
		$type = 'single-marker';
		$slug = $bp->markers->current_marker->slug;
	}

	if ( isset( $_REQUEST['marker-filter-box'] ) || isset( $_REQUEST['s'] ) )
		$search_terms = ( isset( $_REQUEST['marker-filter-box'] ) ) ? $_REQUEST['marker-filter-box'] : $_REQUEST['s'];

	$map_args_default = array(
		'single'=>false,
		'page' => 1,
		'per_page' => 20,
		'max' => false,
		'slug' => $slug, // Pass a marker slug to only return that marker
		'search_terms' => $search_terms // Pass search terms to return only matching markers
	);
	
	$r = wp_parse_args( $map_args, $map_args_default );
	extract( $r );

	
	$map->groups_template = new BP_Maps_Groups_Template( $map, $groups_args, (int)$max, (bool)$single, (int)$page, (int)$per_page, $slug, $search_terms);
	$map->markers_total=bp_maps_groups_count_markers($map->groups_template);

	return apply_filters( 'bp_map_has_markers', $map->groups_template->has_groups(), &$map->groups_template );

}



function bp_markers() {
	global $map_group;
	return $map_group->markers_template->markers();
}

function bp_the_marker() {
	global $map_group;
	return $map_group->markers_template->the_marker();
}

function bp_marker_is_visible( $marker = false ) {
	global $bp;
	global $map_marker;
	
	if (!$map)
		$markers_template=$bp->maps->current_map->markers_template;

	if ( $bp->loggedin_user->is_site_admin )
		return true;

	if ( !$marker )
		$marker =$map_marker;

	if ( 'public' == $marker->status ) {
		return true;
	} else {
		if ( markers_is_user_member( $bp->loggedin_user->id, $marker->id ) ) {
			return true;
		}
	}

	return false;
}

function bp_marker_id() {
	echo bp_get_marker_id();
}
	function bp_get_marker_id() {
		global $bp;
		global $map_marker;

			$marker = $map_marker;

		return apply_filters( 'bp_get_marker_id', $marker->id );
	}

function bp_marker_name() {
	echo bp_get_marker_name();
}
	function bp_get_marker_name( $marker = false ) {
		global $bp;
		global $map_marker;
		
		$marker = $map_marker;

		return apply_filters( 'bp_get_marker_name', $marker->name );
	}

///GENERATES JAVASCRIPT IN HEAD
function bp_maps_map_js() {
	echo bp_maps_get_map_js();
}
function bp_maps_get_map_js() {
	global $bp;
	
	$map=$bp->maps->current_map;

	if (!$map) return false;

	$js[]="\n<script type='text/javascript'>";

	
	$js[]="<!--BP-MAPS #".bp_maps_get_map_slug($map)." INIT|START-->";
	
	$js[]="var bp_maps_messages=new Object();";	
	$geocode_error = addslashes(__('Geocode was not successful for the following reason:','bp-maps'));
	$js[]="bp_maps_messages['error_geocode']='".$geocode_error."';";	
	
	$notitle_error = addslashes(__('Please enter a title before saving','bp-maps'));
	$js[]="bp_maps_messages['error_notitle']='".$notitle_error."';";	
	
	$nodesc_error = addslashes(__('Please enter a description before saving','bp-maps'));
	$js[]="bp_maps_messages['error_nodesc']='".$nodesc_error."';";	
	
	$js[]="var ".bp_maps_get_map_slug($map)."_map;";	
	$js[]="var ".bp_maps_get_map_slug($map)."_Options;";
	$js[]="var ".bp_maps_get_map_slug($map)."_GroupOptions=new Array();";
	$js[]="var ".bp_maps_get_map_slug($map)."_is_init;";

	$js[]="var ".bp_maps_get_map_slug($map)."_groups=new Array();";
	
	$js[]="\n\nfunction bp_maps_".bp_maps_get_map_slug($map)."_init(){";
	$js[]="\n\t".bp_maps_get_map_slug($map)."_is_init=true;";
	$js[]="\t".bp_maps_get_map_slug($map)."_Options = {";
	
	$point = array($map->Lat,$map->Lng);
	
	$option_arr[]="\t\tcenter : new google.maps.LatLng(".implode(",",$point).")";
	
	$option_arr[]="\t\tzoom :".$map->zoom;
	$option_arr[]="\t\tmapTypeControlOptions : {style: google.maps.MapTypeControlStyle.".$map->mapTypeControlOptions."}";
	$option_arr[]="\t\tnavigationControlOptions : {style: google.maps.NavigationControlStyle.".$map->navigationControlOptions."}";
	$option_arr[]="\t\tmapTypeId : google.maps.MapTypeId.".$map->mapTypeId;
	
	if ($map->markers_max)
		$option_arr[]="\t\tmarkers_max : ".$map->markers_max;

	if ($map->type)
		$option_arr[]="\t\ttype : '".$map->type."'";
		
	if ($map->secondary_id)
		$option_arr[]="\t\tsecondary_id : ".$map->secondary_id;
		

	$js[]=implode(",\n",$option_arr);
	
	$js[]="\t};\n";
	

	//MAP INIT
	$js[]="\t".bp_maps_get_map_slug($map)."_map = new google.maps.Map(document.getElementById('".bp_maps_get_map_slug($map)."_content'), ".bp_maps_get_map_slug($map)."_Options);\n";
	
	$group_js=bp_maps_get_groups_js();
	
	$js=array_merge($js,$group_js);

	
	$js[]="};";
	
	$js = apply_filters('bp_maps_get_map_js',$js,$map);

	
	$js[]="<!--BP-MAPS #".bp_maps_get_map_slug($map)." INIT|END-->\n\n";
	$js[]="</script>\n";

	return implode("\n",$js);
}

function bp_maps_get_groups_js() {
	global $bp;
	
	$map=$bp->maps->current_map;
	
	$groups = $map->groups_template->groups;

	$js=array();
	
	$groupkey=0;
	foreach ($groups as $groupkey=>$group) {
		$markers_js=bp_maps_get_group_js($map,$groupkey,$group);
		$js=array_merge($js,$markers_js);
		
		$groupkey++;
	}
	return $js;
}

function bp_maps_get_group_js($map,$groupkey,$group) {

	$markers=$group->markers_template->markers;

	$js=array();
	$js[]="\t<!--group #".$groupkey."|START-->";
	$js[]="\t\tvar ".bp_maps_get_map_slug($map)."_group".$groupkey."=new Array();\n";
	
	//GROUP OPTIONS | START
	$js[]="\t\t".bp_maps_get_map_slug($map)."_GroupOptions".$groupkey." = {";
	
	$point = array($group->Lat,$group->Lng);
	
	//if (!empty($point))
		//$option_arr[]="\t\tcenter : new google.maps.LatLng(".implode(",",$point).")";

	if ($group->markers_max)
		$option_arr[]="\t\t\tmarkers_max : ".$group->markers_max;

	if ($group->type)
		$option_arr[]="\t\t\ttype : '".$group->type."'";
		
	if ($group->secondary_id)
		$option_arr[]="\t\t\tsecondary_id : ".$group->secondary_id;

	if (($group->icon) && ($group->icon['img'])) {
		$option_arr[]="\t\t\ticon : '".$group->icon['img']."'";
		if ($group->icon['shadow'])
			$option_arr[]="\t\t\tshadow : '".$group->icon['shadow']."'";
	}

		
	$js[]=implode(",\n",$option_arr);
	
	$js[]="\t\t};";
	
	$js[]="\t\t".bp_maps_get_map_slug($map)."_GroupOptions.push(".bp_maps_get_map_slug($map)."_GroupOptions".$groupkey.");\n";


	//GROUP OPTIONS | END
	
	foreach ($markers as $key=>$marker) {

		$marker_var_name = bp_maps_get_marker_name().'_js';
		
		if ((!$marker->Lat) || (!$marker->Lng)) continue;
		
		//PRIVACY | APPROX POINT
		$js[]="\t\t<!--marker #".$marker->ID."|START-->";
		
		$marker_position = "new google.maps.LatLng(".$marker->Lat.",".$marker->Lng.")";
		
		unset($marker_args_str);
		unset($marker_args_arr);
		unset($marker_args);

		$marker_args=array(
			'position'=>$marker_position,
			'map'=>bp_maps_get_map_slug($map).'_map'
		);
		
		if (bp_map_group_editable($group)) {
			$marker_args['draggable']='true';
		}

		if (($marker->title) && ($group->infobulles))
			$marker_args['title']="'".addslashes($marker->title)."'";
			
		if (($group->icon) && ($group->icon['img'])) {
			$marker_args['icon']=bp_maps_get_map_slug($map)."_GroupOptions[".$groupkey."].icon";
			if ($group->icon['shadow'])
				$marker_args['shadow']=bp_maps_get_map_slug($map)."_GroupOptions[".$groupkey."].shadow";
		}

		$marker_args=apply_filters('bp_maps_single_marker_js_attr',$marker_args,$marker->ID,$map);


		
		foreach ($marker_args as $name=>$value) {
			$marker_args_arr[]=$name." : ".$value;
		}
		
		unset($marker_args_str);

		$marker_args_str=implode(",",$marker_args_arr);
		
		$js[]="\t\t".bp_maps_get_map_slug($map)."_marker_".$marker->ID."= new Object();";
		$js[]="\t\t".bp_maps_get_map_slug($map)."_marker_".$marker->ID."['ID']='".$marker->ID."';";
		
		if ($marker->type)
			$js[]="\t\t".bp_maps_get_map_slug($map)."_marker_".$marker->ID."['type']='".$marker->type."';";
			
		if ($marker->secondary_id)
			$js[]="\t\t".bp_maps_get_map_slug($map)."_marker_".$marker->ID."['secondary_id']=".$marker->secondary_id.";";

		
		
		
		$js[]="\t\t".bp_maps_get_map_slug($map)."_marker_".$marker->ID."['marker']=new google.maps.Marker({".$marker_args_str."});";
		
		if (($marker->address) && (bp_map_group_editable()))
		$js[]="\t\t".bp_maps_get_map_slug($map)."_marker_".$marker->ID."['address']='".addslashes($marker->address)."';";
		
		$js[]="\t\t".bp_maps_get_map_slug($map)."_group".$groupkey.".push(".bp_maps_get_map_slug($map)."_marker_".$marker->ID.");";
		
		if ($group->infobulles){
			$infobulle_content=addslashes(apply_filters('bp_maps_marker_infobulle_content',$marker->content,$marker,$group,$map));
			if ($infobulle_content) {
				$js[]="\t\t".bp_maps_get_map_slug($map)."_marker_".$marker->ID."['desc']='".$infobulle_content."';";
				$js[]="\t\t".bp_maps_get_map_slug($map)."_marker_".$marker->ID."['infoWindow']=new google.maps.InfoWindow({content: ".bp_maps_get_map_slug($map)."_marker_".$marker->ID."['desc']});";
				$js[]="\t\t".bp_maps_get_map_slug($map)."_marker_".$marker->ID."['listener']=google.maps.event.addListener(".bp_maps_get_map_slug($map)."_group".$groupkey."[".$key."]['marker'], 'click', function() {bp_maps_marker_infowindow('".bp_maps_get_map_slug($map)."',".$groupkey.",".$key.");});";
			}
		}
		$js[]="\t\t<!--marker #".$marker->ID."|END-->\n";
	}
	$js[]="\t\t".bp_maps_get_map_slug($map)."_groups.push(".bp_maps_get_map_slug($map)."_group".$groupkey.");";
	$js[]="\t<!--group #".$groupkey."|END-->\n";

	return $js;
}


function bp_maps_marker_id() {
	echo bp_maps_get_marker_id();
}
	function bp_maps_get_marker_id($marker=false) {
		global $bp;
		global $map_marker;
		
		$marker = $map_marker;

		return apply_filters('bp_maps_get_marker_id',$marker->ID);
	
	}

function bp_maps_marker_index() {
	echo bp_maps_get_marker_index();
}
	function bp_maps_get_marker_index() {
		global $map_group;
		global $map_marker_index;
		
		
		return apply_filters('bp_maps_get_marker_index',$map_group->markers_template->current_marker);
	
	}
	
function bp_maps_marker_name() {
	echo bp_maps_get_marker_name();
}

	function bp_maps_get_marker_name($map_slug=false,$marker_id=false) {
		global $bp;
		
		if (!$marker_id)
			$marker_id = bp_maps_get_marker_id();

		
		if (!$map_slug)
			$map_slug = bp_maps_get_map_slug($map);

		return $map_slug.'_marker'.$marker_id;
		
	}
	
function bp_maps_marker_title() {
	echo bp_maps_get_marker_title();
}
function bp_maps_markerslist_marker_title() {
	echo bp_maps_get_marker_title(false,true);
}
	function bp_maps_get_marker_title($marker=false,$markerlist=false) {
		global $map_marker;

		if ( !$marker )
			$marker = $map_marker;

		if (!$markerlist)
			return apply_filters('bp_maps_get_marker_title',$marker->title,$marker,$map_group,$map);
		else
			return apply_filters('bp_maps_get_marker_list_title',$marker->title,$marker,$map_group,$map);
	}
	
function bp_maps_marker_address() {
	echo bp_maps_get_marker_address();
}
function bp_maps_markerslist_marker_address() {
	echo bp_maps_get_marker_address(false,true);
}
	function bp_maps_get_marker_address($marker=false,$markerlist=false) {
		global $map_marker;
		
		$marker=$map_marker;

		if (!$markerlist)
			return apply_filters('bp_maps_get_marker_address',$marker->address,$marker,$map_group,$map);
		else
			return apply_filters('bp_maps_get_marker_list_address',$marker->address,$marker,$map_group,$map);
	}
	
	
function bp_maps_marker_content() {
	echo bp_maps_get_marker_content();
}
function bp_maps_markerslist_marker_content() {
	echo bp_maps_get_marker_content(false,true);
}

	function bp_maps_get_marker_content($marker=false,$markerlist=false) {
		global $map_marker;

		if ( !$marker )
			$marker = $map_marker;

		if (!$markerlist)
			return apply_filters('bp_maps_get_marker_content',$marker->content,$marker,$map_group,$map);
		else
			return apply_filters('bp_maps_get_marker_list_content',$marker->content,$marker,$map_group,$map);
	}

function bp_maps_marker_classes() {
	echo bp_maps_get_marker_classes();

}
	function bp_maps_get_marker_classes() {
		global $bp;
		global $map_marker;
		
		if ( !$marker )
			$marker = $map_marker;
			


		$marker_classes=array();
		$marker_classes[]='bp_maps_marker';
		$marker_classes[]='item';

		if ($marker->open)
			$marker_classes[]='edit';
			
		if ($marker->approx) $marker_classes[]='approx';
			
		$marker_classes = apply_filters('bp_maps_get_marker_classes',$marker_classes);
		return implode(" ",$marker_classes);
		
	}

function bp_maps_marker_is_visible() {
	if (!bp_maps_marker_user_can_view($marker))	return false;
}


function bp_maps_map_slug() {
	echo bp_maps_get_map_slug();
}
	function bp_maps_get_map_slug($map=false) {
		global $bp;
		
		if (!$map)
			$map=$bp->maps->current_map;

		return apply_filters('bp_maps_get_map_slug',$map->slug);
	}

function bp_maps_map_classes() {
	echo bp_maps_get_map_classes();
}

	function bp_maps_get_map_classes($map=false) {
		global $bp;
		
		if (!$map)
			$map=$bp->maps->current_map;
		
		$classes[]='bp_maps_map';	
		
		if (bp_map_group_editable($map)) {
			$classes[]='editable';	
		}

		if (bp_map_group_enable_desc($map)) {
			$classes[]='enable_desc';	
		}
		
		$classes = apply_filters('bp_maps_get_map_classes',$classes);
		
		if ($classes) return implode(' ',$classes);

	}

function bp_maps_markers_pagination_count() {
	global $bp, $map_group;
	
	$markers_template=$map_group->markers_template;

	$from_num = bp_core_number_format( intval( ( $markers_template->pag_page - 1 ) * $markers_template->pag_num ) + 1 );
	$to_num = bp_core_number_format( ( $from_num + ( $markers_template->pag_num - 1 ) > $markers_template->total_marker_count ) ? $markers_template->total_marker_count : $from_num + ( $markers_template->pag_num - 1) );
	$total = bp_core_number_format( $markers_template->total_marker_count );

		echo sprintf( __( 'Viewing marker %s to %s (of %s markers)', 'bp-maps' ), $from_num, $to_num, $total );

	?><span class="ajax-loader"></span><?php
}

function bp_maps_markers_pagination_links() {
	echo bp_maps_get_markers_pagination_links();
}
	function bp_maps_get_markers_pagination_links() {
		global $map_group;
		$markers_template=$map_group->markers_template;

		return apply_filters( 'bp_maps_get_markers_pagination_links', $markers_template->pag_links );
	}
	
function bp_maps_group_classes() {
	echo bp_maps_get_group_classes();
}
	function bp_maps_get_group_classes($map=false) {

		global $bp;

		$classes[]='markers_block';	
		
		if (bp_map_group_editable()) {
			$classes[]='editable';	
		}

		if (bp_map_group_enable_desc()) {
			$classes[]='enable_desc';	
		}
		
		$classes = apply_filters('bp_maps_get_group_classes',$classes);
		
		if ($classes) return implode(' ',$classes);


	}
	
function bp_maps_map_content_classes() {

	echo bp_maps_get_map_content_classes();
	
}
	function bp_maps_get_map_content_classes($map=false) {
		global $bp;
		
		if (!$map)
			$map=$bp->maps->current_map;
	
			$classes[]='bp_maps_map_container';
			$classes[]=$map->display;
			
			$classes = apply_filters('bp_maps_get_map_content_classes',$classes);
			
			if ($classes) return implode(' ',$classes);
			
	}
	
function bp_maps_map_width() {
	echo bp_maps_get_map_width();
}	
	function bp_maps_get_map_width($map=false) {
		global $bp;
		
		if (!$map)
			$map=$bp->maps->current_map;
			
		$width=$map->width;
		
		if (is_numeric($width))
			$width.='px';
			
		return apply_filters('bp_maps_get_map_width',$width);
	}
function bp_maps_map_height() {
	echo bp_maps_get_map_height();
}	
	function bp_maps_get_map_height($map=false) {
		global $bp;
		
		if (!$map)
			$map=$bp->maps->current_map;
			
		$height=$map->height;
		
		if (is_numeric($height))
			$height.='px';
	
		return apply_filters('bp_maps_get_map_height',$height);
	}
function bp_maps_map_static_img() {
	echo bp_maps_get_map_static_img();
}
	function bp_maps_get_map_static_img($map=false){
		global $bp;
		
		if (!$map)
			$map=$bp->maps->current_map;

		if ($map->display!='dynamic') //STATIC IMAGE
			return '<img src="'.$map->get_static_image().'">';
	}
	
function bp_map_group_markers_list() {
	global $map_group;
	return $map_group->markers_list;
}

function bp_maps_map_privacy_levels($map=false) {
	global $bp;
	
	if (!$map)
		$map=$bp->maps->current_map;
			
	return $map->privacy_levels;
}


function bp_map_the_group_icon() {
	echo bp_map_get_the_group_icon();
}
	function bp_map_get_the_group_icon() {
		global $map_group;
		
		if ($map_group->icon['img'])
			$icon_img =$map_group->icon['img'];
		else
			$icon_img='http://maps.gstatic.com/intl/fr_ALL/mapfiles/marker.png';
			
		$icon='<img src="'.$icon_img.'">';

		return apply_filters('bp_map_get_the_group_icon',$icon,$map_group);
	}

function bp_map_the_group_index() {
	echo bp_map_get_the_group_index();
}
	function bp_map_get_the_group_index() {

		global $bp;
		$map=$bp->maps->current_map;
		$groups_template=$map->groups_template;
		$group_index=$map->groups_template->current_group;

		return apply_filters('bp_map_get_the_group_index',$group_index);
	}

function bp_map_the_group_slug() {
	echo bp_map_get_the_group_slug();
}


function bp_map_get_the_group_slug() {

	global $bp;
	$map=$bp->maps->current_map;
	$groups_template=$map->groups_template;
	$group_index=bp_map_get_the_group_index();

	$map_slug = bp_maps_get_map_slug();
	$group_slug=$map_slug.'_'.$group_index;

	return apply_filters('bp_map_get_the_group_slug',$group_slug);
}

function bp_map_the_group_checkbox() {
	if (bp_map_is_multiple_groups()) return false;
	
	/*
	global $bp;
	$map=$bp->maps->current_map;
	$groups_template=$map->groups_template;
	
	foreach($groups_template->groups as $group) {
		if ($group->markers_list) {
			$groups_with_lists_enabled++;
		}
	}
	
	if ($groups_with_lists_enabled<=1) return false;
	*/
	echo bp_map_get_the_group_checkbox();
	return true;
}

	function bp_map_get_the_group_checkbox() {
		return '<input type="checkbox" class="bp_maps_toggle_group" rel="'.bp_map_get_the_group_index().'" CHECKED>';

	}

function bp_map_is_multiple_groups() {
	global $bp;
	$map=$bp->maps->current_map;
	$groups_template=$map->groups_template;
	
	return $groups_template->single_group;	
}

function bp_map_marker_user_can_admin() {
	global $map_marker;
	global $bp;
	
	$marker=$map_marker;
	$user_id=$marker->user_id;
	
	if ($user_id==$bp->loggedin_user->id) return true;
	if ( $bp->loggedin_user->is_site_admin ) return true;
	
	return false;
	
}

function bp_map_marker_editable() {
	global $map_marker;
	
	if ((bp_map_group_editable()) || bp_map_marker_user_can_admin()) return true;
	
	return false;

}

function bp_map_group_editable($group=false) {
	global $map_group;
	
	if (!$group)
		$group=$map_group;

	return $group->editable;
}

function bp_maps_group_name() {
	echo bp_maps_get_group_name();
}
	function bp_maps_get_group_name() {
		global $map_group;
		return apply_filters('bp_maps_get_group_name',$map_group->name,$map_group);
	}

function bp_map_group_enable_desc() {
	global $map_group;
	return $map_group->enable_desc;
}

function bp_maps_map_max_markers() {
	echo bp_map_get_group_max_markers();
}
	function bp_map_get_group_max_markers() {
		global $map_group;
		return $map_group->markers_max;
	}
	
function bp_maps_get_map_markers_count() {
	global $bp;
	return true;
}

function bp_maps_group_saved_markers_count() {
	echo bp_get_maps_group_saved_markers_count();
}
	function bp_get_maps_group_saved_markers_count() {
		global $map_group;
		$group=$map_group;
		
		foreach ($group->markers_template->markers as $marker) {
			if ($marker->ID)
				$count++;
		}
		return (int)$count;
		
	}

function bp_maps_group_markers_count() {
	echo bp_maps_get_group_markers_count();
}

function bp_maps_get_group_markers_count($group=false) {
	global $map_group;
	if (!$group)
		$group=$map_group;

	return (int)$group->markers_template->marker_count;
}

function bp_maps_group_can_add_marker() {
	global $map_group;

	if (!bp_map_get_group_max_markers()) return true;
	
	if (bp_maps_get_group_markers_count()<bp_map_get_group_max_markers()) return true;
	
	return false;
}

function bp_maps_groups_count_markers($groups_template) {

	if (!$groups_template->group_count) return false;

	foreach ($groups_template->groups as $group) {
		$count+=bp_maps_get_group_markers_count($group);
	
	}


	return $count;

}

function bp_maps_map_can_add_markers($map=false) {
	global $bp;
	
	if (!$map)
		$map=$bp->maps->current_map;

	if (!bp_map_group_editable($map)) return false;
}

function bp_maps_map_privacy($map=false) {
	global $bp;
	
	if (!$map)
		$map=$bp->maps->current_map;

	return $map->privacy;
}

function bp_map_title() {
	echo bp_maps_get_map_title();
}
function bp_maps_get_map_title($map=false) {
	global $bp;
	
	if (!$map)
		$map=$bp->maps->current_map;

	return apply_filters('bp_maps_get_map_title',$map->title);
}

function bp_maps_get_all_groups_markers($map=false) {
	global $bp;
	
	if (!$map)
		$map=$bp->maps->current_map;
		
	if (!$map->groups_template->groups) return false;
	
	$total_markers=array();
		
	foreach ($map->groups_template->groups as $group) {
		$markers = $group->markers_template->markers;
		if (empty($markers)) continue;
		$total_markers=array_merge($total_markers,$markers);
	}
	return $total_markers;

}

function bp_maps_map_last_active() {
	echo bp_maps_get_map_last_active();
}

	function bp_maps_get_map_last_active($map=false) {
		global $bp;
		
		if (!$map)
			$map=$bp->maps->current_map;

		$group_markers=bp_maps_get_all_groups_markers($map);
		
		if (empty($group_markers)) return false;
		
		$map_updated;
		
		foreach ($group_markers as $marker) {

			$date_updated = $marker->date_updated;
			if ($date_updated>$map_updated)
				$map_updated=$date_updated;
		}
		
		$last_activity = bp_core_get_last_activity( $map_updated, __( 'updated %s ago', 'bp-maps' ) );

		return apply_filters('bp_maps_get_map_last_active',$last_activity);
	}

function bp_maps_marker_author(){
	echo bp_maps_get_marker_author();
}
	function bp_maps_get_marker_author($marker=false){
		global $map_marker;
		if (!$marker)
			$marker=$map_marker;
			
		$user_id=$marker->user_id;
		
		$name = bp_core_get_userlink( $user_id );
		$link = bp_core_get_user_domain( $user_id );
		$thumb = bp_core_fetch_avatar( array( 'item_id' => $user_id, 'type' => 'thumb', 'width' => 20, 'height' => 20 ));
		
		$content='<a href="'.$link.'">'.$thumb.$name.'</a>';

		
		return apply_filters('bp_maps_get_marker_author',$content);
	}

function bp_maps_marker_last_active() {
	echo bp_maps_get_marker_last_active();
}

	function bp_maps_get_marker_last_active($marker=false) {
		global $map_marker;
		if (!$marker)
			$marker=$map_marker;
			
			$last_activity = bp_core_get_last_activity( $map_marker->date_updated, __( 'updated %s ago', 'bp-maps' ) );
			
			return apply_filters('bp_maps_get_marker_last_active',$last_activity);
	}

function bp_maps_map_is_visible( $map=false) {
	global $bp;
	
	if (!$map)
		$map=$bp->maps->current_map;

	if (bp_maps_get_map_markers_count()) return true;
	
	if (bp_map_group_editable($map)) return true;

	return false;
}


//////////////TEMPLATES////////////////////


function bp_maps_enqueue_url($file){
	// split template name at the slashes
	
	$stylesheet_path = get_stylesheet_directory_uri();
	$suffix = explode($stylesheet_path,$file);	
	
	$suffix_str=$suffix[1];
	
	$file_path_to_check = BP_MAPS_PLUGIN_DIR . '/theme'.$suffix_str;
	$file_url_to_return = BP_MAPS_PLUGIN_URL . '/theme'.$suffix_str;

	if ( file_exists($file)) {
		return $file;
	}elseif ( file_exists($file_path_to_check)) {
		return $file_url_to_return;
	}
}
add_filter( 'bp_maps_enqueue_url', 'bp_maps_enqueue_url' );

/**
 * Check if template exists in style path, then check custom plugin location (code snippet from MrMaz)
 *
 * @param array $template_names
 * @param boolean $load Auto load template if set to true
 * @return string
 */
function bp_maps_locate_template( $template_names, $load = false ) {

	if ( !is_array( $template_names ) )
		return '';

	$located = '';
	foreach($template_names as $template_name) {

		// split template name at the slashes
		$paths = explode( '/', $template_name );
		
		// only filter templates names that match our unique starting path
		if ( !empty( $paths[0] ) && 'maps' == $paths[0] ) {


			$style_path = STYLESHEETPATH . '/' . $template_name;
			$plugin_path = BP_MAPS_PLUGIN_DIR . "/theme/{$template_name}";

			if ( file_exists( $style_path )) {
				$located = $style_path;
				break;
			} else if ( file_exists( $plugin_path ) ) {
				$located = $plugin_path;
				break;
			}
		}
	}

	if ($load && '' != $located)
		load_template($located);

	return $located;
}

/**
 * Filter located BP template (code snippet from MrMaz)
 *
 * @see bp_core_load_template()
 * @param string $located_template
 * @param array $template_names
 * @return string
 */
function bp_maps_filter_template( $located_template, $template_names ) {

	// template already located, skip
	if ( !empty( $located_template ) )
		return $located_template;

	// only filter for our component
	if ( $bp->current_component == $bp->maps->slug ) {
		return bp_maps_locate_template( $template_names );
	}

	return '';
}
add_filter( 'bp_located_template', 'bp_maps_filter_template', 10, 2 );

/**
 * Use this only inside of screen functions, etc (code snippet from MrMaz)
 *
 * @param string $template
 */
function bp_maps_load_template( $template ) {
	bp_core_load_template( $template );
}




?>