<?php
/*
Plugin Name: Bunyad Shortcodes
Plugin URI: http://theme-sphere.com
Description: Bunyad Shortcodes adds multiple shortcode functionality for ThemeSphere themes. 
Version: 1.0.7
Author: ThemeSphere
Author URI: http://theme-sphere.com
License: GPL2
*/

$bunyad_sc = Bunyad_ShortCodes::getInstance();
add_action('after_setup_theme', array($bunyad_sc, 'setup'));

/**
 * Shortcode handlers
 */
class Bunyad_ShortCodes
{
	protected static $instance;
	protected $_conf = array();
	protected $_open = array();
	
	public $counter = 0; // number of shortcodes processed - not implemented
	public $temp = array();
	public $shortcodes = array();
	public $blocks = array(); // shortcodes to be handled with a file
	

	public function setup()
	{
		// bunyad framework available? optional but good extras
		if (class_exists('Bunyad')) {
			Bunyad::options()->set('shortcodes', $this);
		}
		else {
			
			// bunyad framework themes implement it in main css for both speed and maintainability
			wp_enqueue_style('bunyad-shortcodes', plugin_dir_url(__FILE__) . 'css/shortcodes.css');
			
			// include framework dependencies
			require_once plugin_dir_path(__FILE__) . '/lib/bunyad.php';
		}
		
		// add extra shortcodes that can be enabled or disabled
		$latest_gallery = locate_template('blocks/latest-gallery.php');
				
		$this->add_blocks(apply_filters('bunyad_shortcode_default_blocks', array(
			'latest_gallery' => array('render' => (!empty($latest_gallery) ? $latest_gallery : 'latest-gallery'), 'attribs' => array(
				'format' => 'gallery', 'number' => 5, 'title' => '', 'cat' => '', 'type' => '', 'tax_tag' => '', 'offset' => '', 'post_type' => ''
			)), 
		)));
		
		/*
		 * Admin area?
		 */
		if (is_admin()) { 
			require_once plugin_dir_path(__FILE__)  . 'admin.php';
			$sc = new Bunyad_Admin_ShortCodes;	
		}
		
		// add cleanup
		add_filter('the_content', array($this, 'cleanup'));
		add_filter('shortcode_cleanup', array($this, 'cleanup'));
		
		// init / register shortcodes
		add_action('init', array($this, 'register_all'), 50);
		
		// i18n
		load_plugin_textdomain('bunyad-shortcodes', false, basename(dirname(__FILE__)) . '/languages');
	}
	
	public function __construct()
	{
		// TODO: implement attributes
		$this->shortcodes = array(
		
			'Typography' => array(
				'pullquote'   => array('label' => 'Pull Quote', 'callback' => 'pull_quote'),
				'dropcap' => array('label' => 'Dropcap', 'callback' => 'dropcap'),
			),

			'default' => array(
				'tabs'  => array('label' => 'Tabs', 'callback' => 'tabs', 'dialog' => 'tabs', 'child' => 'tab'),
				   'tab'   => array('callback' => 'tab'),
				
				'columns' => array('label' => 'Columns', 'callback' => 'columns', 'dialog' => 'columns', 'child' => 'column'),
				   'column'  => array('callback' => 'column'),
				
				'button'  => array('label' => 'Button', 'callback' => 'button', 'dialog' => 'button'),
			
				'accordions' => array('label' => 'Accordions', 'callback' => 'accordions', 'dialog' => 'accordions', 'child' => 'accordion'),
					'accordion'  => array('callback' => 'accordion_pane'),
			
				'toggle'  => array('label' => 'Toggle Box', 'callback' => 'toggle', 'dialog' => 'toggles'),
			
				// custom lists
				'list'    => array('label' => 'Custom List', 'callback' => 'list', 'dialog' => 'lists', 'child' => 'li'),
					'li' => array('callback' => 'list_item'),
			
				'box'     => array('label' => 'Info Box', 'callback' => 'box', 'dialog' => 'box'),
			
				// non-printables / no GUI dialog boxes
				'social' => array('callback' => 'social_icons', 'label' => 'Social Icons', 'dialog' => 'social-icons', 'child' => 'social_icon'),
					'social_icon' => array('callback' => 'social_icon'),
				
				'separator' => array('callback' => 'separator'),
				'feedburner' => array('callback' => 'feedburner_form'),
			)
		);
	}

