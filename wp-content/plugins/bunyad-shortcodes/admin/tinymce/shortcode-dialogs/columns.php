<?php

$render  = Bunyad::factory('admin/option-renderer');

$size_options = array(
	
	'1/1' => 'Full Column', 
	'1/2' => 'Half Column (1/2)',

	'Three Columns' => array(
		'1/3' => 'One Third (1/3)',
		'2/3' => 'Two Thirds (2/3)'
	),
	
	'Four Columns' => array(
		'1/4' => 'One Fourth (1/4)',
		'1/2' => 'Two Fourths (Half)',
		'3/4' => 'Three Fourths (3/4)'
	),
	
	'Five Columns' => array(
		'1/5' => 'One Fifth (1/5)',
		'2/5' => 'Two Fifths (2/5)',
		'3/5' => 'Three Fifths (3/5)',
		'4/5' => 'Four Fifths (4/5)'
	),
);

?>

<p>
	<strong><?php _e('Pre-defined Examples:', 'bunyad-shortcodes'); ?></strong>
	<a href="#" class="predefined-cols" data-cols="3" data-val="1/3"><?php echo _e('Three equal columns', 'bunyad-shortcodes'); ?></a>, 
	<a href="#" class="predefined-cols" data-cols="4" data-val="1/4"><?php echo _e('Four equal columns', 'bunyad-shortcodes'); ?></a>,
	<a href="#" class="predefined-cols" data-cols="5" data-val="1/5"><?php echo _e('Five equal columns', 'bunyad-shortcodes'); ?></a>
</p>
 

<p><a href="#" id="add-more-groups"><?php _e('Add Another Column', 'bunyad-shortcodes'); ?></a></p>

<script type="text/html" class="template-group-options">

	<div class="divider-or"><span><?php _e('Column %number%'); ?></span></div>

	<div class="element-control">
		<label><?php _e('Size:', 'bunyad-shortcodes'); ?></label>
		<?php echo $render->render_select(array('name' => 'size[%number%]', 'options' => $size_options)); ?>
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

	var predefined = function() {
		var columns = $(this).data('cols'), size = $(this).data('val');
		
		$('#add-more-groups').parent().data('bunyad_tabs', 0);
		
		$('.element-control, .divider-or').remove();

		for (i = 1; i <= columns; i++) {
			$('#add-more-groups').click();
		}
		
		$(this).parent().parent().find('[name^="size"]').val(size).change();
		
		return false;
	};
	
	$('.predefined-cols').click(predefined);
});
</script>