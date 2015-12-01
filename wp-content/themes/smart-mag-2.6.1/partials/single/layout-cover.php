<?php 
/**
 * Partial Template for Single Post "Cover Layout" - called from single.php
 */
?>

<?php if (have_posts()) : the_post(); ?>

<?php

	// post has review? 
	$review = Bunyad::posts()->meta('reviews');
	
	// Category custom label selected?				
	if (($cat_label = Bunyad::posts()->meta('cat_label'))) {
		$category = get_category($cat_label);
	}
	else {
		$category = current(get_the_category());						
	}

?>
	
<div class="post-wrap <?php
	// hreview has to be first class because of rich snippet classes limit 
	echo ($review ? 'hreview ' : '') . join(' ', get_post_class()); ?>" itemscope itemtype="http://schema.org/Article">

	<section class="post-cover">
	
			<div class="featured">
					
				<?php if (Bunyad::posts()->meta('featured_video')): // featured video available? ?>
				
					<div class="featured-vid">
						<?php echo apply_filters('bunyad_featured_video', Bunyad::posts()->meta('featured_video')); ?>
					</div>
					
				<?php else: ?>
				
					<?php 

					if (get_post_format() == 'gallery'): 
						/**
						 * Emulate disabled sidebar for the gallery to be rendered full-width
						 */
						$sidebar = Bunyad::core()->get_sidebar();
						Bunyad::core()->set_sidebar('none');
						
						get_template_part('partial-gallery');

						Bunyad::core()->set_sidebar($sidebar);
					
					else:
					
						/**
						 * Normal featured image
						 */
				
						$caption = get_post(get_post_thumbnail_id())->post_excerpt;
						$url     = get_permalink();
						
						// on single page? link to image
						if (is_single()):
							$url = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full'); 
							$url = $url[0];
						endif;
					?>
					
						<?php the_post_thumbnail('main-full', array('title' => strip_tags(get_the_title()), 'itemprop' => 'image')); ?>
										
						<?php if (!empty($caption)): // have caption ? ?>
								
							<div class="caption"><?php echo $caption; ?></div>
								
						<?php endif;?>
						
					<?php 
					endif; // end check for featured image/gallery
					?>
					
					<div class="overlay">
						
						<span class="cat-title cat-<?php echo $category->cat_ID; ?>"><a href="<?php 
							echo esc_url(get_category_link($category)); ?>"><?php echo esc_html($category->name); ?></a></span>
						
						<h1 class="item fn" itemprop="name headline"><?php the_title(); ?></h1>
						
						<div class="post-meta">
							<span class="posted-by"><?php _ex('By', 'Post Meta', 'bunyad'); ?> 
								<span class="reviewer" itemprop="author"><?php the_author_posts_link(); ?></span>
							</span>
							 
							<span class="posted-on"><?php _ex('on', 'Post Meta', 'bunyad'); ?>
								<span class="dtreviewed">
									<time class="value-title" datetime="<?php echo esc_attr(get_the_time(DATE_W3C)); ?>" title="<?php 
										echo esc_attr(get_the_time('Y-m-d')); ?>" itemprop="datePublished"><?php echo esc_html(get_the_date()); ?></time>
								</span>
							</span>
							
							<span class="comments">
								<a href="<?php comments_link(); ?>"><i class="fa fa-comments-o"></i> <?php 
									printf(_n('%d Comment', '%d Comments', get_comments_number(), 'bunyad'), get_comments_number()); 
								?></a>
							</span>
						</div>
						
					</div>
					
																			
				<?php if (!empty($caption)): // have caption ? ?>
						
					<div class="caption"><?php echo $caption; ?></div>
						
				<?php endif;?>
				
					
				<?php endif; // end normal featured image ?>
			</div>
	
	</section>
	
	
	<div class="row">

		<div class="col-8 main-content">
		
			<article>
			
				<div class="post-container cf">
				
					<div class="post-content description <?php echo (Bunyad::posts()->meta('content_slider') ? 'post-slideshow' : ''); ?>" itemprop="articleBody">
				
						<?php 
							// get post body content
							get_template_part('partials/single/post-content'); 
						?>
					
					</div><!-- .post-content -->
					
				</div>
		
				<?php 
					// add social share
					get_template_part('partials/single/social-share');
				?>
					
			</article>
			
			<?php 
			
			// add next/previous 
			get_template_part('partials/single/post-navigation');
			
			// add author box
			get_template_part('partials/single/author-box');
			
			// add related posts
			get_template_part('partials/single/related-posts');
			
			?>
	
			<div class="comments">
				<?php comments_template('', true); ?>
			</div>
	
		</div>
	
		<?php Bunyad::core()->theme_sidebar(); ?>
	
	</div> <!-- .row -->

</div> <!-- .post-wrap -->

<?php endif; // end of "the loop" ?>