<?php

/**
 * Fields to show for page meta box
 */

$options = array(
	array(
		'label' => __('Enable Review?', 'bunyad-admin'),
		'name'  => 'reviews', 
		'type'  => 'checkbox',
		'value' => 0,
	),
	
	array(
		'label' => __('Display Position', 'bunyad-admin'),
		'name'  => 'review_pos',
		'type'  => 'select',
		'options' => array(
			'none' => __('Do not display - Disabled', 'bunyad-admin'), 
			'top'  => __('Top', 'bunyad-admin'),
			'bottom' => __('Bottom', 'bunyad-admin')
		)
	),
	
	array(
		'label' => __('Show Rating As', 'bunyad-admin'),
		'name'  => 'review_type',
		'type'  => 'radio',
		'options' => array(
			'percent' => __('Percentage', 'bunyad-admin'),
			'points'  => __('Points', 'bunyad-admin'),
			'stars'   => __('Stars', 'bunyad-admin'),
		), 
		'value' => 'points',
	),
	
	array(
		'label' => __('Heading (optional)', 'bunyad-admin'),
		'name'  => 'review_heading',
		'type'  => 'text',
	),
	
	array(
		'label' => __('Verdict', 'bunyad-admin'),
		'name'  => 'review_verdict',
		'type'  => 'text',
		'value' => __('Awesome', 'bunyad-admin'),
	),
	
	array(
		'label' => __('Verdict Summary', 'bunyad-admin'),
		'name'  => 'review_verdict_text',
		'type'  => 'textarea',
		'options' => array('rows' => 5, 'cols' => 90),
		'value' => '',
	),
	
);