// enable the fields of the new shortcode and save to form to create the shortcode ///
jQuery(document).ready(function() {  
	jQuery("table tr td.wppl-premium-version-only :input").attr('disabled', 'true'); 
	jQuery("table tr td span.wppl-premium-message").html('Premium version only'); 
	
    jQuery("#create-new-shortcode").click(function() {    	
    	jQuery("#wppl-new-shortcode-fields :input").removeAttr('disabled'); 
        jQuery("#shortcode-submit").submit();
        return false;
     });  
});  
 		
 	function editElement(element) {
    jQuery(element).toggle();
	}

	function removeElement(element) {  
    jQuery(element).remove();
    jQuery("#shortcode-submit").submit();
    } 
    
////////////////////////////

function change_it(nam,i_d) {
	
	n = jQuery("input[name='" + nam + "']:checked").length;
	//alert(i_d);
	if(n == 1) { 
    	var sel = jQuery("input[name='" + nam + "']:checked").val(); 
    	//alert(sel);
    	jQuery('#' + sel + '_cat_' + i_d).css('display','block');
    	//jQuery('#' + sel + '_cat_' + i_d).animate({backgroundColor: '#F0DADA'}, 'fast');
    	jQuery('#posts-checkboxes-' + i_d).css('background','#F9F9F9');
    } else {
    	jQuery('#posts-checkboxes-' + i_d).css('background','#F9F9F9');
    	jQuery('.taxes-' + i_d + ' div').css('display','none');
    	jQuery('.taxes-' + i_d + ' input').attr('checked',false);
		
    }    
    if(n == 0) {
    	jQuery('#posts-checkboxes-' + i_d).animate({backgroundColor: '#FAA0A0'}, 'fast');
    } 	
};

/////////////////////
jQuery(document).ready(function() { 
	
	jQuery.each(hideNot, function(index, valueNot) {
  		jQuery('table#shortcode-table-' + valueNot + ' tr.friends-not input').attr('disabled','true')
  		jQuery('table#shortcode-table-' + valueNot + ' tr.friends-not select').attr('disabled','true')
  		jQuery('table#shortcode-table-' + valueNot + ' tr.friends-not').hide()
  		//jQuery('table#shortcode-table-' + valueNot).css("background-color", '#F5ECEB');
  		//jQuery('table#shortcode-header-table-' + valueNot).css("background-color", '#F5ECEB');		
	});
	
	jQuery.each(hideYes, function(index, valueYes) {
  		jQuery('table#shortcode-table-' + valueYes + ' tr.friends-yes input').attr('disabled','true')
  		jQuery('table#shortcode-table-' + valueYes + ' tr.friends-yes select').attr('disabled','true')
  		jQuery('table#shortcode-table-' + valueYes + ' tr.friends-yes').hide()
	});
	
	jQuery('.friends-check-btn').change(function(){
		var tableId = this.id;
   	 	if (this.checked) 
   	 		{
   	 		 	jQuery('table#' + tableId + ' tr.friends-not input').attr('disabled','true')
   	 		 	jQuery('table#' + tableId + ' tr.friends-not select').attr('disabled','true')
   	 		 	jQuery('table#' + tableId + ' tr.friends-not').hide()
   	 		 	
   	 		 	jQuery('table#' + tableId + ' tr.friends-yes input').removeAttr('disabled');
   	 		 	jQuery('table#' + tableId + ' tr.friends-yes select').removeAttr('disabled');
    	 		jQuery('table#' + tableId + ' tr.friends-yes').show()
    	 		jQuery("table tr td.wppl-premium-version-only :input").attr('disabled', 'true'); 
    	 		//jQuery('table#' + tableId).css("background-color", '#F5ECEB');
    		
    		} else {
    	 		jQuery('table#' + tableId + ' tr.friends-not input').removeAttr('disabled');
    	 		jQuery('table#' + tableId + ' tr.friends-not select').removeAttr('disabled');
    	 		jQuery('table#' + tableId + ' tr.friends-not').show()
    	 		
    	 		//jQuery('table#' + tableId).css("background-color", '#f9f9f9');
    	 		jQuery('table#' + tableId + ' tr.friends-yes input').attr('disabled','true')
    	 		jQuery('table#' + tableId + ' tr.friends-yes select').attr('disabled','true')
   	 		 	jQuery('table#' + tableId + ' tr.friends-yes').hide()
    	 	}
	}) 
})

