<?php 
/**
 * Classic post style title and meta partial template - called from content.php
 */
?>
		<div class="heading cf">
			<?php 
				$tag = 'h1';
				if (!is_single() OR is_front_page()) {
					$tag = 'h2';
				}
			?>
	
			<<?php echo $tag; ?> class="post-title item fn" itemprop="name headline">
			<?php if (!is_front_page() && is_singular()): the_title(); else: ?>
			
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark">
					<?php the_title(); ?></a>
					
			<?php endif;?>
			</<?php echo $tag; ?>>
			
			<a href="<?php comments_link(); ?>" class="comments"><i class="fa fa-comments-o"></i> <?php echo get_comments_number(); ?></a>
		
		</div>

		<div class="post-meta cf">
			<span class="posted-by"><?php _ex('By', 'Post Meta', 'bunyad'); ?> 
				<span class="reviewer" itemprop="author"><?php the_author_posts_link(); ?></span>
			</span>
			 
			<span class="posted-on"><?php _ex('on', 'Post Meta', 'bunyad'); ?>
				<span class="dtreviewed">
					<time class="value-title" datetime="<?php echo esc_attr(get_the_time(DATE_W3C)); ?>" title="<?php 
						echo esc_attr(get_the_time('Y-m-d')); ?>" itemprop="datePublished"><?php echo esc_html(get_the_date()); ?></time>
				</span>
			</span>
			
			<span class="cats"><?php echo get_the_category_list(__(', ', 'bunyad')); ?></span>
				
		</div>	