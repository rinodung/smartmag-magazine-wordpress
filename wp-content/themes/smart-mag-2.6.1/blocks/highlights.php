<?php 

if ($columns == 2 OR $columns == 3):

	$cats = array_map('trim', explode(',', $cats));
	$tags = array_map('trim', explode(',', $tags));
	$headings = array_map('trim', explode(',', $headings));
	$offsets = array_map('trim', explode(',', $offsets));
	
?>
	[columns class="highlights-box<?php echo ($columns == 3 ? ' three-col' : ''); ?>"]
	
	<?php foreach (range(0, $columns-1) as $key): 
			if (!$tags[$key] && !$cats[$key]) {
				continue;
			}
	?>
		[highlights column="<?php echo ($columns == 3 ? '1/3' : 'half'); ?>" 
			cat="<?php echo esc_attr($cats[$key]); ?>"
			tax_tag="<?php echo esc_attr($tags[$key]); ?>"
			title="<?php echo esc_attr($headings[$key]); ?>" 
			sort_by="<?php echo esc_attr($sort_by); ?>" sort_order="<?php echo esc_attr($sort_order); ?>"
			posts="<?php echo $posts; ?>"
			taxonomy="<?php echo $taxonomy; ?>"
			offset="<?php echo esc_attr($offsets[$key]); ?>" 
			post_type="<?php echo esc_attr($post_type); ?>"
			heading_type="<?php echo esc_attr($heading_type); ?>"
		/]	
	<?php endforeach; ?>
	
	[/columns]

<?php
	
	return;
	
endif;
?>

<?php if ($column): ?>
	[column size="<?php echo $column; ?>"]
<?php endif; ?>

	<section class="highlights">
		<?php 

		$query_args = array(
			'posts_per_page' => (!empty($posts) ? intval($posts) : 4), 'order' => ($sort_order == 'asc' ? 'asc' : 'desc'), 'offset' =>  ($offset ? $offset : '')
		);
		
		if ($sort_by == 'modified') {
			$query_args['orderby'] = 'modified';
		}
		else if ($sort_by == 'random') {
			$query_args['orderby'] = 'rand';
		}
		
		$query_args = apply_filters('bunyad_block_query_args', $query_args, 'highlights', $atts);
		
		/**
		 * Use custom taxonomy, category, or tag?
		 */
		if (!empty($taxonomy)) {

			$_taxonomy = $taxonomy; // preserve
			
			// get the tag
			$taxonomy = get_term_by('id', $cat, $_taxonomy);
			
			$query = new WP_Query(array_merge($query_args, array(
				'tax_query' => array(array(
					'taxonomy' => $_taxonomy,
					'field' => 'id',
					'terms' => (array) $cat
				))
			)));
			
			if (empty($title)) {
				$title = $taxonomy->slug; 
			}
			
			$link = get_term_link($taxonomy, $_taxonomy);
			
		}
		else if (!empty($cat)) {
			
			// get latest from the specified category
			$taxonomy = $category = is_numeric($cat) ? get_category($cat) : get_category_by_slug($cat);
			$query = new WP_Query(array_merge($query_args, array('category_name' => $category->slug)));
					
			if (empty($title)) {
				$title = $category->cat_name;
			}
			
			$link = get_category_link($category);
		}
		// using a tag 
		else if (!empty($tax_tag)) {
			
			// get the tag
			$taxonomy = get_term_by('slug', $tax_tag, 'post_tag');
			$query = new WP_Query(array_merge($query_args, array('tag' => $tax_tag)));
			
			if (empty($title)) {
				$title = $taxonomy->slug; 
			}
			
			$link = get_term_link($taxonomy, 'post_tag');
		}
		
		$count = 0;
		$type = '';
		$heading = '';
		
		if ($heading_type == 'auto') {
			
			$heading = 'section-head';
			
			if ($column == 'half' OR Bunyad::core()->get_sidebar() == 'none') {
				$type = 'thumb';
				
				$heading = 'overlay';
			}
		}
		else if ($heading_type == 'block') {
			$heading = 'section-head';
		}
		
		// no heading
		if ($heading_type == 'none') {
			$heading = '';
		}
		
		?>
		
		<?php if ($heading == 'section-head'): ?>
			
			<div class="section-head cat-text-<?php echo $taxonomy->term_id; ?>">
				<a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a>
			</div>
							
		<?php endif; ?>
		
		
		<?php while ($query->have_posts()): $query->the_post(); $count++; ?>
		
		<?php if ($count === 1): // main post - better highlighted ?>
		
			<?php if ($heading == 'overlay'): ?>
				<span class="cat-title larger cat-<?php echo $taxonomy->term_id; ?>">
					<a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a></span>
			<?php endif; ?>
			
			<article itemscope itemtype="http://schema.org/Article">
					
				<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="image-link" itemprop="url">
					<?php the_post_thumbnail(
								(Bunyad::core()->get_sidebar() != 'none' && $column == '1/3' 
										? 'gallery-block' 
										: (Bunyad::core()->get_sidebar() == 'none' ?  'main-slider' : 'main-block')
								), 
								array('class' => 'image', 'title' => strip_tags(get_the_title()), 'itemprop' => 'image')); ?>
					
					<?php if (get_post_format()): ?>
						<span class="post-format-icon <?php echo esc_attr(get_post_format()); ?>"><?php
							echo apply_filters('bunyad_post_formats_icon', ''); ?></span>
					<?php endif; ?>
					
					<?php echo apply_filters('bunyad_review_main_snippet', '', 'stars'); ?>
				</a>
				
				<?php echo Bunyad::blocks()->meta('above', 'highlights'); ?>
				
				<h2 itemprop="name headline"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				
				<?php echo Bunyad::blocks()->meta('below', 'highlights'); ?>
				
				<?php if ($type == 'thumb'): ?>
				
				<div class="excerpt">
					<?php echo Bunyad::posts()->excerpt(null, Bunyad::options()->excerpt_length_highlights, array('add_more' => false)); ?>
				</div>
								
				<?php endif; ?>
				
			</article>
			
		<?php if ($query->post_count > 1): ?>
						
			<ul class="block <?php echo ($type == 'thumb' ? 'posts-list thumb' : 'posts'); ?>">
			
		<?php endif; ?>
		
				
		<?php continue; endif; // main post end ?>
			
			<?php // other posts, in a list ?>

				<li>
			
			<?php if ($type == 'thumb'): ?>
				
					<a href="<?php the_permalink() ?>"><?php the_post_thumbnail('post-thumbnail', array('title' => strip_tags(get_the_title()))); ?>

					<?php if (Bunyad::options()->review_show_widgets): ?>
						<?php echo apply_filters('bunyad_review_main_snippet', ''); ?>
					<?php endif; ?>
					
					</a>
					
					<div class="content">

						<?php echo Bunyad::blocks()->meta('above', 'block-small'); ?>
						
						<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
							<?php if (get_the_title()) the_title(); else the_ID(); ?></a>
							
						<?php echo Bunyad::blocks()->meta('below', 'block-small'); ?>
							
						<?php echo apply_filters('bunyad_review_main_snippet', '', 'stars'); ?>
						
					</div>
				
			<?php else: ?>
			
					<i class="fa fa-angle-right"></i>
					<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="title">
						<?php the_title(); ?>
					</a>
					
			<?php endif; ?>
			
				</li>
			
		<?php endwhile; ?>
			
			<?php if ($query->post_count > 1): ?> </ul> <?php endif; ?>
			
	<?php wp_reset_query(); ?>
	
	</section>
	
<?php if ($column): ?>
	[/column]
<?php endif; ?>