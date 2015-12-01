

<div class="mega-menu row">

	<div class="col-3 sub-cats">
		
		<ol class="sub-nav">
			<?php echo $sub_menu; ?>
		</ol>
	
	</div>


	<div class="col-9 extend">
	<section class="col-6 featured">
		
		<?php 
			$query = new WP_Query(apply_filters(
				'bunyad_mega_menu_query_args', 
				array('cat' => $item->object_id, 'meta_key' => '_bunyad_featured_post', 'meta_value' => 1, 'order' => 'date', 'posts_per_page' => 1, 'ignore_sticky_posts' => 1),
				'category-featured'
			));
		?>
		
		<span class="heading"><?php _ex('Featured', 'Categories Mega Menu', 'bunyad'); ?></span>
		
		<div class="highlights">
		
		<?php while ($query->have_posts()): $query->the_post(); ?>
			
			<article>
					
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="image-link">
					<?php the_post_thumbnail('main-block', array('class' => 'image', 'title' => strip_tags(get_the_title()))); ?>
				</a>
				
				<?php echo Bunyad::blocks()->meta('above'); ?>
				
				<h2><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				
				<?php echo Bunyad::blocks()->meta('below'); ?>
				
			</article>
			
		<?php endwhile; wp_reset_postdata(); ?>
		
		</div>
	
	</section>  

	<section class="col-6 recent-posts">
	
		<span class="heading"><?php _ex('Recent', 'Categories Mega Menu', 'bunyad'); ?></span>
			
		<?php 
			$query = new WP_Query(apply_filters(
				'bunyad_mega_menu_query_args',
				array('cat' => $item->object_id, 'posts_per_page' => 3, 'ignore_sticky_posts' => 1),
				'category-recent'
			));
		?>
		
		<div class="posts-list">
	
			<?php while ($query->have_posts()): $query->the_post(); ?>
			
			<div class="post">
				<a href="<?php the_permalink() ?>"><?php the_post_thumbnail('post-thumbnail', array('title' => strip_tags(get_the_title()))); ?>
				
				<?php if (class_exists('Bunyad') && Bunyad::options()->review_show_widgets && Bunyad::posts()->meta('reviews')): ?>
					<div class="review rate-number"><span class="progress"></span>
						<span><?php echo Bunyad::posts()->meta('review_overall'); ?></span></div>
				<?php endif; ?>
				
				</a>
				
				<div class="content">
				
					<?php echo Bunyad::blocks()->meta('above', 'mega-menu', array('type' => 'widget')); ?>
									
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					
					<?php echo Bunyad::blocks()->meta('below', 'mega-menu', array('type' => 'widget')); ?>
																
				</div>
			</div>
			
			<?php endwhile; wp_reset_postdata(); ?>
			
		</div>
		
	</section>
	</div>
</div>
			