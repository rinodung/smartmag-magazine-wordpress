<?php
/**
 * Partial: Top bar template - displayed above header
 */
?>

<?php if (!Bunyad::options()->disable_topbar): ?>
	
	<div <?php Bunyad::markup()->attribs('top-bar', array('class' => array('top-bar', Bunyad::options()->topbar_style))); ?>>

		<div class="wrap">
			<section class="top-bar-content cf">
			
				<?php if (!Bunyad::options()->disable_topbar_ticker): ?>
				<div class="trending-ticker">
					<span class="heading"><?php echo Bunyad::options()->topbar_ticker_text; // filtered html allowed for admins ?></span>

					<ul>
						<?php $query = new WP_Query(apply_filters('bunyad_ticker_query_args', array('orderby' => 'date', 'order' => 'desc', 'posts_per_page' => 8))); ?>
						
						<?php while($query->have_posts()): $query->the_post(); ?>
						
							<li><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
						
						<?php endwhile; ?>
						
						<?php wp_reset_postdata(); ?>
					</ul>
				</div>
				<?php endif; ?>

				<?php if (Bunyad::options()->topbar_search): ?>
					
					<?php get_template_part('partials/header/search'); ?>
					
				<?php endif; ?>

				<?php dynamic_sidebar('top-bar'); ?>
				
			</section>
		</div>
		
	</div>
	
<?php elseif (Bunyad::options()->mobile_nav_search): // add hidden top bar for mobile nav search support ?>
	
	<div class="top-bar hidden">
	
		<?php get_template_part('partials/header/search'); ?>
	
	</div>
	
<?php endif; ?>