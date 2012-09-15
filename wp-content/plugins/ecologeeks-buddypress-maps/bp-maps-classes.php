<?php


/*****************************************************************************
 * Groups Template Class/Tags
 **/
 
class BP_Maps_Map_Group {
	var $name;

	var $editable;
	var $enable_desc;
	
	var $pag_page;
	var $pag_num;
	var $pag_links;
	var $total_marker_count;

	
	var $type;
	var $secondary_id;

	var $icon;
	var $markers_list;
	var $infobulles;
	var $markers_max;
	
	var $markers_template;

	function bp_maps_map_group($name,$user_id, $secondary_id, $type, $editable, $enable_desc, $icon, $markers_list, $infobulles, $include_ids, $page, $per_page, $markers_max, $search_terms) {
		global $bp;


		if (!$secondary_id)
			unset($secondary_id);
			
		$this->pag_page = isset( $_REQUEST['mkpage'] ) ? intval( $_REQUEST['mkpage'] ) : $page;
		$this->pag_num = isset( $_REQUEST['num'] ) ? intval( $_REQUEST['num'] ) : $per_page;
		
		if ($icon)
			$this->icon=$icon;

		
		$this->markers_list=$markers_list;

		$this->markers_max=$markers_max;
		
		$this->editable=$editable;
		$this->enable_desc=$enable_desc;
		
		if ($this->editable)
			$this->markers_list=true;
			
			$this->infobulles=$infobulles;
		
		$args = array(
			'user_id'=> $user_id,
			'secondary_id'=>$secondary_id,
			'type' => $type,
			'include_ids'	=> $include_ids,
			'per_page' => $this->pag_num,
			'page' =>$this->pag_page,
			'search_terms' => $search_terms
		);
		
		$this->name=$name;
		$this->type=$args['type'];
		$this->secondary_id=$args['secondary_id'];

		$this->markers_template = new BP_Maps_Markers_Template($args['user_id'], $args['type'], $args['page'], $args['per_page'], $args['max'], $args['search_terms'], $args['include_ids']);
	}

}

class BP_Maps_Groups_Template {
	var $current_group = -1;
	var $group_count;
	var $groups;
	var $group;

	var $in_the_loop;

	var $total_group_count;

	var $single_group = false;


	function bp_maps_groups_template($map, $groups_args, $max, $single, $page, $per_page, $slug, $search_terms) {
		global $bp;


		$this->groups = bp_maps_get_groups( $groups_args );

		if ($this->groups['total']==1) {
			$this->single_group = true;
			$this->total_group_count = 1;
			$this->group_count = 1;
			$this->groups = $this->groups['groups'];
		} else {
			if ( !$max || $max >= (int)$this->groups['total'] )
				$this->total_group_count = (int)$this->groups['total'];
			else
				$this->total_group_count = (int)$max;

			$this->groups = $this->groups['groups'];

			if ( $max ) {
				if ( $max >= count($this->groups) )
					$this->group_count = count($this->groups);
				else
					$this->group_count = (int)$max;
			} else {
				$this->group_count = count($this->groups);
			}
		}

	}

	function has_groups() {
		if ( $this->group_count )
			return true;

		return false;
	}

	function next_group() {
		$this->current_group++;
		$this->group = $this->groups[$this->current_group];

		return $this->group;
	}

	function rewind_groups() {
	
		$this->current_group = -1;
		if ( $this->group_count > 0 ) {
			$this->group = $this->groups[0];
		}

	}

	function groups() {

		if ( $this->current_group + 1 < $this->group_count ) {
			return true;
		} elseif ( $this->current_group + 1 == $this->group_count ) {
			do_action('loop_end');
			// Do some cleaning up after the loop
			$this->rewind_groups();
		}

		$this->in_the_loop = false;
		return false;
	}

	function the_group() {
		global $map_group;

		$this->in_the_loop = true;
		$map_group = $this->next_group();

		if ( 0 == $this->current_group ) // loop has just started
			do_action('bp_maps_groups_template_loop_start');
	}
}

