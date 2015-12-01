<?php

/**
 * SmartMag Theme!
 * 
 * This is the typical theme initialization file. Sets up the Bunyad Framework
 * and the theme functionality.
 * 
 * ----
 * 
 * Other Code Locations:
 * 
 *  /          -  WordPress default template files
 *  lib/       -  Contains the core Bunyad framework files
 *  inc/       -  Theme functions: Helpers, Hooks, Utilities
 *  admin/     -  Admin-only content
 *  partials/  -  Template parts (partials) called via get_template_part()
 *  blocks/    -  Page-builder block views
 *  
 * Note: If you're looking to edit HTML, look for default WordPress templates in
 * top-level / and in partials/ folder.
 * 
 */

// Already initialized? some buggy plugin call?
if (class_exists('Bunyad_Core')) {
	return;
}

/**
 * Initialize Framework
 * 
 * Include the Bunyad_Base and extend it using our theme-specific class.
 */ 
locate_template('lib/bunyad.php', true, true);
locate_template('inc/bunyad.php', true, true);


// Fire up the theme - make available in Bunyad::get('smart_mag')
Bunyad::register('smart_mag', array(
	'class' => 'Bunyad_Theme_SmartMag',
	'init' => true
));

/**
 * Main Framework Configuration
 */
$bunyad = Bunyad::core()->init(apply_filters('bunyad_init_config', array(

	'theme_name' => 'smartmag',
	'meta_prefix' => '_bunyad',
	'theme_version' => '2.6.1',

	// widgets enabled
	'widgets'    => array('about', 'latest-posts', 'popular-posts', 'tabbed-recent', 'flickr', 'ads', 'latest-reviews', 'bbp-login', 'tabber', 'blocks'),
	'post_formats' => array('gallery', 'image', 'video', 'audio'),

	'shortcode_config' => array(
		'font_icons' => true,
		'social_font' => true,
		'button_colors' => array('default', 'red', 'orange', 'yellow', 'blue', 'black'),
	),
	
	// enabled metaboxes and prefs
	'meta_boxes' => array(
		array('id' => 'post-options', 'title' => __('Post Options', 'bunyad-admin'), 'priority' => 'high', 'page' => array('post')),
		array('id' => 'post-reviews', 'title' => __('Review', 'bunyad-admin'), 'priority' => 'high', 'page' => array('post')),
		array('id' => 'page-options', 'title' => __('Page Options', 'bunyad-admin'), 'priority' => 'high', 'page' => array('page')),
	),
	
	// page builder blocks
	'page_builder_blocks' => array(
	
		// special
		'highlights' => 'Bunyad_PageBuilder_Highlights',
		'news-focus' => 'Bunyad_PageBuilder_NewsFocus',
		'focus-grid' => 'Bunyad_PageBuilder_FocusGrid',
		'blog' => 'Bunyad_PageBuilder_Blog',
		'latest-gallery' => 'Bunyad_PageBuilder_LatestGallery',
		'separator' => 'Bunyad_PbBasic_Separator',
		'rich-text' => 'Bunyad_PbBasic_RichText',
		
		// native
		'text' => 'WP_Widget_Text',
		'latest-posts' => array('class' => 'Bunyad_LatestPosts_Widget', 'name' => __('Latest Posts', 'bunyad-admin')),
		'flickr' => array('class' => 'Bunyad_Flickr_Widget', 'name' => __('Flickr Images', 'bunyad-admin')),
		'ads' => array('class' => 'Bunyad_Ads_Widget', 'name' => __('Advertisement', 'bunyad-admin')),
		'latest-reviews' => array('class' => 'Bunyad_LatestReviews_Widget', 'name' => __('Latest Reviews', 'bunyad-admin'))
	),

)));


/**
 * SmartMag Theme!
 * 
 * Anything theme-specific that won't go into the core framework goes here. 
 * 
 * Also see: inc/admin.php for the admin bootstrap.
 */
class Bunyad_Theme_SmartMag
{
	public $woocommerce;
	public $registry = array();
	
	public function __construct() 
	{
		// init skins
		add_action('bunyad_core_post_init', array($this, 'init_skins'));
		
		// perform the after_setup_theme 
		add_action('after_setup_theme', array($this, 'theme_init'), 12);
		
		/**
		 * Require theme functions, hooks, and helpers
		 * 
		 * Can be overriden in Child Themes by creating the same structure. For
		 * instance, you can create inc/blocks.php in your Child Theme folder
		 * and that will be included.
		 * 
		 * The includes below can be retrieved using the Bunyad::get() method.
		 * 
		 * @see Bunyad::get()
		 */
		require_once locate_template('inc/blocks.php');
		require_once locate_template('inc/bbpress.php');
		require_once locate_template('inc/navigation.php');
		require_once locate_template('inc/custom-css.php');
		
		if (is_admin()) {
			require_once locate_template('inc/admin.php');
		}
			
		// include WooCommerce 
		if (function_exists('is_woocommerce')) {
			require_once get_template_directory() . '/woocommerce/init.php';
			$this->woocommerce = new Bunyad_Theme_SmartMag_WooCommerce;
		}
	}
	
