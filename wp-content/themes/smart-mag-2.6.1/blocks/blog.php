<?php

/**
 * Determine the listing style to use
 */
if (empty($type)) {
	$type = Bunyad::options()->default_cat_template;
}

// loop template
$template = strstr($type, 'modern') ? 'loop' : 'loop-' . $type;

// set loop grid type
$loop_grid = '';

if (strstr($type, '-3')) {
	$loop_grid = 3;
	
	// remove -3 suffix
	$template = str_replace('-3', '', $template);
}

Bunyad::registry()->set('loop_grid', $loop_grid);


// save current options so that can they can be restored later
$options = Bunyad::options()->get_all();

// enable pagination if infinite scroll is enabled - required
if ($pagination_type == 'infinite') {
	$pagination = 1;
}

Bunyad::options()
	->set('blog_no_pagination', ($pagination == 0 ? 1 : 0)) // inverse the original pagination option; different meaning
	->set('pagination_type', $pagination_type);
	
	

if ($heading && $heading_type != 'block'):

?>
	<h1 class="main-heading prominent"><?php echo $heading; ?></h1>
<?php
elseif ($heading):
?>
	<h3 class="section-head prominent cat-text-<?php echo esc_attr($cat); ?>"><?php echo $heading; ?></h3>
<?php
endif;

/**
 * Setup the loop query
 */

// globals to match load_template() - required for loop timeline
global $bunyad_loop, $post, $wp_query;

$page = (is_front_page() ? get_query_var('page') : get_query_var('paged'));
$vars = array('paged' => $page, 'posts_per_page' => intval($posts), 'order' => ($sort_order == 'asc' ? 'asc' : 'desc'), 'offset' => ($offset ? $offset : ''), 'ignore_sticky_posts' => 1);

// have a custom taxonomy?
if (!empty($taxonomy)) {
	$vars['tax_query'] = array(array(
		'taxonomy' => $taxonomy,
		'field' => 'id',
		'terms' => (array) explode(',', $cats)
	));
}
else {
	
	// add main cat
	if (!empty($cat)) {
		$cats = $cat .','. $cats;
	}
	
	// or limiting via cats or tags
	if (!empty($cats)) {
		$vars['cat'] = $cats;
	}

	if (!empty($tags)) {
		$vars['tag'] = $tags;	
	}
}

// sorting
if ($sort_by == 'modified') {
	$vars['orderby'] = 'modified';
}
else if ($sort_by == 'random') {
	$vars['orderby'] = 'rand';
}

// main loop
$bunyad_loop = new WP_Query(apply_filters('bunyad_block_query_args', $vars, 'blog', $atts));

// get our loop template with include to preserve local variable scope
include locate_template(sanitize_file_name($template . '.php'));

// enqueue the js to footer
if (Bunyad::options()->pagination_type == 'infinite') {
	wp_enqueue_script('smartmag-infinite-scroll');
}

// restore all options
Bunyad::options()->set_all($options);
wp_reset_query();
