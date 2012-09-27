<?php
class TL_Latest_News_Widget extends WP_Widget {

	function TL_Latest_News_Widget() {
		$widget_ops = array( 'classname' => 'latestnewswidget', 'description' => __('Displays recent "News" posts from categories of your choosing', 'latest-news-widget') );
		$control_ops = array( 'width' => 200, 'height' => 250, 'id_base' => 'latest-news-widget' );
		$this->WP_Widget( 'latest-news-widget', __('Latest News Widget', 'latest-news-widget'), $widget_ops, $control_ops );
	}

	function widget($args, $instance) {
		extract($args);
		
		$instance = wp_parse_args( (array)$instance, array(
			'title' => __('Latest News', 'latest-news-widget'),
			'posts_cat1' => '',
			'posts_cat2' => '',
			'posts_cat3' => '',
			'posts_num' => '5',
			'posts_offset' => '0',
			'orderby' => '',
			'show_title' => 0,
			'show_byline' => 0,
			'show_content' => 0,
			'content_limit' => '',
			'more_text' => '[Read More...]',
			'use-styles' => 0,
			'font-family' => '',
			'post-font-size' => '',
			'margin-bottom' => '',
			'post-title-font-size' => ''
		) );
		
		echo $before_widget;
		
			// Set up the author bio
			if (!empty($instance['title']))
				echo $before_title . apply_filters('widget_title', $instance['title']) . $after_title;
			$no_show = false;
			if ($instance['posts_cat1'] == 0 || $instance['posts_cat2'] == 0 || $instance['posts_cat3'] == 0) {
				$news_posts = new WP_Query(array('showposts' => $instance['posts_num'],'offset' => $instance['posts_offset'], 'orderby' => $instance['orderby'], 'order' => $instance['order']));
			} else {
				$cat_in = array();
				if ($instance['posts_cat1'] != -1 && !in_array($instance['posts_cat1'], $cat_in)) $cat_in[] = $instance['posts_cat1'];
				if ($instance['posts_cat2'] != -1 && !in_array($instance['posts_cat2'], $cat_in)) $cat_in[] = $instance['posts_cat2'];
				if ($instance['posts_cat3'] != -1 && !in_array($instance['posts_cat3'], $cat_in)) $cat_in[] = $instance['posts_cat3'];
				$news_posts = new WP_Query(array('category__in' => $cat_in, 'showposts' => 0,'offset' => $instance['posts_offset'], 'orderby' => $instance['orderby'], 'order' => $instance['order']));
				if (empty($cat_in)) $no_show = true;
			}
			
			if (!$no_show) {
			if($news_posts->have_posts()) : while($news_posts->have_posts()) : $news_posts->the_post();
				
				
				
				$post_font_size = (!empty($instance['use-styles']) && !empty($instance['post-font-size'])) ? ' font-size:'.$instance['post-font-size'].'; ' : '';
				$post_font_family = (!empty($instance['use-styles']) && !empty($instance['font-family'])) ? ' font-family:'.$instance['font-family'].'; ' : '';
				$post_margin = (!empty($instance['use-styles']) && !empty($instance['margin-bottom'])) ? ' padding-bottom:0px; padding-top:0px; margin-top:0px; margin-bottom:'.$instance['margin-bottom'].'; ' : '';
				echo '<div '; post_class(); echo ' style=" '.$post_font_size.' '.$post_margin.' '.$post_font_family.'">';
				
				if(!empty($instance['show_title'])) {
					$title_style = (!empty($instance['use-styles']) && !empty($instance['post-title-font-size'])) ? ' style="font-size:'.$instance['post-title-font-size'].';" ' : '';
					echo '<a '.$title_style.' class="title-contribution" href="'.get_permalink().'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a>';
				} if(!empty($instance['show_byline'])) {
					echo '<div class="byline">';
					the_time('F j, Y');
					echo ' '.__('by', 'latest-news-widget').' ';
					echo ' - ';
					the_author_posts_link();
					echo '</div>';
				}
				if(!empty($instance['show_content'])) :
				
					if($instance['show_content'] == 'excerpt') :
						the_excerpt();
					elseif($instance['show_content'] == 'content-limit') :
						TL_Latest_News_Widget_Utils::the_content_limit( (int)$instance['content_limit'], esc_html( $instance['more_text'] ) );
					else :
						the_content( esc_html( $instance['more_text'] ) );
					endif;
					
				endif;
				echo '</div><!--end post_class()-->'."\n\n";
					
			endwhile; endif;
			}
			
		
		echo $after_widget;
		wp_reset_query();
	}

	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	function form($instance) { 
		
		$instance = wp_parse_args( (array)$instance, array(
			'title' => 'Latest News',
			'posts_cat1' => '',
			'posts_cat2' => '',
			'posts_cat3' => '',
			'posts_num' => '5',
			'posts_offset' => '0',
			'orderby' => '',
			'show_title' => 0,
			'show_byline' => 0,
			'show_content' => 0,
			'content_limit' => '',
			'more_text' => '[Read More...]',
			'use-styles' => 0,
			'font-family' => '',
			'post-font-size' => '',
			'margin-bottom' => '',
			'post-title-font-size' => ''
		) );
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'latest-news-widget'); ?>:</label>
		<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" style="width:95%;" /></p>
		
