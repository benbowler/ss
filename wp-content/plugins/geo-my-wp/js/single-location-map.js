//var zoomLevel = zoomLevel;
	var mapSingle = new google.maps.Map(document.getElementById('map_single'), {
		zoom: zoomLevel,
    	center: new google.maps.LatLng(singleLocation[0],singleLocation[1]),
    	mapTypeId: google.maps.MapTypeId[singleLocation[8]],
		mapTypeControlOptions: {
			style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
    	}
	});	
	
	var marker;   
	var infowindow = new google.maps.InfoWindow();
	
	marker = new google.maps.Marker({
		position: new google.maps.LatLng(singleLocation[0], singleLocation[1]),
		map: mapSingle,
		shadow:'https://chart.googleapis.com/chart?chst=d_map_pin_shadow'       
	});
 
	google.maps.event.addListener(marker, 'click', (function(marker, i) {
		return function() {
			infowindow.setContent(
        		'<div class="wppl-info-window" style="font-size: 13px;color: #555;line-height: 18px;font-family: arial;">' +
        		'<div class="map-info-title" style="color: #457085;text-transform: capitalize;font-size: 16px;margin-bottom: -10px;">' + singleLocation[2] + '</div>' +
        		'<br /> <span style="font-weight: bold;color: #333;">Address: </span>' + singleLocation[3]  + 
        		'<br /> <span style="font-weight: bold;color: #333;">Phone: </span>' + singleLocation[4] + 
        		'<br /> <span style="font-weight: bold;color: #333;">Fax: </span>' + singleLocation[5] + 
        		'<br /> <span style="font-weight: bold;color: #333;">Email: </span>' + singleLocation[6] + 
        		'<br /> <span style="font-weight: bold;color: #333;">Website: </span><a href="http://' + singleLocation[7] + '" target="_blank">' + singleLocation[7] + '</a>');
    			infowindow.open(mapSingle, marker);    
		}
	})(marker, i));
		