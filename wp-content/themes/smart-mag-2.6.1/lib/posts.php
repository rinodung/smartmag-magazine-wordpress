<?php

/**
 * Functionality relevant to posts (single and multiple)
 */
class Bunyad_Posts
{
	public $more_text;
	public $more_html;
	
	public function __construct()
	{
		// add offsets support
		add_filter('found_posts', array($this, 'fix_offset_pagination'), 10, 2);
		add_action('pre_get_posts', array($this, 'pre_get_posts'));
	}
	
	/**
	 * Custom excerpt function - utilize existing wordpress functions to add real support for <!--more-->
	 * to excerpts which is missing in the_excerpt().
	 * 
	 * Maintain plugin functionality by utilizing wordpress core functions and filters. 
	 * 
	 * @param  string|null  $text
	 * @param  integer  $length
	 * @param  array   $options   add_more: add more if needed, more_text = more link anchor, force_more: always add more link
	 * @return string
	 */
	public function excerpt($text = null, $length = 55, $options = array())
	{
		global $more;

		// add defaults
		$options = array_merge(array('add_more' => null, 'force_more' => null), $options);
		
		// add support for <!--more--> cut-off on custom home-pages and the like
		$old_more = $more;
		$more = false;
		
		// override options
		$more_text = $this->more_text;
		extract($options);

		// set global more_text - used by excerpt_read_more()
		$this->more_text = $more_text;
		
		if (!$text) {
			
			// have a manual excerpt?
			if (has_excerpt()) {
				return apply_filters('the_excerpt', get_the_excerpt()) . ($force_more ? $this->excerpt_read_more() : '');
			}
			
			// don't add "more" link
			$text = get_the_content('');
		}
		
		$text = strip_shortcodes(apply_filters('bunyad_excerpt_pre_strip_shortcodes', $text));
		$text = str_replace(']]>', ']]&gt;', $text);
		
		// get plaintext excerpt trimmed to right length
		$excerpt = wp_trim_words($text, $length, apply_filters('bunyad_excerpt_hellip', '&hellip;') . ($add_more !== false ? $this->excerpt_read_more() : '')); 


		/*
		 * Force "More" link?
		 * 
		 * wp_trim_words() will only add the read more link if it's needed - if the length actually EXCEEDS. In some cases,
		 * for styling, read more has to be always present. 
		 * 
		 */
		if ($force_more) {
			
			$read_more = $this->excerpt_read_more();

			if (substr($excerpt, -strlen($read_more)) !== $read_more) {
				$excerpt .= $this->excerpt_read_more();
			}
		}
		
		// fix extra spaces
		$excerpt = trim(str_replace('&nbsp;', ' ', $excerpt)); 
		
		// apply filters after to prevent added html functionality from being stripped
		// REMOVED: the_content filters often clutter the HTML - use the_excerpt filter instead 
		// $excerpt = apply_filters('the_content', $excerpt);
		
		$excerpt = apply_filters('the_excerpt', apply_filters('get_the_excerpt', $excerpt));
		
		// revert
		$more = $old_more;
		
		return $excerpt;
	}
	
	/**
	 * Wrapper for the_content()
	 * 
	 * @see the_content()
	 */
	public function the_content($more_link_text = null, $strip_teaser = false)
	{
		// add <!--more--> support on static pages when not using excerpts
		if (is_page()) {
			global $more;
			$more = 0;
		}
		
		// get the content
		$content = get_the_content($more_link_text, $strip_teaser);
		
		// delete first gallery shortcode if featured area is enabled
		if (get_post_format() == 'gallery' && !$this->meta('featured_disable')) {
			$content = $this->_strip_shortcode_gallery($content);
		}

		// apply bunyad_main_content filters first - for page builder
		$content = apply_filters('the_content',  $content, 'bunyad_main_content');
		$content = str_replace(']]>', ']]&gt;', $content);

		echo $content; // pre-filtered/escaped content from get_the_content()
	}

