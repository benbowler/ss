jQuery(function() {
	//// show hide map ///
    jQuery('.wppl-help-btn').click(function(event){
    	event.preventDefault();
    	jQuery(this).closest("div").find(".wppl-help-message").slideToggle();
    
    }); 
});


			


