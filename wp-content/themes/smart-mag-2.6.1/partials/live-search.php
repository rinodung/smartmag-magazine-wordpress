<?php
/**
 * Partial template to show results for live search
 */

// setup the search query
$posts = new WP_Query(array(
	's' => $_GET['query'], 
	'posts_per_page' => intval(Bunyad::options()->live_search_number),
	'post_status' => 'publish',
	'post_type'   => (Bunyad::options()->search_posts_only ? 'post' : 'any'), // limit to posts or all
));

?>


	<?php if (!$posts->have_posts()): ?> 

	<span class="no-results">
		<?php _e('Sorry, no results!', 'bunyad'); ?>
	</span>

	<?php 
			return;
		endif;
	?>

	<ul class="posts-list">
	
	<?php while ($posts->have_posts()): $posts->the_post(); ?>

			<li>
			
				<a href="<?php the_permalink() ?>"><?php the_post_thumbnail('post-thumbnail', array('title' => strip_tags(get_the_title()))); ?>
				
				<?php if (class_exists('Bunyad') && Bunyad::options()->review_show_widgets): ?>
					<?php echo apply_filters('bunyad_review_main_snippet', ''); ?>
				<?php endif; ?>
				
				</a>
				
				<div class="content">
				
					<?php echo Bunyad::blocks()->meta('above', 'live-search', array('type' => 'widget')); ?>
				
					<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
						<?php if (get_the_title()) the_title(); else the_ID(); ?></a>
						
					<?php echo Bunyad::blocks()->meta('below', 'live-search', array('type' => 'widget')); ?>
						
					<?php if (class_exists('Bunyad') && Bunyad::options()->review_show_widgets): ?>
						<?php echo apply_filters('bunyad_review_main_snippet', '', 'stars'); ?>
					<?php endif; ?>
																
				</div>
			
			</li>


	<?php endwhile; ?>
	
		<li class="view-all"><a href="<?php echo esc_url(get_search_link($_GET['query'])); ?>"><?php _e('See All Results', 'bunyad'); ?></a></li>
	
	</ul>