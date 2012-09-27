<?php
/** footer.php
 *
 * @author		Konstantin Obenland
 * @package		The Bootstrap
 * @since		1.0.0	- 05.02.2012
 */

				tha_footer_before();
				?>
				<footer id="colophon" role="contentinfo" class="span12">
					<?php tha_footer_top(); ?>
					<div id="page-footer" class="row-fluid clearfix">
						<?php dynamic_sidebar( 'Footer Widgets' ); ?>
						
						<div id="copyright" >
							&copy; Yoga Sport Science <?php echo date('Y'); ?>. All Rights Reserved.
						</div>
					</div><!-- #page-footer .row .clearfix -->

					<?php tha_footer_bottom(); ?>
				</footer><!-- #colophon -->
				<?php tha_footer_after(); ?>
			</div><!-- #page -->
			<div id="site-generator">
				<a href="http://benbowler.com/" alt="Ben Bowler - Web Geek">BB</a>
			</div>
		</div><!-- .container -->
	<!-- <?php printf( __( '%d queries. %s seconds.', 'the-bootstrap' ), get_num_queries(), timer_stop(0, 3) ); ?> -->
	<script src="<?php echo get_template_directory_uri(); ?>/js/jquery.min.js" type="text/javascript"></script><!-- Include main js file --> 
	<script src="<?php echo get_template_directory_uri(); ?>/js/jquery.min.js" type="text/javascript"></script><!-- Include main js file --> 
	<script src="<?php echo get_template_directory_uri(); ?>/js/main.js" type="text/javascript"></script><!-- Include main js file --> 
	<?php wp_footer(); ?>

	</body>
</html>
<?php


/* End of file footer.php */
/* Location: ./wp-content/themes/the-bootstrap/footer.php */