	/**
	 * Setup any skin data and configs
	 */
	public function init_skins()
	{
		// include our skins constructs
		if (Bunyad::options()->predefined_style) {
			locate_template('inc/skins/' . sanitize_file_name(Bunyad::options()->predefined_style) .'.php', true);
		}
	}	
	
	/**
	 * Setup enque data and actions
	 */
	public function theme_init()
	{
		/**
		 * Use this hook instead of after_setup_theme to keep the priority setting
		 * consistent amongst all helpers and utils.
		 */
		do_action('bunyad_theme_init');

		
		/**
		 * Set theme image sizes used in different areas, blocks and posts
		 * 
		 * Notes:
		 * 
		 * - 1280x612 images should be used for no cropping to happen.
		 * - Some images are shared between multiple listing styles and blocks.
		 *   This is done to save space on the server
		 */
		$image_sizes = apply_filters('bunyad_image_sizes', array(

			// Default WordPress thumbnail. 
			'post-thumbnail' => array('width'=> 110, 'height' => 96),
		
			// Featured image in posts with no sidebar.
			'main-full' => array('width' => 1078, 'height' => 516),
		
			// Default slider and posts with sidebar.
			'main-slider' => array('width' => 702, 'height' => 336),
		
			// Shared by several blocks like highlights (when sidebar enabled).
			// Also used at 326x160.
			'main-block' => array('width' => 351, 'height' => 185),
		
			// Smaller image for the default slider.
			'slider-small' => array('width' => 168, 'height' => 137),
		
			// Latest gallery carousel block uses it.
			'gallery-block' => array('width' => 214, 'height' => 140),
		
			// Grid Overlay listing image (categories or Blog block). 
			'grid-overlay' => array('width' => 343, 'height' => 215),
		
			// Tall Grid Overlay listing image (categories or Blog block).
			'tall-overlay'  => array('width' => 233, 'height' => 300),
		
			// Images for the Grid Slider.
			'grid-slider-large' => array('width' => 536, 'height' => 386),
			'grid-slider-small' => array('width' => 269, 'height' => 192),
		
			// Small image for the Focus Grid block.
			'focus-grid-small'  => array('width' => 164, 'height' => 82),
		
		));
		
		foreach ($image_sizes as $key => $size) {
			
			// set default crop to true
			$size['crop'] = (!isset($size['crop']) ? true : $size['crop']);
			
			if ($key == 'post-thumbnail') {
				set_post_thumbnail_size($size['width'], $size['height'], $size['crop']);
			}
			else {
				add_image_size($key, $size['width'], $size['height'], $size['crop']);
			}
			
		} 
		
		/*set_post_thumbnail_size(110, 96, true); // 17:15, also used in 85x75 and more similar aspect ratios

		// 1280x612 images for no cropping of featured and slider image
		add_image_size('main-full', 1078, 516, true); 
		add_image_size('main-slider', 702, 336, true);
		
		add_image_size('main-block', 351, 185, true); 
		add_image_size('slider-small', 168, 137, true); // small thumb for slider
		add_image_size('gallery-block', 214, 140, true); // gallery block image
		
		add_image_size('grid-overlay', 343, 215, true); // size for grid overlay listing
		add_image_size('tall-overlay', 233, 300, true); // size for tall grid overlay listing
		
		
		add_image_size('grid-slider-large', 536, 386, true);
		add_image_size('grid-slider-small', 269, 192, true);

		add_image_size('focus-grid-small', 164, 82, true);
		//add_image_size('focus-grid-small', 164, 87, true);
		
		//add_image_size('grid-slider-large', 536, 354, true);
		//add_image_size('grid-slider-small', 269, 176, true);
		*/
		
		// Enqueue assets (css, js)
		add_action('wp_enqueue_scripts', array($this, 'register_assets'));
		

		/**
		 * i18n
		 * 
		 * We have split front-end and backend translations in separate files. 
		 * 
		 * For en_US, following files be used:
		 * 
		 * smart-mag/languages/en_US.mo
		 * smart-mag/languages/bunyad-admin-en_US.mo
		 * 
		 * @see Bunyad_Thmeme_SmartMag::load_admin_textdomain()
		 */
		load_theme_textdomain('bunyad', get_template_directory() . '/languages');
		
		if (is_admin()) {
			$this->load_admin_textdomain('bunyad-admin', get_template_directory() . '/languages');
		}
		
		// setup navigation menu with "main" key
		register_nav_menu('main', __('Main Navigation', 'bunyad-admin'));
		register_nav_menu('main-mobile', __('Main Navigation - Mobile Optional', 'bunyad-admin'));
		
		/**
		 * Reviews Support
		 */
		add_filter('the_content', array($this, 'add_review'));
		add_filter('bunyad_review_main_snippet', array($this, 'add_review_snippet'), 10, 2);
		
		// Content width is required for for oebmed and Jetpacks
		global $content_width;
		
		if (!isset($content_width)) {
			$content_width = 702;
		}
		
		/**
		 * Register Sidebars
		 */		
		$this->register_sidebars();


		/**
		 * Posts related filter
		 */
		
		// prepare to add body classes in advance
		add_action('wp_head', array($this, 'add_body_classes'));
		
		// custom font icons for post formats
		add_filter('bunyad_post_formats_icon', array($this, 'post_format_icon'));
		
		// video format auto-embed
		add_filter('bunyad_featured_video', array($this, 'video_auto_embed'));
		
		// add custom category per_page limits, if any
		add_filter('pre_get_posts', array($this, 'add_category_limits'));
		
		// remove hentry microformat, we use schema.org/Article
		add_action('post_class', array($this, 'fix_post_class'));
		
		// add the orig_offset for offset support in blocks
		add_filter('bunyad_block_query_args', array(Bunyad::posts(), 'add_query_offset'), 10, 1);
		
		// add post type to blocks
		add_filter('bunyad_block_query_args', array($this, 'add_post_type'), 10, 3);
		
		// ajax post content slideshow - add wrapper
		add_filter('the_content', array($this, 'add_post_slideshow_wrap'));
		
		// limit search to posts?
		if (Bunyad::options()->search_posts_only) {
			add_filter('pre_get_posts', array($this, 'limit_search'));
		}
		
		/**
		 * Prevent duplicate posts
		 */
		if (Bunyad::options()->no_home_duplicates) {
			
			// add to removal list on each loop
			add_filter('loop_end', array($this, 'update_duplicate_posts'));
			
			// exclude on blocks
			add_filter('bunyad_block_query_args', array($this, 'add_duplicate_exclude'));
			
			// exclude on widgets
			foreach (array('tabbed_recent', 'popular_posts', 'latest_posts') as $widget) {
				add_filter('bunyad_widget_' . $widget . '_query_args', array($this, 'add_duplicate_exclude'));
			}
		}
		
		/**
		 * Widgets related hooks
		 */
		add_filter('bunyad_widget_tabbed_recent_options', array($this, 'tabbed_recent_options'));
					
		// add image sizes to the editor
		add_filter('image_size_names_choose', array($this, 'add_image_sizes_editor'));
		
		// set dynamic widget columns for footer
		add_filter('dynamic_sidebar_params', array($this, 'set_footer_columns'));
		
		// add support for live search
		add_action('wp_ajax_bunyad_live_search', array($this, 'live_search'));
		add_action('wp_ajax_nopriv_bunyad_live_search', array($this, 'live_search'));
		
		// setup the init hook
		add_action('init', array($this, 'init'));

	}
	
