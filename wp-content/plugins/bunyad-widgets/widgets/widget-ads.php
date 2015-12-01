<?php

class Bunyad_Ads_Widget extends WP_Widget
{
	public function __construct()
	{
		parent::__construct(
			'bunyad_ads_widget',
			'Bunyad - Advertisement',
			array('description' => __('Advertisements widget for all the ads supported.', 'bunyad-widgets'), 'classname' => 'bunyad-ad')
		);
	}
	
	public function widget($args, $instance)
	{
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		
		?>

		<?php echo $before_widget; ?>
		
			<?php if ($title): ?>
				<?php echo $before_title . $title . $after_title; ?>
			<?php endif; ?>
		
			<div class="adwrap-widget">
			
				<?php echo do_shortcode($instance['code']); ?>
			
			</div>
		
		<?php echo $after_widget; ?>
		
		<?php
	}
	
	public function update($new, $old)
	{
		foreach ($new as $key => $val) {
			$new[$key] = $val;
		}
		
		return $new;
	}
	
	public function form($instance)
	{
		$defaults = array('title' => '', 'code' => '');
		$instance = array_merge($defaults, (array) $instance);
		extract($instance);
		
	?>
	
	<p>
		<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title: (Optional)', 'bunyad-widgets'); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php 
			echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
	</p>
	
	<p>
		<label for="<?php echo esc_attr($this->get_field_id('code')); ?>"><?php _e('Ad Code:', 'bunyad-widgets'); ?></label>
		<textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('code')); ?>" name="<?php 
			echo esc_attr($this->get_field_name('code')); ?>" rows="16" cols="20"><?php echo esc_textarea($code); ?></textarea>
	</p>

	<?php
	}
}