function bp_map_groups() {
	global $bp;
	
	$map=$bp->maps->current_map;
	$groups_template=$map->groups_template;

	return $groups_template->groups();
}

function bp_map_the_group() {
	global $bp;
	
	$map=$bp->maps->current_map;
	$groups_template=$map->groups_template;
		
	return $groups_template->the_group();
}

class BP_Maps_Markers_Template {
	var $current_marker = -1;
	var $marker_count;
	var $markers;
	var $marker;

	var $in_the_loop;

	var $pag_page;
	var $pag_num;
	var $pag_links;
	var $total_marker_count;

	var $single_marker = false;

	var $sort_by;
	var $order;

	function bp_maps_markers_template($user_id, $type, $page, $per_page, $max, $search_terms, $include_ids) {
		global $bp;


		$this->pag_page = isset( $_REQUEST['mkpage'] ) ? intval( $_REQUEST['mkpage'] ) : $page;
		$this->pag_num = isset( $_REQUEST['num'] ) ? intval( $_REQUEST['num'] ) : $per_page;
		
		if (!$secondary_id)
			unset($secondary_id);
		
		$args = array(
			'user_id'=> $user_id,
			'secondary_id'=>$secondary_id,
			'type' => $type,
			'per_page' => $this->pag_num,
			'page' =>$this->pag_page,
			'search_terms' => $search_terms,
			'include_ids'	=> $include_ids

		);
		$this->markers = markers_get_markers( $args );
		

		if ($this->markers['total']==1) {
			$this->single_marker = true;
			$this->total_marker_count = 1;
			$this->marker_count = 1;
			
			$this->markers = $this->markers['markers'];
			
		} else {
			if ( !$max || $max >= (int)$this->markers['total'] )
				$this->total_marker_count = (int)$this->markers['total'];
			else
				$this->total_marker_count = (int)$max;
				
			$this->markers = $this->markers['markers'];

			if ( $max ) {
				if ( $max >= count($this->markers) )
					$this->marker_count = count($this->markers);
				else
					$this->marker_count = (int)$max;
			} else {
				$this->marker_count = count($this->markers);
			}
		}

		$this->pag_links = paginate_links( array(
			'base' => add_query_arg( array( 'mkpage' => '%#%', 'num' => $this->pag_num, 's' => $_REQUEST['s'], 'sortby' => $this->sort_by, 'order' => $this->order ) ),
			'format' => '',
			'total' => ceil($this->total_marker_count / $this->pag_num),
			'current' => $this->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size' => 1
		));



	}

	function has_markers() {
		if ( $this->marker_count )
			return true;

		return false;
	}

	function next_marker() {
		$this->current_marker++;
		$this->marker = $this->markers[$this->current_marker];

		return $this->marker;
	}

	function rewind_markers() {
	
		$this->current_marker = -1;
		if ( $this->marker_count > 0 ) {
			$this->marker = $this->markers[0];
		}

	}

	function markers() {

		if ( $this->current_marker + 1 < $this->marker_count ) {
			return true;
		} elseif ( $this->current_marker + 1 == $this->marker_count ) {
			do_action('loop_end');
			// Do some cleaning up after the loop
			$this->rewind_markers();
		}

		$this->in_the_loop = false;
		return false;
	}

	//TO FIX
	function the_marker() {
		global $map_marker;

		$this->in_the_loop = true;
		$this->marker = $this->next_marker();

		if ( $this->single_marker ) {
			
			$this->marker = new BP_Maps_Map_Marker( $this->marker->ID, true );


		}else {
			if ( $this->marker )
				wp_cache_set( 'markers_marker_nouserdata_' . $marker->ID, $this->marker, 'bp' );
		}

		$map_marker=$this->marker;

		if ( 0 == $this->current_marker ) // loop has just started
			do_action('loop_start');
	}
}

