<?php
/**
 * Navigation menus and mega menu functionality.
 */
class Bunyad_Theme_Navigation
{

	public function __construct()
	{
		add_action('bunyad_theme_init', array($this, 'init'));	
	}
	
	public function init()
	{
		/**
		 * Mega menu support
		 */
		add_filter('bunyad_custom_menu_fields', array($this, 'custom_menu_fields'));
		add_filter('bunyad_mega_menu_end_lvl', array($this, 'attach_mega_menu'));
		
		// menu sticky logo support
		add_filter('wp_nav_menu_items', array($this, 'add_navigation_logo'), 10, 2);
		add_filter('wp_nav_menu_items', array($this, 'add_navigation_search'), 10, 2);
		
	}
	
	/**
	 * Filter callback: Custom menu fields.
	 * 
	 * Required for both back-end and front-end.
	 * 
	 * @see Bunyad_Menus::init()
	 */
	public function custom_menu_fields($fields)
	{
		$fields = array(
			'mega_menu' => array(
				'label' => __('Mega Menu', 'bunyad-admin'), 
				'element' => array(
					'type' => 'select',
					'class' => 'widefat',
					'options' => array(
						0 => __('Disabled', 'bunyad-admin'), 
						'category' => __('Category Mega Menu (Subcats, Featured & Recent)', 'bunyad-admin'), 
						'normal' => __('Mega Menu for Links', 'bunyad-admin')
					)
				),
				'parent_only' => true,
				'locations' => array('main'),
			)
		);
		
		return $fields;
	}

	/**
	 * Filter Callback: Add our custom mega-menus
	 *
	 * @param array $args
	 */
	public function attach_mega_menu($args)
	{
		extract($args);
		
		/**
		 * @todo when not using a cache plugin, wrap in functions or cache the menu
		 */
		
		// category mega menu
		if ($item->mega_menu == 'category') {
			$template = 'blocks/mega-menu-category.php';
		} 
		else if ($item->mega_menu == 'normal') {
			$template = 'blocks/mega-menu-links.php';
		}
		
		if ($template) {
			ob_start();
			include locate_template($template);
			$output = ob_get_clean();
			
			return $output;
		}
		
		return $sub_menu;
	}
	
	/**
	 * Filter callback: Add logo to the sticky navigation
	 */
	public function add_navigation_logo($items, $args)
	{
		if (!Bunyad::options()->sticky_nav OR !Bunyad::options()->sticky_nav_logo OR $args->theme_location != 'main') {
			return $items;
		}
		
		if (Bunyad::options()->image_logo_nav) {
			$logo = '<img src="' . esc_attr(Bunyad::options()->image_logo_nav) .'" />'; 
		}
		else {
			$logo = do_shortcode(Bunyad::options()->text_logo);
		}
		
		$items = '<li class="sticky-logo"><a href="'. esc_url(home_url('/')) .'">' . $logo . '</a></li>' . $items;
		
		return $items;
	}
	
	/**
	 * Filter callback: Add a search icon the navigation
	 */
	public function add_navigation_search($items, $args)
	{
		
		return $items;
		if (!Bunyad::options()->nav_search OR $args->theme_location != 'main') {
			return $items;
		}
		
		ob_start();
		
		// nav search flag used by search.php
		$in_nav = true;
		
		?>
		<li class="search-icon">
			<a href="#" title="<?php esc_attr_e('Search', 'bunyad'); ?>"><i class="fa fa-search"></i></a>
			<?php include locate_template('partials/header/search.php'); ?>
		</li>
		<?php
		
		$items .= ob_get_clean();
		
		return $items;
	}
	
}


// init and make available in Bunyad::get('navigation')
Bunyad::register('navigation', array(
	'class' => 'Bunyad_Theme_Navigation',
	'init' => true
));