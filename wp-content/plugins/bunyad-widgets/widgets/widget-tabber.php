<?php

class Bunyad_Tabber_Widget extends WP_Widget
{
	
	public $count = 1;
	
	public function __construct()
	{
		parent::__construct(
			'bunyad-tabber-widget',
			'Bunyad - Tabber Advanced',
			array('description' => __('Advanced tabber that supports all widgets.', 'bunyad-widgets'), 'classname' => 'tabbed')
		);
		
		add_action('save_post', array($this, 'flush_widget_cache'));
		add_action('edit_post', array($this, 'flush_widget_cache')); // comments covered
		add_action('deleted_post', array($this, 'flush_widget_cache'));
		add_action('switch_theme', array($this, 'flush_widget_cache'));
		add_action('bunyad_widget_flush_cache', array($this, 'flush_widget_cache'));
		
		// flush the cache on widget edit screen
		add_action('sidebar_admin_setup', array($this, 'flush_widget_cache'));
		add_action('update_option_sidebars_widgets', array($this, 'flush_widget_cache'));
	}

	public function widget($args, $instance) 
	{
		add_filter('dynamic_sidebar_params', array($this, 'widget_sidebar_params'));

		extract($args, EXTR_SKIP);

		// posts available in cache? - use instance id to suffix
		$cache = get_transient('bunyad_tabber_advanced');
		
		if (is_array($cache) && isset($cache[$this->number])) {
		        
	        // remove temporary filter
        	remove_filter('dynamic_sidebar_params', array($this, 'widget_sidebar_params'));
        	
			echo $cache[$this->number];
			return;
		}
		
		ob_start();
			
		echo $before_widget;
		
		if ($args['id'] != $instance['sidebar']) 
		{			
			ob_start();
			
			dynamic_sidebar($instance['sidebar']);
			$tabs = ob_get_clean();

			
			echo '<ul class="tabs-list">';

			// get the titles 
			preg_match_all('#<div class="bunyad-tab-title">(.+?)</div>#', $tabs, $titles);
			
			$count = 1;
			foreach ((array) $titles[1] as $key => $title) {
				$tabs = str_replace($titles[0][$key], '', $tabs);
				
				echo '<li class="'. ($count == 1 ? 'active' : '') .'"><a href="#" data-tab="'. $count++ .'">'. esc_html($title) .'</a></li>';
			}
			
			echo '</ul>';
			
			echo '<div class="tabs-data">' . $tabs . '</div>';
			
		}
		
        echo $after_widget;
        
        // retrieve, output, and save the cache
        $cache[ $this->number ] = ob_get_flush();
        set_transient('bunyad_tabber_advanced', $cache, 60*5); // 5 minutes cache
        
        // remove temporary filter
        remove_filter('dynamic_sidebar_params', array($this, 'widget_sidebar_params'));
		
	}

	public function update($new, $old) 
	{
		$new['sidebar'] = strip_tags($new['sidebar']);
		
		$this->flush_widget_cache();
		
		return $new;
	}
	
	public function flush_widget_cache()
	{
		delete_transient('bunyad_tabber_advanced');
	}

	public function form($instance) 
	{
		global $wp_registered_sidebars;
		
		$instance = wp_parse_args((array) $instance, array('sidebar' => ''));
?>

		<?php if (!is_plugin_active('custom-sidebars/customsidebars.php')): ?>
		<p><?php _e('<strong>WARNING:</strong> Required plugin "Custom Sidebars" is missing. The plugin is needed to create a custom sidebar for this widget.', 'bunyad-widgets'); ?></p>
		<?php endif; ?>
		
		<p><?php _e('To use this widget, first <strong>create a new sidebar</strong>. Then add widgets to the new sidebar. Finally select the newly created sidebar below.', 'bunyad-widgets'); ?></p>

		<p><label><?php _e('Select the sidebar:', 'bunyad-widgets'); ?></label>
				
			<select class="widefat" name="<?php echo $this->get_field_name('sidebar'); ?>">
			<?php
			foreach ($wp_registered_sidebars as $id => $sidebar) {
				if ($id != 'wp_inactive_widgets' && !strstr($sidebar['class'], 'inactive')) {
						$selected = $instance['sidebar'] == $id ? ' selected="selected"' : '';
						echo sprintf('<option value="%s"%s>%s</option>', $id, $selected, $sidebar['name']);
				}
			}

			?>
			</select>
		</p>		
<?php

	}
	

	public function widget_sidebar_params($params) 
	{
		$params[0]['before_widget'] = '<ul class="tab-posts posts-list" id="recent-tab-'. $this->count++ .'">';
		$params[0]['after_widget'] = '</ul>';
		$params[0]['before_title'] = '<div class="bunyad-tab-title">';
		$params[0]['after_title'] = '</div>';

		return $params;
	}
}

/*
add_action('init', 'bunyad_widget_tabber_init');

if (!function_exists('bunyad_widget_tabber_init')) {
	function bunyad_widget_tabber_init() {
		register_sidebar(array('name' => 'Bunyad Tabber Area 1', 'description' => __('Used for advanced tabber widget.', 'bunyad-widgets')));
	} 
}*/