	/**
	 * Action callback: Setup that needs to be done at init hook
	 */
	public function init() 
	{		
		Bunyad::reviews();
		
		/*
		 * Setup shortcodes, and page builder assets 
		 */
		
		// setup theme-specific shortcodes and blocks
		$this->setup_shortcodes();
		
		// setup page builder blocks
		$this->setup_page_builder();
	}
	
	/**
	 * Register and enqueue theme CSS and JS files
	 */
	public function register_assets()
	{
		if (!is_admin()) {
			
			// add jquery, theme js
			wp_enqueue_script('jquery');
			wp_enqueue_script('bunyad-theme', get_template_directory_uri() . '/js/bunyad-theme.js', array('jquery'), Bunyad::options()->get_config('theme_version'), true);

			/*
			 * Add CSS styles
			 */
			
			// add google fonts
			$style = $this->get_style(Bunyad::options()->predefined_style);
			$args  = $style['font_args'];
			
			// blockquote font for single
			if (is_singular()) {
				$args['family'] .= '|Merriweather:300italic';
			}
		
			if (Bunyad::options()->font_charset) {
				$args['subset'] = implode(',', array_keys(array_filter(Bunyad::options()->font_charset)));
			}
			
			wp_enqueue_style('smartmag-fonts', add_query_arg($args, (is_ssl() ? 'https' : 'http') . '://fonts.googleapis.com/css'),	array(), null);
			
			// add core
			if (is_rtl()) {
				wp_enqueue_style('smartmag-core', get_stylesheet_directory_uri() . '/css/rtl.css', array(), Bunyad::options()->get_config('theme_version'));
			}
			else {
				wp_enqueue_style('smartmag-core', get_stylesheet_uri(), array(), Bunyad::options()->get_config('theme_version'));
			}
			
			if (!Bunyad::options()->no_responsive) {
				wp_enqueue_style('smartmag-responsive', get_template_directory_uri() . '/css/'. (is_rtl() ? 'rtl-' : '') . 'responsive.css', array(), Bunyad::options()->get_config('theme_version'));
			}
			
			// add prettyphoto to pages and single posts
			if (Bunyad::options()->lightbox_prettyphoto && (is_single() OR is_page())) {
				wp_enqueue_script('pretty-photo-smartmag', get_template_directory_uri() . '/js/jquery.prettyPhoto.js', array(), false, false);
				wp_enqueue_style('pretty-photo', get_template_directory_uri() . '/css/prettyPhoto.css', array(), Bunyad::options()->get_config('theme_version'));
			}
			
			// bbPress?
			if (class_exists('bbPress')) {
				wp_enqueue_style('smartmag-bbpress', get_template_directory_uri() . '/css/' . (is_rtl() ? 'rtl-' : '') . 'bbpress-ext.css', array(), Bunyad::options()->get_config('theme_version'));
			}			
			
			// CDN for font awesome?
			if (Bunyad::options()->font_awesome_cdn) {
				wp_enqueue_style('smartmag-font-awesome', (is_ssl() ? 'https' : 'http') . '://netdna.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.css');
			}
			else {
				wp_enqueue_style('smartmag-font-awesome', get_template_directory_uri() . '/css/fontawesome/css/font-awesome.min.css', array(), Bunyad::options()->get_config('theme_version'));
			}
			
			// flexslider to the footer
			wp_enqueue_script('flex-slider', 
				get_template_directory_uri() . '/js/' . (is_rtl() ? 'rtl-' : '') . 'jquery.flexslider-min.js', array('jquery'), 
				Bunyad::options()->get_config('theme_version'),
				true
			);
			
			// sticky sidebar where enabled
			wp_enqueue_script('sticky-sidebar',
				get_template_directory_uri() . '/js/jquery.sticky-sidebar.min.js', array('jquery'), 
				Bunyad::options()->get_config('theme_version'),
				true
			);
			
 			// register infinite scroll
			wp_register_script('smartmag-infinite-scroll', 
				get_template_directory_uri() . '/js/jquery.infinitescroll.min.js',
				array('jquery'), 
				Bunyad::options()->get_config('theme_version'),
				true
			);
		}
	}
	
