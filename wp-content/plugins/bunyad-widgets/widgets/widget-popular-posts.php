<?php

class Bunyad_PopularPosts_Widget extends WP_Widget
{
	public function __construct()
	{
		parent::__construct(
			'bunyad-popular-posts-widget',
			'Bunyad - Popular Posts',
			array('description' => 'Displays posts with most comments.', 'classname' => 'popular-posts')
		);
	}

	public function widget($args, $instance) 
	{
		extract($args);
		extract($instance);

		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		$type  = (empty($type) ? '' : $type);
		
		if ($type == 'jetpack') {
			
			// jetpack inactive?
			if (!$this->has_jetpack()) {
				_e('Please install Jetpack plugin and activate the WordPress.com Stats module.', 'bunyad-widgets');				
				return;
			}

			/**
			 * Get posts by views from Jetpack stat module (wordpress.com stats)
			 */
			$post_views = stats_get_csv('postviews', array('days' => absint($days), 'limit' => 100));
			if (!$post_views) {
				$post_views = array();
			}

			$post_ids   = array_filter(wp_list_pluck($post_views, 'post_id'));
			$query_args = array('offset' => 0, 'posts_per_page' => $number);
			
			// define posts to get if available 
			if (count($post_ids)) {
				$query_args = array_merge($query_args, array('post__in' => $post_ids, 'orderby' => 'post__in'));
			}
			
			// get the local posts
			$r = new WP_Query(apply_filters('bunyad_widget_popular_posts_query_args', $query_args));
		}
		
		// if not jetpack or don't have any posts yet - fall-back to comments
		if ($type !== 'jetpack' && (!isset($r) OR !$r->have_posts())) {
			$r = new WP_Query(apply_filters('bunyad_widget_popular_posts_query_args', array('posts_per_page' => $number, 'offset' => 0, 'orderby' => 'comment_count', 'ignore_sticky_posts' => 1)));
		}
		
				
		// do custom loop if available
		if (has_action('bunyad_widget_popular_posts_loop')):

			$args['title'] = $title;
			do_action('bunyad_widget_popular_posts_loop', array_merge($args, $instance), $r);
				
		elseif ($r->have_posts()):
		
		?>
			<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			
			<ul class="posts-list">
			<?php  while ($r->have_posts()) : $r->the_post(); global $post; ?>
				<li>
				
					<a href="<?php the_permalink() ?>"><?php the_post_thumbnail('post-thumbnail', array('title' => strip_tags(get_the_title()))); ?>
					
					<?php if (class_exists('Bunyad') && Bunyad::options()->review_show_widgets): ?>
						<?php echo apply_filters('bunyad_review_main_snippet', ''); ?>
					<?php endif; ?>
					
					</a>
					
					<div class="content">
					
						<?php echo Bunyad::blocks()->meta('above', 'popular-posts', array('type' => 'widget')); ?>
					
						<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
							<?php if (get_the_title()) the_title(); else the_ID(); ?></a>
							
						<?php echo Bunyad::blocks()->meta('below', 'popular-posts', array('type' => 'widget')); ?>
							
						<?php if (class_exists('Bunyad') && Bunyad::options()->review_show_widgets): ?>
							<?php echo apply_filters('bunyad_review_main_snippet', '', 'stars'); ?>
						<?php endif; ?>

					</div>
				
				</li>
			<?php endwhile; ?>
			</ul>
			
			<?php echo $after_widget; ?>
		
		<?php

		endif;
		
		// reset global data
		wp_reset_postdata();
		
	}

	public function update($new, $old) 
	{
		$new['title']  = strip_tags($new['title']);
		$new['number'] = intval($new['number']);
		$new['days']   = intval($new['days']);
		$new['type']   = strip_tags($new['type']);

		return $new;
	}

	public function form($instance) 
	{
		
		$instance = wp_parse_args($instance, array('title' => '', 'number' => 5, 'type' => 'comments', 'days' => 30));
		
		extract($instance, EXTR_SKIP);
				
		
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'bunyad-widgets'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to show:', 'bunyad-widgets'); ?></label>
		<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
		

		<p>
			<label for="<?php echo $this->get_field_id('type'); ?>"><?php _e('Sorting Type:', 'bunyad-widgets'); ?></label>
			<select id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>" class="widefat bunyad-popular-posts-type">
				<option value="comments" <?php selected($type, 'comments'); ?>><?php _e('Comments', 'bunyad-widgets'); ?></option>
				<option value="jetpack" <?php selected($type, 'jetpack'); ?>><?php 
					_e((!$this->has_jetpack() ? 'Views (Requires Jetpack plugin with Stats module)' : 'Views - via Jetpack Stats'), 'bunyad-widgets'); ?></option>
			</select>
		</p>
		
		<div class="jetpack-extras hidden">
			<p>
				<label for="<?php echo $this->get_field_id('days'); ?>"><?php _e('From past days:', 'bunyad-widgets'); ?></label>
				<input id="<?php echo $this->get_field_id('days'); ?>" name="<?php echo $this->get_field_name('days'); ?>" type="text" value="<?php echo $days; ?>" size="3" />
			</p>
			
			<p><?php _e('Note that it may take a few hours before views are counted. It will fallback to comments sorting type until then.', 'bunyad-widgets'); ?></p>
		</div>
		
		<script>
		
		// show days only if needed
		jQuery(function($) {
			$(document).on('change', '.bunyad-popular-posts-type', function() {

				var ele = $(this).closest('.widget-content').find('.jetpack-extras');

				if ($(this).val() == 'jetpack') {
					ele.show();
				}
				else {
					ele.hide();
				}
			});

			$('.bunyad-popular-posts-type').trigger('change');
		});
		</script>
		
<?php
	}
	
	public function has_jetpack() {
		return (class_exists('Jetpack') && Jetpack::is_module_active('stats'));
	}
	
}