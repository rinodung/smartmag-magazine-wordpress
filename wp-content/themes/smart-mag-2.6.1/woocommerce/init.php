<?php

/**
 * SmartMag Theme - WooCommerce Functionality
 * 
 * Everything related to integrating WooCommerce functionality into the theme goes in this file.
 */

class Bunyad_Theme_SmartMag_WooCommerce
{
	public function __construct()
	{
		add_theme_support('woocommerce');
		add_filter('init', array($this, 'init'));
		
		add_filter('bunyad_theme_options', array($this, 'add_theme_options'));
		
		/*
		 * Hook in on activation
		 */
		global $pagenow;
		
		if (is_admin() && isset($_GET['activated']) && $pagenow == 'themes.php') {
			add_action('init', array($this, 'image_sizing'), 1);	
		}
	}
	
	/**
	 * Register WooCommrece related hooks
	 */
	public function init()
	{
		// register assets and set sidebar
		add_action('get_header', array($this, 'set_sidebar'), 11);
		add_action('wp_enqueue_scripts', array($this, 'register_assets'));
		
		// change it to 799px
		add_filter('woocommerce_style_smallscreen_breakpoint', create_function('$px', 'return "799px";'));
		
		// number of columns on listing?
		add_filter('loop_shop_columns', array($this, 'columns'));
		
		/*
		 * Modify cart icon for correct appearance and add ajax cart fragments
		 */
		remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail');
		remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
		
		add_action('woocommerce_before_shop_loop_item_title', array($this, 'cart_icon'));
		add_action('woocommerce_after_shop_loop_item_title', array($this, 'product_link_close'));
		
		// add menu cart item
		if (Bunyad::options()->woocommerce_nav_cart) {
			add_filter('add_to_cart_fragments', array($this, 'cart_menu_fragment'));
			add_filter('wp_nav_menu_items', array($this, 'add_cart_icon'), 10, 2);
		}
		
		// comments form
		add_filter('woocommerce_product_review_comment_form_args', array($this, 'comment_form'), 10, 1);
		
		// related post count
		add_filter('woocommerce_output_related_products_args', array($this, 'related_posts'));
		
		// upsell count
		remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
		add_action('woocommerce_after_single_product_summary', array($this, 'output_upsells'), 15);
		
			
		add_action('wp_enqueue_scripts', array($this, 'remove_lightbox'), 99);
		
		// remove page meta from end-points
		add_filter('bunyad_metabox_page_options', array($this, 'remove_page_options'));
		
		// per page settings?
		if (Bunyad::options()->woocommerce_per_page) {
			add_filter(
				'loop_shop_per_page', 
				create_function('$cols', 'return ' . intval(Bunyad::options()->woocommerce_per_page) . ';'), 
				20
			);
		}
	}
	
	/**
	 * Set a default or selected sidebar for WooCommerce pages and archives
	 */
	public function set_sidebar() 
	{
		if (!(is_woocommerce() || is_checkout() || is_cart() || is_account_page())) {
			return;
		}
		
		$layout = '';
		
		// archives and single
		if (is_woocommerce() && !is_product()) {
			$layout = Bunyad::posts()->meta('layout_style', wc_get_page_id('shop'));
		}
		
		// checkout, cart, account and single product pages (not enabled atm)
		if (is_checkout() || is_cart() || is_account_page() || is_product()) {
			// set layout
			$layout = Bunyad::posts()->meta('layout_style');
		}
		
		// have a layout setting?
		if ($layout) {
			Bunyad::core()->set_sidebar(($layout == 'full' ? 'none' : $layout));
		}
		else { 
			// use default sidebar setting for WooCommerce
			Bunyad::core()->set_sidebar(Bunyad::options()->woocommerce_sidebar);
		}
	}
	
	/**
	 * Action callback: Add WooCommerce assets
	 */
	public function register_assets()
	{
		wp_enqueue_style('smartmag-woocommerce', get_template_directory_uri() . '/css/' . (is_rtl() ? 'rtl-' : '') . 'woocommerce.css'); 
	}
	
	/**
	 * Action callback: Disable WooCommerce lightbox and use the internal one
	 */
	public function remove_lightbox()
	{
		wp_dequeue_style('woocommerce_prettyPhoto_css');
		wp_dequeue_script('prettyPhoto-init');
		wp_dequeue_script('prettyPhoto');
	}
	
	/**
	 * Filter callback: Modify the WooCommerce comment form
	 */
	public function comment_form($args)
	{
		$commenter = wp_get_current_commenter();
		
		$args = array_merge($args, array(
			'title_reply'    => '<span class="section-head">' . (have_comments() ? _x('Add a review', 'woocommerce', 'bunyad') : _x('Be the first to review', 'woocommerce', 'bunyad') . ' &ldquo;' . get_the_title() . '&rdquo;') . '</span>',
			'title_reply_to' => '<span class="section-head">' . _x( 'Leave a Reply to %s', 'woocommerce', 'bunyad') . '</span>',
			/*'fields'  => array(
				'author' => '<p class="comment-form-author">' .
				            '<input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30" aria-required="true" placeholder="'. esc_attr__('Name', 'woocommerce') .'"/></p>',
				'email'  => '<p class="comment-form-email">' .
				            '<input id="email" name="email" type="text" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" aria-required="true" placeholder="'. esc_attr__('Email', 'woocommerce') .'"/></p>',
			),*/

		));
		
		return $args;
	}
	
