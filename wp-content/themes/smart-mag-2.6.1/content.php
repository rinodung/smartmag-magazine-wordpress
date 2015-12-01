<?php

/**
 * Content Template is used for every post format and used on single posts
 * 
 * Note: This is only used on classic post layout. Check partials/single/ folder for 
 * other more post layouts (layout-{name}.php). This template is called by single.php
 */


$classes = get_post_class();


// using the title above featured image variant?
$layout = Bunyad::posts()->meta('layout_template');
if (is_single() && $layout == 'classic-above') {
	$classes[] = 'title-above'; 
}
else {
	$layout = 'classic';
}

// post has review? 
$review  = Bunyad::posts()->meta('reviews');
if ($review) {
	
	// hreview has to be first class because of rich snippet classes limit
	array_unshift($classes, 'hreview');
}

?>

<article id="post-<?php the_ID(); ?>" class="<?php echo join(' ', $classes); ?>" itemscope itemtype="http://schema.org/Article">
	
	<header class="post-header cf">
	
	<?php if ($layout == 'classic-above'): ?>
	
		<?php get_template_part('partials/single/classic-title-meta'); ?>
	
	<?php endif; ?>
		

	<?php if (!Bunyad::posts()->meta('featured_disable') OR !is_single()): ?>
		<div class="featured">
			<?php if (get_post_format() == 'gallery'): // get gallery template ?>
			
				<?php get_template_part('partial-gallery'); ?>
				
			<?php elseif (Bunyad::posts()->meta('featured_video')): // featured video available? ?>
			
				<div class="featured-vid">
					<?php echo apply_filters('bunyad_featured_video', Bunyad::posts()->meta('featured_video')); ?>
				</div>
				
			<?php else:  ?>
			
				<?php 
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
			
				<a href="<?php echo $url; ?>" title="<?php the_title_attribute(); ?>" itemprop="image">
				
				<?php if (Bunyad::options()->blog_thumb != 'thumb-left'): // normal container width image ?>
				
					<?php if ((!in_the_loop() && Bunyad::posts()->meta('layout_style') == 'full') OR Bunyad::core()->get_sidebar() == 'none'): // largest images - no sidebar? ?>
				
						<?php the_post_thumbnail('main-full', array('title' => strip_tags(get_the_title()))); ?>
				
					<?php else: ?>
					
						<?php the_post_thumbnail('main-slider', array('title' => strip_tags(get_the_title()))); ?>
					
					<?php endif; ?>
					
				<?php else: ?>
					<?php the_post_thumbnail('thumbnail', array('title' => strip_tags(get_the_title()))); ?>
				<?php endif; ?>
								
				</a>
								
				<?php if (!empty($caption)): // have caption ? ?>
						
					<div class="caption"><?php echo $caption; ?></div>
						
				<?php endif;?>
				
			<?php endif; // end normal featured image ?>
		</div>
	<?php endif; // featured check ?>
	
	<?php if ($layout != 'classic-above'): ?>
	
		<?php get_template_part('partials/single/classic-title-meta'); ?>
	
	<?php endif; ?>
		
	</header><!-- .post-header -->

	
<?php
	// page builder for posts enabled?
	$panels = get_post_meta(get_the_ID(), 'panels_data', true);
	if (!empty($panels) && !empty($panels['grids']) && is_singular() && !is_front_page()):
?>
	
	<?php Bunyad::posts()->the_content(); ?>

<?php 
	else: 
?>

	<div class="post-container cf">
	
		<div class="post-content-right">
			<div class="post-content description <?php echo (Bunyad::posts()->meta('content_slider') ? 'post-slideshow' : ''); ?>" itemprop="articleBody">
	
			<?php 
				// get post body content
				get_template_part('partials/single/post-content'); 
			?>
		
			</div><!-- .post-content -->
		</div>
		
	</div>
	
<?php 
	endif; // end page builder blocks test
?>
	
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

