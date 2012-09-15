
<!-- Panel for managing of lists. -->
<h3><?php _e('View all lists','people-list');?></h3>
<ul>
	<?php foreach($people_list_option['lists'] as $index => $list): ?>
		<li>
			<a href="options-general.php?page=people_lists&panel=edit&list_id=<?php echo $index ;?>"><?php echo $list['title']; ?></a>
		</li>
	<?php endforeach; ?>
</ul>
