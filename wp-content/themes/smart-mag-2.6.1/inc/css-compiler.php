<?php

/**
 * Render the CSS customizations using Bunyad Framework options
 */
class Bunyad_Custom_Css
{
	public $elements;
	public $css_options;
	
	public $css = array();
	public $google_fonts = array();
	
	/**
	 * Stores options relevant to parsing the current CSS
	 * 
	 * @var array
	 */
	public $args = array();
	
	
	/**
	 * Initialize and get all the Custom CSS options
	 */
	public function init()
	{
		// get option elements
		$options = include get_template_directory() . '/admin/options.php';
		//$admin_options = Bunyad::factory('admin/options'); /* @var $admin_options Bunyad_Admin_Options */
		//$this->elements = $admin_options->get_elements_from_tree($options);
		$this->elements = Bunyad::options()->defaults;
		
		// get all css options
		$this->css_options = Bunyad::options()->get_all('css_');
		
		/*
		 * Add parent elements for the rogue pseudo elements like x_size, x_color
		 */
		$offset = 0;
		foreach ($this->css_options as $key => $value)
		{
			$offset++;
			
			if (!preg_match('/_(size|color)$/', $key, $match)) {
				continue;
			}
			
			$key = str_replace($match[0], '', $key);
			
			// if parent already exists or isn't registered in options, skip it
			if (isset($this->css_options[$key]) OR !array_key_exists($key, $this->elements)) {
				continue;
			}
			
			// add parent of the pseudo element
			$this->css_options = array_slice($this->css_options, 0, $offset-1, true) // array 0 to offset
					+ array($key => $this->elements[$key]['value']) // default value
					+ array_slice($this->css_options, $offset-1, null, true); // array offset to end
			
			$offset++; // another index added, so add to the counter
		}
	}
	
	/**
	 * Process the main CSS changes and construct the basic CSS 
	 * for colors, typography etc.
	 */
	public function process()
	{
		$this->init();

		/*
		 * Rendering of all custom CSS
		 */
		foreach ($this->css_options as $key => $value)
		{
			if (!array_key_exists($key, $this->elements)) {
				continue;
			}
			
			$element = $this->elements[$key];
			
			// default? skip!
			if (!$element OR ($element['type'] != 'typography' && $element['value'] == $value) OR empty($value)) {
				continue;
			}
			
			/*
			 * Typography css options: font, size, color
			 */
			if ($element['type'] == 'typography')
			{
				// defaults
				$this->css_options = array_merge(array($key . '_size' => null, $key . '_color' => null), $this->css_options);
				$attribs = '';
				
				$element['fallback_stack'] = !empty($element['fallback_stack']) ? $element['fallback_stack'] : 'Arial, sans-serif';
				
				// skip if same as default
				if ($element['value'] != $value) 
				{
					// system or custom fonts
					if (preg_match('/^(system|custom|typekit):/', $value, $match)) {
					
						$attribs .= 'font-family: ' . str_replace($match[0], '', $value) . '; '; 
					}
					else {
						
						/**
						 * A Google font or font family
						 */
						
						// a specific font?
						if (strstr($value, ':')) {
						
							// font-family value
							$font = explode(':', $value); // [0] => font, [1] regular/weight
							$attribs .= 'font-family: "' . esc_attr($font[0])  . '", ' . esc_attr($element['fallback_stack']) . '; ';
							
							array_push($this->google_fonts, $value);
									
							// italic style - may be in 400italic format
							if (stristr($font[1], 'italic')) {
								$attribs .= "font-style: italic; ";
								$font[1]  = str_replace('italic', '', $font[1]);
							}
							
							if (is_numeric($font[1])) {
								$attribs .= 'font-weight: ' . intval($font[1]) . '; ';
							}
							else if ($font[1] === 'regular') {
								$attribs .= 'font-weight: normal;';
							}
						
						}
						// font family
						else {
		
							// load regular, semi-bold (500/600), bold
							$this->google_fonts = array_merge($this->google_fonts, array($value . ':400', $value . ':600', $value . ':700'));
						
							$attribs .= 'font-family: "' . esc_attr($value) . '", ' . esc_attr($element['fallback_stack']) . ';'; 
						}
					}
					
				}
				
				// have size changed?
				$size = $this->css_options[$key . '_size'];
				if ($size && isset($element['size']['value']) && $element['size']['value'] != $size) {
					$attribs .= 'font-size: ' . intval($size) . 'px; '; 
				}
				
				// have color changed?
				$color = $this->css_options[$key . '_color'];
				if ($color && $this->elements[$key . '_color']['value'] != $color) {
					$attribs .= 'color: ' . esc_attr($color) . '; '; 
				}
				
				$this->css[] = $element['css']['selectors'] . ' { ' . $attribs . ' }';
			}
			// array to selectors to process
			else if (isset($element['css']) && is_array($element['css']['selectors'])) 
			{
				foreach ($element['css']['selectors'] as $css_key => $format)
				{
					// ignore media querie selectors if responsive is disabled
					if (Bunyad::options()->no_responsive && strstr($css_key, '@media')) {
						continue;
					}
					
					if (isset($element['bg_type'])) {
						$element['the_bg_type'] = isset($this->css_options[$key . '_bg_type']) ? $this->css_options[$key . '_bg_type'] : $element['bg_type']['value'];
					}
					
					// create the selector
					$the_css = str_replace("\t", '', $css_key) . ' { ' . $this->create_rules($format, $value, $element); 
					
					// close media queries
					if (strstr($css_key, '@media')) {
						$the_css .= ' }';
					}
					
					$this->css[] = $the_css ." }\n";
				}
			}	
		
		} // end main loop
		
		
		$this->add_fonts();
		$this->category_css();
		
	} // end process()
	
