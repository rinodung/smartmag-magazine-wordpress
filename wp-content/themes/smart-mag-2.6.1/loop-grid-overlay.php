<?php 

/**
 * "loop" to display posts when using an existing query. Used for categories and arhives.
 */

?>

	<?php
	
	global $bunyad_loop;
	
	if (!is_object($bunyad_loop)) {
		$bunyad_loop = $wp_query;
	}
	
	// Define the current loop type
	$loop_type = empty($loop_type) ? 'grid-overlay' : $loop_type;
	
	// Grid type
	$loop_grid = Bunyad::registry()->loop_grid;
	
	if (!isset($loop_grid_class)) {
		$loop_grid_class = $loop_grid ? 'grid-' . $loop_grid : '';
	}

	if ($bunyad_loop->have_posts()):
	
		$attribs = array('class' => array('row listing', 'grid-overlay', $loop_type, $loop_grid_class));
		
		// infinite load?
		if (Bunyad::options()->pagination_type == 'infinite') {
			$attribs['data-infinite'] = Bunyad::markup()->unique_id('listing-'); 
		}
		
		/**
		 * Select the image to use 
		 */
		if (!isset($loop_image)) {
			
			// set larger image when full-width, for 2-col grid
			$image = Bunyad::core()->get_sidebar() == 'none' ?  'main-slider' : 'grid-overlay';
					
			if ($loop_grid) {
				
				// change image to smaller
				$image = 'grid-overlay';
			}
		}
		else {
			$image = $loop_image;
		}
		
		// Loop grid column class
		if (!isset($loop_column)) {
			$loop_column = ($loop_grid == 3 ? 'one-third' : 'half'); 
		}
		
	?>
	
	<div <?php Bunyad::markup()->attribs('loop', $attribs); ?>>
		
		<?php while ($bunyad_loop->have_posts()): $bunyad_loop->the_post(); ?>
			
		<div class="column <?php echo esc_attr($loop_column); ?>">
		
			<article <?php post_class('highlights'); ?> itemscope itemtype="http://schema.org/Article">
				
				<?php echo Bunyad::blocks()->cat_label(); ?>
				
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="image-link">
					<?php the_post_thumbnail($image, array('class' => 'image', 'title' => strip_tags(get_the_title()), 'itemprop' => 'image')); ?>
					
					<?php if (get_post_format()): ?>
						<span class="post-format-icon <?php echo esc_attr(get_post_format()); ?>"><?php
							echo apply_filters('bunyad_post_formats_icon', ''); ?></span>
					<?php endif; ?>

					<?php echo apply_filters('bunyad_review_main_snippet', '', 'stars'); ?>
				</a>
				
				<div class="overlay">
				
					<?php echo Bunyad::blocks()->meta('above', 'grid-overlay'); ?>
					
					<h2 itemprop="name headline"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" itemprop="url"><?php the_title(); ?></a></h2>
					
					<?php echo Bunyad::blocks()->meta('below', 'grid-overlay'); ?>
					
				</div>
			
			</article>
		</div>
			
		<?php endwhile;  ?>
				
	</div>
	
	
	<?php if (!Bunyad::options()->blog_no_pagination): // pagination can be disabled ?>
		
	<div class="main-pagination">
		<?php echo Bunyad::posts()->paginate(array(), $bunyad_loop); ?>
	</div>
		
	<?php endif; ?>
		

	<?php elseif (is_archive() OR is_search()): // show error on archive only ?>

		<article id="post-0" class="page no-results not-found">
			<div class="post-content">
				<h1><?php _e( 'Nothing Found!', 'bunyad' ); ?></h1>
				<p><?php _e('Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'bunyad'); ?></p>
			</div><!-- .entry-content -->
		</article><!-- #post-0 -->
	
	<?php endif; ?>
