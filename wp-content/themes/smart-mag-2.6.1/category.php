<?php

/**
 * Category Template
 * 
 * Sets up the correct loop format to use. Additionally, meta is processed for other
 * layout preferences.
 */

global $bunyad_loop_template;

$category = get_category(get_query_var('cat'), false);
$cat_meta = Bunyad::options()->get('cat_meta_' . $category->term_id);

// save current options so that can they can be restored later
$options = Bunyad::options()->get_all();

if (!$cat_meta OR !$cat_meta['template']) {
	$cat_meta['template'] = Bunyad::options()->default_cat_template;
}

/**
 * Select the listing template to use
 */

// timeline template
if ($cat_meta['template'] == 'timeline') {
	
	$bunyad_loop_template = 'loop-timeline';
	
	$category = get_category(get_query_var('cat'), false);
	$cat_meta = Bunyad::options()->get('cat_meta_' . $category->term_id);
	
	if (empty($cat_meta['per_page'])) {
		query_posts(array('cat' => $category->term_id, 'posts_per_page' => -1));
	}
	
}
// default modern template
else {
	
	if (in_array($cat_meta['template'], array('alt', 'classic', 'grid-overlay', 'grid-overlay-3', 'tall-overlay'))) {
		$bunyad_loop_template = 'loop-' . str_replace('-3', '', $cat_meta['template']);
	}
	else {
		$bunyad_loop_template = 'loop';
	}
	
	// set loop grid type
	$loop_grid = '';
	
	if (in_array($cat_meta['template'], array('modern-3', 'grid-overlay-3'))) {
		$loop_grid = 3;
	}

	Bunyad::registry()->set('loop_grid', $loop_grid);
}

// have a sidebar preference?
if (!empty($cat_meta['sidebar'])) {
	Bunyad::core()->set_sidebar($cat_meta['sidebar']);
}

// enable infinite scroll?
if (!empty($cat_meta['pagination_type'])) {
	
	// normal is default - empty in options
	Bunyad::options()->set('pagination_type', ($cat_meta['pagination_type'] == 'normal' ? '' : $cat_meta['pagination_type']));
}


get_template_part('archive');

// restore modified options
Bunyad::options()->set_all($options);
