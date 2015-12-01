<?php 
/**
 * Partial Template for next/previous post navigation on single page
 */
?>

<?php if (is_single() && Bunyad::options()->post_navigation): ?>

	<section class="navigate-posts">
	
		<div class="previous"><?php 
			previous_post_link('<span class="main-color title"><i class="fa fa-chevron-left"></i> ' . __('Previous Article', 'bunyad') .'</span><span class="link">%link</span>'); ?>
		</div>
		
		<div class="next"><?php 
			next_post_link('<span class="main-color title">'. __('Next Article', 'bunyad') .' <i class="fa fa-chevron-right"></i></span><span class="link">%link</span>'); ?>
		</div>
		
	</section>

<?php endif; ?>