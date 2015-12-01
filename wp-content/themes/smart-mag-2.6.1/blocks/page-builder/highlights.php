<?php

class Bunyad_PageBuilder_Highlights extends Bunyad_PageBuilder_WidgetBase
{
	
	public $no_container = 1;
	public $title_field  = 'cat_1,cat_2,cat_3';
	
	public function __construct()
	{
		parent::__construct(
			'bunyad_pagebuilder_highlights',
			__('Highlights Block', 'bunyad-admin'),
			array('description' => __('A 2 or 3 column block to highlight the latest post and show more posts from 2 or 3 categories.', 'bunyad-admin'))
		);
	}
	
	public function widget($args, $instance)
	{	
		// defaults
		$instance = array_merge(array(
			'cat_1' => null, 'cat_2' => null, 'cat_3' => null,
			'tag_1' => null, 'tag_2' => null, 'tag_3' => null,
			'heading_1' => null, 'heading_2' => null, 'heading_3' => null,
			'offset_1' => null, 'offset_2' => null, 'offset_3' => '' 
		), $instance);
			
		extract($args);
		extract($instance, EXTR_SKIP);
		
		$instance['cats'] = array($cat_1, $cat_2, $cat_3);
		$instance['tags'] = array($tag_1, $tag_2, $tag_3);
		$instance['headings'] = array($heading_1, $heading_2, $heading_3);
		$instance['offsets'] = array($offset_1, $offset_2, $offset_3);
		
		
		// supported attrs		
		$attrs = array('type', 'posts', 'cat', 'column', 'columns', 'cats', 'tags', 'headings', 'sort_by', 'sort_order', 'offsets', 'post_type', 'heading_type');
		
		// do_shortcode will be run by pagebuilder		
		echo '[highlights '. implode(' ', $this->shortcode_attribs($instance, $attrs)) .' /]';		
	}
	
