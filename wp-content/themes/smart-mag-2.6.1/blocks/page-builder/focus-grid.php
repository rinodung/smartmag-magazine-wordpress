<?php

class Bunyad_PageBuilder_FocusGrid extends Bunyad_PageBuilder_WidgetBase
{
	
	public $no_container = 1;
	public $title_field  = 'cat';
	
	public function __construct()
	{
		parent::__construct(
			'bunyad_pagebuilder_focus_grid',
			__('Focus Grid Block', 'bunyad-admin'),
			array('description' => __('A block that lists news from a parent categories, making the latest one prominent, and have tabs for sub-categories.', 'bunyad-admin'))
		);
	}
	
	public function widget($args, $instance)
	{
		extract($args);
		
		// supported attrs
		$attrs = array('posts', 'cat', 'tax_tag', 'sub_cats', 'sub_tags', 'sort_order', 'sort_by', 'highlights', 'offset', 'post_type');
		
		// do_shortcode will be run by pagebuilder		
		echo '[focus_grid '. implode(' ', $this->shortcode_attribs($instance, $attrs)) .' /]';
		
	}
	
	public function form($instance)
	{
		$defaults = array('posts' => 5, 'cat' => '', 'offset' => 0, 'post_type' => '');
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
		<label><?php _e('Highlighted Post(s):', 'bunyad-admin'); ?></label>
		<input name="<?php echo esc_attr($this->get_field_name('highlights')); ?>" type="text" value="1" />
	</p>
	<p class="description"><?php _e('Highlighted posts are displayed larger at left. If you have more than 10 posts, we recommend using 2 highlighted posts.', 'bunyad-admin'); ?></p>
	
	<p>
		<label><?php _e('Parent Category:', 'bunyad-admin'); ?></label>
		<?php wp_dropdown_categories(array(
			'show_option_all' => __('-- None - Use a Tag --', 'bunyad-admin'), 'hierarchical' => 1, 'hide_empty' => 0, 'order_by' => 'name', 'class' => 'widefat', 'name' => $this->get_field_name('cat'))); ?>
	</p>
	
	<p>
		<label><?php _e('Show Category Overlays?', 'bunyad-admin'); ?></label>
		<select class="widefat" name="<?php echo esc_attr($this->get_field_name('cat_labels')); ?>">
			<option value="0"><?php _e('No', 'bunyad-admin'); ?></option>
			<option value="1"><?php _e('Yes', 'bunyad-admin'); ?></option>
		</select>
	</p>
	
	<p>
		<label><?php _e('or Parent Tag: (optional) ', 'bunyad-admin'); ?></label>
		<input type="text" name="<?php echo $this->get_field_name('tax_tag'); ?>" value="" />
	</p>
	<p class="description"><?php _e('You can enter a tag if you wish to show posts from a tag instead. Leave empty if you selected a category.', 'bunyad-admin'); ?></p>
		
	<div class="taxonomydiv"> <!-- borrow wp taxonomydiv > categorychecklist css rules -->
		<label><?php _e('Sub Categories:', 'bunyad-admin'); ?></label>
		
		<div class="tabs-panel">
			<ul class="categorychecklist">
				<?php
				ob_start();
				wp_category_checklist();
				
				echo str_replace('post_category[]', $this->get_field_name('sub_cats') .'[]', ob_get_clean());
				?>
			</ul>			
		</div>
	</div>
	<p class="description"><?php _e('News Focus blocks displays sub-categories as tabs. Select any sub-category you wish to display.', 'bunyad-admin'); ?></p>
	
	<p>
		<label><?php _e('or Sub Tag: (optional) ', 'bunyad-admin'); ?></label>
		<input type="text" name="<?php echo $this->get_field_name('sub_tags'); ?>" class="widefat" />
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