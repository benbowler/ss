<?php get_header() ?>

	<div id="content">
		<div class="padder">
		
				<div id="item-header">
					<?php bp_maps_locate_template( array( 'maps/single/map-header.php' ), true ) ?>
				</div>
				
				<div id="item-nav">
					<div class="item-list-tabs no-ajax" id="object-nav">
						<ul>
							<?php bp_get_options_nav() ?>

							<?php do_action( 'bp_marker_options_nav' ) ?>
						</ul>
					</div>
				</div>

				<div id="item-body">
					<?php do_action( 'bp_before_marker_body' ) ?>


					<?php do_action( 'bp_after_marker_body' ) ?>
				</div>
		
			<?php bp_maps_locate_template( array( 'maps/map.php' ), true ) ?>
		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>
