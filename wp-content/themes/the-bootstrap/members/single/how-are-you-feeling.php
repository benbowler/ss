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
    //echo var_dump($user_id);
    $chart_data = $wpdb->get_results( "SELECT * FROM wp_wpsqt_all_results WHERE person_name='$user_id'" );

    $questions = array();
    $answers = array();

    foreach ($chart_data as $data) {
      $sections = unserialize($data->sections);
      //var_dump($sections);

      foreach ($sections[0][questions] as $key => $question) {
        $questions[$key] = $question[name];
      }

      foreach ($sections[0][answers] as $key => $answer) {
        $answers[$key] = $answer[given];

        //array_push($results[$key], $);
      }

      // Data
      //$name = $question[$key]->name;

      // $chart .= "['Week', 'Q1', 'Q2', 'Q3'],";

    }
    //var_dump($questions);
    //var_dump($answers);

    //php chart lib

    // don't forget to update the path here
    require_once('googlechartapi/lib/GoogleChart.php');

    $chart = new GoogleChart('lc', 800, 320);

    // manually forcing the scale to [0,100]
    $chart->setScale(0,10);

    // add one line
    $data = new GoogleChartData(array(2,5,5,6,7,8,9,8));
    $data->setLegend($questions[0]);
    $data->setColor('F69F38');
    $chart->addData($data);

    // add one line
    $data = new GoogleChartData(array(3,4,4,4,5,5,4,7));
    $data->setLegend($questions[1]);
    $data->setColor('00C0FF');
    $chart->addData($data);

    // add one line
    $data = new GoogleChartData(array(1,1,1,4,3,3,5,7));
    $data->setLegend($questions[2]);
    $data->setColor('FF75EE');
    $chart->addData($data);

    // add one line
    $data = new GoogleChartData(array(4,2,5,7,9,10,10,10));
    $data->setLegend($questions[3]);
    $data->setColor('FFDE00');
    $chart->addData($data);

    // customize y axis
    $y_axis = new GoogleChartAxis('y');
    $y_axis->setDrawTickMarks(false)->setLabels(array(1,2,3,4,5,6,7,8,9,10));
    $chart->addAxis($y_axis);

    // customize x axis
    $x_axis = new GoogleChartAxis('x');
    $x_axis->setDrawTickMarks(false)->setLabels(array(1,2,3,4,5,6,7,8));
    $chart->addAxis($x_axis);

    //$chart->getLegend();

    //header('Content-Type: image/png');
    echo  $chart->toHtml();
    //echo '<img src="data:image/png;base64,'.base64_encode($chart).'" alt="photo"><br>';

    ?>


				<?php do_action( 'bp_template_content' ); ?>

				<?php do_action( 'bp_after_member_body' ); ?>

			</div><!-- #item-body -->

			<?php do_action( 'bp_after_member_plugin_template' ); ?>

	</div><!-- #content -->

<?php get_footer( 'buddypress' ); ?>
