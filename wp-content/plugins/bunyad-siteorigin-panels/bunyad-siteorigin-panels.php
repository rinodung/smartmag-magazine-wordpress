<?php
/*
Plugin Name: Bunyad Page Builder
Plugin URI: http://theme-sphere.com/
Description: A drag and drop, responsive page builder that simplifies building your website.
Version: 1.3.7.6-mod
Author: ThemeSphere & Greg Priday (Original)
Author URI: http://siteorigin.com
License: GPL3
License URI: http://www.gnu.org/licenses/gpl.html
Donate link: http://siteorigin.com/page-builder/donate/
*/

define('SITEORIGIN_PANELS_VERSION', '1.3.7.4-mod');
define('SITEORIGIN_PANELS_BASE_FILE', __FILE__);

include plugin_dir_path(__FILE__) . 'widgets/bunyad-widgets.php';
include plugin_dir_path(__FILE__) . 'widgets/bunyad-base.php';
//include plugin_dir_path(__FILE__).'inc/options.php';
//include plugin_dir_path(__FILE__).'inc/aff.php';

/**
 * Initialize the language files
 */
function siteorigin_panels_init_lang() {
	load_plugin_textdomain('so-panels', false, 'siteorigin-panels/lang');
	
	// setup wpml translations
}
add_action('admin_init', 'siteorigin_panels_init_lang');

/**
 * Get the settings
 *
 * @param string $key Only get a specific key.
 * @return mixed
 */
function siteorigin_panels_setting($key = ''){
	static $settings;

	if (empty($settings)){

		$display_settings = get_option('siteorigin_panels_display', array());

		$settings = get_theme_support('siteorigin-panels');
		if(!empty($settings)) $settings = $settings[0];
		else $settings = array();

		$settings = wp_parse_args( $settings, array(
			'home-page' => false,																								// Is the home page supported
			'home-page-default' => false,																						// What's the default layout for the home page?
			'home-template' => 'home-panels.php',																				// The file used to render a home page.
			'post-types' => array('page'),									// Post types that can be edited using panels.

			'responsive' => !isset( $display_settings['responsive'] ) ? true : $display_settings['responsive'],				    // Should we use a responsive layout
			'mobile-width' => !isset( $display_settings['mobile-width'] ) ? 780 : $display_settings['mobile-width'],			// What is considered a mobile width?

			'margin-bottom' => !isset( $display_settings['margin-bottom'] ) ? 30 : $display_settings['margin-bottom'],			// Bottom margin of a cell
			'margin-sides' => !isset( $display_settings['margin-sides'] ) ? 30 : $display_settings['margin-sides'],				// Spacing between 2 cells
			'affiliate-id' => false,																							// Set your affiliate ID
			'copy-content' => !isset( $display_settings['copy-content'] ) ? false : $display_settings['copy-content'],			// Should we copy across content
			'animations' => !isset( $display_settings['animations'] ) ? true : $display_settings['animations'],					// Should we copy across content
		) );

		// Filter these settings
		$settings = apply_filters('siteorigin_panels_settings', $settings);

		if( empty( $settings['post-types'] ) ) $settings['post-types'] = array();
	}

	if( !empty( $key ) ) return isset( $settings[$key] ) ? $settings[$key] : null;

	return $settings;
}

/**
 * Add the admin menu entries
 */
function siteorigin_panels_admin_menu(){
	if( !siteorigin_panels_setting( 'home-page' ) ) return;
	
	add_theme_page(
		__( 'Custom Home Page Builder', 'so-panels' ),
		__( 'Home Page', 'so-panels' ),
		'edit_theme_options',
		'so_panels_home_page',
		'siteorigin_panels_render_admin_home_page'
	);
}
add_action('admin_menu', 'siteorigin_panels_admin_menu');

/**
 * Render the page used to build the custom home page.
 */
function siteorigin_panels_render_admin_home_page(){
	add_meta_box( 'so-panels-panels', __( 'Page Builder', 'so-panels' ), 'siteorigin_panels_metabox_render', 'appearance_page_so_panels_home_page', 'advanced', 'high' );
	include plugin_dir_path(__FILE__).'tpl/admin-home-page.php';
}

/**
 * Callback to register the Panels Metaboxes
 */
function siteorigin_panels_metaboxes() {
	foreach( siteorigin_panels_setting( 'post-types' ) as $type ){
		add_meta_box( 'so-panels-panels', __( 'Page Builder', 'so-panels' ), 'siteorigin_panels_metabox_render', $type, 'advanced', 'high' );
	}
}
add_action( 'add_meta_boxes', 'siteorigin_panels_metaboxes' );

