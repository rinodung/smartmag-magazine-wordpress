<?php

/**
 * Setup BBPress compatibility for the theme
 *
 */
class Bunyad_Theme_BBPress 
{
	
	public function __construct()
	{
		// Is bbPress active?
		if (!class_exists('bbpress')) {
			return;
		}
		
		add_action('bunyad_theme_init', array($this, 'init'));	
	} 
	
	/**
	 * Setup bbPress hooks
	 */
	public function init()
	{
		// Register support
		add_theme_support('bbpress');
		
		// Add modal to footer
		add_action('wp_footer', array($this, 'auth_modal'));
		
		// Navigation login icon
		add_filter('nav_menu_css_class', array($this, 'nav_login'), 10, 2);
	}
	
	/**
	 * Action callback: Add login/register modal if bbPress is active
	 */
	public function auth_modal()
	{
		get_template_part('bbpress/auth-modal');
	}
	
	/**
	 * Filter callback: Add user login class to the correct menu item.
	 * 
	 * Mainly used for bbPress!
	 * 
	 * @param array $classes
	 */
	public function nav_login($classes, $item)
	{
		if (strstr($item->url, '#user-login')) {
			$classes[] = 'user-login';
		}
		
		return $classes;
	}
}

// init and make available in Bunyad::get('blocks')
Bunyad::register('bbpress', array(
	'class' => 'Bunyad_Theme_BBPress',
	'init' => true
));