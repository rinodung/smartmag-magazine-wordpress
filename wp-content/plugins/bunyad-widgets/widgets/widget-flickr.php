<?php

class Bunyad_Flickr_Widget extends WP_Widget
{
	public function __construct()
	{
		parent::__construct(
			'bunyad_flickr_widget',
			'Bunyad - Flickr',
			array('description' => __('Display latest photos from flickr.', 'bunyad-widgets'), 'classname' => 'bunyad-flickr')
		);
	}
	
	public function widget($args, $instance)
	{
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		
		extract($instance, EXTR_SKIP);
		
		$data = $this->get_flickr($instance); 
		
	?>

		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
		
			<div class="flickr-widget">

			<?php 
				foreach ((array) $data as $item): 
					
					if (empty($item['media']) OR empty($item['link'])):
						continue;
					endif;
			?>
			
				<div class="flickr_badge_image">
					<a href="<?php echo esc_url($item['link']); ?>">
						<img src="<?php echo esc_url($item['media']); ?>" alt="<?php echo esc_attr($item['title']); ?>" />
					</a>
				</div>
				
			<?php endforeach; ?>
			
			</div>
		
		<?php echo $after_widget; ?>
		
	<?php
	}
	
	public function get_flickr($instance)
	{
		extract($instance);
		
		if (empty($this->number)) {
			$this->number = md5(serialize($instance));
		}

		// get from cache
		$cache = get_transient('bunyad_flickr_widget');
		if (is_array($cache) && !empty($cache[$this->number])) {
			return $cache[$this->number];
		}
		
		$data = $this->parse_script(
			'http://api.flickr.com/services/feeds/photos_public.gne?format=json&id='. urlencode($user_id) .'&nojsoncallback=1&tags=' . urlencode($tags),
			$show_num
		);
		
		// store to cache
		$cache = array($this->number => $data);
		set_transient('bunyad_flickr_widget', $cache, 300); // 5 minutes expiry
		
		return $data;
				
	}
	
	/**
	 * Fetch and parse data off flickr feed 
	 * 
	 * @param string $url
	 * @param int $number  number of results
	 */
	public function parse_script($url, $number)
	{
		$file = wp_remote_get($url);
		
		if (is_wp_error($file) OR !$file['body']) {
			return '';
		}
		
		// fix flickr json escape bug
		$file['body'] = str_replace("\\'", "'", $file['body']);
		$data = json_decode($file['body'], true);
		
		if (!is_array($data)) {
			return array();
		}
		
		$data = array_slice($data['items'], 0, $number);
		
		// replace medium with small square image
		foreach ($data as $key => $item) {
			$data[$key]['media'] = preg_replace('/_m\.(jp?g|png|gif)$/', '_s.\\1', $item['media']['m']);	
		}
		
		return $data;
	}	
	
	public function update($new, $old)
	{
		foreach ($new as $key => $val) {
			$new[$key] = wp_filter_kses($val);
		}
		
		delete_transient('bunyad_flickr_widget');
		
		$new['show_num'] = intval($new['show_num']);
		
		return $new;
	}
	
	public function form($instance)
	{
		$defaults = array('title' => 'Flickr Photos', 'show_num' => 12, 'user_id' => '', 'tags' => '');
		$instance = array_merge($defaults, (array) $instance);
		extract($instance);
		
	?>
	
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'bunyad-widgets'); ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php 
				echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('user_id')); ?>"><?php _e('Flickr ID (<a href="http://www.idgettr' . '.com">Get Your ID</a>):', 'bunyad-widgets'); ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('user_id')); ?>" name="<?php 
				echo esc_attr($this->get_field_name('user_id')); ?>" type="text" value="<?php echo esc_attr($user_id); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('show_num')); ?>"><?php _e('Number of Photos:', 'bunyad-widgets'); ?></label>
			<input class="width100" id="<?php echo esc_attr($this->get_field_id('show_num')); ?>" name="<?php 
				echo esc_attr($this->get_field_name('show_num')); ?>" type="text" value="<?php echo esc_attr($show_num); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('tags')); ?>"><?php _e('Tags (comma separated, optional):', 'bunyad-widgets'); ?></label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('tags')); ?>" name="<?php 
				echo esc_attr($this->get_field_name('tags')); ?>" type="text" value="<?php echo esc_attr($tags); ?>" />

		</p>
		
	
	<?php
	
	} // end form()
}