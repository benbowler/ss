<?php

/**
 * BuddyPress - Users Plugins
 *
 * This is a fallback file that external plugins can use if the template they
 * need is not installed in the current theme. Use the actions in this template
 * to output everything your plugin needs.
 *
 * @package BuddyPress
 * @subpackage bp-default
 */

?>

<?php get_header( 'buddypress' ); ?>

	<div id="content" class="span12">

			<?php do_action( 'bp_before_member_plugin_template' ); ?>

			<div id="item-header">

				<?php locate_template( array( 'members/single/member-header.php' ), true ); ?>

			</div><!-- #item-header -->

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
					<ul>

						<?php bp_get_displayed_user_nav(); ?>

						<?php do_action( 'bp_member_options_nav' ); ?>

					</ul>
				</div>
			</div><!-- #item-nav -->

			<div id="item-body" role="main">

				<?php do_action( 'bp_before_member_body' ); ?>

				<div class="item-list-tabs no-ajax" id="subnav">
					<ul>

						<?php bp_get_options_nav(); ?>

						<?php do_action( 'bp_member_plugin_options_nav' ); ?>

					</ul>
				</div><!-- .item-list-tabs -->

				<h2><?php do_action( 'bp_template_title' ); ?></h2>


	 <div id="chart_div" style="width: 900px; height: 500px;"></div>

    <?php
    $user = wp_get_current_user();
    $user_id = $user->data->user_login;
    echo var_dump($user_id);
    $chart_data = $wpdb->get_results( "SELECT * FROM wp_wpsqt_all_results WHERE person_name='$user_id'" );
    echo var_dump($chart_data);
    ?>

    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">

      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Week', 'Sales', 'Expenses'],
          ['1',  1000,      400],
          ['2',  1170,      460],
          ['3',  660,       1120],
          ['4',  660,       1120],
          ['5',  1000,      400],
          ['6',  1170,      460],
          ['7',  660,       1120],
          ['8',  660,       1120]
        ]);

        var options = {
          title: 'How Are You Feeling'
        };

        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>


				<?php do_action( 'bp_template_content' ); ?>

				<?php do_action( 'bp_after_member_body' ); ?>

			</div><!-- #item-body -->

			<?php do_action( 'bp_after_member_plugin_template' ); ?>

	</div><!-- #content -->

<?php get_footer( 'buddypress' ); ?>
