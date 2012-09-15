var bp_maps_google_is_init;

function bp_maps_init_geocoder(){
	if (!geocoder) {
		geocoder = new google.maps.Geocoder();
	}
};

function bp_maps_map_init(el) {
	//CHECK IF MAP HAS BEEN LOADED AND LOAD IT
	var map_div = el.parents('.bp_maps_map');
	var map_id = map_div.attr('rel');
	
	var map_loaded = eval(map_id+'_is_init');
	
	if (!map_loaded) {
		if (!bp_maps_google_is_init) {
			bp_maps_GoogleMaps.initialize(map_div);
		}else { //loaded before
			bp_maps_GoogleMaps.googleMapsLibraryLoaded();
		}
	}else {
		return true;
	}
}

function bp_maps_init_dynamic_maps() {
	var dynamics=jQuery('.bp_maps_map_container.dynamic');
	
	jQuery.each(dynamics, function() { 
		if (!bp_maps_map_init(jQuery(this))) return false;
	});
}

jQuery(document).ready( function() {

	var j = jQuery;

	//MARKERS TABS
	j('#markers_tabs').tabs();
	
	var tabs_links=j('#markers_tabs li a');
	
	//TAB checkbox toggle
	j('.bp_maps_toggle_group').click( function(event) {
		var checked=j(this).attr('checked');
		var map_id=bp_maps_get_parent_id(j(this));
		var group_index=j(this).attr('rel');
		bp_maps_toggle_group(map_id,group_index,checked);

	});

	//INIT MAPS
	j('.bp_maps_map_container img').click( function(event) {
		if (!bp_maps_map_init(j(this))) return false;
	});
	
	bp_maps_init_dynamic_maps();
	


	//PRIVACY RADIO CLICKED
	j('.bp_maps_privacy_action td').click( function(event) {
		bp_maps_map_privacy_rows_init(j(this));

	});
	
	
	//CLICK "ADD MARKER" LINK
	j('.bp_maps_map_add_marker a').click( function(event) {
	
		if (!bp_maps_map_init(j(this))) return false;
	
		var map_id=bp_maps_get_parent_id(j(this));
		var group_index=j(this).parents('.markers_block').attr('rel');

		if (!group_index) return false;
		if (!map_id) return false;
		
		bp_maps_new_geobox(map_id,group_index);

		return false;

	});
	
	//HOVER MARKERS
	j('.bp_maps_marker').hover(function() {
	  j(this).addClass('hover');
	}, function() {
	  j(this).removeClass('hover');
	});
	

	
	//CLICK MARKER ICON (center map)
	j('.bp_maps_marker span.bp_maps_group_icon').live('click',function() {
		if (!bp_maps_map_init(j(this))) return false;
	
		var map_id=bp_maps_get_parent_id(j(this));
		var group_index=bp_maps_get_group_index(j(this));
		var marker_id=bp_maps_marker_get_parent_id(j(this));

		bp_maps_reset_view(map_id,group_index,marker_id,true);

	});
	
	//EDIT MARKERS
	j('.bp_maps_marker .edit').live('click',function() {
	
		if (!bp_maps_map_init(j(this))) return false;

		var marker_div=j(this).parents('.bp_maps_marker');
		
		var map_id=bp_maps_get_parent_id(j(this));
		var group_index=bp_maps_get_group_index(j(this));

		bp_maps_collapse_edit_markers(map_id,group_index,j(this));
		marker_div.addClass('edit');
		marker_div.find('.item-title input').focus();

		return false;

	});
	
	//CLOSE
	j('.bp_maps_marker .close').live('click',function() {
		var map_id=bp_maps_get_parent_id(j(this));
		var group_index=bp_maps_get_group_index(j(this));
		bp_maps_collapse_edit_markers(map_id,group_index,j(this));
		return false;

	});

	//DELETE MARKERS
	j('.bp_maps_marker .delete').live('click',function() {
		var marker_div=j(this).parents('.bp_maps_marker');
		bp_maps_map_deleteMarker(marker_div);
		
		return false;

	});
	
	//SEARCH/SAVE LOCATION
	j('.bp_maps_marker .save').live('click',function() {
		
		bp_maps_init_geocoder();
		
		var map_id=bp_maps_get_parent_id(j(this));
		var marker_div=j(this).parents('.bp_maps_marker');
		var group_index = bp_maps_get_group_index(marker_div);
		var marker_index = marker_div.attr('rel');
		var input_address = marker_div.find('.address input');

		if (!input_address.val()) return false;

		j(this).addClass('loading');

		bp_maps_map_moveMarker(map_id,group_index,marker_index,input_address);

		return false;

		
	});

});

