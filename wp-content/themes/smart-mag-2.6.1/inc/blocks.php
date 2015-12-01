<?php
/**
 * Helper methods for Blocks & Listing Archives
 */
class Bunyad_Theme_Blocks 
{

	/**
	 * Get meta output for the block.
	 * 
	 * @param  string  $position  'above' or 'below - will match against theme settings.
	 * @param  string  $name      Block name that will also be used as $args['type'] to determine
	 *                            pre-defined settings.
	 * @param  array   $args      Override default settings of the method.
	 * 
	 * @uses Bunyad::options()
	 * @return string
	 */
	public function meta($position = 'above', $name = '', $args = array())
	{
		
		if (Bunyad::options()->meta_position != $position) {
			return '';
		}
		
		/**
		 * Default meta settings 
		 */
		$defaults = array('type' => null, 'review' => 1, 'items' => '', 'class' => 'listing-meta meta');
		
		// Pre-defined special settings for few listing types
		$types = array(
			'grid-overlay' => array('review' => 0, 'items' => Bunyad::options()->meta_listing_overlay),
			'listing-alt'  => array('review' => 0),
			'widget'  => array('review' => 0, 'items' => Bunyad::options()->meta_listing_widgets),
			'block-small'  => array('review' => 0, 'items' => array('date' => 1), 'class' => 'listing-meta')
		);
		
		// Check if args contain the type or fallback to name
		$type = (!empty($args['type']) ? $args['type'] : $name);
		
		// Use special settings if type is specified
		if (!empty($types[ $type ])) {
			$defaults = array_merge($defaults, $types[ $type ]);
		}
		
		// Override args as needed
		$args = wp_parse_args($args, $defaults);
		
		/**
		 * Apply a filter hook the final overriden args.
		 * 
		 * This hook can be used to change the block meta configuration for a specific
		 * or all types of blocks.
		 * 
		 * @param array  $args
		 * @param string $position
		 * @param string $type
		 */
		$args = apply_filters('bunyad_blocks_meta_args', $args, $position, $type);
		
		// Configuration from theme settings
		$meta_items = (!$args['items'] ? Bunyad::options()->meta_listing : $args['items']);
		
		// Add review bar to the output?
		if ($args['review']) {
			
			// Add review at 2nd last position
			$offset = count($meta_items);
			$meta_items = array_merge(
				array_slice($meta_items, 0, $offset - 1),
				array('review' => 1),
				array_slice($meta_items, -2)
			);
		}
		
		/**
		 * Prepare meta to output based on the settings
		 */
		$meta = array();
		foreach ($meta_items as $key => $show) {
			
			if (empty($show)) {
				continue;
			}
			
			switch ($key) {

				// Add date to the meta output
				case 'date':
					
					$meta[] = '<time datetime="' . esc_attr(get_the_date(DATE_W3C)) . '" itemprop="datePublished" class="meta-item">' . get_the_date() . '</time>';
					
					break;
				
				// Append author name and link
				case 'author':
					
					global $authordata;
					
					$author_link = sprintf(
						'<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
						esc_url(get_author_posts_url($authordata->ID, $authordata->user_nicename)),
						esc_attr(sprintf(__('Posts by %s', 'bunyad'), get_the_author())),
						get_the_author()
					);
					
					$meta[] = '<span class="meta-item author">' . _x('By', 'Post Meta', 'bunyad') . ' ' . $author_link . '</span>';
					
					break;
					
				case 'review':
					
					$meta[] = apply_filters('bunyad_review_main_snippet', '');
					
					break;
					
				
				// Add comment count with the icon
				case 'comments':
					
					$meta[] = '<span class="meta-item comments"><a href="' . esc_url(get_comments_link()) . '"><i class="fa fa-comments-o"></i> '
							. get_comments_number() .'</a></span>';
							
					break;
			}
		}
		
		$meta = implode('', $meta);
		$args['class'] .= ' ' . $position;
		
		ob_start();
		
		if (!empty($meta)): 
		?>
			<div class="cf <?php echo esc_attr($args['class']); ?>">
					
				<?php echo $meta; ?>
					
			</div>
		<?php
		endif; 
		
		return apply_filters('bunyad_blocks_meta', ob_get_clean());
	}
	
	/**
	 * Get HTML for category label with link. 
	 * 
	 * Checks global and local settings before generating the output.
	 *
	 * @uses  Bunyad::registry()->block_atts  The current block attribs in registry
	 * @return string|void  HTML with category label
	 */
	public function cat_label()
	{
		$category = $this->get_primary_cat();
		$block    = Bunyad::registry()->block_atts;
		
		if (!empty($block) && !$block['cat_labels']) {
			return;
		}
		
		// Object has category taxonomy? i.e., is it a post or a valid CPT?
		if (!in_array('category', get_object_taxonomies(get_post_type()))) {
			return;
		}
		
		ob_start();
		?>
		
		<span class="cat-title cat-<?php echo $category->cat_ID; ?>"><a href="<?php 
			echo esc_url(get_category_link($category)); ?>" title="<?php echo esc_attr($category->name); ?>"><?php echo esc_html($category->name); ?></a></span>
		
		<?php
		
		return apply_filters('bunyad_blocks_cat_label', ob_get_clean());
	}
	
	/**
	 * Get primary category of the current post in loop (if selected)
	 * 
	 * Note: If no primary category is selected, it will return one category 
	 * using the default alphabetical sorting of WordPress.
	 * 
	 * @see get_the_category()
	 * @return object
	 */
	public function get_primary_cat()
	{
		// Custom label selected?
		if (($cat_label = Bunyad::posts()->meta('cat_label'))) {
			$cat = get_category($cat_label);
		}
		else {
			$cat = current(get_the_category());
		}

		return apply_filters('bunyad_get_primary_cat', $cat);
	}
	
}

// init and make available in Bunyad::get('blocks')
Bunyad::register('blocks', array(
	'class' => 'Bunyad_Theme_Blocks',
	'init' => true
));