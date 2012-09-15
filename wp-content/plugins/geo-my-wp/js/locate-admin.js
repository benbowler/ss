window.onload = function() {
	if(document.getElementById('_wppl_address').value == "") {
		document.getElementById('_wppl_lat').value = "";
		document.getElementById('_wppl_long').value = "";
	}		
	document.getElementById('publish').onclick = function () {
		if(addressMandatory == 1) {
			if( (document.getElementById('_wppl_lat').value == "") || (document.getElementById('_wppl_long').value == "") || (document.getElementById('_wppl_address').value == "")) {
				document.getElementById('publish').disabled = true;
				document.getElementById('ajax-loading').style.visibility = "hidden";
				alert("Post cannot be published. You must enter a vallid address.");
				setTimeout(function() {
					document.getElementById("publish").disabled = false;	
       				jQuery('#publish').removeClass('button-primary-disabled');	
				},2000);
						
			} else {
  				document.getElementById('_wppl_lat').disabled = false;
				document.getElementById('_wppl_long').disabled = false;
   				document.getElementById('_wppl_address').disabled = false;
			}
		} else {
  			document.getElementById('_wppl_lat').disabled = false;
			document.getElementById('_wppl_long').disabled = false;
   			document.getElementById('_wppl_address').disabled = false;
		}	
	}
}

///////////////////////////
function removefields() {
		document.getElementById("_wppl_lat").value = "";
        document.getElementById("_wppl_long").value = "";
       	document.getElementById("_wppl_address").value = "";
       	document.getElementById("_wppl_enter_lat").value = "";
		document.getElementById("_wppl_enter_long").value = "";
		document.getElementById("_wppl_street").value = "";
       	document.getElementById("_wppl_apt").value = "";
       	document.getElementById("_wppl_city").value = "";
		document.getElementById("_wppl_state").value = "";
		document.getElementById("_wppl_zipcode").value = "";
		document.getElementById("_wppl_country").value = "";
	}

///////////////////////////	
function loaderOff() {
	document.getElementById("ajax-loader").style.visibility = 'hidden';
    document.getElementById("ajax-loader-image").style.visibility = 'hidden';
    }

///////////////////////////    
function loaderOn() {
	document.getElementById("ajax-loader").style.visibility = 'visible';
    document.getElementById("ajax-loader-image").style.visibility = 'visible';
    }
        
/// auto locate location ////
function getLocation() {
	loaderOn();
	if (navigator.geolocation) {
    	navigator.geolocation.getCurrentPosition(showPosition,showError);
	} else {
   	 	alert("Geolocation is not supported by this browser.");
   	}
}

function showPosition(position) {	
  	var gotLat = position.coords.latitude;
   	var gotLong = position.coords.longitude;		
  	returnAddress(gotLat, gotLong);
}

function showError(error) {
  	switch(error.code) {
    	case error.PERMISSION_DENIED:
      		alert("User denied the request for Geolocation");
      		loaderOff()
     	break;
    	case error.POSITION_UNAVAILABLE:
      		alert("Location information is unavailable.");
      		loaderOff()
      	break;
    	case error.TIMEOUT:
      		alert("The request to get user location timed out.");
      		loaderOff()
     	break;
    	case error.UNKNOWN_ERROR:
      		alert("An unknown error occurred.");
      	loaderOff()
      	break;
	}
}


//// conver lat/long to an address //////
function getAddress() {
 	loaderOn();
 	var gotLat = document.getElementById("_wppl_enter_lat").value;
    var gotLong = document.getElementById("_wppl_enter_long").value;
    returnAddress(gotLat,gotLong);  
}

