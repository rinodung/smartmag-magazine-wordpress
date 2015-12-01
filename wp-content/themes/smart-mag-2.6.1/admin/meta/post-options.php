<?php
/**
 * Meta box for post options
 */

include locate_template('admin/meta/options/post.php');

$options = $this->options($options);

?>

<div class="bunyad-meta cf">

	<input type="hidden" name="bunyad_meta_box" value="post">

<?php foreach ($options as $element): ?> 
	
	<div class="option <?php echo esc_attr($element['name']); ?>">
		<span class="label"><?php echo esc_html(isset($element['label_left']) ? $element['label_left'] : $element['label']); ?></span>
		<span class="field">
			<?php echo $this->render($element); ?>
		
			<?php if (!empty($element['desc'])): ?>
			
			<p class="description"><?php echo esc_html($element['desc']); ?></p>
		
			<?php endif;?>
		
		</span>
	</div>
	
<?php endforeach; ?>

</div>

<?php wp_enqueue_script('theme-options', get_template_directory_uri() . '/admin/js/options.js', array('jquery')); ?>

<script>
/**
 * Conditional show/hide 
 */

jQuery(function($) {

	/**
	 * Hide disable featured and featured video option on cover layout
	 */
	var default_layout = '<?php echo esc_js(Bunyad::options()->post_layout_template); ?>';
	
	$('._bunyad_layout_template select').on('change', function() {

		var depend = '._bunyad_featured_disable, ._bunyad_featured_video', layout = '';

		// if current selection is cover or the default is cover format
		if ($(this).val() == 'cover' || (!$(this).val() && default_layout == 'cover')) {
			layout = 'cover';
		}
		
		(layout == 'cover' ? $(depend).hide() : $(depend).show());
	});

	// on-load
	$('._bunyad_layout_template select').trigger('change');
		
});
</script>