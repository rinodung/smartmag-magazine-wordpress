<?php

class Bunyad_PageBuilder_Blog extends Bunyad_PageBuilder_WidgetBase
{
	
	public $no_container = 1;
	public $title_field  = 'heading,type';
	
	public function __construct()
	{
		parent::__construct(
			'bunyad_pagebuilder_blog',
			__('Blog/Listing Block', 'bunyad-admin'),
			array('description' => __('Used for category style listing - ex. a blog view. Supports pagination.', 'bunyad-admin'))
		);
	}
	
	public function widget($args, $instance)
	{
		extract($args);
		
		// supported attributes
		$attrs = array('pagination', 'heading', 'heading_type', 'posts', 'type', 'cat', 'cats', 'tags', 'sort_order', 'sort_by', 'offset', 'post_type', 'pagination_type', 'cat_labels');

		// do_shortcode will be run by pagebuilder		
		echo '[blog '. implode(' ', $this->shortcode_attribs($instance, $attrs)) .' /]';
		
	}
	
	public function form($instance)
	{
		$defaults = array('pagination' => 0, 'heading' => '', 'heading_type' => '', 'posts' => 4, 'type' => '', 'cats' => '', 'post_type' => '');
		$instance = array_merge($defaults, (array) $instance);
		extract($instance);
				
		$render = Bunyad::factory('admin/option-renderer'); /* @var $render Bunyad_Admin_OptionRenderer */
		
		
	?>
	
	<input type="hidden" name="<?php echo $this->get_field_name('no_container'); ?>" value="1" />
		
	<p>
		<label><?php _e('Number of Posts:', 'bunyad-admin'); ?></label>
		<input name="<?php echo esc_attr($this->get_field_name('posts')); ?>" type="text" value="<?php echo esc_attr($posts); ?>" />
	</p>
	<p class="description"><?php _e('Configures posts to show for each listing. Leave empty to use theme default number of posts.', 'bunyad-admin'); ?></p>
	
	<p>
		<label><?php _e('Sort By:', 'bunyad-admin'); ?></label>
		<select name="<?php echo esc_attr($this->get_field_name('sort_by')); ?>">
			<option value=""><?php _e('Published Date', 'bunyad-admin'); ?></option>
			<option value="modified"><?php _e('Modified Date', 'bunyad-admin'); ?></option>
			<option value="random"><?php _e('Random', 'bunyad-admin'); ?></option>
		</select>
		
		<select name="<?php echo esc_attr($this->get_field_name('sort_order')); ?>">
			<option value="desc"><?php _e('Latest First - Descending', 'bunyad-admin'); ?></option>
			<option value="asc"><?php _e('Oldest First - Ascending', 'bunyad-admin'); ?></option>
		</select>
	</p>
	
	<p>
		<label><?php _e('Listing Style:', 'bunyad-admin'); ?></label>
		
		<select class="widefat" name="<?php echo esc_attr($this->get_field_name('type')); ?>">
			<option value=""><?php _e('Default Style (In Theme Settings)', 'bunyad-admin'); ?></option>
			<option value="modern"><?php _e('Modern Style - 2 Column', 'bunyad-admin'); ?></option>
			<option value="modern-3"><?php _e('Modern Style - 3 Column', 'bunyad-admin'); ?></option>
			<option value="grid-overlay"><?php _e('Grid Overlay - 2 Column', 'bunyad-admin'); ?></option>
			<option value="grid-overlay-3"><?php _e('Grid Overlay - 3 Column', 'bunyad-admin'); ?></option>
			<option value="tall-overlay"><?php _e('Tall Grid Overlay', 'bunyad-admin'); ?></option>
			<option value="alt"><?php _e('Blog Style', 'bunyad-admin'); ?></option>
			<option value="classic"><?php _e('Classic - Large Blog Style', 'bunyad-admin'); ?></option>
			<option value="timeline"><?php _e('Timeline Style', 'bunyad-admin'); ?></option>
		</select>

	</p>
	<p class="description"><?php _e('Check docs and demo to choose the right style.', 'bunyad-admin'); ?></p>
	
	<p>	
		<label><?php _e('Main Category: (Optional)', 'bunyad-admin'); ?></label>
		<?php wp_dropdown_categories(array(
			'show_option_all' => __('-- None --', 'bunyad-admin'), 'hierarchical' => 1, 'hide_empty' => 0, 'order_by' => 'name', 'class' => 'widefat', 'name' => $this->get_field_name('cat')
		)); ?>
	</p>
	<p class="description"><?php _e('Posts will be limited to this category and category color will be used for heading.', 'bunyad-admin'); ?></p>
	
	
	<p>
		<label><?php _e('Heading: (Optional)', 'bunyad-admin'); ?></label>
		<input class="widefat" name="<?php echo esc_attr($this->get_field_name('heading')); ?>" type="text" value="<?php echo esc_attr($heading); ?>" />
	</p>
	<p class="description"><?php _e('Optional heading.', 'bunyad-admin'); ?></p>
	
	<p>
		<label><?php _e('Heading Style:', 'bunyad-admin'); ?></label>
		<select class="widefat" name="<?php echo esc_attr($this->get_field_name('heading_type')); ?>">
			<option value="block"><?php _e('Block Section Heading Style', 'bunyad-admin'); ?></option>
			<option value=""><?php _e('Page Heading Style', 'bunyad-admin'); ?></option>
		</select>
	</p>
	<p class="description"><?php _e('Page heading style is normal heading style used for pages. Block section heading style is what you would see often on 
		homepage with multiple sections.', 'bunyad-admin'); ?></p>
	
	<p>
		<label><?php _e('Show Category Overlays?', 'bunyad-admin'); ?></label>
		<select class="widefat" name="<?php echo esc_attr($this->get_field_name('cat_labels')); ?>">
		<option value="1"><?php _e('Yes', 'bunyad-admin'); ?></option>
			<option value="0"><?php _e('No', 'bunyad-admin'); ?></option>
		</select>
	</p>
			
	<div>
		<label><?php _e('Pagination:', 'bunyad-admin'); ?></label>
		<select class="widefat" name="<?php echo esc_attr($this->get_field_name('pagination')); ?>">
			<option value="0"><?php _e('Disabled', 'bunyad-admin'); ?></option>
			<option value="1"><?php _e('Enabled', 'bunyad-admin'); ?></option>
		</select>
	</div>
	
			
	<div>
		<label><?php _e('Pagination Type:', 'bunyad-admin'); ?></label>
		<select class="widefat" name="<?php echo esc_attr($this->get_field_name('pagination_type')); ?>">
			<option value=""><?php _e('Normal', 'bunyad-admin'); ?></option>
			<option value="infinite"><?php _e('Infinite Scroll', 'bunyad-admin'); ?></option>
		</select>
	</div>
	<p class="description"><?php _e('WARNING: Infinite Scroll will only work for the last block on a page. Infinite scroll loads more posts as user scrolls.', 'bunyad-admin'); ?></p>
	
	<div class="taxonomydiv"> <!-- borrow wp taxonomydiv > categorychecklist css rules -->
		<label><?php _e('Limit Categories:', 'bunyad-admin'); ?></label>
		
		<div class="tabs-panel">
			<ul class="categorychecklist">
				<?php
				ob_start();
				wp_category_checklist();
				
				echo str_replace('post_category[]', $this->get_field_name('cats') .'[]', ob_get_clean());
				?>
			</ul>			
		</div>
	</div>
	<p class="description"><?php _e('By default, all categories will be used. Tick categories to limit to a specific category or categories.', 'bunyad-admin'); ?></p>
	
	<p class="tag">
		<?php _e('or Limit with Tags: (optional)', 'bunyad-admin'); ?> 
		<input type="text" name="<?php echo $this->get_field_name('tags'); ?>" value="" class="widefat" />
	</p>
	
	<p class="description"><?php _e('Separate tags with comma. e.g. cooking,sports', 'bunyad-admin'); ?></p>
	
	<p>
		<label><?php _e('Offset: (Advanced)', 'bunyad-admin'); ?></label> 
		<input type="text" name="<?php echo $this->get_field_name('offset'); ?>" value="0" />
	</p>
	<p class="description"><?php _e('By specifying an offset as 10 (for example), you can ignore 10 posts in the results.', 'bunyad-admin'); ?></p>
	
	<p>
		<label><?php _e('Post Types: (Advanced)', 'bunyad-admin'); ?></label>
		<input name="<?php echo esc_attr($this->get_field_name('post_type')); ?>" type="text" value="<?php echo esc_attr($post_type); ?>" />
	</p>
	<p class="description"><?php _e('Only for advanced users! You can use a custom post type here - multiples supported when separated by comma. Leave empty to use the default format.', 'bunyad-admin'); ?></p>
	
	<?php
	}
}
