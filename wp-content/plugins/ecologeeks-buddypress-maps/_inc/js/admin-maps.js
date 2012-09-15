jQuery(document).ready( function($) {
	//TABS
	jQuery('#slider').tabs();
	//PRIVACY
	//view markers switch
	jQuery('tr.maps_view_markers input').click( function(event) {
		jQuery('tr.maps_view_markers input').removeAttr('checked');
		jQuery(this).attr('checked',true);

	});
	//create switch
	
	bp_maps_admin_toggle_privacy_boxes();
	
	jQuery('tr.maps_create_markers input').click( function(event) {
		bp_maps_admin_toggle_privacy_boxes();
	});
});

function bp_maps_admin_toggle_privacy_boxes() {
	var privacy_options = jQuery('tr.maps_marker_creation_visitors_hide input,tr.maps_marker_creation_visitors_show_approx input');
	if (jQuery('tr.maps_create_markers input').attr('checked')) {
		privacy_options.removeAttr('disabled');
	}else {
		privacy_options.attr('disabled',true);
	}
}