	/**
	 * Deletes first gallery shortcode and returns content
	 */
	public function _strip_shortcode_gallery($content) 
	{
	    preg_match_all('/'. get_shortcode_regex() .'/s', $content, $matches, PREG_SET_ORDER);
	    
	    if (!empty($matches)) 
	    {
	        foreach ($matches as $shortcode) 
	        {
	            if ('gallery' === $shortcode[2]) 
	            {
	                $pos = strpos($content, $shortcode[0]);
	                if ($pos !== false) {
	                    return substr_replace($content, '', $pos, strlen($shortcode[0]));
	                }
	            }
	        }
	    }
	    
	    return $content;
	}
	
	
	public function get_first_gallery_ids($content = null) 
	{
		if (!$content) {
			$content = get_the_content();
		}
		
	    preg_match_all('/'. get_shortcode_regex() .'/s', $content, $matches, PREG_SET_ORDER);
	    
	    if (!empty($matches)) 
	    {
	        foreach ($matches as $shortcode) 
	        {
	            if ('gallery' === $shortcode[2]) 
	            {
	            	$atts = shortcode_parse_atts($shortcode[3]);
	            	
	            	if (!empty($atts['ids'])) {
	            		$ids = explode(',', $atts['ids']);
	            		
	            		return $ids;
	            	}
	            }
	        }
	    }
	    
	    return false;
	}
	
	/**
	 * Get custom post meta
	 * 
	 * @param string|null $key
	 * @param integer|null $post_id
	 * @param boolean $defaults  whether or not to use default options mapped to certain keys - only when $key is set
	 * @uses  Bunyad::options()  used when defaults are tested for a specific key
	 */
	public function meta($key = null, $post_id = null, $defaults = true)
	{
		$prefix = Bunyad::options()->get_config('meta_prefix') . '_';
		
		if (!$post_id) {
			global $post;
			$post_id = $post->ID;
		}
		
		if (is_string($key)) {
			
			$meta = get_post_meta($post_id, $prefix . $key, true);

			/**
			 * Use values from specified key mapping of Bunyad::options() if meta value is empty
			 */
			if ($defaults) {
			
				
				// bool_inverse will inverse the value in Bunyad::options() 
				$default_map = array(
					'featured_disable' => array('key' => 'show_featured', 'bool_inverse' => true),
					'layout_template'  => array('key' => 'post_layout_template', 'bool_inverse' => false),
				);
				
				// have a key association with theme settings?
				if (!$meta && array_key_exists($key, $default_map)) {
					
					$expression = Bunyad::options()->get($default_map[$key]['key']);
										
					return ($default_map[$key]['bool_inverse'] ? !$expression : $expression);
				}
			}
			
			return apply_filters('bunyad_meta_' . $key, $meta);
			
		}
		else 
		{
			$meta     = get_post_custom($post_id);
			$new_meta = array(); 
			foreach ($meta as $key => $value) 
			{
				// prefix at beginning?
				if (strpos($key, $prefix) === 0) {
					$new_meta[$key] = $this->_fix_meta_value($value);
				}
				
			}
			
			return $new_meta;
		}
	}
	
	// helper to fix meta value
	private function _fix_meta_value($value) {
		if (count($value) === 1) {
			return $value[0];
		}
		
		return $value;
	}
	
	
	/**
	 * Get meta for page first if available
	 */
	public function page_meta($key = null, $post_id = null)
	{
		global $page;
		
		if (!$post_id) {
			$post_id = $page->ID;
		}
		
		return $this->meta($key, $post_id);
	}
	
	/**
	 * Get related posts
	 * 
	 * @param integer $count number of posts to return
	 * @param integer|null $post_id
	 */
	public function get_related($count = 5, $post_id = null)
	{	
		if (!$post_id) {
			global $post;
			$post_id = $post->ID;
		}
		
		if (Bunyad::options()->related_posts_yarpp && function_exists('yarpp_get_related')) {
			return yarpp_get_related(array('limit' => $count), $post_id);
		}
				
		$args = array(
			'numberposts' => $count,
			'post__not_in' => array($post_id)
		);
		
		// get related posts using tags or categories?		
		if (Bunyad::options()->related_posts_by == 'tags') {
			$args['tag__in'] = wp_get_post_tags($post_id, array('fields' => 'ids'));
		}
		else {
			$args['category__in'] = wp_get_post_categories($post_id);
		}
		
		$related = get_posts(apply_filters('bunyad_get_related_query', $args));

		return (array) $related;
		
	}
	
