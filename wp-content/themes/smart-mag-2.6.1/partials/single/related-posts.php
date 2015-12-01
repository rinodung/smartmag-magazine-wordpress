<?php 
/**
 * Partial Template for related posts on single pages
 */
?>

<?php 
	
	if (is_single() && Bunyad::options()->related_posts): 
	
		$related = Bunyad::posts()->get_related(Bunyad::core()->get_sidebar() == 'none' ? 3 : 3);
		
		if (!$related) {
			return;
		}
	
?>
	
	<section class="related-posts">
		<h3 class="section-head"><?php _e('Related Posts', 'bunyad'); ?></h3> 
		<ul class="highlights-box three-col related-posts">
		
		<?php foreach ($related as $post): setup_postdata($post); ?>
		
			<li class="highlights column one-third">
				
				<article>
						
					<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="image-link">
						<?php the_post_thumbnail(
							(Bunyad::core()->get_sidebar() == 'none' ? 'main-block' : 'gallery-block'),
							array('class' => 'image', 'title' => strip_tags(get_the_title()))); ?>
	
						<?php if (get_post_format()): ?>
							<span class="post-format-icon <?php echo esc_attr(get_post_format()); ?>"><?php
								echo apply_filters('bunyad_post_formats_icon', ''); ?></span>
						<?php endif; ?>
					</a>
					
					<?php echo Bunyad::blocks()->meta('above', 'related-posts', array('type' => 'widget')); ?>
					
					<h2><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
					
					<?php echo Bunyad::blocks()->meta('below', 'related-posts', array('type' => 'widget')); ?>
					
				</article>
			</li>
			
		<?php endforeach; wp_reset_postdata(); ?>
		</ul>
	</section>

<?php endif; ?>
