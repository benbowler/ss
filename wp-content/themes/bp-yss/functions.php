<?php
/**
 * BP-YSS child theme
 *
 * Child theme (see http://codex.wordpress.org/Theme_Development, http://codex.wordpress.org/Child_Themes
 * and http://codex.buddypress.org/theme-development/building-a-buddypress-child-theme/).
 *
 * @package YSS
 * @subpackage BP-YSS
 * @since 1.2
 */
 
/* Load styles from child theme */

if ( !function_exists( 'bp_dtheme_enqueue_styles' ) ) :
	function bp_dtheme_enqueue_styles() {}
endif;

if ( !function_exists( 'bp_dtheme_enqueue_styles' ) ) :
function bp_dtheme_enqueue_styles() {

	// You should bump this version when changes are made to bust cache
	$version = '20111109';

		// Register stylesheet of bp-dusk child theme
	wp_register_style( 'bp-dusk', get_stylesheet_directory_uri() . '/style.css', array(), $version );

	// Enqueue stylesheet of bp-dusk chid theme
	wp_enqueue_style( 'bp-dusk' );
}
add_action( 'wp_enqueue_scripts', 'bp_dtheme_enqueue_styles' );
endif;