/**
 * Save home page
 */
function siteorigin_panels_save_home_page(){
	if(!isset($_POST['_sopanels_home_nonce']) || !wp_verify_nonce($_POST['_sopanels_home_nonce'], 'save')) return;
	if(!current_user_can('edit_theme_options')) return;
	
	update_option('siteorigin_panels_home_page', siteorigin_panels_get_panels_data_from_post($_POST));
	update_option('siteorigin_panels_home_page_enabled', $_POST['siteorigin_panels_home_enabled'] == 'true' ? true : false);
	
	// If we've enabled the panels home page, change show_on_front to posts, this is required for the home page to work properly
	if( $_POST['siteorigin_panels_home_enabled'] == 'true' ) update_option( 'show_on_front', 'posts' );
}
add_action('admin_init', 'siteorigin_panels_save_home_page');

/**
 * Transfer theme data into new settings
 */
function siteorigin_panels_transfer_home_page(){
	if(get_option('siteorigin_panels_home_page', false) === false && get_theme_mod('panels_home_page', false) !== false) {
		// Transfer settings from theme mods into settings
		update_option( 'siteorigin_panels_home_page', get_theme_mod( 'panels_home_page', false ) );
		update_option( 'siteorigin_panels_home_page_enabled', get_theme_mod( 'panels_home_page_enabled', false ) );

		// Remove the theme mod data
		remove_theme_mod( 'panels_home_page' );
		remove_theme_mod( 'panels_home_page_enabled' );
	}
}
add_action('admin_init', 'siteorigin_panels_transfer_home_page');

/**
 * Modify the front page template
 * 
 * @param $template
 * @return string
 */
function siteorigin_panels_filter_home_template($template){
	if( !get_option('siteorigin_panels_home_page_enabled', siteorigin_panels_setting('home-page-default')) ) return $template;
	
	$GLOBALS['siteorigin_panels_is_panels_home'] = true;
	return locate_template(array(
		'home-panels.php',
		$template
	));
}
add_filter('home_template', 'siteorigin_panels_filter_home_template');

/**
 * If this is the main query, store that we're accessing the front page
 * @param $wp_query
 */
function siteorigin_panels_render_home_page_prepare($wp_query) {
	if ( !$wp_query->is_main_query() ) return;
	if ( !get_option('siteorigin_panels_home_page_enabled', siteorigin_panels_setting('home-page-default') ) ) return;

	$GLOBALS['siteorigin_panels_is_home'] = @ $wp_query->is_front_page();
}
add_action('pre_get_posts', 'siteorigin_panels_render_home_page_prepare');

/**
 * This fixes a rare case where pagination for a home page loop extends further than post pagination.
 */
function siteorigin_panels_render_home_page(){
	if (
		empty($GLOBALS['siteorigin_panels_is_home']) ||
		!is_404() ||
		!get_option('siteorigin_panels_home_page_enabled', siteorigin_panels_setting('home-page-default') )
	) return;

	// This query was for the home page, but because of pagination we're getting a 404
	// Create a fake query so the home page keeps working with the post loop widget
	$paged = get_query_var('paged');
	if( empty($paged) ) return;

	query_posts(array());
	set_query_var('paged', $paged);

	// Make this query the main one
	$GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];
	status_header(200); // Overwrite the 404 header we set earlier.
}
add_action('template_redirect', 'siteorigin_panels_render_home_page');

/**
 * @return mixed|void Are we currently viewing the home page
 */
function siteorigin_panels_is_home(){
	$home = (is_home() && get_option('siteorigin_panels_home_page_enabled', siteorigin_panels_setting('home-page-default')));
	return apply_filters('siteorigin_panels_is_home', $home);
}

/**
 * Disable home page panels when we change show_on_front to something other than posts.
 *
 * @param $old
 * @param $new
 *
 * @action update_option_show_on_front
 */
function siteorigin_panels_disable_on_front_page_change($old, $new){
	if($new != 'posts'){
		// Disable panels home page
		update_option('siteorigin_panels_home_page_enabled', false);
	}
}
add_action('update_option_show_on_front', 'siteorigin_panels_disable_on_front_page_change', 10, 2);


/**
 * Check if we're currently viewing a panel.
 *
 * @param bool $can_edit Also check if the user can edit this page
 * @return bool
 */
