jQuery(document).ready( function() {

	//disable AJAX

	var lis=jQuery('#members-directory-form li')
	var the_li=jQuery('#members-directory-form li#members-map');
	
	//get extras
	var extras;
	extras = JSON.parse(j.cookie('bp-members-extras'));
	if (!extras)
		extras={};

	
	//set extra arg for ajax
	lis.click( function(event) {
	
		if (jQuery(this).attr('id')=='members-map') {
			extras["showmap"] = true;
			
		}else {
			delete(extras["showmap"]);
		}
		
		var extras_json = JSON.stringify(extras);
		j.cookie('bp-members-extras',extras_json);
		
		
		//bp_filter_request('members', j.cookie('bp-members-filter'), j.cookie('bp-members-scope') , jQuery('#members_search').val(), 1,j.cookie('bp-members-extras'));
		//return false;
		
	});

	//listen when the map is inserted (not possible with jquery.live)
	jQuery('#members-directory-form li.selected').livequery('change',function() {
		if (jQuery(this).attr('id')=='members-map') {
			console.log('map');
		}else {
			console.log('nomap');
		}

			
				//init map
				//console.log("map loaded");
				//bp_maps_map_init(jQuery(this));
/*
				//reset args when map is not more shown
				console.log("map unloaded");
				
				var extras;
				extras = JSON.parse(j.cookie('bp-members-extras'));
				extras["showmap"] = false;
				var extras_json = JSON.stringify(extras);
				j.cookie('bp-members-extras',extras_json);
				j.cookie('bp-members-scope','all');
*/
	});


});