	/**
	 * Register shortcodes with WordPress Shortcodes API
	 */
	public function register_all() 
	{
		// add shortcodes
		$this->shortcodes = apply_filters('bunyad_shortcodes_list', $this->shortcodes); 
		foreach ($this->shortcodes as $value) 
		{
			foreach ($value as $key => $shortcode) {
				add_shortcode($key, array($this, 'sc_' . $shortcode['callback']));
			}
		}
		
		// preserve dropcap
		add_filter('bunyad_excerpt_pre_strip_shortcodes', array($this, 'preserve_dropcap'));
	}
	
	public function get_all() 
	{
		return $this->shortcodes;
	}
	
	public function get_one($id)
	{
		// add shortcodes
		foreach ($this->shortcodes as $value) 
		{
			foreach ($value as $key => $shortcode) {
				if ($key === $id) {
					return $shortcode;
				}
			}
		}
	}

	/**
	 * Clean up shortcodes wrapped in incorrect tags
	 * 
	 * @param string $content
	 */
	public function cleanup($content)
	{
		// remove p wrapping around shortcodes and remove any br right after a shortcode
		$content = preg_replace("#(<p>)?\[([a-z_]+)(\s[^\]]+)?\]\s*(</p>|<br />)?#","[\\2\\3]",$content);
		$content = preg_replace("#(<p>)?\[\/([a-z_]+)\](<\/p>|<br \/>)?#","[/\\2]", $content);
		
		return $content;
	}
	
	/**
	 * Add a special kind of shortcode that's handled by an included php file
	 * 
	 * Note: Use this only for including big chunks of HTML using shortcode system
	 * 
	 * @param array $shortcodes
	 */
	public function add_blocks($shortcodes)
	{		
		$this->blocks = array_merge($this->blocks, $shortcodes);
		
		foreach ($shortcodes as $tag => $shortcode) {
			add_shortcode($tag, array($this, 'sc_block_' . $tag));
		}
		
		return $this;
	}
	
	/**
	 * Magic handler for blocks
	 */
	public function __call($name, $args = array())
	{		
		if (!stristr($name, 'sc_block_')) {
			return false;
		}
		
		$tag   = str_replace('sc_block_', '', $name);
		$block = $this->blocks[$tag];
		
		// get attributes and content from the args array
		$atts    = $args[0]; // intentionally supporting non-listed atts
		$content = $args[1];
		
		// extract attributes
		if (isset($block['attribs']) && is_array($block['attribs'])) {
			$atts = shortcode_atts($block['attribs'], $atts);
		}

		extract($atts, EXTR_SKIP);
		
		/*
		 * String replacement
		 */
		if (isset($block['template'])) {
			
			$atts['content'] = $content;
			
			foreach ($atts as $key => $val) {
				$block['template'] = str_replace('%' . $key . '%', $val, $block['template']);
			}
			
			return do_shortcode($block['template']);
		}
		
		
		/*
		 * File based
		 */
		
		// include block file from within plugin if no path is specified
		if (!isset($block['render']) OR !strstr($block['render'], '/')) {
			$block_file = plugin_dir_path(__FILE__) . 'blocks/' . sanitize_file_name(str_replace('_', '-', $block['render'])) . '.php';	
		} 
		else {
			$block_file = $block['render'];
		}
		
		// no file
		if (!is_array($block) OR !file_exists($block_file)) {
			return false;
		}
		
		// save the current block in registry
		if (class_exists('Bunyad_Registry')) {
			Bunyad::registry()
				->set('block', $block)
				->set('block_atts', $atts);
		}
		
		// get file content
		ob_start();
		
		include apply_filters('bunyad_block_file', $block_file, $block);
		
		$block_content = ob_get_clean();
		
		return do_shortcode($block_content);
	}
	

