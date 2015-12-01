<?php

/**
 * Bunyad Framework - factory
 * 
 * Basic namespacing utility is provided by this factory for easy changes
 * for a a theme that has different needs than the original one. 
 * 
 * @author Asad Khan - http://twitter.com/asadkn
 */

class Bunyad {
	
	protected static $_cache = array();
	
	/**
	 * Build the required object instance
	 * 
	 * @param string  $object
	 * @param boolean $fresh 	whether to get a fresh copy; will not be cached and won't override 
	 * 							current copy in cache.
	 */
	public static function factory($object = 'core', $fresh = false)
	{
		if (isset(self::$_cache[$object]) && !$fresh) {
			return self::$_cache[$object];
		}
		
		// convert short-codes to Bunyad_ShortCodes; core to Bunyad_Core etc.
		$class = str_replace('/', '_', $object);
		$class = 'Bunyad_' . self::file_to_class_name($class);
		
		if (!class_exists($class)) {
			// load the file replacing _ to directory separator and lower the name
			require_once dirname(__FILE__) . '/' . $object . '.php';
		}
		
		// don't cache fresh objects
		if ($fresh) {
			return new $class; 
		}
		
		self::$_cache[$object] = new $class;
		return self::$_cache[$object];
	}
	
	public static function file_to_class_name($file_name)
	{
		return implode('', array_map('ucfirst', explode('-', $file_name)));
	}
	
	/**
	 * Core class
	 * 
	 * @return Bunyad_Core
	 */
	public static function core($fresh = false) 
	{
		return self::factory('core', $fresh);
	}
	
	/**
	 * Shortcodes handler
	 *
	 * @param boolean $fresh
	 * @return Bunyad_ShortCodes
	 */
	public static function codes($fresh = false)
	{
		if (is_object(self::options()->shortcodes)) {
			return self::options()->shortcodes;
		}
	}
	
	/**
	 * Options class
	 * 
	 * @return Bunyad_Options
	 */
	public static function options($fresh = false)
	{
		return self::factory('options', $fresh);
	}
	
	/**
	 * Posts related functionality
	 * 
	 * @return Bunyad_Posts
	 */
	public static function posts($fresh = false)
	{
		return self::factory('posts', $fresh);
	}
}
