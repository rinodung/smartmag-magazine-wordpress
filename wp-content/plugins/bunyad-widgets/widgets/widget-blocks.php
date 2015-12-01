<?php
/**
 * A super widget to add theme blocks in the sidebar
 */
class Bunyad_Blocks_Widget extends WP_Widget
{
	public function __construct()
	{
		parent::__construct(
			'bunyad-blocks-widget',
			'Bunyad - Post Listing Blocks',
			array('description' => 'Use suitable page builder blocks, in the sidebar.', 'classname' => 'page-blocks')
		);
	}
	
	/**
	 * Output the widget
	 * 
	 * @see WP_Widget::widget()
	 */
	public function widget($args, $instance) 
	{
		extract($args);
		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		
		if (strstr($instance['block'], 'blog-')) {
			$shortcode = 'blog';
			
			// set listing type
			$instance['type'] = str_replace('blog-', '', $instance['block']);
		}
		else {
			$shortcode = $instance['block'];
		}
		
		// supported attributes
		$attrs  = array('posts', 'type', 'cats', 'tags', 'sort_order', 'sort_by', 'offset', 'post_type', 'pagination_type'); 
		$helper = new Bunyad_PageBuilder_WidgetBase(null, null, array());
		
		// output
		
		echo $before_widget . $before_title . $title . $after_title;
		
		// create and execute the shortcode
		echo do_shortcode(
			"[{$shortcode} " .  implode(' ', $helper->shortcode_attribs($instance, $attrs)) . ' /]'
		);	
					
		echo $after_widget;
	}

	/**
	 * Save the widget data'
	 * 
	 * @see WP_Widget::update()
	 */
	public function update($new, $old) 
	{
		foreach ($new as $key => $val) {
			
			if (is_array($val)) {
				foreach ($val as $key => $value) {
					$val[$key] = wp_filter_kses($val);
				}
			}
			
			$new[$key] = wp_filter_kses($val);
		}

		return $new;
	}


	/**
	 * Add/edit widget form
	 */
	public function form($instance)
	{	
		$defaults = array('title' => '', 'posts' => 4, 'type' => '', 'cats' => '', 'post_type' => '', 'tags' => '', 'offset' => 0, 'sort_by' => '', 'sort_order' => '');
		$instance = array_merge($defaults, (array) $instance);
		extract($instance);
				
		$render = Bunyad::factory('admin/option-renderer'); /* @var $render Bunyad_Admin_OptionRenderer */
		
	?>

	<p>
		<label><?php _e('Title:', 'bunyad-widgets'); ?></label>
		<input name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
	</p>		
		
	<p>
		<label><?php _e('Number of Posts:', 'bunyad-widgets'); ?></label>
		<input name="<?php echo esc_attr($this->get_field_name('posts')); ?>" type="text" value="<?php echo esc_attr($posts); ?>" />
	</p>
	<p class="description"><?php _e('Configures posts to show for each listing. Leave empty to use theme default number of posts.', 'bunyad-widgets'); ?></p>
	
	<p>
		<label><?php _e('Sort By:', 'bunyad-widgets'); ?></label>
		<select name="<?php echo esc_attr($this->get_field_name('sort_by')); ?>">
			<option value=""><?php _e('Published Date', 'bunyad-widgets'); ?></option>
			<option value="modified"  <?php selected($sort_by, 'modified'); ?>><?php _e('Modified Date', 'bunyad-widgets'); ?></option>
			<option value="random" <?php selected($sort_by, 'random'); ?>><?php _e('Random', 'bunyad-widgets'); ?></option>
		</select>
		
		<select name="<?php echo esc_attr($this->get_field_name('sort_order')); ?>">
			<option value="desc" <?php selected($sort_order, 'desc'); ?>><?php _e('Latest First - Descending', 'bunyad-widgets'); ?></option>
			<option value="asc" <?php selected($sort_order, 'asc'); ?>><?php _e('Oldest First - Ascending', 'bunyad-widgets'); ?></option>
		</select>
	</p>
	
	<p>
		<label><?php _e('Block:', 'bunyad-widgets'); ?></label>
		
		<select class="widefat" name="<?php echo esc_attr($this->get_field_name('block')); ?>">
			<option value="blog-modern" <?php selected($block, 'blog-modern'); ?>><?php _e('Listing: Modern Style', 'bunyad-widgets'); ?></option>
			<option value="blog-grid-overlay" <?php selected($block, 'blog-grid-overlay'); ?>><?php _e('Listing: Grid Overlay Style', 'bunyad-widgets'); ?></option>
			<option value="blog-timeline" <?php selected($block, 'blog-timeline'); ?>><?php _e('Listing: Timeline Style', 'bunyad-widgets'); ?></option>
		</select>

	</p>
	<p class="description"><?php _e('Check docs and demo to choose the right style.', 'bunyad-widgets'); ?></p>
	
	<div class="taxonomydiv"> <!-- borrow wp taxonomydiv > categorychecklist css rules -->
		<label><?php _e('Limit Categories:', 'bunyad-widgets'); ?></label>
		
		<div class="tabs-panel">
			<ul class="categorychecklist">
				<?php
				ob_start();
				wp_category_checklist(0, 0, $cats, false, null, false);
				
				echo str_replace('post_category[]', $this->get_field_name('cats') .'[]', ob_get_clean());
				?>
			</ul>			
		</div>
	</div>
	<p class="description"><?php _e('By default, all categories will be used. Tick categories to limit to a specific category or categories.', 'bunyad-widgets'); ?></p>
	
	<p class="tag">
		<?php _e('or Limit with Tags: (optional)', 'bunyad-widgets'); ?> 
		<input type="text" name="<?php echo $this->get_field_name('tags'); ?>" value="<?php echo esc_attr($tags); ?>" class="widefat" />
	</p>
	
	<p class="description"><?php _e('Separate tags with comma. e.g. cooking,sports', 'bunyad-widgets'); ?></p>
	
	<p>
		<label><?php _e('Offset: (Advanced)', 'bunyad-widgets'); ?></label> 
		<input type="text" name="<?php echo $this->get_field_name('offset'); ?>" value="<?php echo esc_attr($offset); ?>" />
	</p>
	<p class="description"><?php _e('By specifying an offset as 10 (for example), you can ignore 10 posts in the results.', 'bunyad-widgets'); ?></p>
	
	<p>
		<label><?php _e('Post Types: (Advanced)', 'bunyad-widgets'); ?></label>
		<input name="<?php echo esc_attr($this->get_field_name('post_type')); ?>" type="text" value="<?php echo esc_attr($post_type); ?>" />
	</p>
	<p class="description"><?php _e('Only for advanced users! You can use a custom post type here - multiples supported when separated by comma. Leave empty to use the default format.', 'bunyad-widgets'); ?></p>
	
	<?php
	}
}