	/**
	 * Custom pagination
	 * 
	 * @param array $options extend options for paginate_links()
	 * @see paginate_links()
	 */
	public function paginate($options = array(), $query = null)
	{
		global $wp_rewrite;

		if (!$query) {
			global $wp_query;
			$query = $wp_query;
		}
		
		$total_pages = $query->max_num_pages;
		if ($total_pages <= 1) {
			return '';
		}
		
		// use page on static front-page and paged on others 
		// non-static home-page uses paged as well 
		$paged = (get_query_var('paged') ? get_query_var('paged') : get_query_var('page'));
		
		$args = array(
			'base'    => add_query_arg('paged', '%#%'), 
			'format'  => '',
			'current' => max(1, $paged),
			'total'   => $total_pages,

			// accessibility + fontawesome for pagination links
			'next_text' => '<span class="visuallyhidden">' . _x('Next', 'pagination', 'bunyad') . '</span><i class="fa fa-angle-right"></i>',
			'prev_text' => '<i class="fa fa-angle-left"></i><span class="visuallyhidden">' . _x('Previous', 'pagination', 'bunyad') . '</span>'
		);
	

		if ($wp_rewrite->using_permalinks() && !is_search()) {
			
			// Use an unlikely page number with get_pagenum_link to get the format /page/1?q=1 instead of /?q=1/page/1
			// Note: Don't use a small number - which can be used in a directory name or domain name and would be replaced below.
			$number = 999999999;
			$args['base'] = str_replace($number, '%#%', remove_query_arg('s', get_pagenum_link($number)));
		}
		
		// No longer needed as the search permalinks are skipped above, due to bug #30831 in WordPress core
		// if (is_search()) {
		//	$args['add_args'] = array('s' => urlencode(get_query_var('s')));
		// }
		
		$pagination = paginate_links(array_merge($args, $options));

		// remove first paged=1 and page/1/ from the first link
		$pagination = preg_replace(
			array('/&#038;paged=1(\'|")/', "#{$wp_rewrite->pagination_base}/1/('|\")#"), '\\1', trim($pagination)
		);
		
		// add wrapper?
		if (!empty($options['wrapper_before'])) {
			$pagination = $options['wrapper_before'] . $pagination . $options['wrapper_after'];
		}
		
		return $pagination;	
	}
	
	/**
	 * Fix query LIMIT when using offsets
	 * 
	 * @param object $query
	 */
	public function fix_query_offset(&$query) 
	{
		if (empty($query->query_vars['offset']) OR empty($query->query_vars['orig_offset'])) {
			return;
		}
		
		if ($query->is_paged) {	
	
		
			// manually determine page query offset (offset + current page (minus one) x posts per page)
			$page_offset = $query->query_vars['offset'] + (($query->query_vars['paged'] - 1) * $query->query_vars['posts_per_page']);
			$query->set('offset', $page_offset);
				
		}
		else {
			// first page? just use the offset
			$query->set('offset', $query->query_vars['offset']);
		}
	}
	
	/**
	 * Preserve original offset query var as it will be changed 
	 * 
	 * @param object $query
	 */
	public function add_query_offset($query = array())
	{
		if (isset($query['offset'])) {
			$query['orig_offset'] = $query['offset'];
		}
		
		return $query;
	}	
	
	/**
	 * A wrapper for common pre_get_posts filters
	 * 
	 * @param object $query
	 */
	public function pre_get_posts(&$query) 
	{
		$this->fix_query_offset($query);
	}
	

	/**
	 * Fix found_posts when an offset is set
	 * 
	 * WordPress found_posts doesn't account for offset.

	 * @param integer $found_posts
	 * @param object $query
	 */
	public function fix_offset_pagination($found_posts, $query)
	{

		if (empty($query->query_vars['offset']) OR empty($query->query_vars['orig_offset'])) {
			return $found_posts;
		}
		
		$offset = $query->query_vars['orig_offset'];
	
		// reduce WordPress's found_posts count by the offset... 
		return $found_posts - $offset;
	}
	
	/**
	 * Add the read more text to excerpts
	 */
	public function excerpt_read_more()
	{
		global $post;
		
		if (is_feed()) {
			return ' [...]';
		}

		// add more link if enabled in options
		if (Bunyad::options()->read_more) {
			
			$text = $this->more_text;
			if (!$text) {
				$text = __('Read More', 'bunyad');
			}
			
			if (empty($this->more_html)) {
				$this->more_html = '<div class="read-more"><a href="%s" title="%s">%s</a></div>';
			}

			return sprintf(apply_filters('bunyad_read_more_html', $this->more_html), get_permalink($post->ID), esc_attr($text), $text);
		}
		
		return '';
	}
}