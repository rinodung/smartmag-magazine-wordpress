<?php 

/**
 * Meta box for post reviews
 */

include locate_template('admin/meta/options/reviews.php');
$options = $this->options($options);

$this->default_values['_bunyad_review_overall'] = (isset($this->default_values['_bunyad_review_overall']) ? $this->default_values['_bunyad_review_overall'] : '');

if (!isset($this->default_values['_bunyad_review_percent'])) {
	$this->default_values['_bunyad_review_percent'] = '';
}

?>

<div class="bunyad-meta cf">

	<input type="hidden" name="_bunyad_review_percent" value="<?php echo esc_attr($this->default_values['_bunyad_review_percent']); ?>" size="3" />

<?php foreach ($options as $element): ?>
	
	<div class="option">
		<span class="label"><?php echo esc_html($element['label']); ?></span>
		<span class="field"><?php echo $this->render($element); ?></span>
	</div>
	
<?php endforeach; ?>

	<div class="option">
		<span class="label"><?php _e('Criteria', 'bunyad-admin'); ?></span>
		<div class="field criteria">
		
			<p><input type="button" class="button add-more" value="<?php esc_attr_e('Add More Criteria', 'bunyad-admin'); ?>" /></p>
			<p><?php _e('Overall rating auto-calculated:', 'bunyad-admin'); ?> <strong>
				<input type="text" name="_bunyad_review_overall" value="<?php echo esc_attr($this->default_values['_bunyad_review_overall']); ?>" size="3" />
				</strong></p>
		</div>
	</div>

</div>

<script type="text/html" class="template-criteria">
	<div>
		<strong><?php _e('Criterion %number%', 'bunyad-admin'); ?></strong> &mdash; 
		<?php _e('Label:', 'bunyad-admin'); ?> <input type="text" name="_bunyad_criteria_label_%number%" />
		<?php _e('Rating:', 'bunyad-admin'); ?>  <input type="text" name="_bunyad_criteria_rating_%number%" size="3" /> / 10
	</div>
</script>

<style type="text/css">
	.criteria > p {
		margin-top: 0;
	}
</style>

<?php 

/*
 * Get existing reviews if editing
 */
$saved_data = array();
foreach ($this->default_values as $key => $value) {
	if (preg_match('/criteria_rating_([0-9]+)$/', $key, $match)) {
		$saved_data[] = array(
			'number' => $match[1],
			'rating' => $value,
			'label'  => $this->default_values['_bunyad_criteria_label_' . $match[1]]
		);
	}
}

$saved_data = json_encode($saved_data);

?>

<script>
jQuery(function($) {
	"use strict";
	
	var add_more = function(e, number) {

		// current count
		var tabs_count = $(this).parent().data('bunyad_tabs') || 0;
		tabs_count++;

		// get our template and modify it
		var html = $('.template-criteria').html();
		html = html.replace(/%number%/g, number || tabs_count);
		
		$('.criteria').append(html);

		// update counter
		$(this).parent().data('bunyad_tabs', tabs_count);

		return false;	
	};

	var overall_rating = function() {
		var count = 0, total = 0, number = null; 
		$('input[name*="criteria_rating"]').each(function() {

			number = parseFloat($(this).val());

			if (!isNaN(number)) {
				total += number;
				count++;
			}
		});

		var rating = (total/count).toFixed(1);
		$('.overall-rating').html(rating);
		$('input[name="_bunyad_review_overall"]').val(rating);
		$('input[name="_bunyad_review_percent"]').val(Math.round(rating / 10 * 100));
		
	};
	
	$('.criteria .add-more').click(add_more);

	$('.criteria').delegate('input[name*="criteria_rating"]', 'blur', function() {
		if ($(this).val() > 10) {
			alert("<?php esc_attr_e('Rating cannot be greater than 10.', 'bunyad-admin'); ?>");
			$(this).val(10);
		}

		overall_rating();
	});

	// add existing
	var saved = <?php echo $saved_data; ?>;

	if (saved.length) { 
		$.each(saved, function(i, val) {
			add_more.call($('.criteria .add-more'), val.number);
			$('[name=_bunyad_criteria_label_' + val.number + ']').val(val.label);
			$('[name=_bunyad_criteria_rating_' + val.number + ']').val(val.rating);
		});

		overall_rating();
	}
	else {
		$('.criteria .add-more').click();
	}
	
});
</script>