class BP_Maps_Map_Marker {

	var $ID;
	var $user_id;
	var $secondary_id; //use if you want to set a secondary ID when saving a marker
	
	var $type;
	var $Lat;
	var $Lng;
	var $title;
	var $address;
	var $content;
	
	var $privacy;
	var $approx=false;
	var $date_created;
	var $date_updated;
	
	var $open;
	
	function bp_maps_map_marker($id=null) {
		global $wpdb, $bp;
		
		if ( $id ) {
			$this->ID = $id;
		}

		$this->populate( $this->ID );
	}
	
	function populate() {
		global $bp;
		global $wpdb;
		
		$query = $wpdb->prepare( "SELECT * FROM {$bp->maps->table_name_markers} mk WHERE id={$this->ID}");
		
		if ( $row = $wpdb->get_row( $query ) ) {
			$this->user_id = $row->user_id;
			$this->secondary_id = $row->secondary_id;
			$this->type = $row->type;
			$this->Lat = $row->lat;
			$this->Lng = $row->lng;
			$this->title = $row->title;
			$this->address = $row->address;
			$this->content = $row->content;
			$this->privacy = $row->privacy;
			$this->date_created = $row->date_created;
			$this->date_updated = $row->date_updated;
		}
		
		if (!self::user_can_view_exact()) {
			$this->approx=true;
			$point = self::randomize_location($this->Lat,$this->Lng);
			$this->Lat=$point[0];
			$this->Lng=$point[1];
		}
		
		
	}
	
	////TYPES
	//types can be a single type or an array.
	//to exclude a type, put !  before. eg. $type="!member_profile"
	function get_markers( $limit = null, $page = null, $user_id = false, $secondary_id=false, $type=false, $search_terms = false, $include_ids=false) {
		global $wpdb, $bp;

		if ( $limit && $page )
			$pag_sql = $wpdb->prepare( " LIMIT %d, %d", intval( ( $page - 1 ) * $limit), intval( $limit ) );
			
		if ($user_id)
			$where_args['user_id'] = "mk.user_id =".$user_id;
			
		if ($secondary_id)
			$where_args['secondary_id'] = "mk.secondary_id =".$secondary_id;
			
		if ( $search_terms ) {
			$search_terms = like_escape( $wpdb->escape( $search_terms ) );
			$where_args['search_terms'] = "( mk.title LIKE '%%{$search_terms}%%' OR mk.content LIKE '%%{$search_terms}%%' OR mk.address LIKE '%%{$search_terms}%%' )";
		}
		
		if ($type)
			$where_args['type']="mk.type='{$type}'";

		
		if ($where_args)
			$where_sql =implode(' AND ',$where_args);


		//TO FIX pagination when $include_ids ?
		if (($include_ids) && (is_array($include_ids))) {
			if ($where_sql)
				$both_sql[] = "(".$where_sql.")";
				
			$ids = implode(',',$include_ids);
			$both_sql[]="mk.id IN ({$ids})";
			$where_sql=implode(' OR ',$both_sql);
		}
		
		$sql_args=" FROM `{$bp->maps->table_name_markers}` mk WHERE {$where_sql}";
		
		$total_sql="SELECT COUNT(DISTINCT mk.id)".$sql_args;
		$paged_sql="SELECT DISTINCT id".$sql_args." ORDER BY mk.date_updated DESC {$pag_sql}";
		

		$paged_markers = $wpdb->get_results($paged_sql);
		$total_markers = $wpdb->get_var($total_sql);

		return array( 'markers' => $paged_markers, 'total' => $total_markers );
	}
	
	
	function randomize_location($lat,$lng,$km=0.15) {
			
		$brg = rand(0,360);
		
		//Convert the starting point latitude 'lat1' (in the range -90 to 90) to radians.
		$lat1 = $lat * pi()/180;
		//Convert the starting point lnggitude 'lng1' (in the range -180 to 180) to radians.
		$lng1 = $lng * pi()/180;
		//Convert the bearing 'brg' (in the range 0 to 360) to radians.
		$brg = $brg * pi()/180;

		$radiusEarth = 6372.7976;
		//Convert distance to the distance in radians.
		$dist = $km/$radiusEarth;

		//Calculate the destination coordinates.
		$lat2 = asin(sin($lat1)*cos($dist) + cos($lat1)*sin($dist)*cos($brg));
		$lng2 = $lng1 + atan2(sin($brg)*sin($dist)*cos($lat1), cos($dist)-sin($lat1)*sin($lat2));
		
		//Calculate the final bearing and back bearing.
		$dLng = $lng1-$lng2;
		$y = sin($dLng) * cos($lat1);
		$x = cos($lat2)*sin($lat1) - sin($lat2)*cos($lat1)*cos($dLng);
		$d=atan2($y, $x);
		$finalBrg = $d+pi();
		$backBrg=$d+2*pi();
		//Convert lat2, lng2, finalBrg and backBrg to degrees
		$lat2 = $lat2 * 180/pi();
		$lng2 = $lng2 * 180/pi();
		$finalBrg = $finalBrg * 180/pi();
		$backBrg = $backBrg * 180/pi();


		// If lng2 is outside the range -180 to 180, add or subtract 360 to bring it back into that range.
		// If finalBrg or backBrg is outside the range 0 to 360, add or subtract 360 to bring them back into that range. 

		return array($lat2,$lng2);
	}
	
