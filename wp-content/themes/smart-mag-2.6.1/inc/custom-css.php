<?php
/**
 * Dynamic CSS is required for modifications to Typography, Colors, and Custom CSS.
 * 
 * Also see: inc/css-compiler.php
 *
 */
class Bunyad_Theme_CustomCss
{
	public function __construct()
	{
		add_action('bunyad_theme_init', array($this, 'init'));	
	}
	
	public function init()
	{
		// Bail if there's no custom css to output
		if (!$this->has_custom_css()) {
			return;
		}
		
		// Handler for external file css output
		if (Bunyad::options()->css_custom_output == 'external') {
			add_action('template_redirect', array($this, 'global_external'), 1);
		}
		
		// Register Custom CSS at a lower priority for CSS specificity
		add_action('wp_enqueue_scripts', array($this, 'register'), 99);
	}
	
	/**
	 * Check if the theme has any custom css
	 */
	public function has_custom_css()
	{
		if (count(Bunyad::options()->get_all('css_'))) {
			return true;
		} 
		
		// check if a cat has custom color
		foreach ((array) Bunyad::options()->get_all('cat_meta_') as $cat) 
		{
			if (!empty($cat)) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Action callback: Output Custom CSS using external CSS method
	 */
	public function global_external()
	{		
		// custom css requested?
		if (empty($_GET['bunyad_custom_css']) OR intval($_GET['bunyad_custom_css']) != 1) {
			return;
		}
		
		// set 200 - might be 404
		status_header(200);
		header("Content-type: text/css; charset: utf-8"); 

		include_once get_template_directory() . '/inc/css-compiler.php';
		
		/**
		 * Output the CSS customizations
		 */
		$render = new Bunyad_Custom_Css;
		$render->args = $_GET;
		echo $render->render();
		exit;
	}
	
	
	/**
	 * Action callback: Register Custom CSS with low priority 
	 */
	public function register()
	{
		
		if (is_admin()) {
			return;
		}
		
		// pre-defined scheme / skin
		if (Bunyad::options()->predefined_style) {
			wp_enqueue_style('smartmag-skin', get_template_directory_uri() . '/css/skin-' . Bunyad::options()->predefined_style . '.css');
		}
		
		// add custom css
		if ($this->has_custom_css()) {
			
			$query_args = array();
			
			/**
			 * Global color changes?
			 */ 
			if (is_category() OR is_single()) {
	
				$object = get_queried_object();
				$query_args['anchor_obj'] = '';
				
				if (is_category()) {
					$query_args['anchor_obj'] = $object->cat_ID;
				}
				else {
					// be consistent with the behavior that's like cat labels
					$categories = current((array) get_the_category($object->ID));
					
					if (is_object($categories)) {
						$query_args['anchor_obj'] = $categories->cat_ID;
					}
				}
				
				// only used for main color
				$meta = Bunyad::options()->get('cat_meta_' . $query_args['anchor_obj']);
				if (empty($meta['main_color'])) {
					unset($query_args['anchor_obj']);
				}
				
			}
			
			$query_args = array_merge($query_args, array('bunyad_custom_css' => 1));
			
			/*
			 * Custom CSS Output Method - external or on-page?
			 */
			if (Bunyad::options()->css_custom_output == 'external') 
			{
				wp_enqueue_style('custom-css', add_query_arg($query_args, get_site_url() . '/'));
						
				// add css that's supposed to be per page
				$this->add_per_page();
			}
			else {

				include_once get_template_directory() . '/inc/css-compiler.php';

				// associate custom css at the end
				$source = 'smartmag-core';
				
				if (wp_style_is('smartmag-skin', 'enqueued')) {
					$source = 'smartmag-skin';
				}
				else if (wp_style_is('smartmag-woocommerce', 'enqueued')) {
					$source = 'smartmag-woocommerce';
				} 
				else if (wp_style_is('smartmag-font-awesome', 'enqueued')) {
					$source = 'smartmag-font-awesome';
				}
				
				// add to on-page custom css
				$render = new Bunyad_Custom_Css;
				$render->args = $query_args;
				Bunyad::core()->enqueue_css($source, $render->render() . $this->add_per_page(true));
			}
		}
	}
	
	/**
	 * Custom CSS for pages and posts that shouldn't be cached through css-compiler.php because 
	 * the size will increase exponentially.
	 * 
	 */
	public function add_per_page($return = false) 
	{
		if (!is_admin() && is_singular() && Bunyad::posts()->meta('bg_image')) {
			
			$bg_type = Bunyad::posts()->meta('bg_image_bg_type');
			$the_css = 'background: url("' . esc_attr(Bunyad::posts()->meta('bg_image')) . '");';
			
			if (!empty($bg_type)) {
				
				if ($bg_type == 'cover') {
					$the_css .= 'background-repeat: no-repeat; background-attachment: fixed; background-position: center center; '  
			 		. '-webkit-background-size: cover; -moz-background-size: cover;-o-background-size: cover; background-size: cover;';
				}
				else {
					$the_css .= 'background-repeat: ' . esc_attr($bg_type) .';';
				}
			}
			
			$the_css = 'body.boxed { ' . $the_css . ' }';
			
			// return the css?
			if ($return) {
				return $the_css;
			}
			
			// or enqueue it for inline css
			Bunyad::core()->enqueue_css(
				(wp_style_is('custom-css', 'enqueued') ? 'custom-css' : 'smartmag-core'), 
				$the_css
			);
		}
	}
	
}

// init and make available in Bunyad::get('custom_css')
Bunyad::register('custom_css', array(
	'class' => 'Bunyad_Theme_CustomCss',
	'init' => true
));
