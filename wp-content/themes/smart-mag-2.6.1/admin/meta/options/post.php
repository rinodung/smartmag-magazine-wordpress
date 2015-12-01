<?php
/**
 * Fields to show for posts meta box
 */

$options = array(

	array(
		'label' => __('Featured Slider Post?', 'bunyad-admin'),
		'name'  => 'featured_post', // _bunyad_featured_post
		'type'  => 'checkbox',
		'value' => 0
	),
	
	array(
		'label' => __('Post Layout', 'bunyad-admin'),
		'name'  => 'layout_template', // will be _bunyad_layout_style
		'type'  => 'select',
		'options' => array(
			'' => __('Default (from Theme Settings)', 'bunyad-admin'),
			'classic' => __('Classic', 'bunyad-admin'),
			'cover' => __('Post Cover', 'bunyad-admin'),
			'classic-above' => __('Classic - Title First', 'bunyad-admin'),
		),
		'value' => '' // default
	),
	
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
		'label' => __('Category Label Overlay', 'bunyad-admin'),
		'name'  => 'cat_label', // _bunyad_cat_label
		'type'  => 'html',
		'html' =>  wp_dropdown_categories(array(
			'show_option_all' => __('-- Auto Detect--', 'bunyad-admin'), 
			'hierarchical' => 1, 'order_by' => 'name', 'class' => '', 
			'name' => '_bunyad_cat_label', 'echo' => false,
			'selected' => Bunyad::posts()->meta('cat_label')
		)),
		'desc' => __('When you have multiple categories for a post, auto detection chooses one in alphabetical order. These labels are shown above image in category listings.', 'bunyad-admin')
	),
	
	array(
		'label' => __('Multi-page Content Slideshow?', 'bunyad-admin'),
		'desc' => __('You can use <!--nextpage--> to split a page into multi-page content slideshow.', 'bunyad-admin'),
		'name'  => 'content_slider', // _bunyad_featured_post
		'type'  => 'select',
		'value' => 0,
		'options' => array(
			'' => __('Disabled', 'bunyad-admin'),
			'ajax' => __('AJAX - No Refresh', 'bunyad-admin'),
			'refresh'  => __('Multi-page - Refresh for next page', 'bunyad-admin'), 
		),
	),
	
	
	array(
		'label_left' => __('Disable Featured?', 'bunyad-admin'),
		'label' => __('Do not show featured Image, Video, or Gallery at the top for this post, on post page.', 'bunyad-admin'),
		'name'  => 'featured_disable', // _bunyad_featured_post
		'type'  => 'checkbox',
		'value' => 0
	),
	
	array(
		'label' => __('Featured Video Code', 'bunyad-admin'),
		'name'  => 'featured_video', // will be _bunyad_layout_style
		'type'  => 'textarea',
		'options' => array('rows' => 7, 'cols' => 90),
		'value' => '',
		'allowed_html' => array('iframe' => array('scrolling' => true, 'src' => true, 'width' => true, 'height' => true, 'frameborder' => true))
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