	/**
	 * Create inner CSS rules based on provided format and value
	 * 
	 * @param string $format
	 * @param mixed $value
	 */
	public function create_rules($format, $value, $element = null)
	{		
		// RGBA color?
		$color = $value;
		if (strstr($format, 'rgba(')) {
			
			$rgb = $this->hex2rgb($value);
			$color = $rgb['red'] . ',' . $rgb['green'] . ',' . $rgb['blue'];

			// add rgb for IE8
			$format = preg_replace('/rgba\([^\)]+\)/', 'rgb(' . $color . ')', $format) . ' ' . $format;
		}

		// add the css
		$the_css = sprintf($format, $color) . ';';
		
		/*
		 * Background image cover or repeat setting
		 */
		if (!empty($element['the_bg_type'])) {

			// get default if not specified
			$bg_type = $element['the_bg_type'];
			
			if ($bg_type == 'cover') {
				$the_css .= 'background-repeat: no-repeat; background-attachment: fixed; background-position: center center; '  
		 		. '-webkit-background-size: cover; -moz-background-size: cover;-o-background-size: cover; background-size: cover;';
			}
			else {
				$the_css .= 'background-repeat: ' . esc_attr($bg_type) .';';
			}
		}
		
		return $the_css;
	}
	
	/**
	 * Add google and custom fonts
	 */
	public function add_fonts()
	{	
		
		/*
		 * Include custom fonts
		 */
		$custom_fonts = Bunyad::options()->fonts_custom;
		
		if (!empty($custom_fonts['url'])) {
			
			$fonts = array();
			
			foreach ($custom_fonts['url'] as $key => $url) {
				
				// missing name?
				if (empty($custom_fonts['name'][$key])) {
					continue;
				}
				
				$name = $custom_fonts['name'][$key];
				
				$fonts[] = "@font-face { font-family: \"{$name}\"; src: url('" . esc_url($url) . "') format('woff'); }";
			}
			
			// add to beginning
			array_unshift($this->css, implode("\n", $fonts));
		}
		
		/*
		 * Include google fonts
		 */
				
		// url-encode the google fonts
		$google_fonts = array_map('urlencode', array_unique($this->google_fonts));
		
		if ($google_fonts) {
			
			if (Bunyad::options()->font_charset) {
				$charsets = implode(',', array_keys(array_filter(Bunyad::options()->font_charset)));
			}
			
			// add to beginning 
			array_unshift(
				$this->css, 
				"@import url('". (is_ssl() ? 'https' : 'http') ."://fonts.googleapis.com/css?family=" . implode('|', $google_fonts)  
					. (isset($charsets) ? '&subset=' . urlencode($charsets) : ''). "');\n\n"
			);
		}
	}
	
