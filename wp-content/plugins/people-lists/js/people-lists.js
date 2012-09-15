
/**
 * save_field_info function.
 * Description: Saving the field information to the database and serializing it to maintain the sorted order.
 */
function save_field_info(){ 
	jQuery("#ajax-response2").show();	
	field_info = jQuery("#profile-field-form").serialize();
	var data = {
		action : 'people_settings_save',
		field_info : field_info
	};
	
	jQuery.post(ajaxurl, data, function(response) {
		jQuery("#ajax-response2").hide();
		jQuery("#add-field-button").removeAttr("disabled");
	});
}

/**
 * make_field_slug function.
 * Description: Creating a slug based on the field name that user's input.
 */
function make_field_slug(fieldName){
	fieldName = fieldName.toLowerCase();
	var   accents={a:/\u00e1/g,e:/u00e9/g,i:/\u00ed/g,o:/\u00f3/g,u:/\u00fa/g,n:/\u00f1/g}
    for (var i in accents) fieldName = fieldName.replace(accents[i],i);
	var fieldName_hyphens = fieldName.replace(/\s/g,'_');
	var finishedslug = fieldName_hyphens.replace(/[^a-zA-Z0-9\-]/g,'_');
    finishedslug = finishedslug.toLowerCase();
    return "people_lists_"+finishedslug;
}

jQuery(function($) {				
	
	
	// Initialize jQuery Sortable for lists.
	$("ul.droptrue").sortable({
		connectWith: 'ul',
		placeholder: 'ui-state-highlight'
	});

	// Initializing and storing attributes upon "submit-people-list" button click by user.
	$('#submit-people-list').live( "click", function(){
   	
    if(!$('#submit-people-list').attr("disabled")) {
    	
      	var groupList 	= $("#sortable2").sortable( "serialize", {attribute: "id"});
 
     	var people_list_form = $('#people-list-form').serialize(); // serialized data is so much easier to deal with
     	     	
        var data = {
			action : 'people_list_save',
			list : groupList,
			form: people_list_form,
		};
		if($('#list-title').val() != '') 
		{
			$(this).attr("disabled",true);
			$("#ajax-response").show();
			
			// Write values in data to the database and open manage panel thereafter.
			$.post(ajaxurl, data, function(response) {
				if($.trim(response) == "new")
				{
					window.location = "options-general.php?page=people_lists&panel=manage";
				}
				if( $('#template-text').val() =='' )
    		 		$('#template-text').val( $('#default-template').html());
				
				$("#ajax-response").hide();
				
				if($.trim(response) != "new")
					$("#submit-people-list").removeAttr("disabled");
			});
		} else {
			alert('Please enter a list name.');
		}
	}
		
	return false;
	});
	
	// Initialize jQuery Sortable for fields and save upon user updating the order of the list.
	$("#sortable-profile-field").sortable({
		connectWith: 'ul',
		placeholder: 'ui-state-highlight',
		update: function(event, ui) {save_field_info() }

	});
	
	//Initializing and storing attributes upon "add-field-button" click by user.
	$('#add-field-button').live( "click", function(){
	
      if(!$('#add-field-button').attr("disabled")) {
		if($.trim($('#contact-info-field').val()) != ''){ 

			$(this).attr("disabled",true);
			$("#ajax-response2").show();
			
			// Get the unique-field from what the user entered.
			var userInput = $.trim($('#contact-info-field').val());
			
			$('#contact-info-field').val(' ');
			var madeSlug = make_field_slug(userInput);

			// Loop though all the hidden elements inside 'sortable-profile-field' and check that the value doesn't
			// equal to the unique-file autocreated else correct it with dash followed by the appropriate #.
			var fieldSlug = madeSlug;
			var flag = true;
			var counter = 1;
			var inputValues = $("#sortable-profile-field input");
			while (flag){
				flag = false;
				inputValues.each(function() {		
					if ($(this).val() == madeSlug){	
						flag = true;
						madeSlug = make_field_slug(userInput) + "_" + counter;					
						counter++;	
					}
					else{
						fieldSlug = madeSlug;
					}										
				});
			}
		 
		 html = '<li class="ui-state-default" >' + userInput + '<span>Template Code: %' + fieldSlug + '%</span>' + '<span><a href="#" class="delete-field-link">Delete</a></span>';
		 html += '<input type="hidden" value="' + fieldSlug + '" name="field_slug_list[]" />';
		 html += '<input type="hidden" value="' + userInput + '" name="field_name_list[]" /></li>';
		
		 $("#sortable-profile-field").append(html);
			save_field_info();
			
		} else {
			alert('Please enter a field name.');
		}	
	  }	
	});
	
	// Deleting a field entry for jQuery Sortable list functionality.
	$(".delete-field-link").live("click", function(){
		if (confirm("Are you sure you want to delete this field?")){
			$(this).parent().parent().remove();
			save_field_info();
		}	
	});
	
	// Pin and Unpin the Selected Users List for easy dragging and dropping useful for larger user lists.
    $("#selected-lock").toggle(function(){
    	$(this).html("Unpin");
    	$("#selectedUserList").addClass("fixed");
    	return false;
    },function(){
    	$(this).html("Pin");
    	$("#selectedUserList").removeClass("fixed");
    	return false;
    });
    
    // List deletion on click & confirmation.
    $(".delete-list").click(function(){
    	return confirm("Are you sure you want to delete the list?");
    });
    
    // Enable template-area to use jQuery tabs functionality. 	
	$(".template-area").tabs();	
	
	
    // Toggling Template Textarea on click
    $('#template-link').click(function(){
    	if( $('#template-text').val() =='' )
    		 $('#template-text').val( $('#default-template').html());
    	
       	$(this).siblings(".template-info").slideToggle(); 
	return false;
    });
 });
 
 /* --- End of File --- */