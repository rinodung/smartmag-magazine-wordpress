<?php
class Bunyad_Menu_Edit_Walker extends Walker_Nav_Menu_Edit 
{
	public $locations = array();
	public $current_menu;
	
	public function __construct() 
	{
		$this->locations = array_flip( (array) get_nav_menu_locations());
	}
	
	public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) 
	{
		// get current menu id
		if (!$this->current_menu) {
			$menu = wp_get_post_terms($item->ID, 'nav_menu');
			
			if (!empty($menu[0])) {
				$this->current_menu = $menu[0]->term_id;
			}
			
			if (!$this->current_menu && $_REQUEST['menu']) {
				$this->current_menu = $_REQUEST['menu'];
			}
		}
		
		$item_output = '';
				
		parent::start_el($item_output, $item, $depth, $args, $id);
		
		// add new fields before <div class="menu-item-actions description-wide submitbox">
		$fields = $this->get_custom_fields($item, $depth);
		
		// nav menu hook support decided by the community of plugin authors
		ob_start();
		do_action('wp_nav_menu_item_custom_fields', $item->ID, $item, $depth, $args);
		$fields .= ob_get_clean();
		 
		if ($fields) {
			$item_output = preg_replace('/(?=<div[^>]+class="[^"]*submitbox)/', $fields, $item_output);
		}
		
		$output .= $item_output;
	}
	
	public function get_custom_fields($item, $depth = 0)
	{
		$fields = apply_filters('bunyad_custom_menu_fields', array());
		$output = '';
		
		foreach ($fields as $key => $field)
		{
			
			// parent menu field only?
			if (!empty($field['parent_only']) && $depth > 0) {
				continue;
			}
			
			// only applies to a specific location?
			if (!empty($field['locations']) && !empty($this->locations[ $this->current_menu ]) && !in_array($this->locations[ $this->current_menu ], $field['locations'])) {
				continue;
			}
			
			// relevant field values
			$name = 'menu-item-' . esc_attr($key) . '[' . $item->ID . ']';
			$value = esc_attr($item->{$key});
			
			// use renderer or a template?
			if (is_array($field['element'])) 
			{
				$renderer = Bunyad::factory('admin/option-renderer'); /* @var $renderer Bunyad_Admin_OptionRenderer */
				
				if ($field['element']['type'] == 'select') {
					$template = $renderer->render_select(array_merge(array('name' => $name, 'value' => $value), $field['element']));
				}
			}
			else {
				// string template
				$template = str_replace(array('%id%', '%name%', '%value%'), array($item->ID, $name, $value), $field['element']);
			}
					
			$output .= '
			<p class="field-custom description description-wide">
				<label for="edit-menu-item-subtitle-' . esc_attr($item->ID) . '">
					' . $field['label'] . '<br />' . $template  . '
				</label>
			</p>';
		}
		
		return $output;
	}
}