	function user_can_view_exact() {

		//if (current_user_can('level_10')) return true;

		//if ($marker->user_id=$bp->loggedin_user->id) return true;
		
		$needed_level = $this->privacy['marker_view_full'];
		
		if (!$needed_level) return true;
		
		if ($user_level >= bp_maps_get_user_privacy_level()) return true;
		
		return false;
		
	}
	
	function save() {
		global $wpdb, $bp;

		if (!$this->user_id)
				$this->user_id=$bp->loggedin_user->id;
				
		if (!$this->date_created)
			$this->date_created = gmdate( "Y-m-d H:i:s" );
			
		$this->date_updated = gmdate( "Y-m-d H:i:s" );

		$this->user_id = apply_filters( 'maps_map_user_id_before_save', $this->user_id, $this->ID );
		$this->secondary_id = apply_filters( 'maps_map_secondary_id_before_save', $this->secondary_id, $this->ID );
		$this->type = apply_filters( 'maps_map_type_before_save', $this->type, $this->ID );
		$this->Lat = apply_filters( 'maps_map_Lat_before_save', $this->Lat, $this->ID );
		$this->Lng = apply_filters( 'maps_map_Lng_before_save', $this->Lng, $this->ID );
		$this->title = apply_filters( 'maps_map_title_before_save', $this->title, $this->ID );
		$this->address = apply_filters( 'maps_map_address_before_save', $this->address, $this->ID );
 		$this->content = apply_filters( 'maps_map_content_before_save', $this->content, $this->ID );
		
		$this->privacy = apply_filters( 'maps_map_privacy_before_save', (array)$this->privacy, $this->ID );
		$this->date_created = apply_filters( 'maps_map_date_created_before_save', $this->date_created, $this->ID );
		$this->date_updated = apply_filters( 'maps_map_date_updated_before_save', $this->date_updated, $this->ID );
		do_action( 'maps_maps_marker_before_save', $this );

		
		if ( $this->ID ) {
			$sql = $wpdb->prepare(
				"UPDATE `{$bp->maps->table_name_markers}` SET
					`secondary_id` = %d,
					`type` = %s,
					`lat` = %s,
					`lng` = %s,
					`title` = %s,
					`address` = %s,
					`content` = %s,
					`privacy` = %s,
					`date_updated` = %s
				WHERE
					id = %d
				",
					$this->secondary_id,
					$this->type,
					$this->Lat,
					$this->Lng,
					$this->title,
					$this->address,
					$this->content,
					$this->privacy,
					$this->date_updated,
					$this->ID
			);
		} else {
			$sql = $wpdb->prepare(
				"INSERT INTO `{$bp->maps->table_name_markers}` (
					`user_id`,
					`secondary_id`,
					`type`,
					`lat`,
					`lng`,
					`title`,
					`address`,
					`content`,
					`privacy`,
					`date_created`,
					`date_updated`
				) VALUES (
					%d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s
				)",
					$this->user_id,
					$this->secondary_id,
					$this->type,
					$this->Lat,
					$this->Lng,
					$this->title,
					$this->address,
					$this->content,
					$this->privacy,
					$this->date_created,
					$this->date_updated
			);
		}

