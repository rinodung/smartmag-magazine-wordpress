<?php 

/**
 * The single post template is selected based on your global Theem Settings or the post 
 * setting. 
 * 
 * Template files for the post layouts are as follows:
 * 
 * Classic: Located below in the code conditional
 * Post Cover: partials/single/layout-cover.php
 */

$template = Bunyad::posts()->meta('layout_template');

if (!$template OR strstr($template, 'classic')) {
	$template = 'classic';
}

if ($template != 'classic') {
	Bunyad::core()->add_body_class('post-layout-' . $template);
}

?>

<?php get_header(); ?>

<div class="main wrap cf">

	<?php if ($template != 'classic'): // not the default layout? ?>
		
		<?php get_template_part('partials/single/layout-' . $template); ?>
	
	<?php else: ?>
	
	<div class="row">
	
		<div class="col-8 main-content">
		
			<?php while (have_posts()) : the_post(); ?>
	
				<?php 
					
					$panels = get_post_meta(get_the_ID(), 'panels_data', true);
					
					if (!empty($panels) && !empty($panels['grid'])):
						
						get_template_part('content', 'builder');
					
					else:
					
						get_template_part('content', 'single');
						
					endif; 
				?>
	
				<div class="comments">
				<?php comments_template('', true); ?>
				</div>
	
			<?php endwhile; // end of the loop. ?>
	
		</div>
		
		<?php Bunyad::core()->theme_sidebar(); ?>
	
	</div> <!-- .row -->
		
	<?php endif; ?>

</div> <!-- .main -->

<?php get_footer(); ?>