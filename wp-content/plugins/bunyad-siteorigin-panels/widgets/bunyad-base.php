<?php

class Bunyad_PageBuilder_WidgetBase extends WP_Widget
{
	/**
	 * Create an array of attributes to be used for shortcodes
	 * 
	 * @param array $instance  data source
	 * @param array $attrs   supported attributes
	 * @return array
	 */
	public function shortcode_attribs($instance, $attrs)
	{
		$sc_attrs = array();
		foreach ($attrs as $attrib) 
		{	
			if (!array_key_exists($attrib, $instance)) {
				continue;
			}
			
			$attr = $instance[$attrib];

			if (!empty($attr) OR $attr == '0') {

				if (is_array($attr)) {
					
					$attr = implode(',', $attr);
				}

				$sc_attrs[] = "{$attrib}=\"" . esc_attr($attr) . '"';
			}
		}
		
		return $sc_attrs;
	}
}