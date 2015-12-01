<?php 
/**
 * Grid slider
 *  
 * To be calld from partial-sliders.php
 */

?>
	
	<div class="main-featured">
		<div class="wrap cf">
		
		<div <?php Bunyad::markup()->attribs('featured-grid', array_merge(array('class' => 'featured-grid'), $data_vars)); ?>>
			<ul class="grid">
			
				<li class="first col-6">
					<?php while ($query->have_posts()): $query->the_post(); ?>

					<div class="item large-item">
						
						<a href="<?php the_permalink(); ?>" class="image-link"><?php
							the_post_thumbnail('grid-slider-large', array('alt' => esc_attr(get_the_title()), 'title' => '')); ?></a>
						
						<div class="caption">
							<?php echo Bunyad::blocks()->cat_label(); ?>
						
							<h3><a href="<?php the_permalink(); ?>" class="item-heading"><?php the_title(); ?></a></h3>
							<time class="the-date" datetime="<?php echo esc_attr(get_the_time(DATE_W3C)); ?>"><?php echo esc_html(get_the_date()); ?></time>

						</div>
												
					</div>
					
					<?php break; ?>
					
					<?php endwhile; ?>
				
				</li>

					
				<li class="second col-6">
					<?php while ($query->have_posts()): $query->the_post(); ?>
					
					<?php $cat = current(get_the_category()); ?>
					
					<div class="col-6 item small-item">
					
						<a href="<?php the_permalink(); ?>" class="image-link"><?php
							the_post_thumbnail('grid-slider-small', array('alt' => esc_attr(get_the_title()), 'title' => '')); ?></a>
							
						<div class="caption caption-small">
							<?php echo Bunyad::blocks()->cat_label(); ?>
						
							<h3><a href="<?php the_permalink(); ?>" class="item-heading heading-small"><?php the_title(); ?></a></h3>
							<time class="the-date" datetime="<?php echo esc_attr(get_the_time(DATE_W3C)); ?>"><?php echo esc_html(get_the_date()); ?></time>

						</div>

					</div>
					
					<?php endwhile; ?>

				</li>
				
			</ul>

			<?php wp_reset_query(); ?>
			
		</div>
		
		</div> <!-- .wrap -->
	</div>
