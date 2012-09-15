
<!-- Panel for editing of a list. -->
<h3 class="edit-name"><?php _e('Currently Editing:','people-list');?> <?php echo $list['title']; ?></h3>
<a href="options-general.php?page=people_lists&panel=manage&delete=<?php echo $list_id; ?>" class="delete-list"><?php _e('delete','people-list');?></a>
<p class="clear"><?php _e('List Shortcode:','people-list');?> [people-lists list=<?php echo $list['slug'];?>]</p>
<?php people_list_form( $list_id, $list ); ?>
		