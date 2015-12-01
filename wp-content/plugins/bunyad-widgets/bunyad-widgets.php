<?php
/*
Plugin Name: Bunyad Widgets
Plugin URI: http://theme-sphere.com
Description: Bunyad Widgets add several widgets for ThemeSphere themes.
Version: 1.0.6
Author: ThemeSphere
Author URI: http://theme-sphere.com
License: GPL2
*/

$widgets = new Bunyad_Widgets;
add_action('widgets_init', array($widgets, 'setup'));

class Bunyad_Widgets 
{
	public function setup() 
	{
		// i18n
		load_plugin_textdomain('bunyad-widgets', false, basename(dirname(__FILE__)) . '/languages');
		
		$widgets = apply_filters('bunyad-active-widgets', array(
			'about', 'twitter', 'latest-posts', 'social-count', 'popular-posts', 'tabbed-recent', 'flickr'
		));
		
		// activate widgets
		foreach ($widgets as $widget) 
		{
			$file = dirname(__FILE__) .'/widgets/widget-'. str_replace('..', '', $widget) .'.php';
			
			if (!file_exists($file)) {
				continue;
			}
			
			include_once $file;
			
			$class = 'Bunyad_' . implode('', array_map('ucfirst', explode('-', $widget))) . '_Widget';
			
			if (method_exists($class, 'register_widget')) {
				$caller = new $class;
				$caller->register_widget(); 
			}
			else {
				register_widget($class);
			}
		}
	}
}