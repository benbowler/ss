<?php

// pagination function //	
function pagination_links($pages, $per_page) {
	$page_n = $_GET['pagen'];
	//$pages = $total_rows[0]/$per_page; //get number of pages //
	if ($pages > 1) {
		echo '<div class="wppl-pagination-holder">';
			if ($page_n > 0) {		
				echo '<div class="prev-btn"><a href="' .add_query_arg('pagen', ($page_n - 1)) .'" onclick="document.wppl_form.submit();">&#171;</a></div>';
			} 
			echo '<div class="wppl-pagination">';
				for($i=0; $i< $pages; $i++): 
					$num = $i + 1;
    				echo '<div '; if( $page_n+1 == $num ) :; echo 'class="current"'; endif; echo ' id="page-' . $num . '"><a href="' .add_query_arg('pagen',$i) .'" onclick="document.wppl_form.submit();">' . $num .  '</a></div>';
				endfor; 
			echo '</div>';
			if ($page_n < ($pages -1)) {
				echo '<div class="next-btn"><a href="' .add_query_arg('pagen', ($page_n + 1)) .'" onclick="document.wppl_form.submit();">&#187;</a></div>';
		 	}			
		echo '</div>';
	} // if pages > 1 //
} // pagination links //
			
/// get random results for featured posts ///
function array_random_assoc($total_results, $random_featured_count) {
    $keys = array_keys($total_results);
    shuffle($keys);
    $featured_posts_within = array();
    for ($i = 0; $i < $random_featured_count; $i++) {
    	if ($total_results[$keys[$i]]) {
    		$featured_posts_within[$keys[$i]] = $total_results[$keys[$i]];
    	}	
    }
   return $featured_posts_within;
}

//// DROPDOWN TAXONOMIES FOR THE SEARCH FORM /////
function custom_taxonomy_dropdown($tax_name) {
$args = array(
		'taxonomy' 			=> $tax_name,
		'hide_empty' 		=> 1,
		'depth' 			=> 10,
		'hierarchical' 		=>	1,
		'show_count'        => 0,
		'id' 				=> $tax_name . '_id',
		'name' 				=> $tax_name,
		'selected' 			=> $_GET[$tax_name],
		'show_option_all'   => ' - All - ',
		);		
wp_dropdown_categories($args); 
} 

///// CONVERT ADDRESS TO COORDINATES //////
function ConvertToCoords($address) {	

	$ch = curl_init();	
    $rip_it = array( " " => "+", "," => "", "?" => "", "&" => "", "=" => "" , "#" => "");
    
    //// MAKE SURE ADDRES DOENST HAVE ANY CHARACTERS THAT GOOGLE CANNOT READ //
    $address = str_replace(array_keys($rip_it), array_values($rip_it), $address);
    
    //// GET THE XML FILE WITH RESUALTS
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/2.0 (compatible; MSIE 3.02; Update a; AK; Windows 95)");
	curl_setopt($ch, CURLOPT_HTTPGET, true);
	curl_setopt($ch, CURLOPT_URL, 'http://maps.google.com/maps/geo?output=xml&q=' . $address  );
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
	$got_xml = curl_exec($ch);    
    //// PARSE THE XML FILE //////////////
	$xml = new SimpleXMLElement($got_xml);	

	//// GET THE LATITUDE/LONGITUDE FROM THE XML FILE ///////////////
	list($longitude, $latitude) = explode(",", $xml->Response->Placemark->Point->coordinates);	
	global $lat;
	global $long;
	$lat = "$latitude";
	$long = "$longitude";		
}

// when post status changes - change it in our table as well //
function filter_transition_post_status( $new_status, $old_status, $post ) { 
    global $wpdb;
    $wpdb->query( 
        $wpdb->prepare( 
          "UPDATE " . $wpdb->prefix . "places_locator SET post_status=%s WHERE post_id=%d", 
           $new_status,$post->ID
         ) 
     );
}
add_action('transition_post_status', 'filter_transition_post_status', 10, 3);

// delete info from our database after post was deleted //
function delete_address_map_rows()	{
	global $wpdb, $post;
	$wpdb->query(
		$wpdb->prepare( 
			"DELETE FROM " . $wpdb->prefix . "places_locator WHERE post_id=%d",$post->ID
		)
	);
}
add_action('before_delete_post', 'delete_address_map_rows'); 	
?>
