<?php

/**
 * Theme Options handler on admin side
 */
class Bunyad_Admin_Options
{
	public $option_key;
	public $options;
	
	public function __construct()
	{
		$this->option_key = Bunyad::options()->get_config('theme_prefix') .'_theme_options';

		// setup menu on init
		add_action('admin_menu', array($this, 'init'));
		
		// check theme version info
		add_action('admin_init', array($this, 'check_version'));
		
		// allow fonts upload for woff
		add_filter('upload_mimes', array($this, 'allow_fonts_upload'));
	}
	
	/**
	 * Register a single option using Setting API and use sanitization hook
	 */
	public function init()
	{
		// current user can edit?
		if (!current_user_can('edit_theme_options')) {
			return;
		}
		
		// create link
		$title = __('Theme Settings', 'bunyad-admin');
		$page  = add_theme_page($title, $title, 'edit_theme_options', 'bunyad-admin-options', array($this, 'render_options'));
		//add_object_page($title, $title, 'edit_theme_options', 'bunyad-options', array($this, 'render_page'));
		
		add_action('admin_print_styles-' . $page, array($this, 'add_assets'));
	}
	
	/**
	 * Check current theme version and run an update hook if necessary
	 */
	public function check_version()
	{
		$stored_version = Bunyad::options()->theme_version;
		
		// update version if necessary
		if ($stored_version != Bunyad::options()->get_config('theme_version')) {
			
			// fire up the hook
			do_action('bunyad_theme_version_change');
			
			// update the theme version
			Bunyad::options()->set('theme_version', Bunyad::options()->get_config('theme_version'));
				
			if ($stored_version) {
				Bunyad::options()->set('theme_version_previous', $stored_version);
			}
			
			// updated changes in database
			Bunyad::options()->update();
		}
	}
	
	/**
	 * Filter callback: Add woff to existing mime types
	 * 
	 * @param array $mimes
	 */
	public function allow_fonts_upload($mimes)
	{
		$mimes['woff'] = 'application/font-woff';
		
		return $mimes;
	}
	
	/**
	 * Render page using admin/options.php setting and showing template-options.php
	 */
	public function render_options()
	{
		
		// backup?
		if (!empty($_GET['backup'])) {
			$this->export_options();
		}
		
		// get options array and initialize renderer
		$this->options = include get_template_directory() . '/admin/options.php';
		
		// update settings; check security
		if ($_POST && check_admin_referer($this->option_key . '_save')) {
			
			// delete css cache
			delete_transient('bunyad_custom_css_cache');
			
			if (!empty($_POST['update'])) {
				return $this->save_options();
			}
	
			// delete all settings
			if (!empty($_POST['delete'])) {
				return $this->delete_options();
			}
		
			// delete color settings
			if (!empty($_POST['reset-colors'])) {
				return $this->delete_options('colors');
			}
		}

		$this->_render_form();
	}

	/**
	 * Render our options form
	 * @param array $populate  an array of default values to use for elements
	 */
	public function _render_form($populate = null, $data = array())
	{
		$renderer = Bunyad::factory('admin/option-renderer'); /* @var $renderer Bunyad_Admin_OptionRenderer */
		
		$renderer->template(
			$this->options, 
			locate_template('/admin/template-options.php'),
			array_merge(Bunyad::options()->get_all(), (array) $populate), // get database values and override with provided $populate (usually $_POST)
			array_merge($data, array('option_key' => $this->option_key))
		);
	}
	
	/**
	 * Enqueue required assets
	 */
	public function add_assets()
	{
		wp_enqueue_style('jquery-options', get_template_directory_uri() . '/admin/css/chosen.jquery.css');
		wp_enqueue_script('jquery-chosen', get_template_directory_uri() . '/admin/js/chosen.jquery.js', array('jquery'));
		
		wp_enqueue_style('theme-options', get_template_directory_uri() . '/admin/css/options.css');
		wp_enqueue_script('theme-options', get_template_directory_uri() . '/admin/js/options.js', array('jquery'));
		
		// @todo: make optional
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');
		
		wp_enqueue_style('farbtastic');
		wp_enqueue_script('farbtastic');
		
		// add media scripts
		wp_enqueue_media(); 
		
	}
	
