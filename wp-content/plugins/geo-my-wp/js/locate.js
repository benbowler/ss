jQuery(window).load(function(){ 
	jQuery('.map-loader').fadeOut(1500); 
});

jQuery(function() {
	//// scroll to top of page ////
	jQuery(window).scroll(function() {
		if(jQuery(this).scrollTop() != 0) {
			jQuery('#wppl-go-top').fadeIn();	
		} else {
			jQuery('#wppl-go-top').fadeOut();
		}
	});
 
	jQuery('#wppl-go-top').click(function() {
		jQuery('body,html').animate({scrollTop:0},800);
	});	
	
});

submitNo = 1;
if (getCookie('wppl_city') == undefined) {
	 if ( (getCookie('wppl_asked_today') != "yes") && (autoLocate == 1) ) {
		submitNo = 0;
		setCookie("wppl_asked_today","yes",1);
		getLocation();
	}
}



function getLocationNoSubmit() {
	submitNo = 0;
	getLocation();
}
	
function foundYouMessage(){
	jQuery('html').prepend('<div id="wppl-justwait"></div>');
	var locateMessage=document.getElementById('wppl-justwait');
	locateMessage.style.display = "";
	locateMessage.innerHTML = '<div id="wppl-wait-within"><div id="wppl-wait-close"><a href="#" onclick="removeMessage();" style="text-decoration:none;">X</a></div><br /><div id="wppl-wait-message"><p>Just one more moment please. Getting your current location...</p><br /></div></div>';
}

//// set cookies ////
function setCookie(name,value,exdays) {
	var exdate=new Date();
	exdate.setTime(exdate.getTime() + (exdays*24*60*60*1000));
	var cooki=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	document.cookie=name + "=" + cooki + "; path=/";
}

function getCookie(c_name) {
	var i,x,y,ARRcookies=document.cookie.split(";");
	
	for (i=0;i<ARRcookies.length;i++) {
  		x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
 		y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
  		x=x.replace(/^\s+|\s+$/g,"");
  		if (x==c_name) {
    		return unescape(y);
    	}
  	}
}

function removeMessage() {
	jQuery('div').remove('#wppl-justwait');
	}

function getLocation() {
	if (navigator.geolocation) {
    	navigator.geolocation.getCurrentPosition(showPosition,showError);
    	if(locateMessage == 1) {
    		foundYouMessage();
    	}
	} else {
   	 	alert("Geolocation is not supported by this browser.");
  	}

	function showPosition(position) {
  		var geocoder = new google.maps.Geocoder();
  		geocoder.geocode({'latLng': new google.maps.LatLng(position.coords.latitude, position.coords.longitude)}, function (results, status) {
        	if (status == google.maps.GeocoderStatus.OK) {
          		
          		if (results[0]) {
					var address = results[0].address_components;
					for ( x in address ) {
          				if(address[x].types == 'locality,political') {
          					city = address[x].long_name;
          				}
          				if (address[x].types == 'administrative_area_level_1,political') {
          					state = address[x].short_name;
          				}
          				if (address[x].types == 'postal_code') {
          					zipcode = address[x].long_name;
          				}
					}
					cityState = city +", "+state;
			
					setCookie("wppl_lat",position.coords.latitude,7);
					setCookie("wppl_long",position.coords.longitude,7);
					setCookie("wppl_zipcode",zipcode,7);
					setCookie("wppl_city",cityState,7);
					
					if(locateMessage == 1) {
						document.getElementById("wppl-wait-message").innerHTML="<p id='wppl-found-you'>We found you at " + cityState + "</p>";
					}
					jQuery(".wppl-address").val(cityState);
				
					if(submitNo == 0) {
						setTimeout(function() {
							window.location.reload();	
						},1000);
					} else {
						setTimeout(function() {
	 						var btnSubmit = document.getElementById("wppl-submit-"+formId);
							btnSubmit.click();
						},1000);
					}
				}
        	} else {
          		alert('Geocoder failed due to: ' + status);
        	}
      	});
  	}

	function showError(error) {
		switch(error.code) {
    		case error.PERMISSION_DENIED:
      			document.getElementById("wppl-wait-message").innerHTML="<p id='wppl-found-you-not'>User denied the request for Geolocation.</p>"; 		
      			setTimeout(function() {
      				setCookie("wppl_denied","denied",1);
      				removeMessage();
      			},1000)
      		break;
    		case error.POSITION_UNAVAILABLE:
      			document.getElementById("wppl-wait-message").innerHTML="<p id='wppl-found-you-not'>Location information is unavailable.</p>";
      			setTimeout(function() {
      				setCookie("wppl_denied","denied",1);
      				removeMessage();
      			},1000)
      		break;
    		case error.TIMEOUT:
      			document.getElementById("wppl-wait-message").innerHTML="<p id='wppl-found-you-not'>The request to get user location timed out.</p>";
      			setTimeout(function() {
      				setCookie("wppl_denied","denied",1);
      				removeMessage();
      			},1000)
      		break;
    			case error.UNKNOWN_ERROR:
      			document.getElementById("wppl-wait-message").innerHTML="<p id='wppl-found-you-not'>An unknown error occurred.</p>";
      			setTimeout(function() {
      				setCookie("wppl_denied","denied",1);
      				removeMessage();
      			},1000)
      		break;
		}
	}
}
  
///// get user current lucation shortcode/widget /////
jQuery(function() {
    jQuery('#location-form-btn').click(function(event){
    	event.preventDefault();
    	jQuery("#wppl-show-location-form").slideToggle(); 
    }); 
});
	
jQuery('.wppl-location-form').submit(function() {
	retAddress = jQuery('.wppl-user-address').val();
	getUserLocation(retAddress);
});
	
function getUserLocation (retAddress) {
	geocoder = new google.maps.Geocoder();
	var city  	= '';
	var state 	= '';
	var zipcode = '';
	
   	geocoder.geocode( { 'address': retAddress}, function(results, status) {
      	if (status == google.maps.GeocoderStatus.OK) {
      		gotLat = results[0].geometry.location.lat();
      		gotLong = results[0].geometry.location.lng();
       	 	setCookie('wppl_lat',gotLat,7);
        	setCookie('wppl_long',gotLong,7);
        }	
        geocoder.geocode({'latLng': new google.maps.LatLng(gotLat, gotLong)}, function(results, status) {
      		if (status == google.maps.GeocoderStatus.OK) {
        		var address = results[0].address_components;
					for ( x in address ) {
          				if(address[x].types == 'locality,political') {
          					city = address[x].long_name + ', ';
          				}
          				if (address[x].types == 'administrative_area_level_1,political') {
          					state = address[x].short_name;
          				}
          				if (address[x].types == 'postal_code') {
          					zipcode = address[x].long_name;
          					setCookie('wppl_zipcode',zipcode,7);
          				}
					}
					setCookie('wppl_city',city + state,7);
					window.location.reload();
    		} else {
        		alert("Geocode was not successful for the following reason: " + status);     
    		}
   		}); 
   	}); 
}
  