	/**
	 * Shortcode: Pull Quote
	 */
	public function sc_pull_quote($atts, $content = null) {
		extract(shortcode_atts(array('style' => 1), $atts), EXTR_SKIP);

		return '<aside class="pullquote alignright">' . do_shortcode($content) . '</aside>';
	}
	
	/**
	 * Shortcode: Drop Cap
	 */
	public function sc_dropcap($atts, $content = null) {
		extract(shortcode_atts(array('style' => 1), $atts), EXTR_SKIP);

		$class = 'drop-caps';
		
		if ($style === 'square') {
			$class .= ' square';
		}
		
		return '<span class="'. $class .'">' . do_shortcode($content) . '</span>';
	}
	
	/**
	 * Filter callback: Preserve dropcaps in excerpts
	 */
	public function preserve_dropcap($content) 
	{
		return preg_replace('#(?:\[/?)dropcap[^\]]*/?\]#is', '', $content); 
	}
	
	/**
	 * Shortcode: Separator
	 */
	public function sc_separator($atts, $content = null) {
		extract(shortcode_atts(array('class' => '', 'type' => 'line'), $atts), EXTR_SKIP);
		
		$classes = array('separator');

		switch ($type) {
			case 'half-line':
				$classes[] = 'half';
				break;
				
			case 'half-space':
				$classes = array_merge($classes, array('half', 'no-line'));
				break;
			
			case 'space':
				$classes[] = 'no-line';
				break;
				
			default:
				break;
		}
		
		$class .= implode(' ', $classes);		
		
		return '<hr class="'. esc_attr($class) .'" />';
	}
	
	
	/**
	 * Shortcode: List
	 */
	public function sc_list($atts, $content = null)
	{
		extract(shortcode_atts(array('style' => '', 'ordered' => false, 'type' => ''), $atts), EXTR_SKIP);
		
		$class = (!empty($style) ? ' sc-list-' . $style : '');
		
		if ($this->get_config('font_icons') && empty($type)) {
			$class .= ' fa-ul';
		}
		
		$this->_open['list_style'] = $style;

		// process shortcodes
		$content = do_shortcode($content);
		$content = preg_replace('#^<\/p>|^<br \/>|<div>|<\/div>|<p>$#', '', $content);
		
		// no list?
		if (!preg_match('#<(ul|ol)[^<]*>#i', $content)) {
			
			// ordered?
			$tag = ($ordered ? 'ol' : 'ul');
			
			$content = "<{$tag} class='". esc_attr($type) ."'>"  . $content . "</{$tag}>";
		}
		
		return '<div class="sc-list'. esc_attr($class) .'">'. $content .'</div>';
	}
	
	/**
	 * Shortcode Child: List 
	 */
	public function sc_list_item($atts, $content = null)
	{
		extract(shortcode_atts(array('style' => ''), $atts), EXTR_SKIP);
		
		// inherit style from ul
		if (!$style && $this->_open['list_style']) {
			$style = $this->_open['list_style'];
		}
		
		// use an icon if style doesn't contain type - which would denote a special case
		$icon = '';
		if ($this->get_config('font_icons') && !empty($style)) {
			$icon = '<i class="fa fa-'. esc_attr($style).'"></i>';
		}
		
		return '<li>' . $icon . do_shortcode($content) .'</li>';	
	}
	
