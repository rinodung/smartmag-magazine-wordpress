<?php

/**
 * bbPress Forum Template 
 */

get_header();

?>

<div class="main wrap cf">

	<div class="row">
		<div class="col-8 main-content">
			
			<?php if (have_posts()): the_post(); endif; // load the page ?>

			<div <?php post_class(); ?>>

				<header class="post-header">				
				
					<h1 class="main-heading"><?php the_title(); ?></h1>
				
				</header><!-- .post-header -->
				
			<?php //endif; ?>
		
			<div>			
				
				<?php Bunyad::posts()->the_content(); ?>
				
			</div>

			</div>
			
		</div>
		
		<?php Bunyad::core()->theme_sidebar(); ?>
		
	</div> <!-- .row -->
</div> <!-- .main -->

<?php get_footer(); ?>