	/**
	 * Extract elements/fields from the options hierarchy
	 * 
	 * @param array $options
	 * @param boolean $sub  add partial sub-elements in the list
	 * @param string  $tab_id  filter using a tab id
	 */
	public function get_elements_from_tree(array $options, $sub = false, $tab_id = null)
	{
		$elements = array();
		
		foreach ($options as $tab) {
			
			if ($tab_id != null && $tab['id'] != $tab_id) {
				continue;
			}
			
			foreach ($tab['sections'] as $section) 
			{
				foreach ($section['fields'] as $element) 
				{
					// pseudo element?
					if (empty($element['name'])) {
						continue;
					}
					
					$elements[$element['name']] = $element;
					
					// special treatment for typography section - it has sub-options
					if ($sub === true && $element['type'] == 'typography') {
						
						if (!empty($element['color'])) {
							// over-write 'value' key from the one in color - to set proper default
							$elements[$element['name'] . '_color'] = array_merge($element, $element['color']);
						}
						
						if (!empty($element['size'])) {
							$elements[$element['name'] . '_size'] = array_merge($element, $element['size']);
						}
					}
					
					// special treatment for typography section - it has sub-options
					if ($sub === true && $element['type'] == 'upload') {
						
						if (!empty($element['bg_type'])) {
							// over-write 'value' key from the one in color - to set proper default
							$elements[$element['name'] . '_bg_type'] = array_merge($element, $element['bg_type']);
						}
					}
					
				} // end fields loop
				
			} // end sections
		}
		
		return $elements;
	}
	
	/**
	 * Delete / reset options - security checks done outside
	 */
	public function delete_options($type = null)
	{
		// get options object
		$options_obj = Bunyad::options();
		
		if ($type == 'colors') 
		{
			$elements = $this->get_elements_from_tree($this->options, true, 'options-style-color');
			
			// preserve this
			unset($elements['predefined_style']);
		}
		else 
		{
			$elements = $this->get_elements_from_tree($this->options, true);
		}
		
		// loop through all elements and reset them
		foreach ($elements as $key => $element) {
			$options_obj->set($key, null); // unset / reset
		}

		// save in database
		$options_obj->update();
		
		// render populated form
		$this->_render_form(null, array('options_deleted' => true));
	}
	
	/**
	 * Save options submitted via normal post - security checks done outside
	 *
	 */
	public function save_options()
	{
		// importing options
		if (isset($_FILES['import_backup']) && is_uploaded_file($_FILES['import_backup']['tmp_name'])) {
			return $this->import_options();
		}
				
		$options  = array();
		$elements = $this->get_elements_from_tree($this->options, true);
		
		$options_obj = Bunyad::options();
		
		$options_saved = false;
		
		// remove magic quote slashes
		$_POST = stripslashes_deep($_POST);
		
		foreach ($elements as $key => $element) 
		{
			// hidden element not in form? set default
			if (!isset($_POST[$key])) {
				$options[$key] = $element['value'];
				continue;
			}
			
			// data available? save only if not the same as default
			$value = $_POST[$key];
			
			if (isset($value) && (!isset($element['value']) OR $element['value'] != $value)) {
				$options[$key] = $_POST[$key]; 
			}
			// unset this option - default? n/a
			else {
				$options_obj->set($key, null);
			}
		}
		
		$result = $this->_validate_sanitize_options($options, $elements);

		if ($result['errors']) {
			$form_errors = $result['errors'];
		}
		else {
			
			$options = apply_filters('bunyad_options_presave', $result['options']); // sanitized
						
			// update elements
			foreach ($options as $key => $value) {
				$options_obj->set($key, $value);
			}

			// save in database
			$options_obj->update();
			
			$options_saved = true;
			
			// flush widgets cache
			do_action('bunyad_widget_flush_cache');
		}
		
		// render populated form
		$this->_render_form($_POST, compact('options_saved', 'form_errors'));
	}
	