	/**
	 * Shortcode: Social Icons
	 */
	public function sc_social_icons($atts, $content = null)
	{
		extract(shortcode_atts(array('align' => '', 'type' => '', 'backgrounds' => ''), $atts), EXTR_SKIP);
	
		
		// add style such as align
		$style = '';
		if (in_array($align, array('left', 'none', 'right'))) {
			$style = ' style="float: ' . esc_attr($align) . '"';
		}
		
		// has type? add as class
		$class = '';
		if ($type) {
			$class = ' ' . $type;
		}
		
		// enable preset background colors?
		if ($backgrounds) {
			$class .= ' box-bg';
		}
		
		return '<ul class="social-icons cf'. esc_attr($class) .'"'. $style  .'>'. do_shortcode($content) .'</ul>';
	}
	
	/**
	 * Shortcode: Social Icon
	 */
	public function sc_social_icon($atts, $content = null)
	{
		extract(
			shortcode_atts(array('link' => '', 'title' => '', 'type' => '', 'bg' => '', 'color' => '', 'size' => ''), $atts), 
			EXTR_SKIP
		);
		
		// invalid type?
		if (!$type) {
			return '';
		}
		
		// using fontawesome?
		if ($this->get_config('social_font')) 
		{
			// custom background?
			$style = array();
			if ($bg) {
				$style[] = 'background-color: ' . esc_attr($bg);
			}
			
			// color?
			if ($color) {
				$style[] = 'color: '. esc_attr($color);
			}
			
			// add inline style
			$style = (count($style) ? ' style="' . implode('; ', $style) .'"' : '');
			
			return '<li><a href="'. esc_attr($link) .'" class="icon fa fa-'. esc_attr($type) .'" title="' . esc_attr($title) .'"'. $style . '>' 
				 . '<span class="visuallyhidden">' . esc_html($title) . '</span></a></li>';
		}

		// using normal image
		return '<li><a href="'. esc_attr($link) .'" class="ir icon '. esc_attr($type) .'" title="' . esc_attr($title) .'">' . esc_html($title) . '</a></li>'; 
	}
	
	/**
	 * Shortcode: Box
	 */
	public function sc_box($atts, $content = null)
	{
		extract(shortcode_atts(array(
			'style'  => 'info',
			'color'  => '',
			'border' => '',
		), $atts), EXTR_SKIP);
		
		$css = array();
		$extra_attribs = '';
		
		if ($color) {
			$css[] = 'background: ' . esc_attr($color);
		}
		
		if ($border) {
			$css[] = 'border-color: ' . esc_attr($border);
		}
		
		// add css
		if ($css) {
			$extra_attribs = ' style="' . implode(';', $css) . '"';
		}
		
		return '<div class="sc-box sc-box-' . esc_attr($style) .'"'. $extra_attribs .'>'. wpautop($content) .'</div>';
	}
	
	/**
	 * Shortcode: Accordion
	 */
	public function sc_accordions($atts, $content = null)
	{
		$this->temp['accordion_panes'] = array();
		do_shortcode($content);
		
		$output = '<dl class="sc-accordions">';
		
		// get our panes
		$count = 0;
		foreach ($this->temp['accordion_panes'] as $pane) {
			$count++;

			$active = ($pane['load'] == 'show' ? ' active' : '');
			
			$output .= '<dt class="sc-accordion-title' . $active . '"><a href="#">'. $pane['title'] .'</a></dt>'
					.  '<dd class="sc-accordion-pane' . $active . '">' . $pane['content'] . "</dd>\n";  
		}

		unset($this->temp['accordion_panes']);
		
		return $output . '</dl>';
	}
	
	/**
	 * Helper: Accordion
	 */
	public function sc_accordion_pane($atts, $content = null)
	{
		extract(shortcode_atts(array('title' => '', 'load' => 'hide'), $atts), EXTR_SKIP);
		$this->temp['accordion_panes'][] = array('title' => $title, 'load' => $load, 'content' => do_shortcode($content));
	}
	
	/**
	 * Shortcode: Toggle
	 */
	public function sc_toggle($atts, $content = null)
	{
		extract(shortcode_atts(array('title' => '', 'load' => 'hide'), $atts), EXTR_SKIP);
		
		 $active = ($load == 'show' ? ' active' : '');
		
		return '<div class="sc-toggle"><div class="sc-toggle-title' . $active . '"><a href="#">' . $title . '</a></div>'
			.  '<div class="sc-toggle-content' . $active . '">' . do_shortcode($content) . "</div></div>\n";
	}
	
