<!-- Panel for profile settings and for managing fields. -->
<h3><?php _e('Profile Settings','people-list');?></h3>
<h4><?php _e('Add a new text input fields to the','people-list');?> <a href="<?php echo admin_url('profile.php'); ?>"><?php _e('user profile','people-list');?></a></h4>
<?php people_list_field_form(); ?>