function bp_maps_toggle_group(map_id,group_index,bool) {

	var markers = eval(map_id+'_groups['+group_index+']');
	
	jQuery.each(markers, function() {
		marker = this.marker;
		if (bool)
			marker.setVisible(true);
		else
			marker.setVisible(false);
	});
	
	bp_maps_reset_view(map_id,'all');

}

function bp_maps_toggle_nomarkers(map_id,group_index){
	var group_div = jQuery(map_div.find('.markers_block[rel='+group_index+']'));
	var addlink = group_div.find('.bp_maps_map_add_marker');

	var li_count=group_div.find('ul.item-list li').length;
	
	if (li_count) {
		nomarkers_div.hide();
		var groupOptions=eval(map_id+'_GroupOptions['+group_index+']');
		var markers_max=groupOptions.markers_max;

		if (li_count>=markers_max)
			addlink.addClass('strike');
	}else{
		addlink.removeClass('strike');
	}
	
}

function bp_maps_collapse_edit_markers(map_id,group_index,el) {

	var markers_edit_div;
	var map_div=bp_maps_marker_get_parent_map_div(map_id);
	var group_div = jQuery(map_div.find('.markers_block[rel='+group_index+']'));

	if (el) {
		el.parents('.bp_maps_marker').removeClass('edit');
	}else {
		group_div.find('.bp_maps_marker.edit').removeClass('edit');
	}
}

function bp_maps_map_privacy_rows_init(cell){

	if (!cell)
		cell=jQuery('.bp_maps_privacy_action');
	
	jQuery.each(cell, function() { 
		var cells = jQuery(this).parent().find('td');
		var enable=false;

		cells.addClass('disabled');

		jQuery.each(cells, function(i) {
			
			if (jQuery(this).find('input[type="radio"]:checked').length)
					enable=true;
				
			if (enable)
				jQuery(this).removeClass('disabled');
			
			//last one and no one checked
			if ((i==cells.length-1) && (!enable))
				cells.removeClass('disabled');
		
		});

	});
	
}

function bp_maps_map_privacy(marker_div){
	var privacy = new Object();
	
	var actions  = marker_div.find('.privacy tr');
	
	jQuery.each(actions, function() { 
		var checkbox = jQuery(this).find('input[type="checkbox"]:checked');
		if (checkbox.length) {
			var radio = jQuery(this).find('input:checked');
			var name = radio.attr('name');
			var value = radio.val();
			privacy[name]=value;
		}
	});
	return privacy;
}

function bp_maps_map_saveMarker(marker_div) {
	
	var map_div=marker_div.parents('.bp_maps_map');
	var map_id=map_div.attr('rel');
	
	var group_div = jQuery(map_div.find('.markers_block[rel='+group_index+']'));
	var group_index=marker_div.parents('.markers_block').attr('rel');

	var marker_index = marker_div.attr('rel');

	var markers = eval(map_id+'_groups['+group_index+']');

	var marker=markers[marker_index];

	var groupOptions=eval(map_id+'_GroupOptions['+group_index+']');

	var action;
	
	var point=new Object();
	
	marker_div.find('a.edit').addClass('loading');
	marker_div.find('input').attr('disabled',true);
	marker_div.find('.save').hide();
	
	
	//SPANS = INPUTS
	var title = marker_div.find('.item-title input').val();
	var title_span = marker_div.find('.item-title span');
	title_span.html(title);
	var address = marker_div.find('.address input').val();
	marker_div.find('.address span').html(address);
	var desc = marker_div.find('.content input').val();
	var desc_span = marker_div.find('.content span');
	desc_span.html(desc);

	//NONCE
	var nonce_input = marker_div.find('.action input:first');


	var nonce = nonce_input.val();

	point.position=new Object();
	point.position.lat = marker['marker'].position.lat();
	point.position.lng = marker['marker'].position.lng();
	point.title = title;
	point.desc = desc;
	point.address = address;
	point.privacy = bp_maps_map_privacy(marker_div);
	point.index = marker_index;
	point.type = groupOptions.type;
	point.secondary_id = groupOptions.secondary_id;
	
	if (marker.type)
		point.type=marker.type;

	if (marker.secondary_id)
		point.type = marker.secondary_id;
		
	if (marker.ID)
		point.ID=marker.ID;

	point = JSON.stringify(point);


	j.post( ajaxurl, {
		action: 'bp_maps_map_marker_save',
		'_wpnonce': nonce,
		point:point,
		map_id:map_id
	},
	function(response)
	{
		marker_div.find('a.edit').removeClass('loading');
		marker_div.find('input').removeAttr('disabled');
		marker_div.find('.save').show();
		
		response =JSON.parse(response);

		if (response.ID) {
			markers[marker_index].ID=response.ID;

			bp_maps_map_markers_input_ids_add_id(map_div,response.ID);
			
			bp_maps_marker_message(map_id,group_index,marker_index,response.msg);
			
		}else {
			bp_maps_marker_message(map_id,group_index,marker_index,response.msg,true);
		}

	});
	return false;

}


