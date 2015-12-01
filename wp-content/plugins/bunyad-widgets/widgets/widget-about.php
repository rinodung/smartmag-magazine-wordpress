<?php

class Bunyad_About_Widget extends WP_Widget
{
	public function __construct()
	{
		parent::__construct(
			'bunyad_about_widget',
			'Bunyad - About Widget',
			array('description' => __('"About" site widget for footer.', 'bunyad-widgets'), 'classname' => 'bunyad-about')
		);
	}
	
	public function widget($args, $instance)
	{
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		
		?>

		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
		
			<div class="about-widget">
			
			<?php 
			
			if (!empty($instance['image'])):
			?>
				<img src="<?php echo esc_attr(strip_tags($instance['image'])); ?>" />			
			<?php 
			elseif (!empty($instance['logo_text'])): ?>
				<p class="logo-text"><?php echo $instance['logo_text']; ?></p>
				
			<?php 
			endif; 
			?>
			
			<?php echo do_shortcode(apply_filters('shortcode_cleanup', wpautop($instance['text']))); ?>
			
			</div>
		
		<?php echo $after_widget; ?>
		
		<?php
	}
	
	public function update($new, $old)
	{
		foreach ($new as $key => $val) {
			$new[$key] = wp_kses_post($val);
		}
		
		return $new;
	}
	
	public function form($instance)
	{
		$defaults = array('title' => 'About', 'image' => '', 'text' => '', 'logo_text' => '');
		$instance = array_merge($defaults, (array) $instance);
		extract($instance);
		
	?>
	
	<p>
		<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'bunyad-widgets'); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php 
			echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
	</p>
	
	<p>
		<label for="<?php echo esc_attr($this->get_field_id('text')); ?>"><?php _e('About Site:', 'bunyad-widgets'); ?></label>
		<textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('text')); ?>" name="<?php 
			echo esc_attr($this->get_field_name('text')); ?>" rows="5"><?php echo esc_textarea($text); ?></textarea>
	</p>
	
		
	<p>
		<label for="<?php echo esc_attr($this->get_field_id('logo_text')); ?>"><?php _e('Logo Text:', 'bunyad-widgets'); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id('logo_text')); ?>" name="<?php 
			echo esc_attr($this->get_field_name('logo_text')); ?>" type="text" value="<?php echo esc_attr($logo_text); ?>" />
	</p>
	
	<p>
		<label for="<?php echo esc_attr($this->get_field_id('image')); ?>"><?php _e('OR Logo Image:', 'bunyad-widgets'); ?></label>
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id('image')); ?>" name="<?php 
			echo esc_attr($this->get_field_name('image')); ?>" type="text" value="<?php echo esc_attr($image); ?>" />
	</p>
	
	
	<?php
	}
}