	/**
	 * Setup the sidebars
	 */
	public function register_sidebars()
	{
	
		// register dynamic sidebar
		register_sidebar(array(
			'name' => __('Main Sidebar', 'bunyad-admin'),
			'id'   => 'primary-sidebar',
			'description' => __('Widgets in this area will be shown in the default sidebar.', 'bunyad-admin'),
			'before_title' => '<h3 class="widgettitle">',
			'after_title'  => '</h3>',
		));

		
		// register dynamic sidebar
		register_sidebar(array(
			'name' => __('Top Bar (Above Header)', 'bunyad-admin'),
			'id'   => 'top-bar',
			'description' => __('Please place only a single widget. Preferably a text widget.', 'bunyad-admin'),
			'before_title' => '',
			'after_title'  => '',
			'before_widget' => '',
			'after_widget'  => ''
			
		));
		
		// register dynamic sidebar
		register_sidebar(array(
			'name' => __('Header Right', 'bunyad-admin'),
			'id'   => 'header-right',
			'description' => __('Please place only a single widget. Preferably text-widget.', 'bunyad-admin'),
			'before_title' => '',
			'after_title'  => '',
			'before_widget' => '',
			'after_widget'  => ''
			
		));
		
		// register dynamic sidebar
		register_sidebar(array(
			'name' => __('Footer (3 widgets columns)', 'bunyad-admin'),
			'id'   => 'main-footer',
			'description' => __('Widgets in this area will be shown in the footer. Max 3 widgets.', 'bunyad-admin'),
			'before_title' => '<h3 class="widgettitle">',
			'after_title'  => '</h3>',
			'before_widget' => '<li class="widget column %2$s">',
			'after_widget' => '</li>'
		));
		
		
		// register dynamic sidebar
		register_sidebar(array(
			'name' => __('Lower Footer', 'bunyad-admin'),
			'id'   => 'lower-footer',
			'description' => __('Prefer simple text widgets here.', 'bunyad-admin'),
			'before_title' => '',
			'after_title'  => '',
			'before_widget' => '',
			'after_widget'  => ''
		));
	}
	
	/**
	 * Load admin textdomain
	 * 
	 * WordPress's default theme textdomain can get too cluttered with translations. Our Admin
	 * translations are split up from the main translations.
	 * 
	 * @see load_theme_textdomain()
	 */
	public function load_admin_textdomain($domain, $path)
	{
		
		$locale = get_locale();
		
		/**
		 * Filter a theme's locale.
		 * 
		 * @param string $locale The theme's current locale.
		 * @param string $domain Text domain. Unique identifier for retrieving translated strings.
		 */
		$locale = apply_filters('theme_locale', $locale, $domain);
	
		// Load the textdomain according to the theme
		$mofile = untrailingslashit($path) . "/{$domain}-{$locale}.mo";
		if ($loaded = load_textdomain($domain, $mofile)) {
			return $loaded;
		}
	
		// Otherwise, load from the languages directory
		$mofile = WP_LANG_DIR . "/themes/{$domain}-{$locale}.mo";
		return load_textdomain($domain, $mofile);
	}
	
