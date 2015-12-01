<?php

$render = Bunyad::factory('admin/option-renderer');

?>

<p><a href="#" id="add-more-groups"><?php _e('Add More Tabs', 'bunyad-shortcodes'); ?></a></p>

<script type="text/html" class="template-group-options">

	<div class="element-control">
		<label><?php _e('Tab #<span>%number%</span> Title:', 'bunyad-shortcodes'); ?></label>
		<?php echo $render->render_text(array('name' => 'title[%number%]')); ?>
	</div>

	<div class="element-control">
		<label><?php _e('Tab #<span>%number%</span> Content:', 'bunyad-shortcodes'); ?></label>
		<?php echo $render->render_textarea(array('name' => 'content[%number%]')); ?>
	</div>

	<div style="padding-top:20px;"></div>
</script>

<script>
jQuery(function($) {
	$('#add-more-groups').click();
	Bunyad_Shortcodes_Helper.set_handler('advanced');
});
</script>