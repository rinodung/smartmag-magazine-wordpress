<?php
/*
	Template Name: Sitemap
*/

get_header();


if (Bunyad::posts()->meta('featured_slider')):
	get_template_part('partial-sliders');
endif;

?>

<div class="main wrap cf">

	<div class="row">
		<div class="col-8 main-content">
			
			<?php if (have_posts()): the_post(); endif; // load the page ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<?php if (Bunyad::posts()->meta('page_title') == 'yes'): ?>
			
				<header class="post-header">				
					
				<?php if (has_post_thumbnail()): ?>
					<div class="featured">
						<a href="<?php $url = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full'); echo $url[0]; ?>" title="<?php the_title_attribute(); ?>">
						
						<?php if ((!in_the_loop() && Bunyad::posts()->meta('layout_style') == 'full') OR Bunyad::core()->get_sidebar() == 'none'): // largest images - no sidebar? ?>
						
							<?php the_post_thumbnail('main-full', array('title' => strip_tags(get_the_title()))); ?>
						
						<?php else: ?>
							
							<?php the_post_thumbnail('main-slider', array('title' => strip_tags(get_the_title()))); ?>
							
						<?php endif; ?>
						
						</a>
					</div>
				<?php endif; ?>
				
					<h1 class="main-heading">
						<?php the_title(); ?>
					</h1>
				</header><!-- .post-header -->
				
			<?php endif; ?>
		
			<div class="post-content">
				
				<?php the_content(); ?>
				
				<div class="cf row">
					
					<div class="column half">
						<h3><?php _e('Pages', 'bunyad'); ?></h3>
						
						<ul><?php wp_list_pages(array('title_li' => '')); ?></ul>
						
					</div>
					
					<div class="column half">
						<h3><?php _e('Categories', 'bunyad'); ?></h3>
					
						<ul><?php wp_list_categories(array('title_li' => '')); ?></ul>
					</div>
				
				</div>
				
				<div class="cf row">
				
					<div class="column half">
						<h3><?php _e('Recent Posts', 'bunyad'); ?></h3>
			
						<?php query_posts(array('posts_per_page' => 10, 'orderby' => 'date', 'order' => 'desc')); ?>
						
						<?php if (have_posts()): ?>
							
							<ul class="posts">
							<?php while (have_posts()): the_post(); ?>
							
								<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
							
							<?php endwhile; ?>
							</ul>
						
						<?php endif; ?>
					</div>
					
					
					<div class="column half">
						<h3><?php _e('Archives', 'bunyad'); ?></h3>
						
						<ul><?php wp_get_archives(); ?></ul>
						
					</div>
					
				</div>
				
			</div>

			</article>
			
		</div>
		
		<?php Bunyad::core()->theme_sidebar(); ?>
		
	</div> <!-- .row -->
</div> <!-- .main -->

<?php get_footer(); ?>