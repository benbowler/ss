///// friends map ////
jQuery(function() {
	//// show hide map ///
    jQuery('.map-show-hide-btn').click(function(event){
    	event.preventDefault();
    	jQuery("#wppl-hide-map").slideToggle();
    
    }); 
});
 	
	var latlngbounds = new google.maps.LatLngBounds( );
	if (your_location != "0") {
		var yourLocation  = new google.maps.LatLng(your_location[1],your_location[2]);
		latlngbounds.extend(yourLocation);
	}

	var map = new google.maps.Map(document.getElementById('map'), {
		zoom: zoomLevel,
    	center: new google.maps.LatLng(your_location[1],your_location[2]),
    	mapTypeId: google.maps.MapTypeId[mapType],
		mapTypeControlOptions: {
			style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
    	}
	});				
 
	var infowindow = new google.maps.InfoWindow();
	var marker, i;
	var ib = new InfoBox(myOptions);
    	
	for (i = 0; i < locations.length; i++) {  
		var point = new google.maps.LatLng(locations[i]['lat'], locations[i]['long']);
		latlngbounds.extend(point);
		
		mapIcon = 'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld='+ ((page * 1) + i + 1) +'|FF776B|000000';
			
		var marker = new google.maps.Marker({
         map: map,
         position: new google.maps.LatLng(locations[i]['lat'], locations[i]['long']),
         icon:mapIcon,
         shadow:'https://chart.googleapis.com/chart?chst=d_map_pin_shadow'       
        });
           
        var boxText = document.createElement("div");
                   
        var myOptions = {
                 content: boxText
                ,disableAutoPan: false
                ,maxWidth: 0
                ,boxClass: "wppl-map-info-box"   
                ,pixelOffset: new google.maps.Size(0, -200)
                ,zIndex: null
                ,boxStyle: { 
                  background: "url('tipbox.gif') no-repeat"
                  ,width: "160px"
                 }
                ,closeBoxMargin: "-20px 0 0 0"
                ,closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif"
                ,infoBoxClearance: new google.maps.Size(1, 1)
                ,isHidden: false
                ,pane: "floatPane"
                ,enableEventPropagation: false
        };
		
		google.maps.event.addListener(marker, 'click', (function(marker, i) {
    		return function() {
    			
    			 boxText.innerHTML = 
    
        				'<div class="bp-map-infobox-title">' + avatar[locations[i]['member_id']][1] + '</div>' +
        				'<div class="bp-map-infobox-image" style="float:left;position:relative">' + avatar[locations[i]['member_id']][0] + '</div>' +
        				'<div class="bp-map-infobox-info">' +
        					'<span>Address: </span>' + locations[i]['address']  +
        					'<br /><span>Distance: </span>' + locations[i]['distance'] + ' ' + units['name'] +
        				'</div>'
       			
       			ib.setContent(boxText); 
        		ib.open(map, marker); 
    		}
		})(marker, i));
        	  
		if(autoZoom == 1) {
			map.fitBounds(latlngbounds);
		}
    
		marker = new google.maps.Marker({
			position: new google.maps.LatLng(your_location[1],your_location[2]),
			map: map,
    		icon:'http://maps.google.com/mapfiles/ms/icons/green-dot.png',
		});
	}