function bp_maps_map_deleteMarker(marker_div) {

	var marker_li = marker_div.parent('li');
	
	var marker_index = marker_div.attr('rel');
	var group_index = bp_maps_get_group_index(marker_div);
	var map_div=marker_div.parents('.bp_maps_map');
	
	var map_id=map_div.attr('rel');
	
	var markers=eval(map_id+'_groups['+group_index+']');
	var marker=markers[marker_index];
	
	var action;
	var count;
	
	
	if ((!marker) || (!marker.ID)) {
	
		marker_li.remove();
		
		if (!marker) { //no yet on map
			return true;
		}

		if (!marker.ID) { //not yet in the DB
			marker['marker'].setMap(null);
			markers.splice(marker_index,1);
			return true;
		}
		
	}
	
	var map = marker['marker'].map;

	marker_div.find('a.edit').addClass('loading');
	marker_div.find('input').attr('disabled',true);
	marker_div.find('.save').hide();


	
	//NONCE
	var nonce_input = marker_div.find('.action input:first');

	var nonce = nonce_input.val();
	
	var point=new Object();

	point.ID=marker.ID;
	point.index=marker_index;

	point = JSON.stringify(point);
	
	j.post( ajaxurl, {
		action: 'bp_maps_map_marker_delete',
		'_wpnonce': nonce,
		point:point,
		map_id:map_id
	},
	function(response)
	{
		marker_div.find('a.edit').removeClass('loading');
		marker_div.find('input').removeAttr('disabled');
		marker_div.find('.save').show();
		
		response =JSON.parse(response);

		if (response.result) {
			marker['marker'].setMap(null);
			markers.splice(marker_index,1);
			marker_li.remove();
			bp_maps_map_markers_input_ids_remove_id(map_div,response.ID);
			bp_maps_toggle_nomarkers(map_id,group_index);
			bp_maps_reset_view(map_id,group_index);
			return true;
		}else {
			bp_maps_marker_message(map_id,group_index,marker_index,response.msg,true);
		}

	});
	return false;

}

function bp_maps_marker_message(map_id,group_index,marker_index,msg,error) {

	var map_div=bp_maps_marker_get_parent_map_div(map_id);
	var group_div = jQuery(map_div.find('.markers_block[rel='+group_index+']'));
	
	var marker_div=group_div.find('.bp_maps_marker[rel='+marker_index+']');

	msg_div=marker_div.find('#message');
	
	if (error) {
		msg_div.addClass('error');
	}else {
		msg_div.removeClass('error');
	}
		
	msg_div.children('p').html(msg);
}

function bp_maps_count_markers(map_id) {
	var markers = eval(map_id+'_groups');
	return markers.length;
	
}


//get array of saved markers IDs
function bp_maps_map_get_markers_input_ids(input) {
	var value=input.val();
	var ids_arr=new Array();
	ids_arr=value.split(',');
	return ids_arr;
}
//add ID to markers IDs input
function bp_maps_map_markers_input_ids_add_id(mapdiv,marker_id) {
	var input=mapdiv.find('.markers_ids');
	var ids_arr=bp_maps_map_get_markers_input_ids(input);
	ids_arr.push(marker_id);
	input.val(ids_arr.join(','));
}
//remove ID from markers IDs input
function bp_maps_map_markers_input_ids_remove_id(mapdiv,marker_id) {
	var input=mapdiv.find('.markers_ids');
	var ids_arr=bp_maps_map_get_markers_input_ids(input);
	var index= jQuery.inArray(marker_id, ids_arr);
	if(index!= -1) {
		ids_arr.splice(index,1);
	}
	input.val(ids_arr.join(','));
}

