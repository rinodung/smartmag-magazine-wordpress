<?php

class Bunyad_Menu_Walker extends Walker_Nav_Menu
{
	public $in_mega_menu = false;
	public $current_item;
	
	/**
	 * Stores mega menu inner-data
	 */
	public $last_lvl;
	
	public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) 
	{
		if ($item->object == 'category') {
			$item->classes = array_merge((array) $item->classes, array('menu-cat-' . $item->object_id)); 
		}
		
		parent::start_el($item_output, $item, $depth, $args, $id);

		//echo $item_output .' ------- <br /><br />' . "\n\n\n";
		if ($depth == 0) {
			$this->in_mega_menu = false;
			$this->current_item = null;
		}
		
		// is a mega menu parent?
		if ($item->mega_menu) {
			$this->in_mega_menu = true;
			$this->current_item = $item;		
		}
		
		// in mega menu
		if ($this->in_mega_menu && $depth > 0) {
			$this->last_lvl .= $item_output;
			return;
		}
		
		$output .= $item_output;
	}
	
	function end_el(&$output, $item, $depth = 0, $args = array()) 
	{	
		$item_output = '';
		parent::end_el($item_output, $item, $depth, $args);
			
		if ($this->in_mega_menu && $depth > 0) {
			$this->last_lvl .= $item_output;
			return;
		}
				
		$output .= $item_output;
	}
	
	
	public function start_lvl(&$output, $depth = 0, $args = array()) 
	{
		$item_output = '';
		parent::start_lvl($item_output, $depth, $args);
			
		if ($this->in_mega_menu) {
			
			// mega-menu item level greater than 2 - start a default WordPress start_lvl
			if ($depth >= 1) {
				$this->last_lvl .= $item_output;
			}
			
			return;
		}
		
		
		$output .= $item_output;
	}
	
	public function end_lvl(&$output, $depth = 0, $args = array())
	{	
		$item_output = '';
		parent::end_lvl($item_output, $depth, $args);
		
		if ($this->in_mega_menu) 
		{
			// end of mega-menu parent - at top-level!
			if ($depth == 0) {
				$output .= apply_filters('bunyad_mega_menu_end_lvl', array('sub_menu' => $this->last_lvl, 'item' => $this->current_item));
				
				// unset
				$this->last_lvl = '';
				
				return;
			}
			
			$this->last_lvl .= $item_output;
			return;
		}

		$output .= $item_output;
	}
}