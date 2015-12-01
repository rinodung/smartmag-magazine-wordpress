<?php

class Bunyad_Admin_OptionRenderer
{
	public $default_values = array();
	
	/**
	 * Initialize the template file
	 * 
	 * @param array $options
	 * @param string $file
	 * @param array $populate  default form values for elements
	 * @param array $data 
	 */
	public function template($options, $file, $populate = array(), $data = array())
	{
		$this->default_values = (array) $populate;
		extract($data);

		require_once $file;
	}
	
	public function render($element)
	{
		// defaults
		$element = array_merge(array('name' => null, 'label' => null), $element);
		
		// set default value if available
		if (isset($this->default_values[$element['name']])) {
			$element['value'] = $this->default_values[$element['name']];
		}
		
		switch ($element['type'])
		{
			case 'select':
				$output = $this->render_select($element);
				break;
			
			case 'checkbox':
				$output = $this->render_checkbox($element);
				break;
				
			case 'text':
				$output = $this->render_text($element);
				break;
				
			case 'textarea':
				$output = $this->render_textarea($element);
				break;
				
			case 'radio':
				$output = $this->render_radio($element);
				break;
				
			case 'color':
				$output = $this->render_color_picker($element);
				break;
				
			case 'bg_image':
				$output = $this->render_bg_image($element);
				break;
				
			case 'upload':
				$output = $this->render_upload($element);
				break;

			case 'typography':			
				$output = $this->render_typography($element);
				break;
				
			case 'html':
				$output = $element['html'];
				break;
			
			default:
				break;
		}
		
		
		// decorate it
		if ($output) {
			$output = '<label class="element-title">'. $element['label'] . '</label>'
					. '<div class="element-control">' . $output . (isset($element['html_post_output']) ? $element['html_post_output'] : '') . '</div>';
		}
		
		return $output;
	}
	
	public function render_select($element)
	{
		$element = array_merge(array('value' => null), $element);
		
		$output = '<select name="'. esc_attr($element['name']) .'"' . (isset($element['class']) ? ' class="'. esc_attr($element['class']) .'"' : '') . '>';
		
		foreach ( (array) $element['options'] as $key => $option) 
		{
			if (is_array($option)) {
				$output .= '<optgroup label="' . esc_attr($key) . '">' . $this->_render_options($option) . '</optgroup>';
			}
			else {
				$output .= $this->_render_options(array($key => $option), $element['value']);
			}
			
		}
		
		return $output . '</select>';
	}
	
	// helper for: render_select()
	private function _render_options($options, $selected = '') 
	{	
		$output = '';
		
		foreach ($options as $key => $option) {
			$output .= '<option value="'. esc_attr($key) .'"'. selected((string) $selected, $key, false) .'>' . esc_html($option) . '</option>';
		}
		
		return $output;
	}
	
	public function render_checkbox($element)
	{
		
		$element = array_merge(array('value' => null), $element);
		$element['options'] = array_merge(
			array('checked' => __('Yes', 'bunyad'), 'unchecked' => __('No', 'bunyad')), 
			!empty($element['options']) ? $element['options'] : array()
		);
		
		$output = '<input type="hidden" name="'. esc_attr($element['name']) .'" value="0" />' // always send in POST - even when empty
				. '<input type="checkbox" name="'. esc_attr($element['name']) .'" value="1"'
				. checked($element['value'], 1, false) . ' data-yes="'. esc_attr($element['options']['checked']) .'" data-no="'. esc_attr($element['options']['unchecked']) .'" />
				<label for="'. esc_attr($element['name']) .'">' . $element['options']['checked'] . '</label>
				';
				
		return $output;
	}
	
	public function render_text($element)
	{
		$element = array_merge(array('value' => null), $element);
		
		$output = '<input type="text" name="'. esc_attr($element['name']) .'" value="'. esc_attr($element['value'])  .'" class="input" />';
				
		return $output;
	}
	
	public function render_textarea($element)
	{
		// defaults
		$element = array_merge(array(
			'value' => null,
			'options' => array('rows' => null, 'cols' => null)
		), $element);
		
		$row_cols = '';
		if ($element['options']['rows'] OR $element['options']['cols']) {
			$row_cols = ' rows="' . intval($element['options']['rows']) . '" cols="' . $element['options']['cols'] . '"'; 
		}
		
		$output = '<textarea name="' . esc_attr($element['name']) . '"' . $row_cols .'>'. esc_html($element['value']) .'</textarea>';
		
		return $output;
	}
	
