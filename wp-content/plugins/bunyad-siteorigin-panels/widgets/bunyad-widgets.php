<?php

class Bunyad_PbBasic_Separator extends WP_Widget
{
	public function __construct()
	{
		parent::__construct(
			'bunyad_pagebuilder_separator',
			__('Separator - Space or Line', 'so-panels'),
			array('description' => __('Create an empty space or line separator.', 'so-panels'))
		);
	}
	
	public function widget($args, $instance)
	{
		extract($args);
		
		// supported attrs		
		$attrs = array('posts', 'cat', 'column');
		$sc_attrs = array();
		
		if (!empty($instance['type'])) {
			$sc_attrs[] = 'type="'. esc_attr($instance['type']) .'"';
		}
		
		// do_shortcode will be run by pagebuilder		
		echo '[separator '. implode(' ', $sc_attrs) .' /]';
		
	}
	
	public function form($instance)
	{
		$defaults = array('type' => '', 'image' => '', 'text' => '', 'email' => '', 'posts' => '');
		$instance = array_merge($defaults, (array) $instance);
		extract($instance);
				
		$render = Bunyad::factory('admin/option-renderer'); /* @var $render Bunyad_Admin_OptionRenderer */
		
		
	?>
	
	<input type="hidden" name="<?php echo $this->get_field_name('no_container'); ?>" value="1" />
	
	
	<p><strong><?php _e('Important:', 'so-panels'); ?></strong> <?php 
		_e('This block automatically shows the sub-categories under a parent category. Only a parent category needs to be selected.', 'so-panels'); ?></p>
	
	<p>
		<label><?php _e('Separator Type:', 'so-panels'); ?></label>
		<select class="widefat" name="<?php echo esc_attr($this->get_field_name('type')); ?>">
			<option value="line"><?php _e('Line', 'so-panels'); ?></option>
			<option value="space"><?php _e('Space', 'so-panels'); ?></option>
			<option value="half-line"><?php _e('Half Height Line', 'so-panels'); ?></option>
			<option value="half-space"><?php _e('Half Height Space', 'so-panels'); ?></option>
		</select>
	</p>
	
	<?php
	}
}

/**
 * Rich Text widget for page builder
 */
class Bunyad_PbBasic_RichText extends WP_Widget {

	public function __construct()
	{
		parent::__construct(
			'bunyad_pagebuilder_richtext',
			__('Rich Text - Visual Editor', 'so-panels'),
			array('description' => __('Visual editor for text and HTML.', 'so-panels'))
		);
	}

	public function widget($args, $instance) 
	{
		extract($args);
		
		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		$text = apply_filters('widget_text', $instance['text'], $instance);
		
		echo $before_widget;
		
		if (!empty($title)) { 
			echo $before_title . $title . $after_title; 
		}
		 
		?>
		
			<div class="textwidget post-content">
				<?php echo do_shortcode( !empty($instance['filter']) ? wpautop($text) : $text ); ?>
			</div>
			
		<?php
		
		echo $after_widget;
	}

	function update($new_instance, $old)
	{
		$instance = array();
		
		if (current_user_can('unfiltered_html')) {
			$instance['text'] =  $new_instance['text'];
		}
		else {
			$instance['text'] = wp_kses($new_instance['text'] , 'post');
			//$instance['text'] = stripslashes(wp_filter_post_kses(addslashes($new_instance['text']))); // wp_filter_post_kses() expects slashed
		}
		
		return $instance;
	}

	function form($instance) 
	{
		$instance = wp_parse_args((array) $instance, array('title' => '', 'text' => '', 'type' => 'visual', 'filter' => 1));
		$title = strip_tags($instance['title']);
		$text = esc_textarea($instance['text']);
			
        // enqueue js to the footer
        add_action('admin_print_footer_scripts', array($this, 'add_javascript'), 60);

		
?>
		<input type="hidden" class="<?php echo $this->get_field_name('the_id'); ?>" value="<?php echo str_replace('{$id}', '-id-', $this->get_field_id('text')); ?>" />

		<div>
	
		<?php 
			$settings = array('media_buttons' => true, 'textarea_name' => $this->get_field_name('text'));
			wp_editor($text, str_replace('{$id}', '-id-', $this->get_field_id('text')), $settings);
		?>
        </div>
        
		<p>
			<input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php 
			checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php
				 _e('Automatically add paragraphs', 'so-panels'); ?></label>
		 </p>
		
        <?php

	}
	
	/**
	 * Action callback: Print to admin footer
	 */
	public function add_javascript()
	{
		
	?>
	<script type="text/javascript">
		jQuery(function($) {
			"use strict";
				
			var Bunyad_PbBasic_RichText = (function() {

				var self;

				return {
					cur_ID: null,
					the_ID: null,
	
					init: function()
					{
						self = this;
						
						// bind to panel events
						$(document).on('panelsopen', this.open);
						$(document).on('panelsdone', this.close);
						$(document).on('dialogbeforeclose', this.close);
	
	
						// set current editor for add media button
						$(document).on('click', '.ui-dialog:visible .insert-media', function() {
							self.set_ids();
							wpActiveEditor = self.cur_ID;
						});
	
					},
						
					set_ids: function() {
						self.cur_ID = $('.ui-dialog:visible [class*=the_id]').val();
						self.the_ID = '<?php echo esc_attr(str_replace('{$id}', '-id-', $this->get_field_id('text'))); ?>';
					},
	
					open: function()
					{						
						self.set_ids();
	
						// get preInit data pre-loaded in footer JS
						tinyMCEPreInit.mceInit[self.cur_ID] = tinyMCEPreInit.mceInit[self.the_ID];
						tinyMCEPreInit.qtInit[self.cur_ID] = tinyMCEPreInit.qtInit[self.the_ID];
						
						var init = tinyMCEPreInit.mceInit[self.cur_ID],
							qt_init = tinyMCEPreInit.qtInit[self.cur_ID];
	
						
						// set correct ids
						init.elements = self.cur_ID;
						init.selector = '#' + self.cur_ID;
						qt_init.id = self.cur_ID;
						
						try 
						{					
							// activate quicktags if not active
							//if (!$('.ui-dialog:visible .quicktags-toolbar').length) {
								quicktags(qt_init);
							//}
	
							tinyMCE.init(init);
							
						} catch (e) {};
	
						self.set_visual();
					},
	
					set_visual: function() {
	
						// activate the visual tab
						if (tinyMCE.get(self.cur_ID) != null) {
							$('.ui-dialog:visible .wp-editor-wrap').addClass('tmce-active').find('.switch-tmce').click();
						}
						else {
							setTimeout(self.set_visual, 100);
						}
					},
	
					close: function()
					{
						self.set_ids();
						
						if ($('.ui-dialog:visible textarea[id*=richtext]').length) {

							// remove the editor
							try {
	
								self.set_visual();
								
								var content = tinyMCE.get(self.cur_ID).getContent();
								tinyMCE.execCommand("mceRemoveControl", false, self.cur_ID);
								
								$('textarea#' + self.cur_ID).val(content);
							}
							catch (e) { }
						}
					}

				} // end return
			})();

			Bunyad_PbBasic_RichText.init();

		});
	</script>
		
	<?php 
	
	} // add_javascript()

}