	/**
	 * Shortcode: Button
	 */
	public function sc_button($atts, $content = null)
	{
		extract(shortcode_atts(array(
			'color' => 'default', // for default hover styling
			'link' => '#',
			'border' => '',
			'text_color' => '',
			'size' => '',
			'target' => ''
		), $atts));
		
		// deafult classes
		$classes = array('sc-button');
		
		// get supported colors by this theme
		$styled_colors = $this->get_config('button_colors');

		// custom color? - not defined in theme supported colors?
		$extra_attribs = '';
		if (!in_array($color, $styled_colors)) {
			$css[] = 'background: ' . esc_attr($color);
			
			if ($border) {
				$css[] = 'border-color: ' . esc_attr($border);
			}
			
			if ($text_color) {
				$css[] = 'color: ' . esc_attr($text_color);
			}

			$extra_attribs = ' style="' . implode(';', $css) . '"';
		}
		else {
			$classes[] = 'sc-button-' . $color;
		}
		
		// size?
		if ($size) {
			$classes[] = 'sc-button-' . $size;
		}
		
		// new window?
		if ($target == 'new') {
			$extra_attribs .= ' target="_blank"';
		}
		
		// don't run inner shortcodes here
		return '<a href="' . esc_attr($link) . '" class="' . esc_attr(implode(' ', $classes)) . '"' . $extra_attribs .'><span>' . shortcode_unautop($content) . '</span></a>';
	}
	
	/**
	 * Shortcode: Columns
	 */
	public function sc_columns($atts, $content = null)
	{
	
		extract(shortcode_atts(array('class' => ''), $atts));
		
		$classes = $this->get_config('row_classes', array('row', 'cf'));
		
		if ($class) {
			$classes = array_merge($classes, explode(' ', $class));
		}
		
		$output  = '<div class="'. implode(' ', $classes) .'">';
		
		$this->temp['columns'] = array();

		// parse inner shortcodes 
		do_shortcode($content);
		
		// strip <br /> tags somehow
		
		foreach ($this->temp['columns'] as $column) {
			$output .= $column;
		}
		
		unset($this->temp['columns']);
		
		return $output .'</div>';
	}
	
	/**
	 * Helper: Column
	 */
	public function sc_column($atts, $content = null)
	{
		extract(shortcode_atts(array('size' => '1/1', 'class' => '', 'text_align' => ''), $atts), EXTR_SKIP);
		
		$classes = array('column');
		
		if ($class) {
			$classes = array_merge($classes, explode(' ', $class));
		}
		
		// check n/n ratio
		if (stristr($size, '/')) 
		{
			$nth   = explode('/', $size);
			$ratio = $nth[0] / $nth[1];
			
			unset($size);
			
			if ($ratio == 0.5) {
				$size = 'half';
			}
			else {
				$size = str_replace(array(1, 2, 3, 4, 5), array('one', 'two', 'three', 'four', 'five'), $nth[0]) .'-'
					  . str_replace(array(3, 4, 5), array('third', 'fourth', 'fifth'), $nth[1]);
			}
		}

		// add to classes
		array_push($classes, $size);
		
		// add style such as text-align
		$style = '';
		if (in_array($text_align, array('left', 'center', 'right'))) {
			//$style = ' style="text-align: ' . esc_attr($text_align) . '"';
			array_push($classes, esc_attr(strip_tags($text_align)));
		}
		
		// strip off 
		//$content = preg_replace('#(^<br />|<br />$)#', '', $content);
		$this->temp['columns'][] = $column = '<div class="'. implode(' ', $classes) .'"'. $style . '>' . do_shortcode($content) . '</div>'; 

		return $column;
	}
	