	public function render_radio($element)
	{
		$output = '';
		
		foreach ($element['options'] as $key => $option)
		{
			$output .= '<div class="radio-option"><label><input type="radio" name="'. esc_attr($element['name']) .'" value="'. esc_attr($key) . '"'
					.  checked($element['value'], $key, false) .' /><span>' . esc_html($option) . '</span></label></div>';
		}
				
		return $output;
	}
	
	public function render_color_picker($element)
	{
		$element = array_merge(array('value' => null), $element);
		
		$output = '<input type="text" class="color-picker" name="'. esc_attr($element['name']) .'"'
				. ' value="' . esc_attr($element['value']) . '" /><div class="color-picker-element"></div>';
				
		return $output;
	}
	
	/**
	 * Render background image selector with options to select bg position
	 * 
	 * @param array $element
	 * @uses Bunyad_Admin_OptionRenderer::render_upload()
	 */
	public function render_bg_image($element)
	{
		// future themes on-need-basis implementation
	}
	
	/**
	 * Render an upload element
	 * 
	 * @param array $element
	 */
	public function render_upload($element)
	{
		$button_label = __( 'Upload', 'bunyad' );
		if (!empty($element['options']['button_label'])) {
			$button_label = $element['options']['button_label'];
		}
		
		$element = array_merge(array('value' => null), $element);
		$element['options'] = array_merge(array('editable' => null, 'title' => null, 'type' => null), $element['options']);
		
		
		$classes = $image = '';
		
		$output = '<input type="'. ($element['options']['editable'] ? 'text' : 'hidden') .'" name="'. esc_attr($element['name']) .'" class="element-upload" value="'
				. esc_attr($element['value']) .'" />'
				. '<input type="button" class="button upload-btn" value="'. esc_attr($button_label) .'"' 
				. ' data-insert-label="'. esc_attr($element['options']['insert_label']) .'"'
				. ' data-title="'. esc_attr($element['options']['title']) .'"'
				. ' data-title="'. esc_attr($element['options']['type']) .'"'
				.'"/> ';
		
		// image type?
		if ($element['options']['type'] == 'image') 
		{ 
			// existing image?
			if ($element['value']) {
				$image   = '<img src="'. esc_attr($element['value']) .'" />';
				$classes = ' visible ';
			}
		
			$output .= '<div class="image-upload'. $classes .'">'. $image .'<a href="" class="remove-image">'. __('Remove', 'bunyad') .'</a></div>';
		}
				
		return $output;
	}
	
	/**
	 * Google web fonts - uses api to get fonts list
	 */
	public function render_typography($element)
	{
		// defaults
		$element = array_merge(array('size' => null, 'color' => null, 'no_preview' => null), $element);
		
		// get fonts
		$fonts = $this->get_google_fonts();
		
		foreach ($fonts['items'] as $font) 
		{
			foreach ($font['variants'] as $variant)	
			{
				// not the regular variant?
				$variation = '';
				if ($variant !== 'regular')  {
					$variation = ' ('. ucfirst($variant) . ')';
				}
				
				$options[$font['family'] .':'. $variant] = $font['family'] . $variation;
			}
		}
		
		$select = $this->render_select(array_merge(
			$element, array('options' => $options, 'class' => 'font-picker chosen-select')
		));
		
		$output = '<div class="typography">' . $select;
		
		if ($element['size']) {
			// TODO: custom range for each element
			$output .= $this->render_select(array_merge($element, array(
				'name' => $element['name'] . '_size',
				'options' => array_combine(range(9, 60), range(9, 60)),
				'value'   => $element['size']['value'],
				'class'   => 'size-picker'
			))) . 'px';
		}
		
		// add a color picker?
		if ($element['color']) {
			$output .= $this->render_color_picker(array_merge($element, array(
				'name'  => $element['name'] . '_color',
				'value' => $element['color']['value'],
			)));
		}
		
		if ($element['no_preview'] !== true) {
			$output .= '<p class="preview"></p>';
		}
		
		return $output . '</div>';
	}
	
	
	/**
	 * Get a list of google fonts
	 */
	public function get_google_fonts()
	{
		$fonts = file_get_contents(get_template_directory() .'/admin/fonts.json');
		return json_decode($fonts, true);
	}
}