		if ( false === $wpdb->query($sql) )
			return false;

		if ( !$this->ID ) {
			$this->ID = $wpdb->insert_id;
		}

		do_action( 'maps_maps_marker_after_save', $this );

		return $this->ID;
	}
	
	function delete() {
		global $wpdb, $bp;

		do_action( 'bp_maps_delete_map', $this );

		// Finally remove the map entry from the DB
		if ( !$wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->maps->table_name_markers} WHERE id = %d", $this->ID ) ) )
			return false;

		return true;
	}
	
	function delete_all_for_user( $user_id ) {
		global $wpdb, $bp;

		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->maps->table_name_markers} WHERE user_id = %d", $user_id ) );
	}

}



/*****************************************************************************
 * Maps Template Class
 **/

class Bp_Map {
	var $slug; //unique ID | needed for the CSS|JS
	var $title;

	var $display; //static, dynamic or both 
	var $size;
	var $width;
	var $height;
	/*
	var $markers_max; //max markers
	var $markers_template;
	var $markers_total;
	*/
	var $Lat;
	var $Lng;
	
	var $zoom;
	var $mapTypeControlOptions;
	var $navigationControlOptions;
	var $mapTypeId;
	//var $privacy_levels;
	
	var $image;


	function bp_map($map_args=false) {
		global $bp;

		$default_center = explode(",",$bp->maps->options['map']['options']['center']);

		$defaults = array(
			'display'	=>$bp->maps->options['map']['options']['display'],
			'size'		=>$bp->maps->options['map']['options']['size'],
			'lat'			=>$default_center[0],
			'lng'			=>$default_center[1],
			'zoom'			=>$bp->maps->options['map']['options']['zoom'],
			'mapTypeControlOptions'	=>$bp->maps->options['map']['options']['mapTypeControlOptions'],
			'navigationControlOptions'=>$bp->maps->options['map']['options']['navigationControlOptions'],
			'mapTypeId'		=>$bp->maps->options['map']['options']['mapTypeId'],
			/*
			'privacy_levels'	=>array(
				0	=> array(
						'name'=>__('Everyone','bp_maps'),
						'can_view'=>array(
							0=>'hide',
							1=>'approx',
							2=>'exact'
						),
					),
				1	=> array(
						'name'=>__('Members','bp_maps'),
						'can_view'=>array(
							0=>'hide',
							1=>'approx',
							2=>'exact'
						),
					),
				2	=> array(
						'name'=>__('Friends','bp_maps'),
						'can_view'=>array(
							0=>'hide',
							1=>'approx',
							2=>'exact'
						),
					),
				3	=> array(
						'name'=>__('Only Me','bp_maps'),
						'can_view'=>array(
							0=>'hide',
							1=>'approx',
							2=>'exact'
						),
					)
			)
			*/
		);
		

		
		$r = wp_parse_args( $map_args, $defaults );

		extract( $r, EXTR_SKIP );
		
		$this->slug="map_".$slug;

		$this->display=$display;
			
		//CUSTOM SIZE OR NOT
		if (($width) && (((is_numeric($width)) && ($display!='dynamic')) || ($display=='dynamic'))){
			$this->width=$width;
		}else {
			$key = $bp->maps->options['map']['options']['size'];
			$this->width = $bp->maps->options['map']['sizes'][$key]['width'];
		}
		if (($height) && (((is_numeric($height)) && ($display!='dynamic')) || ($display=='dynamic'))){
			$this->height=$height;
		}else {
			$key = $bp->maps->options['map']['options']['size'];
			$this->height = $bp->maps->options['map']['sizes'][$key]['height'];
		}

		//MAP TITLE
		$this->title=$title;

		//LAT/LNG
		$this->Lat=$lat;
		$this->Lng=$lng;
		$this->zoom=$zoom;

		$this->mapTypeControlOptions=$mapTypeControlOptions;
		$this->navigationControlOptions=$navigationControlOptions;
		$this->mapTypeId=$mapTypeId;
		
		if ((!$bp->maps->options['api_key']) || ($editable))
			$this->display='dynamic';
			
		//PRIVACY LEVELS
		$this->privacy_levels=$privacy_levels;
		
		if (!bp_map_has_markers($this,$groups_args)) {
		
			if ($this->editable) { //no markers
				if ($bp->maps->options['map']['options']['default_loc']==2) {//use user IP for default map ?
					$user_geo=bp_maps_geoip();

					if (($user_geo->latitude) && ($user_geo->longitude)) {
						$this->Lat=$user_geo->latitude;
						$this->Lng=$user_geo->longitude;
					}

				}

			}else {
				return false;
			}
		}


		foreach ($this->groups_template->groups as $group) {

			if (bp_maps_get_group_markers_count($group)) {


				foreach ($group->markers_template->markers as $key=>$unpopulated_marker) {

					$group->markers_template->markers[$key]=new BP_Maps_Map_Marker($unpopulated_marker->id);
				}


				//one single marker

				if ($group->marker_count==1) {
					$single = $group->markers[0];

					$this->Lat=$single->Lat;
					$this->Lng=$single->Lng;
				}
			//editable map, no yet markers -> add first one and open it	
			}else {
				if (bp_map_group_editable($group)) {

					$newmarker=new BP_Maps_Map_Marker();
					$newmarker->user_id=$group->user_id;
					$newmarker->type=$group->type;
					$newmarker->open=true;
					$group->markers_template->markers[]=$newmarker;

					$group->markers_template->marker_count=1;
					$group->markers_template->total_marker_count=1;
				}
			}


		}

	}
	