	/**
	 * Shortcode: Tabs
	 */
	public function sc_tabs($atts, $content = null)
	{
		extract(shortcode_atts(array('type' => ''), $atts));

		$this->temp['tab_count'] = 0;

		// nesting - parser will store tabs inside in memory - $this->temp
		do_shortcode($content);

		if (is_array($this->temp['tabs'])) 
		{
			$count = 0;
			foreach ($this->temp['tabs'] as $tab) 
			{
				$count++;
				
				$active = ($count == 1 ? ' class="active"' : '');
				
				$tabs[]  = '<li'. $active .'><a href="#" data-id="'. $count .'">' . $tab['title'] . '</a></li>';
				$panes[] = '<li id="sc-pane-'. $count .'"'. $active .'>'. $tab['content'] . '</li>';
			}
			
			// vertical tabs?
			$class = ($type == 'vertical' ? ' vertical' : '');
			
			$return = '<div class="sc-tabs-wrap'. $class .'"><ul class="sc-tabs">' . implode('', $tabs) . '</ul><ul class="sc-tabs-panes">' . implode("\n", $panes) . '</ul></div>';
		}

		unset($this->temp['tabs'], $this->temp['tab_count']);

		return $return;
	}
	
	/**
	 * Helper: Tab - part of tabs
	 */
	public function sc_tab($atts, $content = null)
	{
		extract(shortcode_atts(array('title' => 'Tab %d'), $atts), EXTR_SKIP);
		
		$this->temp['tabs'][$this->temp['tab_count']] = array('title' => sprintf($title, $this->temp['tab_count']), 'content' => do_shortcode($content));
		$this->temp['tab_count']++;
	}
	
	/**
	 * Shortcode: Subscription Form
	 */
	public function sc_feedburner_form($atts, $content = null)
	{
		extract(shortcode_atts(array('heading' => '', 'label' => '', 'button_text' => '', 'user' => ''), $atts), EXTR_SKIP);
		
		return '
		<div class="feedburner">
			<p class="heading">'. esc_html($heading) .'</p>
			<form method="post" action="http://feedburner.google.com/fb/a/mailverify">
			
				<input type="hidden" value="'. esc_attr($user) .'" name="uri" />
				<input type="hidden" name="loc" value="en_US" />
			
				<label for="feedburner-email">' . esc_html($label) .'</label>
				<input type="text" id="feedburner-email" name="email" class="feedburner-email" placeholder="'. esc_attr($label) .'" />
				
				<input class="feedburner-subscribe" type="submit" name="submit" value="'. esc_attr($button_text) .'" />
				
			</form>
		</div>
		';
	}
	
	/**
	 * Add suffix to the CSS selector
	 * 
	 * @param  $selector
	 */
	public function css_suffix($selector) {
		return $selector . '-' . $this->counter;
	}
	
	/**
	 * Set configuration relating to shortcodes
	 * 
	 * @param string|array $key
	 * @param mixed $value
	 */
	public function set_config($key, $value = null) 
	{
		if (is_array($key)) {
			$this->_conf = array_merge($this->_conf, $key);
		}
		else {
			$this->_conf[$key] = $value;
		}
		
		return $this;
	}
	
	/**
	 * Get Configuration
	 * 
	 * @param string $key 
	 * @param mixed $default  default value to return
	 */
	public function get_config($key, $default = null)
	{
		if (isset($this->_conf[$key])) {
			return $this->_conf[$key];
		}
		
		return $default;
	}
	
	/**
	 * Singleton instance
	 */
	public static function getInstance() 
	{
		if (!isset(self::$instance)) {
			self::$instance = new self;
		}
		
		return self::$instance;
	}
	
	/**
	 * Override? Convert this class to a proxy
	 * 
	 * @param  $object   an object to set as the shortcode object
	 * @todo   Switch to Bunyad_Registry for dependencies
	 */
	public static function set_proxy($object)
	{
		self::$instance = $object;
	}
}