	/**
	 * Any layout blocks that are layout/page/theme-specific will be included to extend
	 * the default shortcodes supported by the Bunyad Shortcodes Plugin.
	 */
	public function setup_shortcodes()
	{
		if (!is_object(Bunyad::options()->shortcodes)) {
			return false;
		}
		
		Bunyad::options()->shortcodes->add_blocks(array(
			// file based
			'blog' => array('render' => locate_template('blocks/blog.php'), 'attribs' => array(
				'pagination' => 0, 'heading' => '', 'heading_type' => '', 'posts' => 4, 'type' => '', 'cat' => '', 'cats' => '', 'tags' => '',
				'sort_by' => '', 'sort_order' => '', 'taxonomy' => '', 'offset' => '', 'post_type' => '', 'pagination_type' => '',
				'cat_labels' => 1
			)),
			
			'highlights' => array('render' => locate_template('blocks/highlights.php'), 'attribs' => array(
				'type' => '', 'posts' => 4, 'cat' => null, 'column' => '', 'columns' => '', 'cats' => '', 'tags' => '', 
				'tax_tag' => '', 'headings' => '', 'title' => '', 'sort_by' => '', 'sort_order' => '', 'taxonomy' => '',
				'offset' => '', 'offsets' => '', 'post_type' => '', 'heading_type' => 'auto'
			)),
			
			'review' => array('render' => locate_template('blocks/review.php'), 'attribs' => array('position' => 'bottom')),
			
			'news_focus' => array('render' => locate_template('blocks/news-focus.php'), 'attribs' => array(
				'posts' => 5, 'cat' => null, 'column' => '', 'tax_tag' => '', 'sub_cats' => '', 'sub_tags' => '',
				'sort_by' => '', 'sort_order' => '', 'highlights' => 1, 'taxonomy' => '', 'offset' => '', 'post_type' => '',
				'title' => '', 'heading_type' => 'block-filter'
			)),
			
			'focus_grid' => array('render' => locate_template('blocks/focus-grid.php'), 'attribs' => array(
				'posts' => 5, 'cat' => null, 'column' => '', 'tax_tag' => '', 'sub_cats' => '', 'sub_tags' => '',
				'sort_by' => '', 'sort_order' => '', 'highlights' => 1, 'taxonomy' => '', 'offset' => '', 'post_type' => '',
				'title' => '', 'heading_type' => 'block-filter'
			)),			
			
			// string based
			'main-color' => array('template' => '<span class="main-color">%text%</span>', 'attribs' => array('text' => '')),
		));
		
		// setup shortcode modifications
		if (is_admin()) {
			add_filter('bunyad_shortcodes_list', array($this, 'shortcodes_list'));
			add_filter('bunyad_shortcodes_lists_options', array($this, 'shortcodes_lists_options'));
			
			// add editor styling
			if (Bunyad::options()->editor_styling) {
				add_editor_style('css/editor-style.css');
			}
			
			add_filter('mce_buttons', array($this, 'add_editor_buttons'));
			add_filter('tiny_mce_before_init', array($this, 'add_editor_formats'), 1);
		}
	}


	public function add_editor_buttons($buttons) {
		array_push($buttons, 'styleselect');
		return $buttons;
	}

	/**
	 * Filter callback: Add formats to the TinyMCE Editor
	 * 
	 * @param array $settings
	 */
	public function add_editor_formats($settings) {
	
		$formats = array(
		
			array(
				'title'   => __('Quote - Modern', 'bunyad-admin'),
				'block'   => 'blockquote',
				'classes' => 'modern-quote full',
				'wrapper' => true,
			),
			
			array(
				'title'  => __('Citation (for quote)', 'bunyad-admin'),
				'inline' => 'cite',
			),
			
			array(
				'title'   => __('Quote Left - Modern', 'bunyad-admin'),
				'block'   => 'aside',
				'classes' => 'modern-quote pull alignleft',
				'wrapper' => true,
			),
			
			array(
				'title'   => __('Quote Right - Modern', 'bunyad-admin'),
				'block'   => 'aside',
				'classes' => 'modern-quote pull alignright',
				'wrapper' => true,
			),
		);
	
		$settings = array_merge($settings, array(
			'style_formats_merge' => false,
			'style_formats' =>  json_encode($formats),
		));
		
		// editor styling enabled?
		if (Bunyad::options()->editor_styling) {
			$settings['body_class'] = 'post-content';
		}
	
		// Return New Settings
		return $settings;
	}

