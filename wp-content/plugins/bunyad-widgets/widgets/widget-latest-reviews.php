<?php

class Bunyad_LatestReviews_Widget extends WP_Widget
{
	public function __construct()
	{
		parent::__construct(
			'bunyad-latest-reviews-widget',
			'Bunyad - Latest Reviews',
			array('description' => 'Latest Reviews with thumbnails.', 'classname' => 'latest-reviews')
		);
		
		add_action('save_post', array($this, 'flush_widget_cache'));
		add_action('deleted_post', array($this, 'flush_widget_cache'));
		add_action('updated_post_meta', array($this, 'flush_widget_cache'));
		add_action('switch_theme', array($this, 'flush_widget_cache'));
		add_action('bunyad_widget_flush_cache', array($this, 'flush_widget_cache'));
	}
	
	// code below is modified from default
	public function widget($args, $instance) 
	{
		$cache = get_transient('bunyad_widget_latest_reviews');
		
		if (!is_array($cache)) {
			$cache = array();
		}

		if (!isset($args['widget_id'])) {
			$args['widget_id'] = $this->number;
		}
		
		// cache available
		if (isset($cache[ $args['widget_id'] ])) {
			//echo $cache[ $args['widget_id'] ];
			//return;
		}

		ob_start();
		
		extract($args);
		extract($instance, EXTR_SKIP);

		$title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Posts', 'bunyad-widgets') : $instance['title'], $instance, $this->id_base);
		if (empty($instance['number']) || !$number = absint($instance['number'])) {
 			$number = 5;
		}

		$query_args = array(
			'posts_per_page' => $number, 'post_status' => 'publish', 'ignore_sticky_posts' => 1, 'meta_key' => '_bunyad_review_overall'	
		);
		
		// order by rating?
		if (isset($order) && $order == 'rating') {
			$query_args['orderby'] = 'meta_value';
		}
		else {
			$query_args['orderby'] = 'date';
		}
		
		$r = new WP_Query(apply_filters('bunyad_widget_latest_reviews_query_args', $query_args));
		
		// do custom loop if available
		if (has_action('bunyad_widget_latest_review_loop')):
			
			$args['title'] = $title;
			do_action('bunyad_widget_latest_review_loop', array_merge($args, $instance), $r);
		
		elseif ($r->have_posts()):
?>

			<?php echo $before_widget; ?>
			
			<?php if ($title): ?>
				<?php echo $before_title . $title . $after_title; ?>
			<?php endif;?>
			
			<ul class="posts-list">
			
			<?php  while ($r->have_posts()) : $r->the_post(); global $post; ?>
				<li>
				
					<a href="<?php the_permalink() ?>"><?php the_post_thumbnail('post-thumbnail', array('title' => strip_tags(get_the_title()))); ?>
					
					<?php if (class_exists('Bunyad') && Bunyad::options()->review_show_widgets): ?>
						<?php echo apply_filters('bunyad_review_main_snippet', ''); ?>
					<?php endif; ?>
					
					</a>
					
					<div class="content">
					
						<?php echo Bunyad::blocks()->meta('above', 'latest-reviews', array('type' => 'widget')); ?>
					
						<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
							<?php if (get_the_title()) the_title(); else the_ID(); ?></a>
							
						<?php echo Bunyad::blocks()->meta('below', 'latest-reviews', array('type' => 'widget')); ?>
							
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
		
		// reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();
		
		$cache[$args['widget_id']] = ob_get_flush();
		
		set_transient('bunyad_widget_latest_reviews', $cache);
	}

	public function update($new_instance, $old_instance) 
	{
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['order'] = strip_tags($new_instance['order']);
		
		$this->flush_widget_cache();

		return $instance;
	}

	public function flush_widget_cache() 
	{
		delete_transient('bunyad_widget_latest_reviews');
	}

	public function form($instance) 
	{
		$values = array_merge(array('title' => '', 'number' => 5, 'order' => ''), (array) $instance);
		extract($values);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'bunyad-widgets'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to show:', 'bunyad-widgets'); ?></label>
		<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
		
		<p>
			<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order By:', 'bunyad-widgets'); ?></label>
			<select name="<?php echo $this->get_field_name('order'); ?>" id="<?php echo $this->get_field_id('order'); ?>">
				<option value="date"<?php selected($order, 'date'); ?>><?php _e('Date', 'bunyad-widgets'); ?></option>
				<option value="rating"<?php selected($order, 'rating'); ?>><?php _e('Rating', 'bunyad-widgets'); ?></option>
			</select>
		</p>
<?php
	}
}