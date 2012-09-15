<?php


function bp_maps_map_marker_save() {

	if ($_POST['point']) {
		$point_datas = stripslashes($_POST['point']);
		$point_datas = json_decode($point_datas);
	}

	$nonce_name = $_POST['map_id'].'_marker'.$point_datas->index."_save";

	//TO FIX !!!
	//if ( !check_admin_referer( $nonce_name ) )  return false;


	$point=new BP_Maps_Map_Marker($point_datas->ID);

	$point->Lat=$point_datas->position->lat;
	$point->Lng=$point_datas->position->lng;

	if ($point_datas->secondary_id)
		$point->secondary_id=$point_datas->secondary_id;
	
	if ($point_datas->title)
		$point->title=$point_datas->title;
	
	if ($point_datas->desc)
		$point->content=$point_datas->desc;
	
	if ($point_datas->address)
		$point->address=$point_datas->address;
		
	$point->type=$point_datas->type;
	
	if ($point_datas->privacy)
		$point->privacy=$point_datas->privacy;

	$new_id = $point->save();

	if ($new_id) {
		if (!$point_datas->ID) {
			$msg[]=__('Marker has been saved','bp-maps');
		}else{
			$msg[]=__('Marker has been updated','bp-maps');
		}
	}else {
		$msg[]=__('Error occured while trying to save marker information','bp-maps');
	}

	$response->ID = $new_id;
	$response->msg=implode("\n",$msg);
	echo json_encode($response);
}

add_action( 'wp_ajax_bp_maps_map_marker_save', 'bp_maps_map_marker_save' );


function bp_maps_map_marker_delete() {

	if ($_POST['point']) {
		$point_datas = stripslashes($_POST['point']);
		$point_datas = json_decode($point_datas);
	}
	
	$nonce_name = $_POST['map_id'].'_marker'.$point_datas->index."_save";

	if ( !check_admin_referer( $nonce_name ) )  return false;

	$point=new BP_Maps_Map_Marker($point_datas->ID);

	if ($point->delete()) {
		$msg[]=__('Marker has been deleted','bp-maps');
		$result=true;
	}else {
		$msg[]=__('Error occured while trying to delete marker','bp-maps');
	}
	$response->result=$result;
	$response->ID=$point_datas->ID;
	$response->msg=implode("\n",$msg);
	echo json_encode($response);

}

add_action( 'wp_ajax_bp_maps_map_marker_delete', 'bp_maps_map_marker_delete' );

function get_include_contents($filename) {
    if (is_file($filename)) {
        ob_start();
        include $filename;
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }
    return false;
}

function bp_maps_map_marker_add() {

	if ($_POST['enable_desc']=='undefined')
		unset($_POST['enable_desc']);
	
	//temp map to get the good HTML
	global $bp;
	
	
	$slug = explode('map_',$_POST['map_id']);
	$map_slug=$slug[1];
	$marker_index=$_POST['marker_index'];
	$enable_desc=$_POST['enable_desc'];

	if ($_POST['editable'])
		$html=bp_maps_map_get_ajax_marker_html($map_slug,$marker_index,$enable_desc);
	
	if ( !$html )  return false;
	
	$result=true;
	
	


	$response->result=$result;
	$response->html=$html;
	echo json_encode($response);

}

add_action( 'wp_ajax_bp_maps_map_marker_add', 'bp_maps_map_marker_add' );


?>