function siteorigin_panels_is_panel($can_edit = false){
	// Check if this is a panel
	$is_panel =  ( siteorigin_panels_is_home() || ( is_singular() && get_post_meta(get_the_ID(), 'panels_data', false) != '' ) );
	return $is_panel && (!$can_edit || ( (is_singular() && current_user_can('edit_post', get_the_ID())) || ( siteorigin_panels_is_home() && current_user_can('edit_theme_options') ) ));
}

/**
 * Render a panel metabox.
 *
 * @param $post
 */
function siteorigin_panels_metabox_render( $post ) {
	include plugin_dir_path(__FILE__).'tpl/metabox-panels.php';
}


/**
 * Enqueue the panels admin scripts
 *
 * @action admin_print_scripts-post-new.php
 * @action admin_print_scripts-post.php
 * @action admin_print_scripts-appearance_page_so_panels_home_page
 */
function siteorigin_panels_admin_enqueue_scripts($prefix) {
	$screen = get_current_screen();
	
	if ( ( $screen->base == 'post' && in_array( $screen->id, siteorigin_panels_setting('post-types') ) ) || $screen->base == 'appearance_page_so_panels_home_page') {
		wp_enqueue_script( 'jquery-ui-resizable' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script( 'jquery-ui-button' );

		wp_enqueue_script( 'so-undomanager', plugin_dir_url(__FILE__) . 'js/undomanager.min.js', array( ), 'fb30d7f' );

		wp_enqueue_script( 'so-panels-clonefix', plugin_dir_url(__FILE__) . 'js/jquery.fix.clone.min.js', array( 'jquery' ), SITEORIGIN_PANELS_VERSION );
		wp_enqueue_script( 'so-panels-admin', plugin_dir_url(__FILE__) . 'js/panels.admin.js', array( 'jquery' ), SITEORIGIN_PANELS_VERSION );
		wp_enqueue_script( 'so-panels-admin-panels', plugin_dir_url(__FILE__) . 'js/panels.admin.panels.js', array( 'jquery' ), SITEORIGIN_PANELS_VERSION );
		wp_enqueue_script( 'so-panels-admin-grid', plugin_dir_url(__FILE__) . 'js/panels.admin.grid.js', array( 'jquery' ), SITEORIGIN_PANELS_VERSION );
		wp_enqueue_script( 'so-panels-admin-prebuilt', plugin_dir_url(__FILE__) . 'js/panels.admin.prebuilt.min.js', array( 'jquery' ), SITEORIGIN_PANELS_VERSION );
		wp_enqueue_script( 'so-panels-admin-tooltip', plugin_dir_url(__FILE__) . 'js/panels.admin.tooltip.min.js', array( 'jquery' ), SITEORIGIN_PANELS_VERSION );
		wp_enqueue_script( 'so-panels-admin-media', plugin_dir_url(__FILE__) . 'js/panels.admin.media.min.js', array( 'jquery' ), SITEORIGIN_PANELS_VERSION );

		wp_enqueue_script( 'so-panels-chosen', plugin_dir_url(__FILE__) . 'js/chosen/chosen.jquery.min.min.js', array( 'jquery' ), SITEORIGIN_PANELS_VERSION );

		wp_localize_script( 'so-panels-admin', 'panels', array(
			'previewUrl' => wp_nonce_url(add_query_arg('siteorigin_panels_preview', 'true', get_home_url()), 'siteorigin-panels-preview'),
			'i10n' => array(
				'buttons' => array(
					'insert' => __( 'Insert', 'so-panels' ),
					'cancel' => __( 'cancel', 'so-panels' ),
					'delete' => __( 'Delete', 'so-panels' ),
					'duplicate' => __( 'Duplicate', 'so-panels' ),
					'edit' => __( 'Edit', 'so-panels' ),
					'done' => __( 'Done', 'so-panels' ),
					'undo' => __( 'Undo', 'so-panels' ),
					'add' => __( 'Add', 'so-panels' ),
				),
				'messages' => array(
					'deleteColumns' => __( 'Columns deleted', 'so-panels' ),
					'deleteWidget' => __( 'Widget deleted', 'so-panels' ),
					'confirmLayout' => __( 'Are you sure you want to load this layout? It will overwrite your current page.', 'so-panels' ),
					'editWidget' => __('Edit %s Widget', 'so-panels'),
					'invalid_row_format' => __('The string is not formed correctly. Correct format example: 1/2+1/2', 'so-panels'),
					'invalid_column_count' => __('Columns dont add to full width. Please recheck. Amounted to: {total}%', 'so-panels')
				),
			),
		) );

		// Localize the panels with the panels data
		if($screen->base == 'appearance_page_so_panels_home_page'){
			$panels_data = get_option('siteorigin_panels_home_page', null);
			if(is_null($panels_data)){
				// Load the default layout
				$layouts = apply_filters('siteorigin_panels_prebuilt_layouts', array());
				$panels_data = !empty($layouts['home']) ? $layouts['home'] : current($layouts);
			}
			$panels_data = apply_filters( 'siteorigin_panels_data', $panels_data, 'home');
		}
		else{
			global $post;
			$panels_data = get_post_meta( $post->ID, 'panels_data', true );
			$panels_data = apply_filters( 'siteorigin_panels_data', $panels_data, $post->ID );
		}
		
		if ( empty( $panels_data ) ) $panels_data = array();

		// Remove any panels that no longer exist.
		if ( !empty( $panels_data['panels'] ) ) {
			foreach ( $panels_data['panels'] as $i => $panel ) {
				if ( !class_exists( $panel['info']['class'] ) ) unset( $panels_data['panels'][$i] );
			}
		}

		if ( !empty( $panels_data ) ) {
			wp_localize_script( 'so-panels-admin', 'panelsData', $panels_data );
		}

		// This gives panels a chance to enqueue scripts too, without having to check the screen ID.
		do_action( 'siteorigin_panel_enqueue_admin_scripts' );
	}
}
add_action( 'admin_print_scripts-post-new.php', 'siteorigin_panels_admin_enqueue_scripts' );
add_action( 'admin_print_scripts-post.php', 'siteorigin_panels_admin_enqueue_scripts' );
add_action( 'admin_print_scripts-appearance_page_so_panels_home_page', 'siteorigin_panels_admin_enqueue_scripts' );


/**
 * Enqueue the admin panel styles
 *
 * @action admin_print_styles-post-new.php
 * @action admin_print_styles-post.php
 */
function siteorigin_panels_admin_enqueue_styles() {
	$screen = get_current_screen();
	if ( in_array( $screen->id, siteorigin_panels_setting('post-types') ) || $screen->base == 'appearance_page_so_panels_home_page') {
		//wp_enqueue_style( 'so-panels-jquery-ui', plugin_dir_url(__FILE__) . 'css/jquery-ui-theme.css' );
		wp_enqueue_style( 'so-panels-admin', plugin_dir_url(__FILE__) . 'css/admin.css' );
		wp_enqueue_style( 'so-panels-chosen', plugin_dir_url(__FILE__) . 'js/chosen/chosen.css' );
	
		do_action( 'siteorigin_panel_enqueue_admin_styles' );
	}
}
add_action( 'admin_print_styles-post-new.php', 'siteorigin_panels_admin_enqueue_styles' );
add_action( 'admin_print_styles-post.php', 'siteorigin_panels_admin_enqueue_styles' );
add_action( 'admin_print_styles-appearance_page_so_panels_home_page', 'siteorigin_panels_admin_enqueue_styles' );

/**
 * Add a help tab to pages with panels.
 */
function siteorigin_panels_add_help_tab($prefix) {
	$screen = get_current_screen();
	if(
		($screen->base == 'post' && ( in_array( $screen->id, siteorigin_panels_setting( 'post-types' ) ) || $screen->id == ''))
		|| ($screen->id == 'appearance_page_so_panels_home_page')
	) {
		$screen->add_help_tab( array(
			'id' => 'panels-help-tab', //unique id for the tab
			'title' => __( 'Page Builder', 'so-panels' ), //unique visible title for the tab
			'callback' => 'siteorigin_panels_add_help_tab_content'
		) );
	}
}
add_action('load-page.php', 'siteorigin_panels_add_help_tab');
add_action('load-post-new.php', 'siteorigin_panels_add_help_tab');
add_action('load-appearance_page_so_panels_home_page', 'siteorigin_panels_add_help_tab');

/**
 * Display the content for the help tab.
 */
function siteorigin_panels_add_help_tab_content(){
	include plugin_dir_path(__FILE__) . 'tpl/help.php';
}

/**
 * Save the panels data
 *
 * @param $post_id
 * @param $post
 *
 * @action save_post
 */
function siteorigin_panels_save_post( $post_id, $post ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( empty( $_POST['_sopanels_nonce'] ) || !wp_verify_nonce( $_POST['_sopanels_nonce'], 'save' ) ) return;
	if ( !current_user_can( 'edit_post', $post_id ) ) return;

	$panels_data = siteorigin_panels_get_panels_data_from_post($_POST);
	update_post_meta( $post_id, 'panels_data', $panels_data );

	if( !empty($panels_data['widgets']) && siteorigin_panels_setting('copy-content') ) {
		// Save the panels data into post_content for SEO and search plugins
		$content = siteorigin_panels_render( $post_id, false );
		$content = preg_replace(
			array(
			  // Remove invisible content
				'@<head[^>]*?>.*?</head>@siu',
				'@<style[^>]*?>.*?</style>@siu',
				'@<script[^>]*?.*?</script>@siu',
				'@<object[^>]*?.*?</object>@siu',
				'@<embed[^>]*?.*?</embed>@siu',
				'@<applet[^>]*?.*?</applet>@siu',
				'@<noframes[^>]*?.*?</noframes>@siu',
				'@<noscript[^>]*?.*?</noscript>@siu',
				'@<noembed[^>]*?.*?</noembed>@siu',
			),
			array(' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',),
			$content
		);
		$content = strip_tags($content, '<img><h1><h2><h3><h4><h5><h6><a><p><em><strong>');
		$content = explode("\n", $content);
		$content = array_map('trim', $content);
		$content = implode("\n", $content);
		$post->post_content = $content;

		// Update the post, removing this action first so we don't infinite loop.
		remove_action('save_post', 'siteorigin_panels_save_post');
		wp_update_post($post);
		add_action( 'save_post', 'siteorigin_panels_save_post', 10, 2 );
	}
}
add_action( 'save_post', 'siteorigin_panels_save_post', 10, 2 );

/**
 * Convert form post data into more efficient panels data.
 * 
 * @param $form_post
 * @return array
 */
function siteorigin_panels_get_panels_data_from_post($form_post){
	$panels_data = array();

	$panels_data['widgets'] = array_map( 'stripslashes_deep', isset( $form_post['widgets'] ) ? $form_post['widgets'] : array() );
	$panels_data['widgets'] = array_values( $panels_data['widgets'] );

	if ( empty( $panels_data['widgets'] ) ) {
		return array();
	}

	foreach ( $panels_data['widgets'] as $i => $widget ) {
		$info = $widget['info'];
		if ( !class_exists( $widget['info']['class'] ) ) continue;

		$the_widget = new $widget['info']['class'];
		if ( method_exists( $the_widget, 'update' ) ) {
			unset( $widget['info'] );
			$widget = $the_widget->update( $widget, $widget );
		}
		$widget['info'] = $info;
		$panels_data['widgets'][$i] = $widget;
	}

	$panels_data['grids'] = array_map( 'stripslashes_deep', isset( $form_post['grids'] ) ? $form_post['grids'] : array() );
	$panels_data['grids'] = array_values( $panels_data['grids'] );

	$panels_data['grid_cells'] = array_map( 'stripslashes_deep', isset( $form_post['grid_cells'] ) ? $form_post['grid_cells'] : array() );
	$panels_data['grid_cells'] = array_values( $panels_data['grid_cells'] );

	return $panels_data;
}

/**
 * Get the home page panels layout data.
 * 
 * @return mixed|void
 */
function siteorigin_panels_get_home_page_data(){
	$panels_data = get_option('siteorigin_panels_home_page', null);
	if(is_null($panels_data)){
		// Load the default layout
		$layouts = apply_filters('siteorigin_panels_prebuilt_layouts', array());
		$panels_data = !empty($layouts['default_home']) ? $layouts['default_home'] : current($layouts);
	}
	
	return $panels_data;
}

/**
 * Prepare the panels data early so widgets can enqueue their scripts and styles for the header.
 */
function siteorigin_panels_prepare_home_content( ) {
	if( siteorigin_panels_is_home() ) {
		global $siteorigin_panels_cache;
		if(empty($siteorigin_panels_cache)) $siteorigin_panels_cache = array();
		$siteorigin_panels_cache['home'] = siteorigin_panels_render( 'home' );
	}
}
add_action('wp_enqueue_scripts', 'siteorigin_panels_prepare_home_content', 11);

function siteorigin_panels_prepare_single_post_content(){
	if( is_singular() ) {
		global $siteorigin_panels_cache, $post;
		if( empty($siteorigin_panels_cache[$post->ID] ) ) {
			$siteorigin_panels_cache[$post->ID] = siteorigin_panels_render( $post->ID );
		}
	}
}
add_action('wp_enqueue_scripts', 'siteorigin_panels_prepare_single_post_content');

/**
 * Filter the content of the panel, adding all the widgets.
 *
 * @param $content
 * @return string
 *
 * @filter the_content
 */
function siteorigin_panels_filter_content($content, $type = '') {
	global $post;
	
	if ($type != 'bunyad_main_content') {
		return $content;
	}
	
	if (empty($post)) {
		return $content;
	}
	
	if ( in_array( $post->post_type, siteorigin_panels_setting('post-types') ) ) {
		$panel_content = siteorigin_panels_render( $post->ID );

		if ( !empty( $panel_content ) ) $content = $panel_content;
	}

	return $content;
}

add_filter('the_content', 'siteorigin_panels_filter_content', 10, 2);


/**
 * Render the panels
 *
 * @param bool $post_id
 * @param bool $enqueue_css Should we also enqueue the layout CSS.
 * @return string
 */
function siteorigin_panels_render( $post_id = false, $enqueue_css = true ) {
	if( empty($post_id) ) $post_id = get_the_ID();

	global $siteorigin_panels_current_post;
	$old_current_post = $siteorigin_panels_current_post;
	$siteorigin_panels_current_post = $post_id;

	// Try get the cached panel from in memory cache.
	global $siteorigin_panels_cache;
	if(!empty($siteorigin_panels_cache) && !empty($siteorigin_panels_cache[$post_id]))
		return $siteorigin_panels_cache[$post_id];

	if($post_id == 'home'){
		$panels_data = get_option( 'siteorigin_panels_home_page', get_theme_mod('panels_home_page', null) );

		if(is_null($panels_data)){
			// Load the default layout
			$layouts = apply_filters('siteorigin_panels_prebuilt_layouts', array());
			$panels_data = !empty($layouts['home']) ? $layouts['home'] : current($layouts);
		}
	}
	else{
		if ( post_password_required($post_id) ) return false;
		$panels_data = get_post_meta( $post_id, 'panels_data', true );
	}

	$panels_data = apply_filters( 'siteorigin_panels_data', $panels_data, $post_id );
	if( empty( $panels_data ) || empty( $panels_data['grids'] ) ) return '';

	// Create the skeleton of the grids
	$grids = array();
	foreach ( $panels_data['grids'] as $gi => $grid ) {
		$gi = intval( $gi );
		$grids[$gi] = array();
		for ( $i = 0; $i < $grid['cells']; $i++ ) {
			$grids[$gi][$i] = array();
		}
	}

	if( !empty( $panels_data['widgets'] ) ){
		foreach ( $panels_data['widgets'] as $widget ) {
			$grids[intval( $widget['info']['grid'] )][intval( $widget['info']['cell'] )][] = $widget;
		}
	}

	$weights = array();
	foreach ($panels_data['grid_cells'] as $cell) {
		$weights[$cell['grid']][] = $cell['weight']; 
	}
	
	ob_start();
	
	foreach ( $grids as $gi => $cells ) {
		
		$no_container = false;	

		$grid_classes = array('row', 'cf');
		$grid_classes = apply_filters('siteorigin_panels_row_classes', $grid_classes);
		$grid_classes = array_map('esc_attr', $grid_classes);
		
		/*
		 * Disable container row for special blocks?
		 */
		if (count($cells) <= 1 && count($cells[0]) <= 1 && isset($cells[0][0])) {
			
			$widget = $cells[0][0];				
			
			// no container if alone
			if (!empty($widget['no_container'])) {
				$no_container = true;
			}
		}
		
		// add builder class for normal rows
		if (!$no_container) {
			$grid_classes[] = 'builder';
		}

		?>
		
		<?php if (!$no_container): ?>
			<div class="<?php echo implode(' ', $grid_classes) ?>">
		<?php endif; ?>	
		
		<?php

		foreach ($cells as $ci => $widgets) 
		{
			
			$cell_classes = apply_filters('siteorigin_panels_row_cell_classes', array('column', 'builder'));
			$cell_classes = array_map('esc_attr', $cell_classes);
			
			$weight = (float) $weights[$gi][$ci];
		
			// convert weight from 0.x to readable format like one-fourth
			$one_weight = 1;
			if ($weight) {
				$one_weight = 1/$weight;
			}
			
			// 1/x widths?
			if (round($one_weight, 0) === $one_weight) {
				
				$weight_class = ($one_weight == 2 ? 'half' : 'one-' . $one_weight);
				$weight_class = str_replace(array(3, 4, 5), array('third', 'fourth', 'fifth'), $weight_class);

				$cell_classes[] = $weight_class;
			}
			// other supported widths
			else {

				$map = array(
					'0.40' => 'two-fifth',
					'0.60' => 'three-fifth',
					'0.67' => 'two-third', 
					'0.75' => 'three-fourth',
					'0.80' => 'four-fifth',				 
				); 
				
				$num = number_format($weight, 2);

				if (array_key_exists($num, $map)) {
					$cell_classes[] = $map[$num];
				}
			}

			?>
			
			<?php if (!$no_container): ?>
				<div class="<?php echo implode(' ', $cell_classes) ?>">
			<?php endif; ?>
				
			<?php
			foreach ($widgets as $pi => $widget_info) {
				$data = $widget_info;
				unset($data['info']);

				siteorigin_panels_the_widget( $widget_info['info']['class'], $data, $gi, $ci, $pi, $pi == 0, $pi == count( $widgets ) - 1 );
			}
			
			if (empty($widgets)) {
				//echo '&nbsp;';
			}
			
			?>
			
			<?php if (!$no_container): ?> </div> <?php endif; ?>
			
			<?php
		} // foreach
		?>
		
		<?php if (!$no_container): ?>
			</div>
		<?php endif; ?>	
		
		<?php
	}

	$html = ob_get_clean();

	// Reset the current post
	$siteorigin_panels_current_post = $old_current_post;

	return apply_filters( 'siteorigin_panels_render', $html, $post_id, !empty($post) ? $post : null );
}

/**
 * Render the widget. 
 * 
 * @param string $widget The widget class name.
 * @param array $instance The widget instance
 * @param $grid
 * @param $cell
 * @param $panel
 * @param $is_first
 * @param $is_last
 */
function siteorigin_panels_the_widget( $widget, $instance, $grid, $cell, $panel, $is_first, $is_last ) {
	if ( !class_exists( $widget ) ) return;

	$the_widget = new $widget;
	
	if (!empty($instance['text'])) {
		global $wp_embed;
		
		if (is_object($wp_embed)) {
			$instance['text'] = $wp_embed->run_shortcode($instance['text']);
			$instance['text'] = $wp_embed->autoembed($instance['text']);
		}
		
	}

	$classes = array( 'panel', 'widget' );
	if ( !empty( $the_widget->id_base ) ) $classes[] = 'widget_' . $the_widget->id_base;
	if ( $is_first ) $classes[] = 'panel-first-child';
	if ( $is_last ) $classes[] = 'panel-last-child';

	$the_widget->widget( array(
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
		'widget_id' => 'widget-' . $grid . '-' . $cell . '-' . $panel
	), $instance );
}

/**
 * Add the Edit Home Page item to the admin bar.
 * 
 * @param WP_Admin_Bar $admin_bar
 * @return WP_Admin_Bar
 */
function siteorigin_panels_admin_bar_menu($admin_bar){
	/**
	 * @var WP_Query $wp_query
	 */
	global $wp_query;
	
	if( ( $wp_query->is_home() && $wp_query->is_main_query() ) || siteorigin_panels_is_home() ){
		// Check that we support the home page
		if ( !siteorigin_panels_setting('home-page') || !current_user_can('edit_theme_options') ) return $admin_bar;
		
		$admin_bar->add_node(array(
			'id' => 'edit-home-page',
			'title' => __('Edit Home Page', 'so-panels'),
			'href' => admin_url('themes.php?page=so_panels_home_page')
		));
	}
	
	return $admin_bar;
}
add_action('admin_bar_menu', 'siteorigin_panels_admin_bar_menu', 100);

/**
 * Handles creating the preview.
 */
function siteorigin_panels_preview(){
	if(isset($_GET['siteorigin_panels_preview']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'siteorigin-panels-preview')){
		global $siteorigin_panels_is_preview;
		$siteorigin_panels_is_preview = true;
		// Set the panels home state to true
		if(empty($_POST['post_id'])) $GLOBALS['siteorigin_panels_is_panels_home'] = true;
		add_action('option_siteorigin_panels_home_page', 'siteorigin_panels_preview_load_data');
		locate_template(siteorigin_panels_setting('home-template'), true);
		exit();
	}
}
add_action('template_redirect', 'siteorigin_panels_preview');

function siteorigin_panels_is_preview(){
	global $siteorigin_panels_is_preview;
	return (bool) $siteorigin_panels_is_preview;
}

/**
 * Hide the admin bar for panels previews.
 * 
 * @param $show
 * @return bool
 */
function siteorigin_panels_preview_adminbar($show){
	if(!$show) return false;
	return !(isset($_GET['siteorigin_panels_preview']) && wp_verify_nonce($_GET['_wpnonce'], 'siteorigin-panels-preview'));
}
add_filter('show_admin_bar', 'siteorigin_panels_preview_adminbar');

/**
 * This is a way to show previews of panels, especially for the home page.
 *
 * @param $val
 * @return array
 */
function siteorigin_panels_preview_load_data($val){
	if(isset($_GET['siteorigin_panels_preview'])){
		$val = siteorigin_panels_get_panels_data_from_post($_POST);
	}
	
	return $val;
}

function siteorigin_panels_body_class($classes) {
	$classes[] = 'page-builder';
	return $classes;
}
add_filter('body_class', 'siteorigin_panels_body_class');

/**
 * Add current pages as cloneable pages
 * 
 * @param $layouts
 * @return mixed
 */
function siteorigin_panels_cloned_page_layouts($layouts){
	$pages = get_posts( array(
		'post_type' => 'page',
		'post_status' => array('publish', 'draft'),
		'numberposts' => 200,
	) );
	
	foreach($pages as $page){
		$panels_data = get_post_meta( $page->ID, 'panels_data', true );
		$panels_data = apply_filters( 'siteorigin_panels_data', $panels_data, $page->ID );
		
		if(empty($panels_data)) continue;
		
		$name =  empty($page->post_title) ? __('Untitled', 'so-panels') : $page->post_title;
		if($page->post_status != 'publish') $name .= ' ( ' . __('Unpublished', 'so-panels') . ' )';

		if(current_user_can('edit_post', $page->ID)) {
			$layouts['post-'.$page->ID] = wp_parse_args(
				array(
					'name' => sprintf(__('Clone Page: %s', 'so-panels'), $name )
				),
				$panels_data
			);
		}
	}

	// Include the current home page in the clone pages.
	$home_data = get_option('siteorigin_panels_home_page', null);
	if ( !empty($home_data) ) {

		$layouts['current-home-page'] = wp_parse_args(
			array(
				'name' => __('Clone: Current Home Page', 'so-panels'),
			),
			$home_data
		);
	}
	
	return $layouts;
}
add_filter('siteorigin_panels_prebuilt_layouts', 'siteorigin_panels_cloned_page_layouts', 20);

/**
 * Add a filter to import panels_data meta key. This fixes serialized PHP.
 */
function siteorigin_panels_wp_import_post_meta($post_meta){
	foreach($post_meta as $i => $meta) {
		if($meta['key'] == 'panels_data') {
			$value = $meta['value'];
			$value = preg_replace("/[\r\n]/", "<<<br>>>", $value);
			$value = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $value);
			$value = unserialize($value);
			$value = array_map('siteorigin_panels_wp_import_post_meta_map', $value);

			$post_meta[$i]['value'] = $value;
		}
	}

	return $post_meta;
}
//add_filter('wp_import_post_meta', 'siteorigin_panels_wp_import_post_meta');