	public function form($instance)
	{
        // enqueue js to the footer
        add_action('admin_print_footer_scripts', array($this, 'add_javascript'), 60);
		
		$defaults = array('posts' => 4, 'cats' => '', 'sorting' => '', 'post_type' => '');
		$instance = array_merge($defaults, (array) $instance);
		extract($instance);

		$render = Bunyad::factory('admin/option-renderer'); /* @var $render Bunyad_Admin_OptionRenderer */
		
		
	?>
	
	<input type="hidden" name="<?php echo $this->get_field_name('no_container'); ?>" value="1" />
	
	<p><strong><?php _e('Important:', 'bunyad-admin'); ?></strong> <?php _e('Select 2 categories for 2 columns and 3 categories for 3 columns.', 'bunyad-admin'); ?></p>
	
	<p>
		<label><?php _e('Number of Posts:', 'bunyad-admin'); ?></label>
		<input name="<?php echo esc_attr($this->get_field_name('posts')); ?>" type="text" value="<?php echo esc_attr($posts); ?>" />
	</p>
	<p class="description"><?php _e('Configures posts to show in each column. Leave empty to use theme default number of posts.', 'bunyad-admin'); ?></p>
	
		
	<p>
		<label><?php _e('Sort By:', 'bunyad-admin'); ?></label>
		<select name="<?php echo esc_attr($this->get_field_name('sort_by')); ?>">
			<option value=""><?php _e('Published Date', 'bunyad-admin'); ?></option>
			<option value="modified"><?php _e('Modified Date', 'bunyad-admin'); ?></option>
			<option value="random"><?php _e('Random', 'bunyad-admin')?></option>
		</select>
		
		<select name="<?php echo esc_attr($this->get_field_name('sort_order')); ?>">
			<option value="desc"><?php _e('Latest First - Descending', 'bunyad-admin'); ?></option>
			<option value="asc"><?php _e('Oldest First - Ascending', 'bunyad-admin'); ?></option>
		</select>
	</p>
	
	<div>
		<label><?php _e('Columns:', 'bunyad-admin'); ?></label>
	
		<select class="widefat" name="<?php echo esc_attr($this->get_field_name('columns')); ?>">
			<option value="2"><?php _e('2 Columns', 'bunyad-admin'); ?></option>
			<option value="3"><?php _e('3 Columns', 'bunyad-admin'); ?></option>
		</select>
	</div>
	
	<div>
		<label><?php _e('Heading Style:', 'bunyad-admin'); ?></label>
	
		<select class="widefat" name="<?php echo esc_attr($this->get_field_name('heading_type')); ?>">
			<option value="auto"><?php _e('Auto: Overlay for 2 Column - Block Heading for 3 Columns', 'bunyad-admin'); ?>
			<option value="none"><?php _e('No Heading', 'bunyad-admin'); ?></option>
		</select>
	</div>
	
	<hr />
	
	<div>
		<h3><?php _e('First Column', 'bunyad-admin'); ?></h3>
		
		<label><?php _e('Category', 'bunyad-admin'); ?></label>
		<?php wp_dropdown_categories(array(
			'show_option_all' => __('-- None - Use a Tag --', 'bunyad-admin'), 'hierarchical' => 1, 'hide_empty' => 0, 'order_by' => 'name', 'class' => 'widefat', 'name' => $this->get_field_name('cat_1')
		)); ?>
		
		<p class="tag"><label><?php _e('or Enter a Tag:', 'bunyad-admin'); ?></label> <input type="text" name="<?php echo $this->get_field_name('tag_1'); ?>" value="" /></p>
		
		<p>
			<label><?php _e('Custom Heading (optional)', 'bunyad-admin'); ?></label>
			<input type="text" class="widefat" name="<?php echo $this->get_field_name('heading_1'); ?>" value="" />
		</p>
		
		<p>
			<label><?php _e('Offset: (Advanced)', 'bunyad-admin'); ?></label> 
			<input type="text" name="<?php echo $this->get_field_name('offset_1'); ?>" value="0" />
		</p>
		<p class="description"><?php _e('By specifying an offset as 10 (for example), you can ignore 10 posts in the results.', 'bunyad-admin'); ?></p>
	
	</div>
	
	<hr />
	
	<div>
		<h3><?php _e('Second Column', 'bunyad-admin'); ?></h3>
		
		<label><?php _e('Category', 'bunyad-admin'); ?></label>
		<?php wp_dropdown_categories(array(
			'show_option_all' => __('-- None - Use a Tag --', 'bunyad-admin'), 'hierarchical' => 1, 'hide_empty' => 0, 'order_by' => 'name', 'class' => 'widefat', 'name' => $this->get_field_name('cat_2')
		)); ?>
		
		<p class="tag"><label><?php _e('or Enter a Tag:', 'bunyad-admin'); ?></label> <input type="text" name="<?php echo $this->get_field_name('tag_2'); ?>" value="" /></p>
		
		<p>
			<label><?php _e('Custom Heading (optional)', 'bunyad-admin'); ?></label>
			<input type="text" class="widefat" name="<?php echo $this->get_field_name('heading_2'); ?>" value="" />
		</p>
		
		<p>
			<label><?php _e('Offset: (Advanced)', 'bunyad-admin'); ?></label> 
			<input type="text" name="<?php echo $this->get_field_name('offset_2'); ?>" value="0" />
		</p>
		<p class="description"><?php _e('By specifying an offset as 10 (for example), you can ignore 10 posts in the results.', 'bunyad-admin'); ?></p>
		
	</div>

	<div class="cat-3">
	
		<hr />
		
		<h3><?php _e('Third Column', 'bunyad-admin'); ?></h3>
		
		<label><?php _e('Category', 'bunyad-admin'); ?></label>
		<?php wp_dropdown_categories(array(
			'show_option_all' => __('-- None - Use a Tag --', 'bunyad-admin'), 'hierarchical' => 1, 'hide_empty' => 0, 'order_by' => 'name', 'class' => 'widefat', 'name' => $this->get_field_name('cat_3')
		)); ?>
		
		<p class="tag"><label><?php _e('or Enter a Tag:', 'bunyad-admin'); ?></label> <input type="text" name="<?php echo $this->get_field_name('tag_3'); ?>" value="" /></p>
		
		<p>
			<label><?php _e('Custom Heading (optional)', 'bunyad-admin'); ?></label>
			<input type="text" class="widefat" name="<?php echo $this->get_field_name('heading_3'); ?>" value="" />
		</p>
		
		<p>
			<label><?php _e('Offset: (Advanced)', 'bunyad-admin'); ?></label> 
			<input type="text" name="<?php echo $this->get_field_name('offset_3'); ?>" value="0" />
		</p>
		<p class="description"><?php _e('By specifying an offset as 10 (for example), you can ignore 10 posts in the results.', 'bunyad-admin'); ?></p>
		
	</div>
	
	<p>
		<label><?php _e('Post Types: (Advanced)', 'bunyad-admin'); ?></label>
		<input name="<?php echo esc_attr($this->get_field_name('post_type')); ?>" type="text" value="<?php echo esc_attr($post_type); ?>" />
	</p>
	<p class="description"><?php _e('Only for advanced users! You can use a custom post type here - multiples supported when separated by comma. Leave empty to use the default format. .', 'bunyad-admin'); ?></p>
	
	<?php /**
	<div class="taxonomydiv"> <!-- borrow wp taxonomydiv > categorychecklist css rules -->
		<label><?php _e('Categories:', 'bunyad-admin'); ?></label>
		
		<div class="tabs-panel">
			<ul class="categorychecklist">
				<?php
				ob_start();
				wp_category_checklist();
				
				echo str_replace('post_category[]', $this->get_field_name('cats') .'[]', ob_get_clean());
				?>
			</ul>			
		</div>
		
	</div>*/ ?>
	
	
	<?php
	} // form()
	
	public function add_javascript() 
	{
	?>
	
		<script type="text/javascript">

		jQuery(function($) {
			"use strict";

			var change_cols = function() {
				var cols = $('.ui-dialog:visible [name*=columns]').val(),
					ele = $('.ui-dialog:visible .cat-3');

				if (cols == 2) {
					ele.hide();
				}
				else {
					ele.show();
				}
			};

			var change_cat = function() {
				
				var cats = $('.ui-dialog:visible [name*=cat_]');
				
				$.each(cats, function() {
					if ($(this).val() == "0") {
						$(this).parent().find('.tag').show();
					}
					else {
						$(this).parent().find('.tag').hide();
					}
				});
			};

			$(document).on('panelsopen', change_cols);
			$(document).on('panelsopen', change_cat);
			$(document).on('change', '.ui-dialog [name*=columns]', change_cols);
			$(document).on('change', '.ui-dialog [name*=cat_]', change_cat);
			
		});

		</script>
	
	<?php
	} // add_javascript()
}