	public function shortcodes_list($list)
	{
		// de-register unsupported shortcodes
		unset(
			$list['default']['box'],
			$list['default']['social']['dialog'], 
			$list['default']['social']['label']
		);
		return $list;
	}
	
	public function shortcodes_lists_options($options)
	{
		// remove arrow option from defaults for "Custom Lists" in gui creator
		$options['style']['options']['arrow-right'] = $options['style']['options']['arrow'];
		unset($options['style']['options']['arrow']);
		unset($options['ordered']);
		
		return $options;
	}
	
	/**
	 * Initialize the blocks used by page builder
	 */
	public function setup_page_builder()
	{
		// plugin is not active?
		if (!class_exists('Bunyad_PageBuilder_WidgetBase')) {
			return;
		}
		
		$blocks = Bunyad::options()->get_config('page_builder_blocks');
		add_filter('siteorigin_panels_widgets', array($this, 'register_builder_blocks'));
		
		foreach ($blocks as $block => $class) 
		{
			if (is_array($class)) {
				$class = $class['class'];
			}			
			
			if (strstr($class, 'Bunyad_PageBuilder')) {
				include_once get_template_directory() . '/blocks/page-builder/' . sanitize_file_name($block) . '.php';
			}
		}

		// pre-made layouts
		add_filter('siteorigin_panels_prebuilt_layouts', array($this, 'register_builder_layouts'));
	}
	
	/**
	 * Filter callback: Register usable page builder blocks
	 */
	public function register_builder_blocks($defaults)
	{
		$blocks = Bunyad::options()->get_config('page_builder_blocks');
		
		$pb_blocks = array();
		foreach ($blocks as $block => $class) {
			
			if (is_array($class)) {
				$pb_blocks[$block] = $class;
				continue;
			}
			
			$pb_blocks[$block] = array('class' => $class);
		}
		
		return array_merge((array) $defaults, $pb_blocks);
	}
	
	/**
	 * Filter callback: Setup pre-built layouts for page builder
	 * 
	 * @param array $layouts
	 */
	public function register_builder_layouts($layouts)
	{
		$layouts['Main Page'] = json_decode('{"widgets":[{"no_container":"1","posts":"","columns":"2","cat_1":"14","cat_2":"15","cat_3":"0","info":{"class":"Bunyad_PageBuilder_Highlights","id":"1","grid":"0","cell":"0"}},{"no_container":"1","posts":"","cat":"17","info":{"class":"Bunyad_PageBuilder_NewsFocus","id":"2","grid":"1","cell":"0"}},{"no_container":"1","posts":"","cat":"16","info":{"class":"Bunyad_PageBuilder_NewsFocus","id":"3","grid":"2","cell":"0"}},{"no_container":"1","type":"line","info":{"class":"Bunyad_PbBasic_Separator","id":"4","grid":"3","cell":"0"}},{"no_container":"1","posts":"","columns":"3","cat_1":"19","cat_2":"15","cat_3":"18","info":{"class":"Bunyad_PageBuilder_Highlights","id":"5","grid":"4","cell":"0"}},{"no_container":"1","title":"Recent Videos","number":"10","format":"video","cat":"0","info":{"class":"Bunyad_PageBuilder_LatestGallery","id":"6","grid":"5","cell":"0"}}],"grids":[{"cells":"1","style":""},{"cells":"1","style":""},{"cells":"1","style":""},{"cells":"1","style":""},{"cells":"1","style":""},{"cells":"1","style":""}],"grid_cells":[{"weight":"1","grid":"0"},{"weight":"1","grid":"1"},{"weight":"1","grid":"2"},{"weight":"1","grid":"3"},{"weight":"1","grid":"4"},{"weight":"1","grid":"5"}],"name":"Main Homepage Example"}', true);
		
		return $layouts;
	}
	
	/**
	 * Get a skin settings
	 * 
	 * @param string $style
	 */
	public function get_style($style = '')
	{
		$styles = array(
			'default' => array(
				'font_args' => array('family' => 'Open+Sans:400,400Italic,600,700|Roboto+Slab'),
			),
			
			'tech' => array(
				'font_args' => array('family' => 'Open+Sans:400,400italic,600,700|Roboto:400,500|Roboto+Condensed:400,600'),
			),
		);
		
		if (empty($styles[$style])) {
			return $styles['default'];
		}
		
		return $styles[$style];
	}
	
