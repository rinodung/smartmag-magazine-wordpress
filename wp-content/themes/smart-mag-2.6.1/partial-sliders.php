<?php

/**
 * Partial Template - Display the featured slider and the blocks
 */

// using revolution slider? output and return
if (Bunyad::posts()->meta('featured_slider') == 'rev-slider' && function_exists('putRevSlider')) {
	
	echo '<div class="main-featured"><div class="wrap cf">'
		. do_shortcode('[rev_slider ' . esc_attr(Bunyad::posts()->meta('slider_rev')) .']')
		. '</div></div>';
	
	return;
}

// setup configuration vars
$data_vars = array(
	'data-animation' => Bunyad::options()->slider_animation,
	'data-animation-speed' => intval(Bunyad::options()->slider_animation_speed),
	'data-slide-delay' => Bunyad::options()->slider_slide_delay,
);


// featured posts query args
$args = array('meta_key' => '_bunyad_featured_post', 'meta_value' => 1, 'order' => 'date', 'ignore_sticky_posts' => 1);

/**
 * Category slider?
 */
if (is_category()) {
	
	$cat = get_query_var('cat');
	$meta = Bunyad::options()->get('cat_meta_' . $cat);
	
	// slider not enabled? quit!
	if (empty($meta['slider'])) {
		return;
	}
		
	$args['cat'] = $cat;
	
	// latest posts?
	if ($meta['slider'] == 'latest') {
		unset($args['meta_key'], $args['meta_value']);
	}
	
	$slider_type = (!empty($meta['slider_type']) ? $meta['slider_type'] : '');
	$number = null;
	
	if (!empty($meta['slider_tags'])) {
		$args['tag_slug__in'] = explode(',', $meta['slider_tags']);
	}
	
}
else {
	
	// Normal slider on a page
	$slider_type = Bunyad::posts()->meta('slider_type');
	$number = intval(Bunyad::posts()->meta('slider_number'));
	

	// limited to tag?
	if (Bunyad::posts()->meta('slider_tags')) {
		$args['tag_slug__in'] = explode(',', Bunyad::posts()->meta('slider_tags'));
	}
	
	// manual post ids?
	if (Bunyad::posts()->meta('slider_posts')) {
		$args['post__in'] = explode(',', Bunyad::posts()->meta('slider_posts'));
	}
	
	// use latest posts?
	if (Bunyad::posts()->meta('featured_slider') == 'default-latest') {
		unset($args['meta_key'], $args['meta_value']);
	}
}


/**
 * Slider Type - set relevant variables
 */

switch ($slider_type) {
	
	case 'grid':
		$slider   = 'grid';
		$per_page = $main_limit = ($number ? $number : 5);
		break;
		
	default:
		$slider  = 'classic';
		
		// add +3 for right side grid of classic slider
		$main_limit = ($number ? $number : 5);
		$per_page   =  $main_limit + 3;
		
		break;
}

$args['posts_per_page'] = $per_page;


/**
 * Main slider posts query and apply filters for args and query
 */
$args  = apply_filters('bunyad_block_query_args', $args, 'slider'); // for legacy support
$query = apply_filters('bunyad_featured_area_query', new WP_Query($args));

if (!$query->have_posts()) {
	return;
}


/**
 * Include our slider!
 * 
 * Get the template from partials/slider/ folder depending on selected
 * slder.
 * 
 * @see locate_template()  Used to preserve variable scope
 */

include locate_template('partials/slider/' . $slider . '.php');