	function get_static_image() {
		require_once ( BP_MAPS_PLUGIN_DIR . '/_inc/php/static-maps/Maps.php' );
		global $bp;
		
		///MARKERS
		if ($this->markers_template->markers) {
			foreach ($this->markers_template->markers as $marker) {
				if ((!$marker->Lat) || (!$marker->Lng)) continue;

				$coord = new Google_Maps_Coordinate($marker->Lat, $marker->Lng);
				$marker = new Google_Maps_Marker($coord);
				//$marker->setColor('green');
				/*
				$marker->content = "test";
				
				if ($marker->content) {
					$bubble = new Google_Maps_Infowindow($marker->content);
					$bubble->setMarker($marker);
					$map->addInfowindow($bubble);
				}
				*/
				
				$markers[]=$marker;
			}
		}

		///MARKERS
		
		///MAP
		//init
		$map=Google_Maps::create('static');

		//center
		$center=new Google_Maps_Coordinate($this->Lat,$this->Lng);
		$map->setCenter($center);

		$map->setSize($this->width.'x'.$this->height);
		$map->setKey($bp->maps->options['api_key']);

		$map->setZoom($this->zoom);
		
	
		if ($markers)
			$map->setMarkers($markers);
		///MAP
		
		//MAP CENTER
		if ($this->markers_template->marker_count>1) { //several markers
			$map->zoomToFit();
		}
		
		$this->zoom=$map->getZoom();

		$this->Lat = $map->getCenter()->getLat();
		$this->Lng = $map->getCenter()->getLon();

		return $map;
	}
}



?>