		<p><label for="<?php echo $this->get_field_id('posts_cat1'); ?>"><?php _e('Pick Up to 3 Categories to Pull Posts From', 'latest-news-widget'); ?>:</label></p>
		<p><?php wp_dropdown_categories(array('name' => $this->get_field_name('posts_cat1'), 'selected' => $instance['posts_cat1'], 'orderby' => 'Name' , 'hierarchical' => 1, 'show_option_none' => __('None', 'latest-news-widget'), 'show_option_all' => __("All Categories", 'latest-news-widget'), 'hide_empty' => '0')); ?>
		<?php wp_dropdown_categories(array('name' => $this->get_field_name('posts_cat2'), 'selected' => $instance['posts_cat2'], 'orderby' => 'Name' , 'hierarchical' => 1, 'show_option_none' => __('None', 'latest-news-widget'), 'show_option_all' => __("All Categories", 'latest-news-widget'), 'hide_empty' => '0')); ?>
		<?php wp_dropdown_categories(array('name' => $this->get_field_name('posts_cat3'), 'selected' => $instance['posts_cat3'], 'orderby' => 'Name' , 'hierarchical' => 1, 'show_option_none' => __('None', 'latest-news-widget'), 'show_option_all' => __("All Categories", 'latest-news-widget'), 'hide_empty' => '0')); ?></p>
		
		<p><input id="<?php echo $this->get_field_id('show_title'); ?>" type="checkbox" name="<?php echo $this->get_field_name('show_title'); ?>" value="1" <?php checked(1, $instance['show_title']); ?>/> <label for="<?php echo $this->get_field_id('show_title'); ?>"><?php _e('Show Post Title', 'latest-news-widget'); ?></label></p>
		
		<p><input id="<?php echo $this->get_field_id('show_byline'); ?>" type="checkbox" name="<?php echo $this->get_field_name('show_byline'); ?>" value="1" <?php checked(1, $instance['show_byline']); ?>/> <label for="<?php echo $this->get_field_id('show_image'); ?>"><?php _e('Show Post Byline', 'latest-news-widget'); ?></label></p>
		
		<p><label for="<?php echo $this->get_field_id('posts_num'); ?>"><?php _e('Number of Posts to Show', 'latest-news-widget'); ?>:</label>
		<input type="text" id="<?php echo $this->get_field_id('posts_num'); ?>" name="<?php echo $this->get_field_name('posts_num'); ?>" value="<?php echo esc_attr( $instance['posts_num'] ); ?>" size="2" /></p>
		
		<p><label for="<?php echo $this->get_field_id('posts_offset'); ?>"><?php _e('Number of Posts to Offset', 'latest-news-widget'); ?>:</label>
		<input type="text" id="<?php echo $this->get_field_id('posts_offset'); ?>" name="<?php echo $this->get_field_name('posts_offset'); ?>" value="<?php echo esc_attr( $instance['posts_offset'] ); ?>" size="2" /></p>
		
