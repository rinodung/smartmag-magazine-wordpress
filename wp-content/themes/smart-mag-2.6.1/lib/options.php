<?php

/**
 * Front-end options handler
 */
class Bunyad_Options
{
	private $configs = array();
	private $cache   = array();
	public $defaults = array();
	
	/**
	 * Initialize cache
	 */
	public function init()
	{
		// save defaults - also used by custom-css generator via Bunyad::options()->default
		$options_tree = include get_template_directory() . '/admin/options.php';
		$this->defaults = Bunyad::factory('admin/options')->get_elements_from_tree($options_tree);
		
		$options = get_option($this->configs['theme_prefix'] . '_theme_options');
		
		if (is_array($options) && $options) {
			$this->cache = $options;
		}
	}
	
	public function __get($key)
	{
		return $this->get($key);
	}
	
	/**
	 * Get an option from the database (cached) or the default value provided 
	 * by the options setup. 
	 * 
	 * @param string $key
	 * @return mixed|null
	 */
	public function get($key)
	{		
		if (isset($this->cache[$key])) {
			return $this->cache[$key];
		}
		
		if (isset($this->defaults[$key]['value'])) {
			return $this->defaults[$key]['value'];
		}
		
		return null;
	}
	
	/**
	 * Remove all cache options - USE WITH CARE!
	 * 
	 * It will destroy changes made via meta to categories if it's used before saving options. 
	 */
	public function clear()
	{
		$this->cache = array();
		return $this;
	}
	
	/**
	 * Get all options data
	 * 
	 * @param null|string $prefix prefix to limit
	 */
	public function get_all($prefix = null) 
	{
		if ($prefix) {
			
			$options = array();
			
			foreach ($this->cache as $key => $value) {
				if (preg_match('/^' . preg_quote($prefix) . '/', $key)) {
					$options[$key] = $value;
				}
			}
			
			return $options;
		}
		
		return $this->cache;
	}
	
	/**
	 * Overwrite all options in cache 
	 * 
	 * @param array $options
	 */
	public function set_all(array $options)
	{
		$this->cache = $options;
		
		return $this;
	}
	
	/**
	 * Updates local cache - DOES NOT saves to DB. Use update() to save.
	 * 
	 * @param string|array $key
	 * @param mixed $value  a value of null will unset the option
	 * @return Bunyad_Options
	 */
	public function set($key, $value = null)
	{
		// array? merge it!
		if (is_array($key)) {
			$this->cache = array_merge($this->cache, $key);
			return $this;
		}
		
		if ($value === null) {
			unset($this->cache[$key]);
		}
		else {
			$this->cache[$key] = $value;
		}
		
		return $this;
	}
	
	/**
	 * Updates the options from local cache to the database
	 * 
	 * @param null|mixed $key
	 * @param null|mixed $value
	 */
	public function update($key = null, $value = null) 
	{
		if ($key != null && $value != null) {
			$this->set($key, $value);
		}
		
		unset($this->cache['shortcodes']);
		
		return update_option($this->configs['theme_prefix'] . '_theme_options', (array) $this->cache);
	}
	
	/**
	 * Set local configurations
	 * 
	 * @param string|array $key
	 * @param mixed  $value
	 */
	public function set_config($key, $value = '')
	{
		if (is_array($key)) {
			$this->configs = array_merge($this->configs, $key);
			return $this;
		}
		
		$this->configs[$key] = $value;
		return $this;
	}
	
	/**
	 * Get local configurations
	 */
	public function get_config($key) {
		return $this->configs[$key];
	}
}