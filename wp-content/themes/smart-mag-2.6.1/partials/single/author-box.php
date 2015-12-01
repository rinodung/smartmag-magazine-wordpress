<?php
/**
 * Partial Template for Author Box on single pages
 */
?>

<?php if (is_single() && Bunyad::options()->author_box) : // author box? ?>

		<h3 class="section-head"><?php _e('About Author', 'bunyad'); ?></h3>

		<?php get_template_part('partials/author'); ?>

<?php endif; ?>