		<p><label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Order By', 'latest-news-widget'); ?>:</label>
		<select id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>">
			<option style="padding-right:10px;" value="date" <?php selected('date', $instance['orderby']); ?>><?php _e('Date', 'latest-news-widget'); ?></option>
			<option style="padding-right:10px;" value="title" <?php selected('title', $instance['orderby']); ?>><?php _e('Title', 'latest-news-widget'); ?></option>
			<option style="padding-right:10px;" value="parent" <?php selected('parent', $instance['orderby']); ?>><?php _e('Parent', 'latest-news-widget'); ?></option>
			<option style="padding-right:10px;" value="ID" <?php selected('ID', $instance['orderby']); ?>><?php _e('ID', 'latest-news-widget'); ?></option>
			<option style="padding-right:10px;" value="comment_count" <?php selected('comment_count', $instance['orderby']); ?>><?php _e('Comment Count', 'latest-news-widget'); ?></option>
			<option style="padding-right:10px;" value="rand" <?php selected('rand', $instance['orderby']); ?>><?php _e('Random', 'latest-news-widget'); ?></option>
		</select></p>

		<hr class="div" />
		
		<p>
		<label><input type="radio" name="<?php echo $this->get_field_name('show_content'); ?>" value="" <?php checked('', $instance['show_content']); ?> /> <?php _e('Hide the Content', 'bookreview'); ?></label><br />
		<label><input type="radio" name="<?php echo $this->get_field_name('show_content'); ?>" value="excerpt" <?php checked('excerpt', $instance['show_content']); ?> /> <?php _e('Show the Excerpt', 'bookreview')?></label><br />
		<label><input type="radio" name="<?php echo $this->get_field_name('show_content'); ?>" value="content" <?php checked('content', $instance['show_content']); ?> /> <?php _e('Show the Content', 'bookreview')?></label><br />
		<label><input type="radio" name="<?php echo $this->get_field_name('show_content'); ?>" value="content-limit" <?php checked('content-limit', $instance['show_content']); ?> /> <?php _e('Content Limit', 'bookreview')?></label> 
		<input type="text" name="<?php echo $this->get_field_name('content_limit'); ?>" value="<?php echo esc_attr(intval($instance['content_limit'])); ?>" size="3" /> <?php _e('characters', 'bookreview'); ?>
		
		<p><label for="<?php echo $this->get_field_id('more_text'); ?>"><?php _e('More Text (if applicable)', 'bookreview'); ?>:</label>
		<input type="text" id="<?php echo $this->get_field_id('more_text'); ?>" name="<?php echo $this->get_field_name('more_text'); ?>" value="<?php echo esc_attr($instance['more_text']); ?>" /></p>
		<hr class="div" />
		<h4>Style Settings</h4>
		<p><input id="<?php echo $this->get_field_id('use-styles'); ?>" type="checkbox" name="<?php echo $this->get_field_name('use-styles'); ?>" value="1" <?php checked(1, $instance['use-styles']); ?>/> <label for="<?php echo $this->get_field_id('use-styles'); ?>"><?php _e('Apply Style Settings', 'latest-news-widget'); ?></label></p>
		
		<p><label for="<?php echo $this->get_field_id('font-family'); ?>"><?php _e('Font Family', 'latest-news-widget'); ?>:</label>
		<input type="text" id="<?php echo $this->get_field_id('font-family'); ?>" name="<?php echo $this->get_field_name('font-family'); ?>" value="<?php echo esc_attr( $instance['font-family'] ); ?>" style="width:95%;" /></p>
		
		<p><label for="<?php echo $this->get_field_id('post-font-size'); ?>"><?php _e('Content Font Size', 'latest-news-widget'); ?>:</label>
		<input type="text" id="<?php echo $this->get_field_id('post-font-size'); ?>" name="<?php echo $this->get_field_name('post-font-size'); ?>" value="<?php echo esc_attr( $instance['post-font-size'] ); ?>" style="width:95%;" /></p>
		
		<p><label for="<?php echo $this->get_field_id('margin-bottom'); ?>"><?php _e('Content Bottom Margin', 'latest-news-widget'); ?>:</label>
		<input type="text" id="<?php echo $this->get_field_id('margin-bottom'); ?>" name="<?php echo $this->get_field_name('margin-bottom'); ?>" value="<?php echo esc_attr( $instance['margin-bottom'] ); ?>" style="width:95%;" /></p>
		
		<p><label for="<?php echo $this->get_field_id('post-title-font-size'); ?>"><?php _e('Post Title Font Size', 'latest-news-widget'); ?>:</label>
		<input type="text" id="<?php echo $this->get_field_id('post-title-font-size'); ?>" name="<?php echo $this->get_field_name('post-title-font-size'); ?>" value="<?php echo esc_attr( $instance['post-title-font-size'] ); ?>" style="width:95%;" /></p>
		
	<?php 
	}
}
?>