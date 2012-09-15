/*
 * All this script does is
 * pre-fill the fields of a registration form
 * with their corresponding POST data
 *
 * by Mike Lopez
 */

jQuery(document).ready(function(){
	var fields = jQuery('table.wpm_registration .fld');
	jQuery.each(fields, function(i,obj){
		var tagName = obj.tagName;
		obj = jQuery(obj);
		var fldName = "";

        if(obj.attr('name')) {
            fldName = obj.attr("name").split('[')[0];
        }
   
        //checks if wlm_regform_values is set
       if (typeof wlm_regform_values !== "undefined") 
            var value = wlm_regform_values[fldName];
           
            
		if(value!=undefined){
			switch(obj.attr('type')){
				case 'radio':
					if(value == obj.val()){
						obj.attr('checked','checked');
					}
					break;
				case 'checkbox':
					if(typeof value == 'string'){
						if(value == obj.val()){
							obj.attr('checked','checked');
						}
					}else {
						if(jQuery.inArray(obj.val(), value) >= 0){
							obj.attr('checked','checked');
						}
					}
					break;
				case 'password':
					obj.val('');
					break;
				default:
					obj.val(value);
			}
		}
	});
	
})
