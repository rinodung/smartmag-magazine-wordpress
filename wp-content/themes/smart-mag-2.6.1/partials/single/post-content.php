<?php 
/**
 * Partial template to get post body content on single page
 */
?>
		
		<?php
		// multi-page content slideshow post?
		if (Bunyad::posts()->meta('content_slider')):
			get_template_part('partials/pagination-next');
		endif;
		
		?>
		
		<?php
		// excerpts or main content?
		if ((!is_page() && is_singular()) OR !Bunyad::options()->show_excerpts_classic OR Bunyad::posts()->meta('content_slider')): 
			Bunyad::posts()->the_content();
		else:
			echo Bunyad::posts()->excerpt(null, Bunyad::options()->excerpt_length_classic, array('force_more' => true));
		endif;
		
		?>

		
		<?php 
		// multi-page post - add numbered pagination
		if (!Bunyad::posts()->meta('content_slider')):
		
			wp_link_pages(array(
				'before' => '<div class="main-pagination post-pagination">', 
				'after' => '</div>', 
				'link_before' => '<span>',
				'link_after' => '</span>'));
		endif;
		
		?>
		
		<?php if (is_single() && Bunyad::options()->show_tags): ?>
			<div class="tagcloud"><?php the_tags('', ' '); ?></div>
		<?php endif; ?>