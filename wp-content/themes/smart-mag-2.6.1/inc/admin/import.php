<?php
/**
 * Additional actions to perform at import
 *
 * @see Bunyad_Admin_Importer
 */

class Bunyad_Theme_Admin_Import
{
	public function __construct()
	{
		add_filter('bunyad_import_attachments_original', array($this, 'original_attachments'));
		add_action('bunyad_import_process_posts_pre', array($this, 'process_posts'));
		add_action('bunyad_import_completed', array($this, 'set_home'));
		
		// fix menu and fields 
		add_filter('bunyad_import_menu_fields', array($this, 'import_menu_fields'));
		add_action('bunyad_import_completed', array($this, 'import_fix_menu'));
	}
	
	/**
	 * Attachment URLs to preserve - do not replace with a random image
	 * 
	 * @param array $map
	 */
	public function original_attachments($map)
	{
		$map = array();
		
		return $map;
	}

	/**
	 * Action callback: Replace category ids in shortcodes - for pages
	 * @param Bunyad_Admin_Importer_WpImport $object
	 */
	public function process_posts($object)
	{
		foreach ($object->posts as $post_key => $post) {

			if ($post['post_type'] != 'page' OR $post['status'] != 'publish') {
				continue;
			}

			// modify shortcode categories
			preg_match_all('#\[([a-z\_]+)\s.+?cat=\"(\d+)\"[^\]]+\]#', $post['post_content'], $matches);
			foreach ((array) $matches[0] as $key => $match) {

				$term_id = $object->processed_terms[ $matches[2][$key] ];
				if (!isset($term_id)) {
					continue;
				}

				$replace = str_replace('cat="'. $matches[2][$key] .'"', 'cat="' . $term_id . '"', $match);
				$post['post_content'] = str_replace($match, $replace, $post['post_content']);
			}

			$object->posts[$post_key]['post_content'] = $post['post_content'];
		}
	}

	public function set_home()
	{
		if ($_POST['import_demo_type'] == 'tech') {
			// set the home page
			$home = get_page_by_title('Homepage');

			if (is_object($home)) {
				update_option('show_on_front', 'page');
				update_option('page_on_front', $home->ID);
			}
		}
	}

	
	/**
	 * Action callback: Fix menu on sample import
	 * 
	 * @param object $import
	 */
	public function import_fix_menu($import)
	{
		// remove an item from menu
		$item = get_page_by_title('Shop With Sidebar', OBJECT, 'nav_menu_item');
		
		if (is_object($item)) {
			wp_delete_post($item->ID);
		}
	}

	/**
	 * Custom Menu fields for the sample menu
	 * 
	 * @param array $values
	 */
	public function import_menu_fields($values = array())
	{
		return array(
			'mega_menu' => array('Entertainment' => 'category', 'Tidbits' => 'category', 'Features' => 'normal'),
			'url' => array('Forums' => home_url('/forums/')),
		);
	}

}


// init and make available in Bunyad::get('admin_import')
Bunyad::register('admin_import', array(
	'class' => 'Bunyad_Theme_Admin_Import',
	'init' => true
));