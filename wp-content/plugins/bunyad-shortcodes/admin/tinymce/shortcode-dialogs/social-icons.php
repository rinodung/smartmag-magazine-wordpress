<?php

$render = Bunyad::factory('admin/option-renderer');

// all the attributes
$options = array(
	array(
		'label' => __('Icons Size', 'bunyad-shortcodes'),
		'type'  => 'select',
		'name'  => 'type',
		'options' => array(
			'' => __('Small', 'bunyad-shortcodes'),
			'medium' => __('Medium', 'bunyad-shortcodes'),
			'large' => __('Large', 'bunyad-shortcodes'),
			'x-large' => __('Extra Large', 'bunyad-shortcodes'),
		),
	),
	
	array(
		'label' => __('Backgrounds', 'bunyad-shortcodes'),
		'type'  => 'select',
		'name'  => 'backgrounds',
		'options' => array(
			'' => __('Transparent', 'bunyad-shortcodes'),
			'1' => __('Colored', 'bunyad-shortcodes'),
		),
	),

);

foreach ($options as $option) {
	echo $render->render($option);
}

?>

<p><hr /></p>

<p><a href="#" id="add-more-groups"><?php _e('Add Icon', 'bunyad-shortcodes'); ?></a></p>

<script type="text/html" class="template-group-options">

	<div class="container">
	<div class="element-control">
		<label><?php _e('Icon Type:', 'bunyad-shortcodes'); ?></label>
		<?php echo $render->render_select(array('name' => 'icon_type[]', 'options' => array(
			'' => __('- Select -', 'bunyad-shortcodes'),
			'facebook' => __('Facebook', 'bunyad-shortcodes'),
			'twitter' => __('Twitter', 'bunyad-shortcodes'),
			'linkedin' => __('LinkedIn', 'bunyad-shortcodes'),
			'google-plus' => __('Google+', 'bunyad-shortcodes'),
			'pinterest' => __('Pinterest', 'bunyad-shortcodes'),
			'dribbble' => __('Dribbble', 'bunyad-shortcodes'),
			'youtube' => __('YouTube', 'bunyad-shortcodes'),
			'instagram' => __('Instagram', 'bunyad-shortcodes'),
			
		))); ?>
	</div>

	<div class="element-control">
		<label><?php _e('Icon Name:', 'bunyad-shortcodes'); ?></label>
		<?php echo $render->render_text(array('name' => 'type[%number%]')); ?>
	</div>

	<div class="element-control">
		<label><?php _e('Link:', 'bunyad-shortcodes'); ?></label>
		<?php echo $render->render_text(array('name' => 'link[%number%]')); ?>
	</div>

	<div class="element-control">
		<label><?php _e('Custom Text Color:', 'bunyad-shortcodes'); ?></label>
		<?php echo $render->render_color_picker(array('name'  => 'color[%number%]')); ?>
	</div>

	<div class="element-control">
		<label><?php _e('Custom Background Color:', 'bunyad-shortcodes'); ?></label>
		<?php echo $render->render_color_picker(array('name'  => 'bg[%number%]')); ?>
	</div>

	<span><input type="hidden" name="sc-group[%number%]" /></span>

	<div style="padding-top:20px;"></div>
	</div>

</script>

<script>
jQuery(function($) {
	$('#add-more-groups').click();
	Bunyad_Shortcodes_Helper.set_handler('advanced');

	$(document).on('change', '[name^=icon_type]', function() {
		if (!$(this).val()) {
			return;
		}
		$(this).closest('.container').find('[name*=type]').val( $(this).val() );
	});
});
</script>