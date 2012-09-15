/*
 * Main JS file for Yoga Sport Science assessments...
 *
 * @author: Ben Bowler
 *
 */

$(document).ready(function() {
	// Quiz 1
	var pathname = window.location.pathname;
	if(/week-1\/how-are-you-feeling/i.test(pathname)) {
		$(".wpst_question_7 .wpsqt_likert_answer_1 input").attr('checked', true);
		$(".wpst_question_7").hide();
	}
	$('.quiz_4 .wpsqt_multiple_question input').click(function() {
		$(this).parent().parent().find('input').each(function() {
			$(this).attr('checked', false);
		});
		$(this).attr('checked', true);
	});

	// General Radio Buttons
	$('.wpsqt_likert_answer input').hide();
	$('.wpsqt_likert_answer label').click(function() {
		$(this).parent().parent().find('.selected').each(function() {
			$(this).removeClass('selected');
		});
		$(this).addClass('selected');
	});
	// Graphic Man (Red)
	$('.quiz_4 .wpst_question_37').load('/wp-content/themes/the-bootstrap/graphicman/graphicman.html', function() {

		$(this).show();
		$('.detail').hide();
		$(".wpst_question_35 textarea").attr("readonly","readonly");

		$("#graphic-man ul a").click(function(e) {
			var txt = $(e.target).text();

			$(".wpst_question_35 textarea").val(txt);

			e.preventDefault();
		});
	});
	// Graphic Man (Green)
	$('.quiz_4 .wpst_question_38').load('/wp-content/themes/the-bootstrap/graphicman/graphicman.html', function() {

		$(this).show();
		$('#graphic-man ul').removeClass('red').addClass('green');
		$('.detail').hide();
		$(".wpst_question_36 textarea").attr("readonly","readonly");

		$("#graphic-man ul a").click(function(e) {
			var txt = $(e.target).text();

			$(".wpst_question_36 textarea").val(txt);

			e.preventDefault();
		});
	});
});