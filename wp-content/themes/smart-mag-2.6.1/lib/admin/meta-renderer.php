<?php

class Bunyad_Admin_MetaRenderer extends Bunyad_Admin_OptionRenderer
{
	private $prefix;
	
	public function set_prefix($prefix)
	{
		$this->prefix = $prefix;
		return $this;
	}
	
	/**
	 * Template file and metadata
	 * 
	 * @see Bunyad_Admin_OptionRenderer::template()
	 */
	public function template($options = array(), $file, $populate = array(), $data = array())
	{
		parent::template(array(), $file, $populate, $data); 
	}
	
	public function render($element)
	{
		// set default value if available
		if (isset($this->default_values[$element['name']])) {
			$default = $this->default_values[$element['name']];

			// array? - possible messed up import
			if (is_array($default) && isset($default[0])) {
				$default = $default[0];
			}

			$element['value'] = $default;
		}
		
		$renderer = 'render_' . $element['type'];
		if (method_exists($this, $renderer)) {
			return $this->$renderer($element);
		}
	}

	/**
	 * Get compatible options - currently just adds prefix to name 
	 * 
	 * @param array $options multi-dimensional array of options
	 */
	public function options($options)
	{
		$new_options = array();
		foreach ($options as $key => $option)
		{
			$option['name']    = $this->prefix . $option['name'];
			$new_options[$key] = $option;
		}
		
		return $new_options;
	}
}