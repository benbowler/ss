<?php

/* Enable before going live. */
// add_filter( 'bp_registration_needs_activation', '__return_false' );

//echo var_dump($SESSION);


function bbg_change_tabs() {
	global $bp;

	//$bp->bp_nav['profile']['name'] = 'New Profile Verbiage';
	$bp->bp_nav['activity']['name'] = 'Share You Training Activity';
	//$bp->bp_nav['friends']['name'] = 'New Friends Verbiage';
	//$bp->bp_nav['groups']['name'] = 'New Groups Verbiage';
	$bp->bp_nav['activity']['position'] = 10;
	$bp->bp_nav['how-are-you-feeling']['position'] = 20;
	$bp->bp_nav['identify-your-goals']['position'] = 25;
	$bp->bp_nav['profile']['position'] = 30;
	$bp->bp_nav['friends']['position'] = 40;
	//$bp->bp_nav['groups']['position'] = 40;
	//$bp->bp_nav['blogs']['position'] = 50;
	$bp->bp_nav['messages']['position'] = 60;
	$bp->bp_nav['settings']['position'] = 70;
}
add_action( 'bp_setup_nav', 'bbg_change_tabs', 999);

/* How Are You Feeling? */

function my_bp_nav_adder()
{
	bp_core_new_nav_item(
		array(
			'name' => __('How Are You Feeling?', 'buddypress'),
			'slug' => 'how-are-you-feeling',
			'position' => 75,
			'show_for_displayed_user' => true,
			'screen_function' => 'all_conversations_link',
			'item_css_id' => 'all-conversations'
		));
}
function all_conversations_link () {
	//add title and content here - last is to call the members plugin.php template
    add_action( 'bp_template_title', 'my_all_conversations_title' );
    add_action( 'bp_template_content', 'my_all_conversations_content' );
    bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/how-are-you-feeling' ) );
}

function my_all_conversations_title() {
	echo 'How Are You Feeling?';
}
function my_all_conversations_content() { 
}
add_action( 'bp_setup_nav', 'my_bp_nav_adder', 100 );

/* Identify You Goals */

function my_bp_nav_adder_1()
{
	bp_core_new_nav_item(
		array(
			'name' => __('Identify Your Goals', 'buddypress'),
			'slug' => 'identify-your-goals',
			'position' => 75,
			'show_for_displayed_user' => true,
			'screen_function' => 'all_conversations_link_1',
			'item_css_id' => 'all-conversations_1'
		));
}
function all_conversations_link_1 () {
	//add title and content here - last is to call the members plugin.php template
    add_action( 'bp_template_title', 'my_all_conversations_title_1' );
    add_action( 'bp_template_content', 'my_all_conversations_content_1' );
    bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugin' ) );
}

function my_all_conversations_title_1() {
	echo 'Identify Your Goals';
}
function my_all_conversations_content_1() { 
	http_redirect('http://google.com/');
	echo 'Add the loop here';
}
add_action( 'bp_setup_nav', 'my_bp_nav_adder_1', 101 );
