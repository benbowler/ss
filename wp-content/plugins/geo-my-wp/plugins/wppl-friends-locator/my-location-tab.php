<div id="wppl-bp-wrapper">
	<div id="bp-location-form-wrapper">
		<form name="addLocation" type="post" action="" id="wppl-location-form">
			
			<div id="saved-data">
				<h2>This is your saved location (this box must contain an address, latitude and longitude).</h2>
				<div class="single-input-fields">
					<label for="address">Address:</label>
					<input name="wppl_address" id="wppl_address" value="<?php echo $member_info[0]['address']; ?>"  type="text" size="40" disabled/>
 				</div>
 				<div class="single-input-fields">
					<label for="address">Latitude:</label>
					<input name="wppl_lat" id="wppl_lat" value="<?php echo $member_info[0]['lat']; ?>" type="text" disabled />
 				</div>
 				<div class="single-input-fields">
					<label for="address">Longitude:</label>
					<input name="wppl_long" id="wppl_long" value="<?php echo $member_info[0]['long']; ?>" type="text" disabled />
				</div>
				
				<div class="single-input-fields">
					<input type="button" id="edit-user-location-btn" class="bp-btn" style="float:left" value="Edit Location" />
					<div id="wppl-ajax-loader-bp" style="float:left; display:none"><img src="<?php echo plugins_url('images/ajax-loader.gif', __FILE__); ?>" id="wppl-loader-image" alt="" /> Loading</div>
					<div id="wppl-bp-feedback"></div>						
					<input type="button" id="remove-address-btn" style="float:right;" class="bp-btn" value="Delete Location" />	
 				</div>
 				
 			</div><!-- saved data -->
 			<div id="edit-user-location">
 				<h2>Enter your address below:</h2>
 				
				<div class="metabox-tabs-div">
					<ul class="metabox-tabs" id="metabox-tabs">
						<li class="active address-tab" style="width:70px"><a onclick="showAddressTab()" class="active" href="javascript:void(null);">Address</a></li>				
					</ul>
					
					<div class="address-tab">	
 						<div class="single-input-fields">
							<label for="street">Street:</label>
							<input name="wppl_street" id="wppl_street" type="text" />
						</div>
						<div class="single-input-fields">
							<label for="apt">Apt/Suit:</label>
							<input name="wppl_apt" id="wppl_apt" type="text" />
 						</div>
 				
 						<div class="single-input-fields">
							<label for="city">City:</label>
							<input name="wppl_city" id="wppl_city" type="text" value="<?php echo $member_info[0]['city']; ?>" />
 						</div>
 						
 						<div class="single-input-fields">
							<label for="state">State:</label>
							<input name="wppl_state" id="wppl_state" type="text" value="<?php echo $member_info[0]['state']; ?>" />
 						</div>
 				
 						<div class="single-input-fields">
							<label for="zipcode">Zipcode:</label>
							<input name="wppl_zipcode" id="wppl_zipcode" type="text" value="<?php echo $member_info[0]['zipcode']; ?>" />
						</div>
				
						<div class="single-input-fields">
							<label for="country">Country:</label>
							<input name="wppl_country" id="wppl_country" type="text" value="<?php echo $member_info[0]['country']; ?>" />
 						</div>
 						<!--<div><input type="button" class="bp-btn" value="Get Lat/Long" onClick="getLatLong();"></div>-->
 					</div><!-- address tab -->
 				
 					<input type="hidden" name="action" value="addLocation"/>
				
				</div><!-- meta tabs wrapper -->
				
				<div class="single-input-fields">
 					<input type="button" id="wppl-location-submit" value="Save Location" class="bp-btn" style="float:left;">
 				</div>
			</div><!-- edit users location -->
		</form>
	</div><!-- location form wrapper -->

</div><!-- wppl bp wrapper -->
   
<script type="text/javascript">
	
jQuery(document).ready(function() {
				
		/// show hide edit user's location//// 
	jQuery('#edit-user-location-btn').click(function(event){
    	event.preventDefault();
    	jQuery("#edit-user-location").slideToggle();
    }); 
    
    /// convert address to lat/long ////////
	function getLatLong() {
   	 	var street = document.getElementById("wppl_street").value;
    	var apt = document.getElementById("wppl_apt").value;
    	var city = document.getElementById("wppl_city").value;
    	var state = document.getElementById("wppl_state").value;
    	var zipcode = document.getElementById("wppl_zipcode").value;
    	var country = document.getElementById("wppl_country").value;
    
    	retAddress = street + " " + apt + " " + city + " " + state + " " + zipcode + " " + country
    
    	geocoder = new google.maps.Geocoder();
    	geocoder.geocode( { 'address': retAddress}, function(results, status) {
      		if (status == google.maps.GeocoderStatus.OK) {
      		
        		retLat = results[0].geometry.location.lat();
        		retLong = results[0].geometry.location.lng();
       			document.getElementById("wppl_lat").value = retLat;
        		document.getElementById("wppl_long").value = retLong;
       			document.getElementById("wppl_address").value = retAddress;
       			saveLocation();
    	} else {
        	alert("Location could not be saved. Please check the address fields again.Geocode was not successful for the following reason: " + status);     
       		deleteLocation();
    	}
    });
	} 
     
    ////// save location //////////
   	function saveLocation() {
  	 	
  	 	if(jQuery("#edit-user-location").is(":visible")) { jQuery("#edit-user-location").slideToggle(); }
		
		jQuery("#wppl-ajax-loader-bp").show();
		jQuery("#wppl-bp-feedback").html('');
		jQuery("#wppl-bp-feedback").css('opacity',1);
   		jQuery("#saved-data :text").removeAttr('disabled'); 
    	
		var newLocation = jQuery('#wppl-location-form').serialize();
 		
		jQuery.ajax({
			type:"POST",
			url: "/wp-admin/admin-ajax.php",
			data: newLocation,
			success:function(data){
				setTimeout(function() {
					jQuery("#wppl-bp-feedback").html(data);
    				jQuery("#wppl-ajax-loader-bp").hide();
    				jQuery("#saved-data :text").attr('disabled', 'true'); 
    				
   				},500);
   				setTimeout(function() {
					jQuery("#wppl-bp-feedback").animate({opacity:0})
   				},2500);
			}
		});
		
		return false;
 	};
 	
 	////// delete location  //////	
 	function deleteLocation() {
  	 	document.getElementById("wppl_lat").value = "";
    	document.getElementById("wppl_long").value = "";
   	 	document.getElementById("wppl_address").value = "";
		document.getElementById("wppl_street").value = "";
    	document.getElementById("wppl_apt").value = "";
    	document.getElementById("wppl_city").value = "";
		document.getElementById("wppl_state").value = "";
		document.getElementById("wppl_zipcode").value = "";
		document.getElementById("wppl_country").value = "";
		saveLocation();	
 	};
 	
 	//// when click on save button /////
  	jQuery('#wppl-location-submit').click(function(){
  		jQuery("#saved-data :text").removeAttr('disabled'); 
  		getLatLong();
  	});
  
  	//// remove address button /////
  	jQuery('#remove-address-btn').click(function(){
    	deleteLocation();	
    });
 	
	function hideEdit() { jQuery("#edit-user-location").hide(); }
	window.onload = hideEdit;
});

</script>