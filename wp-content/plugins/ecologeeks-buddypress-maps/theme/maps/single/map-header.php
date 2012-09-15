<?php do_action( 'bp_before_map_header' ) ?>

<div id="item-actions">
	<?php do_action( 'bp_map_header_actions' ) ?>
</div><!-- #item-actions -->


<div id="item-header-content">
	<h2><?php bp_map_title() ?></h2>
	<span class="activity"><?php bp_maps_map_last_active(); ?></span>

	<?php do_action( 'bp_before_map_header_meta' ) ?>

	<div id="item-meta">
		<?php do_action( 'bp_map_header_meta' ) ?>
	</div>
</div><!-- #item-header-content -->

<?php do_action( 'bp_after_map_header' ) ?>

<?php do_action( 'template_notices' ) ?>