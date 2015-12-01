<?php
/**
 * Category meta template.
 * 
 * WARNING: Include it in an action callback only. 
 */

// editing?
$meta = array('template' => '', 'sidebar' => '', 'color' => '', 'bg_image' => '', 'slider' => '', 'slider_type' => '', 'slider_tags' => '', 'main_color' => '', 'per_page' => '', 'pagination_type' => '');

if (is_object($term)) {
	$meta = array_merge($meta, (array) Bunyad::options()->get('cat_meta_' . $term->term_id));
}

$render = Bunyad::factory('admin/option-renderer'); /* @var $render Bunyad_Admin_OptionRenderer */

?>

<tr class="form-field">
	<th scope="row" valign="top"><label for="meta[template]"><?php _e('Category Listing Style', 'bunyad-admin'); ?></label></th>
	<td>
		<?php
			$listing_options = array_merge(
				array(0 => __('Default Style (In Theme Settings)', 'bunyad-admin')), 
				Bunyad::options()->defaults['default_cat_template']['options']
			);
			
			echo $render->render_select(array(
				'name' => 'meta[template]',
				'options' => $listing_options,
				'value' => $meta['template'],
			));
		?>
		<p class="description custom-meta"><?php _e('Select a template to use for this category. It is not recommended to use 3 columns with a sidebar.', 'bunyad-admin'); ?></p>
	</td>
</tr>

<tr class="form-field">
	<th scope="row" valign="top"><label for="meta[template]"><?php _e('Show Sidebar?', 'bunyad-admin'); ?></label></th>
	<td>
		<?php 
			echo $render->render_select(array(
				'name' => 'meta[sidebar]',
				'options' => array('' => __('Default', 'bunyad-admin'), 'none' => __('No Sidebar', 'bunyad-admin'), 'right' => __('Right Sidebar', 'bunyad-admin')),
				'value' => $meta['sidebar'],
			));
		?>
		<p class="description custom-meta"><?php _e('Select layout sidebar preference for this category\'s listing.', 'bunyad-admin'); ?></p>
	</td>
</tr>

<tr class="form-field">
	<th scope="row" valign="top"><label for="meta[template]"><?php _e('Featured Area?', 'bunyad-admin'); ?></label></th>
	<td>
		<?php 
			echo $render->render_select(array(
				'name' => 'meta[slider]',
				'options' => array(
					'' => __('Disabled', 'bunyad-admin'),
					'default' => __('Show Posts Marked for Featured Slider', 'bunyad-admin'),
					'latest' => __('Show Latest Posts/Use a tag filter', 'bunyad-admin'),
				),
				'value' => $meta['slider'],
			));
		?>
		<p class="description custom-meta"><?php _e('Featured slider will display on category listing.', 'bunyad-admin'); ?></p>
	</td>
</tr>

<tr class="form-field">
	<th scope="row" valign="top"><label for="meta[slider_tags]"><?php _e('Featured Filter by Tag(s)', 'bunyad-admin'); ?></label></th>
	<td>
		<input type="text" name="meta[slider_tags]" class="input-small" value="<?php echo esc_html($meta['slider_tags']); ?>" />
		<p class="description custom-meta"><?php _e('Enter a tag slug to limit posts from.', 'bunyad-admin'); ?></p>
	</td>
</tr>

<tr class="form-field">
	<th scope="row" valign="top"><label for="meta[slider_type]"><?php _e('Featured Style', 'bunyad-admin'); ?></label></th>
	<td>
		<?php 
			echo $render->render_select(array(
				'name' => 'meta[slider_type]',
				'options' => array(
					'' => __('Default Slider', 'bunyad-admin'),
					'grid' => __('Featured Grid', 'bunyad-admin'),
				),
				'value' => $meta['slider_type'],
			));
		?>
		<p class="description custom-meta"></p>
	</td>
</tr>


<tr class="form-field">
	<th scope="row" valign="top"><label for="meta[per_page]"><?php _e('Posts Per Page (Optional)', 'bunyad-admin'); ?></label></th>
	<td>
		<?php 
			echo $render->render_text(array(
				'name' => 'meta[per_page]',
				'value' => $meta['per_page'],
				'input_type'  => 'number',
				'input_class' => 'input-number',
			));
		?>
		<p class="description custom-meta"><?php printf(
			__('Override default posts per page setting for this category. Leave empty for default (from Settings > Reading): %s', 'bunyad-admin'),
			esc_attr(get_option('posts_per_page'))); ?></p>
	</td>
</tr>

<tr class="form-field">
	<th scope="row" valign="top"><label for="meta[pagination_type]"><?php _e('Pagination Type', 'bunyad-admin'); ?></label></th>
	<td>
		<?php 
			echo $render->render_select(array(
				'name' => 'meta[pagination_type]',
				'options' => array(
					'' => __('Default', 'bunyad-admin'),
					'normal' => __('Normal', 'bunyad-admin'),
					'infinite' => __('Infinite Scroll', 'bunyad-admin'),
				),
				'value' => $meta['pagination_type'],
			));
		?>
		<p class="description custom-meta"><?php _e('Infinite scroll can be globally enabled/disabled from Theme Settings.', 'bunyad-admin'); ?></p>
	</td>
</tr>

<tr class="form-field">
	<th scope="row" valign="top"><label for="meta[color]"><?php _e('Category Color', 'bunyad-admin'); ?></label></th>
	<td>
		<input type="text" name="meta[color]" class="colorpicker" value="<?php echo esc_html($meta['color']); ?>" data-default-color="#e54e53" />
		<p class="description custom-meta"><?php _e('SmartMag uses this in several areas such as navigation and homepage blocks.', 'bunyad-admin'); ?></p>
	</td>
</tr>

<tr class="form-field">
	<th scope="row" valign="top"><label for="meta[main_color]"><?php _e('Main Site Color', 'bunyad-admin'); ?></label></th>
	<td>
		<input type="text" name="meta[main_color]" class="colorpicker" value="<?php echo esc_html($meta['main_color']); ?>" data-default-color="#e54e53" />
		<p class="description custom-meta"><?php _e('Setting this color will change the entire site\'s main color when viewing this category or posts that belong to this category.', 'bunyad-admin'); ?></p>
	</td>
</tr>

<?php if (Bunyad::options()->layout_style == 'boxed'): ?>
<tr class="form-field">
	<th scope="row" valign="top"><label for="meta[bg_image]"><?php _e('Category Background', 'bunyad-admin'); ?></label></th>
	<td>
		<?php 
		echo $render->render_upload(array(
			'name'  => 'meta[bg_image]',
			'value' => $meta['bg_image'],
			'options' => array(
				'type'  => 'image',
				'title' => __('Upload This Picture', 'bunyad-admin'), 
				'button_label' => __('Upload', 'bunyad-admin'),
				'insert_label' => __('Use as Background', 'bunyad-admin')
			),
		));
		?>
		<p class="description custom-meta"><?php 
			_e('SmartMag can use an image as body background in boxed layout. Note: It is not a repeating pattern. A large photo is to be used as background.', 'bunyad-admin'); ?></p>
	</td>
</tr>

<?php endif; ?>