	/**
	 * Categories can have custom colors, backgrounds, and so on.
	 */
	public function category_css()
	{
		
		$cat_css = <<<EOF

.cat-%key%, .cat-title.cat-%key% { background: %color%; }
.cat-text-%key%, .section-head.cat-text-%key% { color: %color%; }

.navigation .menu > .menu-cat-%key%:hover > a, .navigation .menu > .menu-cat-%key%.current-menu-item > a, .navigation .menu > .menu-cat-%key%.current-menu-parent > a {
	border-bottom-color: %color%;
}

EOF;

		if (Bunyad::options()->nav_style == 'nav-light') {
			
			$cat_css .= <<<EOF
	
.nav-light .menu > .menu-cat-%key%:hover > a, 
.nav-light .menu > .menu-cat-%key%.current-menu-item > a, 
.nav-light .menu > .menu-cat-%key%.current-menu-parent > a {
	color: %color%;
}
		
EOF;
			
		}

		$cat_css = apply_filters('bunyad_css_generator_cat', $cat_css);
		$anchors = array();
		
		if (!empty($this->args['anchor_obj'])) {
			$anchors = explode(',', $this->args['anchor_obj']);
		}
		
		foreach ((array) Bunyad::options()->get_all('cat_meta_') as $key => $cat) {
			$key = intval(substr($key, strrpos($key, '_') + 1)); 
			
			// change main site color?
			if (!empty($cat['main_color']) && in_array($key, $anchors)) {

				// TODO: refactor with top parser			
				$rules = $this->elements['css_main_color']['css']['selectors'];
				foreach ($rules as $css_key => $format) {
					
					// ignore media querie selectors if responsive is disabled
					if (Bunyad::options()->no_responsive && strstr($css_key, '@media')) {
						continue;
					}
	
					// create the selector
					$the_css = str_replace("\t", '', $css_key) . ' { ' . $this->create_rules($format, $cat['main_color']); 
					
					// close media queries
					if (strstr($css_key, '@media')) {
						$the_css .= ' }';
					}
					
					$this->css[] = $the_css ." }";
				}
			}
			
			if (!empty($cat['color'])) {
				$this->css[] = str_replace(array('%key%', '%color%'), array($key, esc_attr($cat['color'])), $cat_css);		
			}
			
			// background image?
			if (!empty($cat['bg_image'])) {
				$this->css[] = 'body.boxed.category-'. esc_attr($key) .' { background: url('. esc_attr($cat['bg_image']) .') no-repeat center center fixed; '  
				 		. '-webkit-background-size: cover; -moz-background-size: cover;-o-background-size: cover; background-size: cover; }';
			}
			
		}
	}
	
	/**
	 * Convet hex to rgb
	 * 
	 * @param array $color
	 */
	public function hex2rgb($color) 
	{
		if ($color[0] == '#') {
			$color = substr($color, 1);	
		}
	
		// convert 3 to 6 char hex
		if (strlen($color) == 3) {
			$color = str_repeat($color[0], 2) . str_repeat($color[1], 2) . str_repeat($color[2], 2);
		}
	
		return array(
			'red' => hexdec($color[0] . $color[1]),
			'green' => hexdec($color[2] . $color[3]),
			'blue' => hexdec($color[4] . $color[5])
		);
	}
	
	/**
	 * Render and return the output
	 * 
	 * @see get_transient(), set_transient()
	 * @return string
	 */
	public function render()
	{

		$anchor = (!empty($this->args['anchor_obj']) ? '_' . $this->args['anchor_obj'] : 0);
		
		// have data in cache?
		$cache = get_transient('bunyad_custom_css_cache');
		if (is_array($cache) && !empty($cache[$anchor])) {
			return $cache[$anchor];
		}
		
		$this->process();
		
		$output = implode("\n", $this->css) . "\n\n" . 
			(!empty($this->css_options['css_custom']) ? wp_specialchars_decode($this->css_options['css_custom']) : '');
		
		// cache it
		set_transient('bunyad_custom_css_cache', array($anchor => $output));

		return $output;
	}
	
}