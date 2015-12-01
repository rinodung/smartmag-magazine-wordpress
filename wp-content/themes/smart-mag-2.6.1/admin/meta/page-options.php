<?php

/**
 * Options metabox for pages
 */

include locate_template('admin/meta/options/page.php');

$options = $this->options(apply_filters('bunyad_metabox_page_options', $options));

?>

<div class="bunyad-meta cf">

<?php foreach ($options as $element): ?>
	
	<div class="option <?php echo esc_attr($element['name']); ?>">
		<span class="label"><?php echo esc_html($element['label']); ?></span>
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
	$('._bunyad_featured_slider select').on('change', function() {

		var depend_default = '._bunyad_slider_number, ._bunyad_slider_posts, ._bunyad_slider_tags, ._bunyad_slider_type',
			depend_rev = '._bunyad_slider_rev';

		// hide all dependents
		$([depend_default, depend_rev].join(',')).hide();
		
		if ($(this).val() == 'rev-slider') {
			$(depend_rev).show();
		}
		else if ($(this).val() != '') {
			$(depend_default).show();
		}

		return;
	});

	// on-load
	$('._bunyad_featured_slider select').trigger('change');
		
});
</script>