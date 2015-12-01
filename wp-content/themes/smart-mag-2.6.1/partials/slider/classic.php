<?php 
/**
 * Classic default slider + 3 posts grid area of SmartMag
 * 
 * To be calld from partial-sliders.php
 */

// Posts grid generated from a category or a tag? (right side of slider)

$limit_cat = Bunyad::options()->featured_right_cat;
$limit_tag = Bunyad::options()->featured_right_tag;

if (!empty($limit_cat)) {
	
	$args['posts_per_page'] = $main_limit;
	$grid_query = array('cat' => $limit_cat, 'posts_per_page' => 3);
}
else if (!empty($limit_tag)) {
	
	$args['posts_per_page'] = $main_limit;
	$grid_query = array('tag' => $limit_tag, 'posts_per_page' => 3);
}

// Use rest of the 3 posts for grid if not post grid is not using 
// any category or tag. Create reference for to main query.
if (empty($grid_query) && $query->found_posts > $main_limit) {
	$grid_query = &$query;
}

$i = $z = 0; // loop counters

?>
	
	<div class="main-featured">
		<div class="wrap cf">
		
		<div class="row">
			<div <?php Bunyad::markup()->attribs('main-slider', array_merge(array('class' => 'slider frame flexslider col-8'), $data_vars)); ?>>
				<ul class="slides">
				
				<?php while ($query->have_posts()): $query->the_post(); ?>
					
					<li>
						<a href="<?php the_permalink(); ?>" class="image-link"><?php 
							the_post_thumbnail(
								apply_filters('bunyad_featured_image', 'main-slider', 'slider'),
								array('alt' => esc_attr(get_the_title()), 'title' => '')
							); 
						?></a>					
	
						<?php $cat = Bunyad::blocks()->get_primary_cat(); ?>
						
						<a href="<?php echo get_category_link($cat->term_id); ?>" class="cat cat-title cat-<?php echo $cat->cat_ID; ?>"><?php echo esc_html($cat->cat_name); ?></a>
						
						<div class="caption">

							<time class="the-date" datetime="<?php echo esc_attr(get_the_time(DATE_W3C)); ?>"><?php echo esc_html(get_the_date()); ?></time>
							
							<h3><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
	
						</div>					
						
					</li>
					
				<?php 
						if ($i++ == ($main_limit - 1)) {
							
							// give a chance to loop_end hook to run because we are breaking - required for duplicates prevention
							$query->have_posts();
							break;
						}
					
					endwhile; //rewind_posts(); 
				?>
			
				</ul>
				
				<div class="pages" data-number="<?php echo esc_attr($main_limit); ?>">
				
				<?php foreach (range(1, $main_limit) as $page): ?>
					<a href="#"></a>
				<?php endforeach; ?>
				
				</div>
				
				
			</div> <!-- .flexslider -->
		
			<div class="blocks col-4">
			
			<?php
			 
			// init the grid query
			if (is_array($grid_query)) {
				$grid_query = new WP_Query(apply_filters('bunyad_block_query_args', $grid_query, 'slider_grid'));
			}
			
			if (!empty($grid_query) && $grid_query->have_posts()): 
			?>
			
				<?php 
				while ($grid_query->have_posts()): $grid_query->the_post(); $z++; 
				
						if (!has_post_thumbnail()) {
							continue;
						}
						
						// custom label selected?				
						if (($cat_label = Bunyad::posts()->meta('cat_label'))) {
							$category = get_category($cat_label);
						}
						else {
							$category = current(get_the_category());						
						}
				?>
				
				<article class="<?php echo ($z == 1 ? 'large' : ($z == 2 ? 'small' : 'small last')); ?>">
					 
				<?php if ($z == 1): ?>
					 <span class="cat cat-title cat-<?php echo $category->cat_ID; ?>"><a href="<?php echo esc_url(get_category_link($category)); ?>"><?php echo esc_html($category->name); ?></a></span>
				<?php endif; ?>
					 
					 <a href="<?php the_permalink(); ?>" class="image-link"><?php 
					 	the_post_thumbnail(
					 		apply_filters('bunyad_featured_image', ($z == 1 ? 'main-block' : 'slider-small'), 'slider-right', $z), 
					 		array('alt' => esc_attr(get_the_title()), 'title' => '')
					 	); ?></a>
					 
					 <h3><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
					
				</article>
				
				
				<?php endwhile; ?>
				
		<?php endif; // end grid query check ?>				
		</div>
			
		</div> <!-- .row -->

		<?php wp_reset_query(); ?>

		</div> <!--  .wrap  -->
	</div>
