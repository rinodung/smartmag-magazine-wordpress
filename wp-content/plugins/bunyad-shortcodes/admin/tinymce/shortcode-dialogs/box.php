<?php
$render  = Bunyad::factory('admin/option-renderer');

// all the attributes
$options = array(
	array(
		'label' => __('Content', 'bunyad-shortcodes'),
		'type'  => 'text',
		'name'  => 'enclose'
	),
	
	array(
		'label' => __('Alert Type', 'bunyad-shortcodes'),
		'type'  => 'select',
		'name'  => 'style',
		'options' => array(
			'info' => __('Info', 'bunyad-shortcodes'),
			'warning' => __('Warning', 'bunyad-shortcodes'),
			'success' => __('Success', 'bunyad-shortcodes'),
			'error'   => __('Error', 'bunyad-shortcodes'),
			'download' => __('Download Arrow', 'bunyad-shortcodes'),
		),
	),

);

foreach ($options as $option) {
	echo $render->render($option);
}

?>

<script>
jQuery(function($) {

	// replace customized color - this will be hooked before main handler
	var button_handler = function() {
		
		var bg_color = $(this).find('input[name=color]'),
			preset = $(this).find('select[name=preset]');
		
		if (bg_color.val() == '') {
			bg_color.val(preset.val());
		}

		preset.remove();

		// don't return false or it will stop propagation
	};
	
	$('form.bunyad-sc-visual').submit(button_handler);
});
</script>
