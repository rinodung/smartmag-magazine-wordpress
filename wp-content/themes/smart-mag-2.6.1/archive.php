<?php

/**
 * Archives Page!
 * 
 * This page is used for all kind of archives from custom post types to blog to 'by date' archives.
 * 
 * Bunyad framework recommends this template to be used as generic template wherever any sort of listing 
 * needs to be done.
 * 
 * Types of archives handled:
 * 
 *  - Categories
 *  - Tags
 *  - Taxonomies
 *  - Date Archives
 *  - Custom Post Types
 * 
 * @link http://codex.wordpress.org/images/1/18/Template_Hierarchy.png
 */

global $bunyad_loop_template;

get_header();

// no template set?
if (empty($bunyad_loop_template)) {
	
	$template = Bunyad::options()->archive_loop_template;
	
	// modern loop template is loop.php (default)
	if (!empty($template)) {
		
		if (!strstr($template, 'modern')) {
			$bunyad_loop_template = 'loop-' . str_replace('-3', '', $template);
		}
		
		if (strstr($template, '-3')) {
			Bunyad::registry()->loop_grid = 3;
		}
	}
}

// slider for categories
if (is_category()) {
	$meta = Bunyad::options()->get('cat_meta_' . get_query_var('cat'));
	get_template_part('partial-sliders');
}

// enqueue the infinite scrol js if needed
if (Bunyad::options()->pagination_type == 'infinite') {
	wp_enqueue_script('smartmag-infinite-scroll');
}

?>

<div class="main wrap cf">
	<div class="row">
		<div class="col-8 main-content">
	
		<?php 
		/* can be combined into one below with is_tag() || is_category() || is_tax() - extended for customization */
		?>
		
		<?php if (is_tag()): ?>
		
			<h2 class="main-heading"><?php printf(__('Browsing: %s', 'bunyad'), '<strong>' . single_tag_title( '', false ) . '</strong>'); ?></h2>
			
			<?php if (tag_description()): ?>
				<div class="cat-description post-content"><?php echo do_shortcode(tag_description()); ?></div>
			<?php endif; ?>
		
		<?php elseif (is_category()): // category page ?>
		
			<h2 class="main-heading"><?php printf(__('Browsing: %s', 'bunyad'), '<strong>' . single_cat_title('', false) . '</strong>'); ?></h2>
			
			<?php if (category_description()): ?>
				<div class="cat-description post-content"><?php echo do_shortcode(category_description()); ?></div>
			<?php endif; ?>
			
		<?php elseif (is_tax()): // custom taxonomies ?>
			
			<h2 class="main-heading"><?php printf(__('Browsing: %s', 'bunyad'), '<strong>' . single_term_title('', false) . '</strong>'); ?></h2>
			
			<?php if (term_description()): ?>
				<div class="cat-description post-content"><?php echo do_shortcode(term_description()); ?></div>
			<?php endif; ?>
			
		<?php elseif (is_search()): // search page ?>
			<?php $results = $wp_query->found_posts; ?>
			<h2 class="main-heading"><?php printf(__('Search Results: %s (%d)', 'bunyad'),  get_search_query(), $results); ?></h2>
			
		<?php elseif (is_archive()): ?>
			<h2 class="main-heading"><?php
	
			if (is_day()):
				printf(__('Daily Archives: %s', 'bunyad'), '<strong>' . get_the_date() . '</strong>');
			elseif (is_month()):
				printf(__('Monthly Archives: %s', 'bunyad'), '<strong>' . get_the_date('F, Y') . '</strong>');
			elseif (is_year()):
				printf(__('Yearly Archives: %s', 'bunyad'), '<strong>' . get_the_date('Y') . '</strong>');
			endif;
				
			?></h2>
		<?php endif; ?>
	
		<?php get_template_part(($bunyad_loop_template ? sanitize_file_name($bunyad_loop_template) : 'loop')); ?>

		</div>
		
		<?php Bunyad::core()->theme_sidebar(); ?>
		
	</div> <!-- .row -->
</div> <!-- .main -->

<?php get_footer(); ?>