function bp_maps_new_geobox(map_id,group_index) {

		var map_div=bp_maps_marker_get_parent_map_div(map_id);
		var markers = eval(map_id+'_groups');
		var group_div = jQuery(map_div.find('.markers_block[rel='+group_index+']'));
		var markers_list = group_div.find('ul');
		

		var marker_index=markers_list.find('.bp_maps_marker').length;
		var groupOptions=eval(map_id+'_GroupOptions['+group_index+']');
		var markers_max=groupOptions.markers_max;

		//TO FIX : SEEMS MARKER IS STILL IN markers ARRAY

		//LAST HAS NOT BEEN SAVED

		if ((marker_index) && (!bp_maps_group_get_last_marker(map_id,group_index))) {
			
			bp_maps_map_last_marker_focus(group_div);
			return false;
		}

		//BREAK IF MAX MARKERS
		if ((markers_max) && (marker_index>=markers_max)) return false;
		

		bp_maps_collapse_edit_markers(map_id,group_index);

		//var newmarker_index=jQuery('.bp_maps_marker').length+1;
		//var newmarker_div;

		var add_markers_link=group_div.find('.bp_maps_map_add_marker a');

		var editable;
		if (group_div.hasClass('editable'))
			editable=true;
			
		var enable_desc;
		if (group_div.hasClass('enable_desc'))
			enable_desc=true;

			
		var new_html;
		add_markers_link.addClass('loading');

		j.post( ajaxurl, {
			action: 'bp_maps_map_marker_add',
			map_id:map_id,
			marker_index:marker_index,
			enable_desc:enable_desc,
			editable:editable
		},
		function(response)
		{
	
			add_markers_link.removeClass('loading');

			response =JSON.parse(response);

			if (response.result) {
				new_html=jQuery(response.html);
				new_html.find('.bp_maps_marker').addClass('edit');

				new_html.prependTo(markers_list);
				bp_maps_toggle_nomarkers(map_id,group_index);
				
				var input=new_html.find(".address .input");
				input.focus();
				
			}
		});
		
		


		return false;
}


function bp_maps_group_get_last_marker(map_id,group_index) {
	if ((!map_id) || (!group_index)) return false;
	
	var map_div=bp_maps_marker_get_parent_map_div(map_id);
	var group_div = jQuery(map_div.find('.markers_block[rel='+group_index+']'));
	
	var last_marker = group_div.find('.bp_maps_marker:first');
	
	var last_marker_index=last_marker.attr('rel');

	return bp_maps_get_marker(map_id,group_index,last_marker_index);
}

function bp_maps_map_last_marker_focus(group_div) {
	var last_marker = group_div.find('.bp_maps_marker:first');
	last_marker.addClass('edit');
	var input_addr = last_marker.find('.address input');

	input_addr.focus();
}


function bp_maps_map_moveMarker(map_id,group_index,marker_index,input_address) {

		var address=input_address.val();
		var map_div=jQuery('#'+map_id);
		var group_div = jQuery(map_div.find('.markers_block[rel='+group_index+']'));
		var marker_div=jQuery(group_div.find('.bp_maps_marker[rel='+marker_index+']'));
		var marker_save_link = marker_div.find('a.save');

		var title_div=marker_div.find('.item-title');
		var title_input=title_div.find('input');
		if (title_input.length>0) {
			if (!title_input.val()) {
				marker_save_link.removeClass('loading');
				bp_maps_marker_message(map_id,group_index,marker_index,bp_maps_messages['error_notitle'],true);
				title_input.focus();
				return false;
			}
		}
		
		var desc_div=marker_div.find('.content');
		if (desc_div.length>0) {
			var desc_input=desc_div.find('input');
			if (!desc_input.val()) {
				marker_save_link.removeClass('loading');
				bp_maps_marker_message(map_id,group_index,marker_index,bp_maps_messages['error_nodesc'],true);
				desc_input.focus();
				return false;
			}
		}
		
		


		if (geocoder) {
		
		  geocoder.geocode( { 'address': address}, function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
			
				coords=results[0].geometry.location;
				
				//remove Loading Class
				marker_save_link.removeClass('loading');
				
				//get Marker
				var markers=eval(map_id+'_groups['+group_index+']');
				
				var mapOptions=eval(map_id+'_Options');
				var markers_max=mapOptions.markers_max;
				var map = eval(map_id+'_map');
				
				var marker = markers[marker_index];
				
				//marker do not exists yet
				if (!marker) {
				
					if (markers.length>markers_max)
						return false;
					new_marker = bp_maps_map_addMarker(map_id,group_index);
					new_marker.address=address;
					marker = new_marker['marker'];
				//marker exists
				}else {
					marker.address=address;
					marker = marker['marker'];
				}
				
				marker.setPosition(coords);
				
				bp_maps_map_saveMarker(marker_div);
				bp_maps_reset_view(map_id,group_index,marker_index);


			} else {
				marker_save_link.removeClass('loading');
				var message = bp_maps_messages['error_geocode']+" "+status;
				bp_maps_marker_message(map_id,group_index,marker_index,message,true);
			}
		  });
		}
}