	/**
	 * Action callback: Add classes to body
	 */
	public function add_body_classes()
	{
		
		// Add body class for pages with slider
		if (is_page() && Bunyad::posts()->meta('featured_slider')) {
			Bunyad::core()->add_body_class('has-featured');
		}
		
		// Add body class for archives with slider
		if (is_category()) {
			$meta = Bunyad::options()->get('cat_meta_' . get_query_var('cat'));

			if (!empty($meta['slider'])) {
				Bunyad::core()->add_body_class('has-featured');
			}
		}
		
		// Add navigation style, such as has-nav-light
		Bunyad::core()->add_body_class('has-' . (Bunyad::options()->nav_style ? Bunyad::options()->nav_style : 'nav-dark'));
		
		// Add navigation layout style, such has has-nav-full
		if (Bunyad::options()->nav_layout) {
			Bunyad::core()->add_body_class('has-' . Bunyad::options()->nav_layout);
		}
		
		// Add mobile header style class
		if (Bunyad::options()->mobile_header == 'modern') {
			Bunyad::core()->add_body_class('has-mobile-head');
		}
	}
	
	/**
	 * Fontawesome based post format icon
	 */
	public function post_format_icon() 
	{
		switch (get_post_format()) {
			
			case 'image':
			case 'gallery':
				$icon = 'fa-picture-o';
				break;
			
			case 'video';
				$icon = 'fa-film';
				break;
				
			case 'audio':
				$icon = 'fa-music';
				break;
				
			default:
				return '';
		}	
		
		return '<i class="fa ' . $icon .'"></i>';
	}
	
	/**
	 * Filter callback: Auto-embed video using a link
	 * 
	 * @param string $content
	 */
	public function video_auto_embed($content) 
	{
		global $wp_embed;
		
		if (!is_object($wp_embed)) {
			return $content;
		}
		
		return $wp_embed->autoembed($content);
	}
	
	/**
	 * Filter callback: Fix search by limiting to posts
	 * 
	 * @param object $query
	 */
	public function limit_search($query)
	{
		if (!$query->is_search OR !$query->is_main_query()) {
			return $query;
		}

		// ignore if on bbpress and woocommerce - is_woocommerce() cause 404 due to using get_queried_object()
		if (is_admin() OR (function_exists('is_bbpress') && is_bbpress()) OR (function_exists('is_shop') && is_shop())) {
			return $query;
		}
		
		// limit it to posts
		$query->set('post_type', 'post');
		
		return $query;
	}
	
	/**
	 * Filter callback: Add custom per page limits where set for individual category
	 * 
	 * @param object $query
	 */
	public function add_category_limits($query)
	{
		// bail out if incorrect query
		if (is_admin() OR !$query->is_category() OR !$query->is_main_query()) {
			return $query;
		}
		
		// permalinks have id or name?
		if ($query->get('cat')) {
			$category = get_category($query->get('cat'));
		}
		else {
			$category = get_category_by_slug($query->get('category_name'));	
		}
		
		// category meta
		$cat_meta = (array) Bunyad::options()->get('cat_meta_' . $category->term_id);
		
		// set user-specified per page
		if (!empty($cat_meta['per_page'])) {
			$query->set('posts_per_page', intval($cat_meta['per_page']));
		}
		
		return $query;
	}
	
	/**
	 * Add review/ratings to content
	 * 
	 * @param string $content
	 */
	public function add_review($content)
	{
		if (!is_single() OR !Bunyad::posts()->meta('reviews')) {
			return $content;
		}
		
		$position  = Bunyad::posts()->meta('review_pos');
		$shortcode = do_shortcode('[review position="'. esc_attr($position) .'"]');
		
		// based on placement
		if (strstr($position, 'top')) { 
			$content =  $shortcode . $content;
		}
		else if ($position == 'bottom') {
			$content .= $shortcode; 
		}
		
		return $content;
	}
	
	/**
	 * Filter callback: Add theme's default review snippet
	 * 
	 * @param string $content
	 */
	public function add_review_snippet($content, $type = null)
	{
		if (!Bunyad::posts()->meta('reviews')) {
			return $content;
		}	
		
		// star or bar rating?
		if ($type == 'stars') 
		{
			if (Bunyad::options()->review_style == 'stars') 
			{
				return '
					<span class="star-rating">
						<span class="main-stars"><span style="width: '. Bunyad::reviews()->decimal_to_percent(Bunyad::posts()->meta('review_overall')) .'%;">
							<strong class="rating">' . Bunyad::posts()->meta('review_overall') . '</strong></span>
						</span>
					</span>';
			}
			
			return $content;
		}
		else if (Bunyad::options()->review_style == 'bar') {
				
			return '<div class="review rate-number"><span class="progress"></span><span>' . Bunyad::posts()->meta('review_overall') . '</span></div>';
		}
		
		return $content;
	}
	
	/**
	 * Filter callback: Remove unnecessary classes
	 */
	public function fix_post_class($classes = array())
	{
		// remove hentry, we use schema.org
		$classes = array_diff($classes, array('hentry'));
		
		return $classes;
	}
	
	/**
	 * Filter callback: Add post types to page builder blocks
	 * 
	 * @param array $args  query args
	 * @param string|null $type 
	 * @param array|null $atts  shortcode attributes for this block
	 */
	public function add_post_type($args, $type = null, $atts = null)
	{
		if (is_array($atts) && !empty($atts['post_type'])) {
			$args['post_type'] = array_map('trim', explode(',', $atts['post_type']));
		}
	
		return $args;
	}
	
