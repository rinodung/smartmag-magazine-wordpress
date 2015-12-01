<?php

/**
 * Global Shared Registry 
 * 
 * An in-memory global shared registry to store shared objects and variables in memory.
 */
class Bunyad_Registry
{
	private $registry = array();

	/**
	 * Magic method for get()
	 * 
	 * @see Bunyad_Registry::get()
	 */
	public function __get($key)
	{
		return $this->get($key);
	}
	
	/**
	 * Magic method for set()
	 * 
	 * @see Bunyad_Registry::set()
	 */
	public function __set($key, $value)
	{
		return $this->set($key, $value);
	}
	
	/**
	 * Get a key stored in registry
	 * 
	 * @param string $key
	 * @return mixed|null
	 */
	public function get($key)
	{		
		if (isset($this->registry[$key])) {
			return $this->registry[$key];
		}
		
		return null;
	}
	
	/**
	 * Empty the registry
	 */
	public function clear()
	{
		$this->registry = array();
		return $this;
	}
	
	/**
	 * Add an entry or multiple entries to registry
	 * 
	 * @param string|array $key
	 * @param mixed $value  a value of null will unset the option
	 * @return Bunyad_Registry
	 */
	public function set($key, $value = null)
	{
		// array? merge it!
		if (is_array($key)) {
			$this->registry = array_merge($this->registry, $key);
			return $this;
		}
		
		if ($value === null) {
			unset($this->registry[$key]);
		}
		else {
			$this->registry[$key] = $value;
		}
		
		return $this;
	}
}