<?php

/**
 * Fields to show for page meta box
 */

$rev_slider = (class_exists('RevSlider') ? array('rev-slider' => __('Revolution Slider Plugin', 'bunyad-admin')) : array()); 

$options = array(
	array(
		'label' => __('Layout Style', 'bunyad-admin'),
		'name'  => 'layout_style', // will be _bunyad_layout_style
		'type'  => 'radio',
		'options' => array(
			'' => __('Default', 'bunyad-admin'),
			'right' => __('Right Sidebar', 'bunyad-admin'),
			'full' => __('Full Width', 'bunyad-admin')),
		'value' => '' // default
	),
	
	array(
		'label' => __('Show Page Title?', 'bunyad-admin'),
		'name'  => 'page_title', 
		'type'  => 'select',
		'options' => array('yes' => 'Yes', 'no' => 'No'),
		'value' => 'yes' // default
	),
	
	array(
		'label' => __('Show Featured Area?', 'bunyad-admin'),
		'name'  => 'featured_slider',
		'type'  => 'select',
		'options' => array_merge(array(
			''	=> __('None', 'bunyad-admin'),
			'default' => __('Use Posts Marked as "Featured Slider Post?"', 'bunyad-admin'),
			'default-latest' => __('Use Latest Posts from Whole Site', 'bunyad-admin'),
		), $rev_slider),
		'value' => '' // default
	),
	
	array(
		'label' => __('Featured Style', 'bunyad-admin'),
		'name'  => 'slider_type',
		'type'  => 'select',
		'options' => array(
			''	=> __('Default Slider', 'bunyad-admin'),
			'grid' => __('Featured Grid', 'bunyad-admin'),
		),
		'value' => '' // default
	),

	array(
		'label' => __('Number of Slides', 'bunyad-admin'),
		'name'  => 'slider_number',
		'type'  => 'text',
		'desc'  => __('Number of posts to show on the left side of the slider. 3 are displayed on the right as a post grid.', 'bunyad-admin'),
		'value' => 5, // default
	),
	
	array(
		'label' => __('Slider Limit by Tag', 'bunyad-admin'),
		'name'  => 'slider_tags',
		'desc'  => __('Optional: To limit slider to certain tag or tags. If multiple, separate tag slugs by comma.', 'bunyad-admin'),
		'type'  => 'text',
		'value' => '' // default
	),
	
	array(
		'label' => __('Slider Manual Post Ids', 'bunyad-admin'),
		'name'  => 'slider_posts',
		'desc'  => __('Optional: ADVANCED! If you only want to show a set of selected pre-selected posts. Enter post ids separated by comma.', 'bunyad-admin'),
		'type'  => 'text',
		'value' => '' // default
	),
	
	array(
		'label' => __('Revolution Slider Alias', 'bunyad-admin'),
		'name'  => 'slider_rev',
		'desc'  => __('Enter alias of a slider you created in revolution slider plugin.', 'bunyad-admin'),
		'type'  => 'text',
		'value' => '' // default
	),
);

if (Bunyad::options()->layout_style == 'boxed') {
	
	$options[] = array(
		'label' => __('Custom Background Image', 'bunyad-admin'),
		'name'  => 'bg_image',
		'type' => 'upload',
		'options' => array(
				'type'  => 'image',
				'title' => __('Upload This Picture', 'bunyad-admin'), 
				'button_label' => __('Upload', 'bunyad-admin'),
				'insert_label' => __('Use as Background', 'bunyad-admin')
		),	
		'value' => '', // default
		'bg_type' => array('value' => 'cover'),
	);
}