function bp_maps_get_parent_id(el) {

	var map_id=el.parents('.bp_maps_map').attr('rel');

	return map_id;
}
function bp_maps_get_group_index(el) {

	var group_index=el.parents('.markers_block').attr('rel');

	return group_index;
}

function bp_maps_marker_get_parent_id(el) {

	var marker_id=el.parents('.bp_maps_marker').attr('rel');

	
	return marker_id;
}


function bp_maps_marker_get_parent_map(el) {

	map  = eval(bp_maps_marker_get_parent_map_name(el));

	return map;
}

function bp_maps_marker_get_parent_map_div(map_id) {


	return jQuery('#'+map_id);
}

function bp_maps_marker_get_parent_map_name(el) {

	var map_id=bp_maps_get_parent_id(el);
	
	return map_id+'_map';
}


function bp_maps_map_addMarker(map_id,group_index) {

	
	markers=eval(map_id+'_groups['+group_index+']');
	
	var mapOptions=eval(map_id+'_Options');
	var markers_max=mapOptions.markers_max;
	
	markers_count = jQuery(markers).length;

	if (markers_count>=markers_max)
		return false;
	
	//NEW MARKER ID
	var marker_index=markers_count;

	if (markers[marker_index])
		return false;

	var mapname=map_id+'_map';
	markers[marker_index] = new Object();
	markers[marker_index]['marker'] = new google.maps.Marker({map: eval(mapname),draggable:true,position: eval(mapname).getCenter()});

	return markers[marker_index];

}



function bp_maps_address_to_coords(address) {
	if (geocoder) {
	
	  geocoder.geocode( { 'address': address}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {

			coords=results[0].geometry.location;

			return coords;

		} else {
		  alert("Geocode was not successful for the following reason: " + status);
		}
	  });
	}
}

function bp_maps_get_marker(map_id,group_index,marker_index) {
	var markers = eval(map_id+'_groups['+group_index+']');
	var marker = markers[marker_index];


	return marker;
}

function bp_maps_get_markers_positions(markers) {

	var list=new Array();

		var markers_count = jQuery(markers).length;

		jQuery.each(markers, function(ID, obj) { 
			if (obj) {
				if (obj['marker'])
					list.push(obj['marker'].position);
			}
		});

	return list;
}

var last_infowindow;

function bp_maps_marker_infowindow(map_id,group_index,marker_index) {

	var markers=eval(map_id+'_groups['+group_index+']');

	var marker = markers[marker_index];


	//TO FIX
	//if (hide)
	//close all open infowindows
	if (last_infowindow) last_infowindow.close();

	
	marker['infoWindow'].open(eval(map_id+'_map'),marker['marker']);
		
	last_infowindow=marker['infoWindow'];
}

function bp_maps_get_all_visible_markers(map_id) {
	var groups=eval(map_id+'_groups');
	var markers=new Array();
	//TO FIX find better fn ?
	jQuery.each(groups, function(key,group) {
		jQuery.each(group, function(key,obj) {
			var marker=obj['marker'];

			var visible=marker.visible;
			
			if (visible || (typeof(visible)=='undefined'))
				markers.push(obj);
		});
	});

	return markers;
	
}


function bp_maps_reset_view(map_id,group_index,marker_index,infowindow) {

	var map = eval(map_id+'_map');
	var markers;
	
	if (group_index=='all') //get all markers
		markers=bp_maps_get_all_visible_markers(map_id);
	else //get markers for group
		markers=eval(map_id+'_groups['+group_index+']');

	//  Make an array of the LatLng's of the markers you want to show

	if (marker_index) {
	
		var marker = markers[marker_index];

		if (infowindow)
			bp_maps_marker_infowindow(map_id,group_index,marker_index);

		map.setCenter(marker['marker'].position);

	}
	
	var LatLngList = bp_maps_get_markers_positions(markers);

	
	if (!LatLngList) return false;
	
	
	if (LatLngList.length<=1) {
		if (LatLngList.length==1) {
			map.setCenter(LatLngList[0]);
			return true;
		}else { //nothing
			return true;
		}
	
	}

	//  Create a new viewpoint bound
	var bounds = new google.maps.LatLngBounds();
	//  Go through each...

	for (var i = 0, LtLgLen = LatLngList.length; i < LtLgLen; i++) {
	  //  And increase the bounds to take this point
	  bounds.extend (LatLngList[i]);
	}
	//  Fit these bounds to the map

	map.fitBounds (bounds);

}