/**
 * A callback that replaces temporary break tag with actual line breaks.
 *
 * @param $val
 * @return array|mixed
 */
function siteorigin_panels_wp_import_post_meta_map($val) {
	if(is_string($val)) return str_replace('<<<br>>>', "\n", $val);
	else return array_map('siteorigin_panels_wp_import_post_meta_map', $val);
}

/**
 * Admin ajax handler for loading a prebuilt layout.
 */
function siteorigin_panels_ajax_action_prebuilt(){
	// Get any layouts that the current user could edit.
	$layouts = apply_filters('siteorigin_panels_prebuilt_layouts', array());

	if(empty($_GET['layout'])) exit();
	if(empty($layouts[$_GET['layout']])) exit();

	header('content-type: application/json');
	echo json_encode($layouts[$_GET['layout']]);
	exit();
}
add_action('wp_ajax_so_panels_prebuilt', 'siteorigin_panels_ajax_action_prebuilt');

function siteorigin_panels_dump(){
	if( defined('WP_DEBUG') && WP_DEBUG ) {
		echo "<!--\n\n";
		echo "// Panels Data dump\n\n";

		if(isset($_GET['page']) && $_GET['page'] == 'so_panels_home_page') {
			var_export( get_option( 'siteorigin_panels_home_page', null ) );
		}
		else{
			global $post;
			var_export( get_post_meta($post->ID, 'panels_data', true));
		}
		echo "\n\n-->";
	}
}
add_action('siteorigin_panels_metabox_end', 'siteorigin_panels_dump');


function bunyad_builder_disable_updates($value)
{
	if (is_object($value) && property_exists($value, 'response')) {
		unset($value->response[ plugin_basename(__FILE__) ]);
	}
	
	return $value;
}

add_filter('site_transient_update_plugins', 'bunyad_builder_disable_updates');