	/**
	 * Download / export options
	 */
	public function export_options()
	{
		$options = json_encode(Bunyad::options()->get_all());
		
		// send file
		header('Content-type: application/json');
		header('Content-Disposition: attachment; filename="options_panel_backup_'. time() .'.json"');
			
		die($options);
	}
	
	/**
	 * Import options from a backup file
	 */
	public function import_options()
	{
		$file = wp_handle_upload($_FILES['import_backup'], array('test_form' => false, 'test_type' => false));
		$form_data = $_POST;
		
		$options_saved = false;
		
		// no errors?
		if (!empty($file['error'])) {
			$form_errors = array($file['error']);
		}
		else {
			
			// import data
			$data = file_get_contents($file['file']);
			$data = json_decode($data, true);
			
			if (is_array($data)) {
				
				// update options
				Bunyad::options()->set_all($data)->update();
				
				$form_data = $data;
				$options_saved = true;
			}
			else {
				$form_errors = array(__('Could not import options. Are you sure the uploaded file was a valid backup file?',  'bunyad-admin'));
			}
			
			// remove temporary backup file
			@unlink($file['file']);
		}
		
		// render form
		return $this->_render_form($form_data, compact('options_saved', 'form_errors'));
	}
	
	/**
	 * Validate and sanitize options
	 * 
	 * @param array $options  array of options key => value pairs
	 * @param array $elements array with elements and their configuration
	 * @return array errors and options keys
	 */
	public function _validate_sanitize_options($options, $elements)
	{
		$errors = array();
		
		foreach ($options as $key => $value)
		{ 
			$element  = $elements[$key];
			switch ($element['type'])
			{
				case 'checkbox':					
				case 'radio':
				case 'select':
					
					// checkboxes support 0 or 1
					if ($element['type'] == 'checkbox') {
						$element['options'] = array(0 => '', 1 => '');
						
						if (is_array($value)) {
							
							//$options[$key] = array_filter($value);
							// keep 0 values for all keys too - to check for defaults - DON'T remove empty
							$options[$key] = $value; 

							break; 
						}
						
					}
					
					// other elements need options
					if (!array_key_exists($value, (array) $element['options'])) {
						$errors[] = esc_html(sprintf('"%s" is not a valid option for element %s (%s)', $value, $element['label'], $key));
					}
					
					break;
					
				case 'text':
				case 'textarea':
				case 'color':
					
					// by default, support html sanitization
					if (empty($element['strip']) OR $element['strip'] == 'bad_html') {
						// sanitize
						$options[$key] = wp_kses_post($value);
					}
					// text only
					else if ($element['strip'] == 'all_html') {
						$options[$key] = strip_tags($value);
					}
					
					break;
					
				case 'multiple':
					
					$options[$key] = array_filter($value);
					
					/** 
					 * If filtering the array recursively returns in an empty array, unset value.
					 * 
					 * Note: DON'T filter the main array as key => value associations will be affected.
					 */
					if (!$this->array_filter_recursive($value)) {
						$options[$key] = '';
					}
					
					break;
			}
			
		}
		
		return compact('errors', 'options');
	}
	
	
	/**
	 * Helper function to filter empty entries from array recursively
	 * 
	 * @todo move to utilities
	 * @see array_filter
	 */
	public function array_filter_recursive($input) 
	{ 
		foreach ($input as &$value) 
		{ 
			if (is_array($value)) { 
				$value = $this->array_filter_recursive($value); 
			} 
		}
		
		return array_filter($input);
	}
	
}