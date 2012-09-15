jQuery(function() {
    jQuery('.show-directions').click(function(event){
   	 	event.preventDefault();
    	jQuery(this).closest("div").find(".wppl-single-member-direction").slideToggle(); 
    }); 
});
function singleMap() {
	var i;
	for (i = 1; i < (mapId + 1); i++) { 
		var mapSingle = new google.maps.Map(document.getElementById('member-map-'+ i), {
			zoom: 14,
    		center: new google.maps.LatLng(singleLocation[0],singleLocation[1]),
    		mapTypeId: google.maps.MapTypeId[memberMapType[i]],
			mapTypeControlOptions: {
				style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
    		}
		});	
	
		var marker;   
	
		marker = new google.maps.Marker({
			position: new google.maps.LatLng(singleLocation[0], singleLocation[1]),
			map: mapSingle,
			shadow:'https://chart.googleapis.com/chart?chst=d_map_pin_shadow'       
		});
	}
}
window.onload = singleMap();