	/**
	 * Filter callback: Change the related post count
	 */
	public function related_posts($args)
	{
		$args = array_merge($args, array(
			'posts_per_page' => (Bunyad::core()->get_sidebar() == 'none' ? 4 : 3),
			'columns' => (Bunyad::core()->get_sidebar() == 'none' ? 4 : 3),
		));
		
		return $args;
	}
	
	/**
	 * Output upsell products in correct number and columns
	 */
	public function output_upsells() 
	{
		
		$number = Bunyad::core()->get_sidebar() == 'none' ? 4 : 3;
		woocommerce_upsell_display($number, $number);
	}
	
	/**
	 * Setup image sizes for WooCommerce
	 */
	public function image_sizing()
	{
		// keeping extra room for responsive versions
	  	$catalog = array(
			'width' 	=> '300',	// px
			'height'	=> '300',	// px - 224px resize
			'crop'		=> 1 		// true
		);
	
		$single = array(
			'width' 	=> '600',	// px
			'height'	=> '600',	// px
			'crop'		=> 1 		// true
		);
	
		$thumbnail = array(
			'width' 	=> '180',	// px
			'height'	=> '180',	// px 
			'crop'		=> 1 		// false
		);

		// Image sizes
		update_option('shop_catalog_image_size', $catalog); 		// Product category thumbs
		update_option('shop_single_image_size', $single); 		// Single product image
		update_option('shop_thumbnail_image_size', $thumbnail); 	// Image gallery thumbs	
	}
	
	/**
	 * Product listing columns in WooCommerce
	 */
	public function columns()
	{		
		return (Bunyad::core()->get_sidebar() == 'none' ? 4 : 3);
	}
	
	/**
	 * Add to Cart for product listing
	 */
	public function cart_icon()
	{
		?>

		<div class="product-thumb">

		<?php 
			echo woocommerce_get_product_thumbnail(); 
			wc_get_template('loop/add-to-cart.php'); 
		?>

		</div>
		
		<a href="<?php the_permalink(); ?>">
   		
		<?php
   		
	} // cart_icon()
	
	
	public function product_link_close() {
		echo '</a>';
	}
	
	/**
	 * Filter callback: Add cart icon to main menu
	 */
	public function add_cart_icon($items, $args) 
	{		
    	if ($args->theme_location == 'main') {
    		
    		ob_start();
    		
    	?>
        	<li class="shopping-cart menu-item menu-item-type-custom menu-item-object-custom">
        		<?php echo $this->cart_menu_link(); ?>
        		<?php the_widget('WC_Widget_Cart', 'title= ', array('before_widget' => '<div class="mega-menu cart-widget widget_shopping_cart">')); ?>	
        	</li>
        <?php
        	
        	$items .= ob_get_clean();
    	}
    	
    	return $items;		
	}
	
	/**
	 * Filter callback: Add cart menu icon fragment
	 */
	public function cart_menu_fragment($fragments)
	{
		$fragments['a.cart-link'] = $this->cart_menu_link();
			
		return $fragments;	
	}
	
