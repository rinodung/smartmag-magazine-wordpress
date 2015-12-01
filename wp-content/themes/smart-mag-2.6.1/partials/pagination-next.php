<?php
/**
 * Partial: Pagination used in multi-page content slideshows
 */
?>

			
		<div class="post-pagination-next" data-type="<?php echo esc_attr(Bunyad::posts()->meta('content_slider')); ?>">
		
			<?php global $page, $numpages; // get multi-page numbers ?>
			<span class="info"><?php printf(__('Showing %s of %s', 'bunyad'), '<strong>' . $page . '</strong>', '<strong>' . $numpages. '</strong>'); ?></span>
			
			<?php wp_link_pages(array(
					'before' => '<div class="links">', 
					'after' => '</div>', 
					'link_before' => '<span class="button">',
					'next_or_number' => 'next',
					'nextpagelink' => __('Next', 'bunyad') . ' <i class="next fa fa-chevron-right"></i>',
					'previouspagelink' => '<i class="prev fa fa-chevron-left"></i> ' . __('Prev', 'bunyad'),
					'link_after' => '</span>')); ?>
		</div>
