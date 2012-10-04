<?php

/**
 * BuddyPress - Users Header
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

?>

<?php do_action( 'bp_before_member_header' ); ?>

<div id="item-header-avatar" class="span2">
	<a href="<?php bp_displayed_user_link(); ?>">

		<?php bp_displayed_user_avatar( 'type=full' ); ?>

	</a>
</div><!-- #item-header-avatar -->

<div id="item-header-content" class="span6">

	<?php 
	// Do shortcode in PHP
	//echo do_shortcode('[wppl_member_location]'); ?>

	<h2>
		<a href="<?php bp_displayed_user_link(); ?>"><?php bp_displayed_user_fullname(); ?></a>
	</h2>

	<span class="user-nicename">@<?php bp_displayed_user_username(); ?></span>
	<span class="activity"><?php bp_last_activity( bp_displayed_user_id() ); ?></span>

	<?php do_action( 'bp_before_member_header_meta' ); ?>

	<div id="item-meta">

		<?php if ( bp_is_active( 'activity' ) ) : ?>

			<div id="latest-update">

				<?php bp_activity_latest_update( bp_displayed_user_id() ); ?>

			</div>

		<?php endif; ?>

		<div id="item-buttons">

			<?php do_action( 'bp_member_header_actions' ); ?>

		</div><!-- #item-buttons -->

		<?php
		/***
		 * If you'd like to show specific profile fields here use:
		 * bp_member_profile_data( 'field=About Me' ); -- Pass the name of the field
		 */
		 do_action( 'bp_profile_header_meta' );

		 ?>

	</div><!-- #item-meta -->

</div><!-- #item-header-content -->
<?php if( bp_is_my_profile() ): ?>
<?php
	$user = wp_get_current_user();
	//var_dump($user);
	$role = $user->roles[0];
	echo $role;

	if($role == 'subscriber') {

	    $time = time() - strtotime($user->user_registered); // to get the time since that moment
	    $dif = ceil($time/604800);

	    if($dif > 8) {
	    	$weeks = "Bronze Complete!"; 
	    } else {
	    	$weeks = "Week $dif";
	    }
 	}
?>

<div id="item-header-week" class="span4">
	<h2><?php echo $weeks; ?></h2>
</div>
<?php endif; ?>

<?php do_action( 'bp_after_member_header' ); ?>

<?php do_action( 'template_notices' ); ?>