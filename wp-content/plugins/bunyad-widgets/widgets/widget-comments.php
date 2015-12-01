<?php

class Bunyad_Comments_Widget extends WP_Widget
{
	public function __construct()
	{
		parent::__construct(
			'bunyad-latest-comments-widget',
			'Bunyad - Recent Comments',
			array('description' => 'Recent comments with avatar.', 'classname' => 'latest-comments')
		);
		
		add_action('save_post', array($this, 'flush_widget_cache'));
		add_action('edit_post', array($this, 'flush_widget_cache')); // comments covered
		add_action('deleted_post', array($this, 'flush_widget_cache'));
		add_action('switch_theme', array($this, 'flush_widget_cache'));
		add_action('bunyad_widget_flush_cache', array($this, 'flush_widget_cache'));
	}
	
	// code below is modified from default
	public function widget($args, $instance) 
	{
		$cache = get_transient('bunyad_widget_latest_posts');
		
		if (!is_array($cache)) {
			$cache = array();
		}

		if (!isset($args['widget_id'])) {
			$args['widget_id'] = $this->id;
		}
		
		// cache available
		if (!defined('ICL_LANGUAGE_CODE') && isset($cache[ $args['widget_id'] ])) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();
		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? __('Latest Comments', 'bunyad-widgets') : $instance['title'], $instance, $this->id_base);
		if (empty($instance['number']) || !$number = absint($instance['number'])) {
 			$number = 5;
		}
		
		$query = get_comments(apply_filters('bunyad_widget_comments_query_args', array('number'=> $number, 'status' => 'approve')));
		
		// do custom loop if available
		if (has_action('bunyad_widget_comments_loop')):

			$args['title'] = $title;
			do_action('bunyad_widget_comments_loop', array_merge($args, $instance), $query);
		
		elseif ($query):
?>
			<?php echo $before_widget; ?>
			
			<?php if ($title): ?>
				<?php echo $before_title . $title . $after_title; ?>
			<?php endif;?>
			
			<ul class="comments-list">
			
			<?php foreach ($query as $comment):	?>
					
					<li class="comment">
						
						<span class="author"><?php printf('%s said', get_comment_author_link($comment->comment_ID)); ?></span>
						
						<p class="text"><?php comment_excerpt($comment->comment_ID); ?></p>
						
						<a href=""><?php echo get_the_title($comment->comment_post_ID); ?></a>
					
					</li>
	
					<?php				
					endforeach; 
					?>
			</ul>
			
			<?php echo $after_widget; ?>
		
		<?php
		
		endif;
		
		// reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		$cache[$args['widget_id']] = ob_get_flush();
		set_transient('bunyad_widget_comments', $cache);
	}

	public function update($new, $old) 
	{
		foreach ($new as $key => $val) {
			$new[$key] = wp_filter_kses($val);
		}
		
		$this->flush_widget_cache();

		return $new;
	}

	public function flush_widget_cache() 
	{
		delete_transient('bunyad_widget_comments');
	}

	public function form($instance)
	{	
		$instance = array_merge(array('title' => '', 'number' => 5), $instance);
		extract($instance);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'bunyad-widgets'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of comments:', 'bunyad-widgets'); ?></label>
		<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
<?php
	}
}