	/**
	 * Filter callback: Add a wrapper to the content slideshow wrapper
	 * 
	 * @param string $content
	 */
	public function add_post_slideshow_wrap($content)
	{
		if (is_single() && Bunyad::posts()->meta('content_slider') == 'ajax') {
			return '<div class="content-page">' . $content . '</div>';
		}
		
		return $content;
	}
	
	/**
	 * Action callback: Add to list processed posts to handle duplicates
	 * 
	 * @param object $query
	 */
	public function update_duplicate_posts(&$query)
	{
		// the query must enable logging
		if (empty($query->query_vars['handle_duplicates']) OR !did_action('bunyad_pre_main_content')) {
			return;
		}

		// add to list
		foreach ($query->posts as $post) 
		{
			$duplicates = (array) $this->registry['page_duplicate_posts'];
			array_push($duplicates, $post->ID); 
			
			$this->registry['page_duplicate_posts'] = $duplicates;
		}
	}
	
	/**
	 * Filter callback: Enable duplicate prevention on these query args
	 * 
	 * @param array $query  query arguments
	 */
	public function add_duplicate_exclude($query) 
	{
		if (!is_front_page()) {
			return $query;
		}
		
		if (!isset($this->registry['page_duplicate_posts'])) {
			$this->registry['page_duplicate_posts'] = array();
		}
		
		$query['post__not_in'] = $this->registry['page_duplicate_posts'];
		$query['handle_duplicates'] = true;
				
		return $query;
	}
	
	/**
	 * Modify available options for Recent Tabs widget
	 * 
	 * @param array $options
	 */
	public function tabbed_recent_options($options)
	{
		if (!empty($options['comments'])) {
			unset($options['comments']);
		}
		
		return $options;
	}	
	
	/**
	 * Filter callback: Add custom image sizes to the editor image size selection
	 * 
	 * @param array $sizes
	 */
	public function add_image_sizes_editor($sizes) 
	{
		global $_wp_additional_image_sizes;
		
		if (empty($_wp_additional_image_sizes)) {
			return $sizes;
		}

		foreach ($_wp_additional_image_sizes as $id => $data) {

			if (in_array($id, array('main-full', 'main-slider', 'main-block', 'gallery-block')) && !isset($sizes[$id])) {
				$sizes[$id] = __('Theme - ', 'bunyad-admin') . ucwords(str_replace('-', ' ', $id));
			}
		}
		
		return $sizes;
	}

	/**
	 * Filter callback: Set column for widgets where dynamic widths are set
	 * 
	 * @param array $params
	 * @see dynamic_sidebar()
	 */
	public function set_footer_columns($params)
	{
		static $count = 0, $columns, $last_id;
		
		if (empty($columns)) {
			$columns = array(
				'main-footer' => $this->parse_column_setting(Bunyad::options()->footer_columns)
			);
		}
		
		/**
		 * Set correct column class for each widget in footer
		 */
		
		$id = $params[0]['id'];
		
		// reset counter if last sidebar id was different than current
		if ($last_id != $id) {
			$count = 0;
		}
		
		// skip everything but these
		if (in_array($params[0]['id'], array('main-footer'))) {
			
			if (isset($columns[$id][$count])) {
				$params[0]['before_widget'] = str_replace('column', $columns[$id][$count], $params[0]['before_widget']);
			}
			
			$count++;	
		}
		
		$last_id = $id;
	
		return $params;	
	}	
	
	/**
	 * Parse columns of format 1/2+1/4+1/4 into an array of col-X
	 * 
	 * @param   array  $cols
	 * @return  array  Example: array('col-6', 'col-3', ...)
	 */
	public function parse_column_setting($cols)
	{
		$columns = array();
		
		foreach (explode('+', $cols) as $col) 
		{			
			$col = explode('/', trim($col));
			
			if (!empty($col[0]) && !empty($col[1])) {
				
				$width = number_format($col[0] / $col[1], 2);
				
				// pre-parsed map to save computation time
				$map = array(
					'0.08' => 'col-1', '0.17' => 'col-2', '0.25' => 'col-3', '0.33' => 'col-4', 
					'0.42' => 'col-5', '0.50' => 'col-6', '0.58' => 'col-7', '0.67' => 'col-8', 
					'0.75' => 'col-9', '0.83' => 'col-10', '0.92' => 'col-11', '1.00' => 'col-12'
				);
				
				if (array_key_exists($width, $map)) {
					array_push($columns, $map[$width]);
				}
			}	
		}
		
		return $columns;
	}
	
	/**
	 * Action callback: AJAX handler for live search results
	 */
	public function live_search()
	{
		
		get_template_part('partials/live-search');

		// terminate ajax request
		wp_die();
	}
}
