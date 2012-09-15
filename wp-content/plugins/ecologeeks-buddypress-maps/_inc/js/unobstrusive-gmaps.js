//code idea by August Lilleaas
window.bp_maps_GoogleMaps = {
  initialize: function(map_div){
	bp_maps_GoogleMaps.loadInteractiveMap();
	this.map_id=map_div.attr('rel');

	this.load_link=map_div.find('.bp_maps_map_add_marker a');
	this.load_link.addClass('loading');
  },
  
  loadInteractiveMap: function(){
	var url = [
	  "http://maps.google.com/maps/api/js?",
	  "sensor=false",
	  "&callback=bp_maps_GoogleMaps.googleMapsLibraryLoaded"
	];

	jQuery.getScript(url.join(""));
  },
  
  googleMapsLibraryLoaded: function(){
	bp_maps_google_is_init=true;
	this.load_link.removeClass('loading');
	//LOAD MAP
	eval('bp_maps_'+this.map_id+'_init()');
	//CENTER MAP
	bp_maps_reset_view(this.map_id,'all');
	//LOAD GEOCODER
	bp_maps_init_geocoder();

  }
}

