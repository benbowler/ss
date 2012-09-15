var fields_address_updated=false;
var marker_address_updated=false;
var saved_marker_address;
var saved_profile_address;

jQuery(document).ready( function() {
	
	//WARN USER WHEN SAVING PROFILE

	jQuery('#profile-group-edit-submit').click( function(event) {
	
		//the checkbox is disabled, escape
		if(typeof(map_profile_checkbox)=='undefined') return true;
		if (!map_profile_checkbox.attr('checked')) return true;
		
		//get addresses
		var fields_address=bp_maps_profile_get_fields_adress();
		var marker_address=bp_maps_profile_get_marker_adress();
		var new_saved_marker_address=bp_maps_profile_get_saved_marker_address();
		
		//updated profile == saved profile
		if (fields_address==saved_profile_address)
			fields_address_updated = false;

		//profile not updated
		if (!fields_address_updated) return true;
		
		//marker address not updated,escape
		//if (!marker_address_updated) return true;
		
		//marker updated and saved
		//if (new_saved_marker_address!=saved_marker_address) return true;

		var updatemarker = confirm(map_profile_confirm_msg);
		
		if (!updatemarker) return true;
		
		var marker_div=jQuery('.bp_maps_marker:first');
		if (bp_maps_map_deleteMarker(marker_div)) {
		
			return true;
			
		}

	});
});



//instead of jQuery ready or the saved_marker_address will be undefined
jQuery(window).load(function(){

	if (!bp_maps_profile_get_fields()) return false;

	var fields_address=bp_maps_profile_get_fields_adress();
	saved_profile_address=fields_address;
	var marker_address=bp_maps_profile_get_marker_adress();
	saved_marker_address=bp_maps_profile_get_saved_marker_address();
	update_marker_field=true;
	
	//there is a saved marker
	//and its location is != fields location, -> escape
	if ((saved_marker_address) && ((fields_address) != (marker_address))) 
		update_marker_field=false;
	
	var fields_inputs = bp_maps_profile_get_fields();
	var marker_input = bp_maps_profile_get_marker_field();
	
	//init
	if (marker_input.val()=='')
		marker_input.val(fields_address);
	
	//KEYUP
	
	var marker_field = bp_maps_profile_get_marker_field();
	
	marker_field.keyup(function() {
		marker_address_updated=true;

	});
	
	jQuery.each(fields_inputs, function() { 

		jQuery(this).keyup(function() {
			fields_address_updated=true;
			if (update_marker_field) {
				fields_address = bp_maps_profile_get_fields_adress();
				marker_input.val(fields_address);
			}

		});
	});

	
	
});

function bp_maps_profile_get_saved_marker_address() {

	var map_div=jQuery('.bp_maps_map:first');
	var map_id=map_div.attr('rel');
	var markers=eval(map_id+'_groups[0]');
	var marker=markers[0];
	
	if(typeof(marker)=='undefined') return false;

	if (marker)
		return marker.address;
	
}

function bp_maps_profile_get_fields() {

	//no map fields option
	if(typeof(map_profile_fields)=='undefined') return false;

	var fields=eval(map_profile_fields);
	
	var fields_address;
	
	var fields_inputs=new Array();
	
	for (var i = 0, fields_count = fields.length; i < fields_count; i++) {
		var the_field;
		the_field = jQuery('#field_'+fields[i]);
		if (the_field.length)
			fields_inputs.push(the_field);
	}
	
	return fields_inputs;

}

function bp_maps_profile_get_fields_adress() {
	var inputs = bp_maps_profile_get_fields();
	var address = bp_maps_profile_build_fields_address(inputs);
	return address;
	
}

function bp_maps_profile_get_marker_field() {
	return jQuery('.bp_maps_marker:first .address input');
}

function bp_maps_profile_get_marker_adress() {

	var el = bp_maps_profile_get_marker_field();
	return el.val();

}

function bp_maps_profile_build_fields_address(inputs_in) {
	var newaddress='';

	jQuery.each(inputs_in, function() { 
			newaddress=newaddress+' '+jQuery(this).val();
	
	});
	
	return jQuery.trim(newaddress);
}