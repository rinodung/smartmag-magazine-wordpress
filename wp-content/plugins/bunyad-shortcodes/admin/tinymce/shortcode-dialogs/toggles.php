<?php

$render  = Bunyad::factory('admin/option-renderer');

?>

<p><a href="#" id="add-more-groups"><?php _e('Add More Toggles', 'bunyad-shortcodes'); ?></a></p>

<script type="text/html" class="template-group-options">

	<div class="divider-or"><span><?php _e('Toggle Box %number%'); ?></span></div>

	<div class="element-control">
		<label><?php _e('Title:', 'bunyad-shortcodes'); ?></label>
		<?php echo $render->render_text(array('name' => 'title[%number%]')); ?>
	</div>

	<div class="element-control">
		<label><?php _e('Default:', 'bunyad-shortcodes'); ?></label>
		<?php echo $render->render_select(array('name' => 'load[%number%]', 'options' => array('hide' => 'Hide', 'show' => 'Show'))); ?>
		<span class="help"><?php _e('Whether to show or hide the content by default.', 'bunyad-shortcodes'); ?></span>
	</div>

	<div class="element-control">
		<label><?php _e('Content:', 'bunyad-shortcodes'); ?></label>
		<?php echo $render->render_textarea(array('name' => 'content[%number%]')); ?>
	</div>

	
</script>

<script>
jQuery(function($) {
	$('#add-more-groups').click();
	Bunyad_Shortcodes_Helper.set_handler('advanced');
});
</script>