	/**
	 * The cart menu link fragment
	 */
	public function cart_menu_link()
	{
		global $woocommerce;
		
		$count =  $woocommerce->cart->cart_contents_count;

		ob_start();

		?>
		
			<a href="<?php echo $woocommerce->cart->get_cart_url(); ?>" class="cart-link"><i class="fa fa-shopping-cart"></i>
				<span class="counter<?php echo ($count ? ' active' : ''); ?>"><?php echo $count; ?></span>
				<span class="text"><?php _e('Shopping Cart', 'bunyad'); ?></span></a>
		
		<?php 

		return ob_get_clean();
	}
	
	
	public function remove_page_options($options)
	{
		return $options;
	}
	
	
	/**
	 * Add extra settings to Theme Settings
	 * 
	 * @param array $options
	 */
	public function add_theme_options($options)
	{
		$extra_options = array(
			'title' => __('WooCommerce', 'bunyad'),
			'id'    => 'options-tab-woocommerce',
			'icon'  => 'dashicons-admin-tools',
			'sections' => array(
				array(
					'fields' => array(
						array(
							'name' 	  => 'woocommerce_sidebar',
							'label'   => __('WooCommerce Default Sidebar', 'bunyad'),
							'value'   => 'none',
							'desc'    => __('Specify the sidebar to use by default on WooCommerce listings and pages. This can be overriden per-page except for single page product.', 'bunyad'),
							'type'    => 'radio',
							'options' =>  array('none' => __('No Sidebar', 'bunyad'), 'right' => __('Right Sidebar', 'bunyad'))
						),
						
						array(
							'name' 	  => 'woocommerce_per_page',
							'label'   => __('Posts Per Page', 'bunyad'),
							'value'   => '',
							'desc'    => __('The number of posts to show per page on WooCommerce shop listing. Leave empty to use default WordPress posts per page settings.', 'bunyad'),
							'type'    => 'text',
						),
						
						array(
							'name' 	  => 'woocommerce_nav_cart',
							'label'   => __('Add Cart In Navigation', 'bunyad'),
							'value'   => 1,
							'desc'    => __('When enabled, a cart icon is shown in the navigation to the right side.', 'bunyad'),
							'type'    => 'checkbox',
						),
					)
				)
			)
		);
		
		// add to the main options array
		$options[] = $extra_options;
		
		/*foreach ($options as $key => $config) 
		{
			if ($config['id'] == 'options-tab-global') {
				array_splice($options[$key]['sections'][0]['fields'], 2, 0, array($option));				
				break;
			}
		}*/
		
		/*
		 * Dynamic CSS Selectors to update with style changes in Theme Settings
		 */
		$selectors['main'] = array(
			'body.woocommerce .main-wrap .button, body.woocommerce-page .main-wrap .button, .woocommerce.widget .button,
			.woocommerce #respond input#submit, body.woocommerce .main-wrap .button:hover, body.woocommerce .main-wrap .button:active,
			body.woocommerce-page .main-wrap .button:hover, body.woocommerce-page .main-wrap .button:active,
			.woocommerce.widget .button:active, .woocommerce.widget .button:hover, .woocommerce #respond input#submit:hover,
			.woocommerce #respond input#submit:active, .woocommerce ul.products .add_to_cart_button.added:after,
			.woocommerce span.onsale, .woocommerce-page span.onsale, .woocommerce .widget_price_filter .ui-slider .ui-slider-range,
			.woocommerce .widget_layered_nav ul li.chosen a, .woocommerce-page .widget_layered_nav ul li.chosen a, 
			.shopping-cart .counter, .navigation .menu .cart-widget .button' 
				=> 'background: %s',
		
			'.woocommerce .woocommerce-message, .woocommerce .woocommerce-error, .woocommerce .woocommerce-info, 
			.woocommerce-page .woocommerce-message, .woocommerce-page .woocommerce-error, .woocommerce-page .woocommerce-info,
			.woocommerce .related h2, .woocommerce-page .related h2, .woocommerce .checkout h3, .woocommerce-account .post-content h2, 
			.woocommerce-checkout .post-content h2, .woocommerce-account form > h3, .woocommerce ul.products li.product h3:before, 
			.woocommerce-page ul.products li.product h3:before, .cross-sells h2, .upsells h2'
				=> 'border-left-color: %s',
				
			'.woocommerce .widget_price_filter .ui-slider .ui-slider-handle' => 'border-color: %s',
				
			'.woocommerce div.product .woocommerce-tabs ul.tabs li.active a' => 'border-bottom-color: %s',
				
			'.woocommerce form .form-row .required, .woocommerce-page form .form-row .required, .woocommerce ul.products li.product .amount,
			.woocommerce div.product p.price, .cart_totals .order-total .amount, .woocommerce .star-rating:before, 
			.woocommerce-page .star-rating:before, .woocommerce .products .star-rating, .woocommerce #reviews .meta > strong,
			.woocommerce .comment-form-rating .stars a, .woocommerce .star-rating span, .woocommerce .product .price ins, 
			.woocommerce-page .product .price ins'
				=> 'color: %s'				
		);
		
		$selectors['main_font'] = 
			'.woocommerce .cart_totals h2, .woocommerce-page .cart_totals h2, .woocommerce .shipping_calculator h2,
			.woocommerce-page .shipping_calculator h2, .woocommerce .addresses .title h3, .woocommerce-page .addresses .title h3, 
			.woocommerce .related h2, .woocommerce-page .related h2, .woocommerce .checkout h3, .woocommerce-account .post-content h2, 
			.woocommerce-checkout .post-content h2, .woocommerce-account form > h3, .cross-sells h2';
		
		$selectors['contrast_font'] = 'ul.product_list_widget li a, .woocommerce ul.products li.product h3, .woocommerce-page ul.products li.product h3';
		
		/*
		 * Loop through and append the WooCommerce CSS selectors to main_color and post_title_font styling
		 */
		foreach ($options as $key => $group) {
			foreach ($group['sections'] as $section_key => $section) {
				foreach ($section['fields'] as $field_key => $field) {
					
					if (empty($field['name']) OR !in_array($field['name'], array('css_main_color', 'css_main_font', 'css_heading_font'))) {
						continue;
					}
					
					$cur_sel = &$options[$key]['sections'][$section_key]['fields'][$field_key]['css']['selectors'];
					
					// add main css selectors
					if ($field['name'] == 'css_main_color') {
						$cur_sel = array_merge((array) $cur_sel, $selectors['main']);
					}
					
					if ($field['name'] == 'css_main_font') {
						$cur_sel .= ', ' . $selectors['main_font'];
					}
					
					if ($field['name'] == 'css_heading_font') {
						$cur_sel .= ', ' . $selectors['contrast_font'];
					}
				}
			}
		}
		
		
		return $options;
	}
}