///// main function to conver lat/long to address /////
function returnAddress(gotLat, gotLong) {
	geocoder = new google.maps.Geocoder();
	var latlng = new google.maps.LatLng(gotLat ,gotLong);
	geocoder.geocode({'latLng': latlng, 'region':   'es'}, function(results, status) {
      	if (status == google.maps.GeocoderStatus.OK) {
       	 	if (results[0]) {
         		addressf = results[0].formatted_address;
         		alert('address successfully returned');
         		showTab1();
        		document.getElementById("_wppl_lat").value = gotLat;
        		document.getElementById("_wppl_long").value = gotLong;
       			document.getElementById("_wppl_address").value = addressf;
       			document.getElementById("wppl-addresspicker").value = addressf;
       	         
         		var address = results[0].address_components;
         		//document.getElementById("retAddress").innerHTML = address;
				for ( x in address ) {
					if(address[x].types == 'street_number') {
						if(address[x].long_name != undefined) {
          					var streetNumber = address[x].long_name;
          					document.getElementById("_wppl_street").value = streetNumber;
          				}
          			}
          				
          			if (address[x].types == 'route') {
          				if(address[x].long_name != undefined) {
          					var streetName = address[x].long_name;
          					if(streetNumber != undefined) {
          						street = streetNumber + " " + streetName;
          						document.getElementById("_wppl_street").value = street;
          					} else {
          						street = streetName;
          						document.getElementById("_wppl_street").value = street;
          					}
          				}		
          			}
          				
          			if(address[x].types == 'locality,political') {
          				if(address[x].long_name != undefined) {
          					city = address[x].long_name;
          					document.getElementById("_wppl_city").value = city;
          				}
          			}
          			
          			if (address[x].types == 'administrative_area_level_1,political') {
          				if(address[x].long_name != undefined) {
          					var state = address[x].long_name;
          					document.getElementById("_wppl_state").value = state;
          				}
          					
          			}
          			
          			if (address[x].types == 'postal_code') {
          				if(address[x].long_name != undefined) {
          					var zipcode = address[x].long_name;
          					document.getElementById("_wppl_zipcode").value = zipcode;
          				}	
          			}
          			
          			if (address[x].types == 'country,political') {
          				if(address[x].short_name != undefined) {
          					var country = address[x].short_name;
          					document.getElementById("_wppl_country").value = country;
          				}		
          			}
				}
				
			document.getElementById("_wppl_enter_lat").value = "";
			document.getElementById("_wppl_enter_long").value = "";
        	}
        	loaderOff();
      	} else {
        	alert("Geocoder failed due to: " + status);
        	removefields();
        	loaderOff();
        
      	}
    });
} 

/// convert address to lat/long ////////
function getLatLong() {
	loaderOn();
    var street = document.getElementById("_wppl_street").value;
    var apt = document.getElementById("_wppl_apt").value;
    var city = document.getElementById("_wppl_city").value;
    var state = document.getElementById("_wppl_state").value;
    var zipcode = document.getElementById("_wppl_zipcode").value;
    var country = document.getElementById("_wppl_country").value;
    
    retAddress = street + ", " + apt + " " + city + ", " + state + " " + zipcode + ", " + country
    
    geocoder = new google.maps.Geocoder();
    geocoder.geocode( { 'address': retAddress}, function(results, status) {
      	if (status == google.maps.GeocoderStatus.OK) {
      		alert('Latitude / Longitude successfully returned');
        	retLat = results[0].geometry.location.lat();
        	retLong = results[0].geometry.location.lng();
       		document.getElementById("_wppl_lat").value = retLat;
        	document.getElementById("_wppl_long").value = retLong;
       		document.getElementById("_wppl_address").value = retAddress;
       		loaderOff();
    	} else {
        	alert("Geocode was not successful for the following reason: " + status);     
       		removefields();
       		loaderOff();
    	}
    });
}

///////////////////////////
function showTab1() {	
	if ( 'active' != jQuery(this).attr('class') ) {
		jQuery('div.lat-long-tab').hide();
		jQuery('div.address-tab').show();
		jQuery('div.extra-info-tab').hide();
		jQuery('div.metabox-tabs-div ul li.active').removeClass('active');
		jQuery('div.metabox-tabs-div ul li.address-tab').addClass('active');
	}
}

///////////////////////////
function showTab2() {	
	if ( 'active' != jQuery(this).attr('class') ) {
		jQuery('div.address-tab').hide();
		jQuery('div.extra-info-tab').hide();
		jQuery('div.lat-long-tab').show();
		jQuery('div.metabox-tabs-div ul li.active').removeClass('active');
		jQuery('div.metabox-tabs-div ul li.lat-long-tab').addClass('active');
	}
}

//////// TABS ///////////////
jQuery(document).ready(function() {
	// tab between them
	jQuery('.metabox-tabs li a').each(function(i) {
		var thisTab = jQuery(this).parent().attr('class').replace(/active /, '');
		if ( 'active' != jQuery(this).attr('class') )
			jQuery('div.' + thisTab).hide();

		jQuery('div.' + thisTab).addClass('tab-content');
 
		jQuery(this).click(function(){
			// hide all child content
			jQuery(this).parent().parent().parent().children('div').hide();
 
			// remove all active tabs
			jQuery(this).parent().parent('ul').find('li.active').removeClass('active');
 
			// show selected content
			jQuery('div.' + thisTab).show();
			jQuery('li.'+thisTab).addClass('active');
		});
	});

	jQuery('.heading').hide();
	jQuery('.metabox-tabs').show();
});