<?php
/**
 * Theme Settings - All the relevant options!
 * 
 * @uses Bunyad_Admin_Options::render_options()
 */

return apply_filters('bunyad_theme_options', array(
	array(
		'title' => __('General Settings', 'bunyad-admin'),
		'id'    => 'options-tab-global',
		'icon'  => 'dashicons-admin-generic',
		'sections' => array(
			array(
				'fields' => array(
			
					array(
						'name'   => 'layout_style',
						'value' => 'full',
						'label' => __('Layout Style', 'bunyad-admin'),
						'desc'  => __('Select whether you want a boxed or a full-width layout. It affects every page and the whole layout.', 'bunyad-admin'),
						'type'  => 'select',
						'options' => array(
							'full' => __('Full Width', 'bunyad-admin'),
							'boxed' => __('Boxed', 'bunyad-admin'),
						),
					),

					array(
						'name' => 'default_sidebar',
						'label'   => __('Default Sidebar', 'bunyad-admin'),
						'value'   => 'right',
						'desc'    => __('Specify the sidebar to use by default. This can be overriden per-page or per-post basis when creating a page or post.', 'bunyad-admin'),
						'type'    => 'radio',
						'options' =>  array('none' => __('No Sidebar', 'bunyad-admin'), 'right' => __('Right Sidebar', 'bunyad-admin'))
					),
					
					array(
						'name'   => 'sticky_sidebar',
						'value' => 0,
						'label' => __('Sticky Sidebar', 'bunyad-admin'),
						'desc'  => __('Setting this to Yes will make sidebar sticky as the user scrolls.', 'bunyad-admin'),
						'type'  => 'checkbox'
					),
					
					array(
						'name'   => 'no_responsive',
						'value' => 0,
						'label' => __('Disable Responsive Layout', 'bunyad-admin'),
						'desc'  => __('Disabling responsive layout means mobile phones and tablets will no longer see a better optimized design. Do not disable this unless really necessary.', 'bunyad-admin'),
						'type'  => 'checkbox'
					),
					
					array(
						'name'   => 'header_custom_code',
						'value' => '',
						'label' => __('Head Code', 'bunyad-admin'),
						'desc'  => esc_html(__('This code will be placed before </head> tag in html. Useful if you have an external script that requires it.', 'bunyad-admin')),
						'type'  => 'textarea',
						'options' => array('cols' => 75, 'rows' => 10),
						'strip' => 'none',
					),
					
					array(
						'name'   => 'search_posts_only',
						'value' => 1,
						'label' => __('Limit Search To Posts', 'bunyad-admin'),
						'desc'  => __('WordPress, by default, uses pages and other posts types to display search results. Enabling this feature will limit it to posts only.', 'bunyad-admin'),
						'type'  => 'checkbox'
					),
					
					array(
						'name'   => 'editor_styling',
						'value' => 1,
						'label' => __('Post Editor Front-end Styles', 'bunyad-admin'),
						'desc'  => __('While not exactly same as front-end, this will make the post editor mimimc font sizes, blockquote styles, width etc. from the front-end.', 'bunyad-admin'),
						'type'  => 'checkbox'
					),
					
				),
			), // end section
			
			array(
				'title'  => __('Footer', 'bunyad-admin'),
				'fields' => array(
					
					array(
						'name'  => 'disable_footer',
						'label' => __('Disable Large Footer', 'bunyad-admin'),
						'desc'  => __('Setting this to yes will disable the large footer that appears above the lowest footer. Used to contain large widgets.', 'bunyad-admin'),
						'type'  => 'checkbox'
					),
					
					array(
						'name'  => 'disable_lower_footer',
						'label' => __('Disable Lower Footer', 'bunyad-admin'),
						'desc'  => __('Setting this to yes will disable the smaller footer at bottom.', 'bunyad-admin'),
						'type'  => 'checkbox' 
					),
					
					array(
						'name'  => 'footer_columns',
						'value' => '1/3+1/3+1/3',
						'label' => __('Footer Columns ', 'bunyad-admin'),
						'desc'  => __('Sets the columns width and number of columns. Other examples: 1/2+1/2, 1/4+1/4+1/2', 'bunyad-admin'),
						'type'  => 'text'
					),
					
					array(
						'name'   => 'footer_custom_code',
						'value' => '',
						'label' => __('Footer Code', 'bunyad-admin'),
						'desc'  => esc_html(__('This code will be placed before </body> tag in html. Use for Google Analytics or similar external scripts.', 'bunyad-admin')),
						'type'  => 'textarea',
						'options' => array('cols' => 75, 'rows' => 10),
						'strip' => 'none',
					),
				),
			
			), // end section
			
			array(
				'title'  => __('Favicons (optional)', 'bunyad-admin'),
				'fields' => array(
					array(
						'name'  => 'favicon',
						'label' => __('Favicon', 'bunyad-admin'),
						'desc'  => __('32x32px recommended. IMPORTANT: .ico file only!', 'bunyad-admin'),
						'type'    => 'upload',
						'options' => array(
							'type'  => 'image',
							'title' => __('Upload Favicon (.ico file)', 'bunyad-admin'), 
							'insert_label' => __('Use As Favicon', 'bunyad-admin')
						),
					),
					
					array(
						'name'  => 'apple_icon',
						'label' => __('Mobile Icon', 'bunyad-admin'),
						'desc'  => __('152x152 recommended in PNG format. This icon will be used when users add your '
								. 'website as a shortcut on mobile devices like iPhone, iPad, Android etc.', 'bunyad-admin'),
						'type'    => 'upload',
						'options' => array(
							'type'  => 'image',
							'title' => __('Upload Mobile Icon', 'bunyad-admin'), 
							'insert_label' => __('Use As Mobile Icon', 'bunyad-admin')
						),
					),
				)
			), // end section
						
		), // end sections
	),
	
	array(
		'title' => __('Header & Navigation', 'bunyad-admin'),
		'id'    => 'options-header',
		'icon'  => 'dashicons-welcome-widgets-menus',
		'sections' => array(
		
			array(
				'fields' => array(
					array(
						'name'  => 'header_style',
						'value' => 'default',
						'label' => __('Header Style', 'bunyad-admin'),
						'desc'  => __('Select the header style you want to use. <strong>Warning:</strong> It will adjust several related settings below on selection.', 'bunyad-admin'),
						'type'  => 'radio',
						'options' => array(
							'default'  => __('Default - Left Logo + Right Ad', 'bunyad-admin'),
							'centered' => __('Centered Logo', 'bunyad-admin'),
							'tech'     => __('Tech: Dark Topbar & Light Nav', 'bunyad-admin'),
						),
					),
					
					array(
						'name' => 'mobile_header',
						'value' => 'classic',
						'label' => __('Mobile Header Style', 'bunyad-admin'),
						'desc'  => __('The default header style varies a bit based on the selected header style, while the modern light stays consistent in its appearance. Modern Light only supports off-canvas menu.', 'bunyad-admin'),
						'type'    => 'select',
						'options' =>  array(
							'' => __('Default', 'bunyad-admin'),
							'modern' => __('Modern Light', 'bunyad-admin')
						),
						'events' => array('change' => array('value' => 'modern', 'actions' => array('set' => array('mobile_menu_type' => 'off-canvas'), 'hide' => 'mobile_menu_type')))
					),
				)
			),
			
			array(
				'title'  => __('Logo', 'bunyad-admin'),
				'fields' => array(
			
					array(
						'name'    => 'text_logo',
						'label'   => __('Logo Text', 'bunyad-admin'),
						'desc'    => __('It will be used if logo images are not provided below.', 'bunyad-admin'),
						'value'   => get_bloginfo('name'),
						'type'    => 'text',
					),
			
					array(
						'name'    => 'image_logo',
						'label'   => __('Logo Image', 'bunyad-admin'),
						'desc'    => __('By default, a text-based logo is created using your site title. But you can upload an image-based logo here.', 'bunyad-admin'),
						'type'    => 'upload',
						'options' => array(
							'type'  => 'image',
							'title' => __('Upload This Picture', 'bunyad-admin'), 
							'insert_label' => __('Use As Logo', 'bunyad-admin')
						),
					),
					
					array(
						'name'    => 'image_logo_retina',
						'label'   => __('Logo Image Retina (2x)', 'bunyad-admin'),
						'desc'    => __('The retina version is 2x version of the same logo above. This will be used for higher resolution devices like iPhone. Requires WP Retina 2x plugin.', 'bunyad-admin'),
						'type'    => 'upload',
						'options' => array(
							'type'  => 'image',
							'title' => __('Upload This Picture', 'bunyad-admin'), 
							'insert_label' => __('Use As Logo', 'bunyad-admin')
						),
					),
					
					array(
						'name'    => 'image_logo_mobile',
						'label'   => __('Mobile Logo (2x) - Optional', 'bunyad-admin'),
						'desc'    => __('Set if you wish to use a different logo for mobile devices. Use a 2x image (twice the size of your original logo) since most mobile phones have retina screens.', 'bunyad-admin'),
						'type'    => 'upload',
						'options' => array(
							'type'  => 'image',
							'title' => __('Upload This Picture', 'bunyad-admin'), 
							'insert_label' => __('Use As Logo', 'bunyad-admin')
						),
					),
										
					array(
						'name'  => 'sticky_nav_logo',
						'value' => 0,
						'label' => __('Enable Sticky Nav Logo', 'bunyad-admin'),
						'desc'  => __('Enabling this adds your logo to the sticky navigation. For image-based logos, you will have to upload a different logo due to color difference.', 'bunyad-admin'),
						'type'  => 'checkbox',
						'events' => array('change' => array('value' => 'checked', 'actions' => array('show' => 'image_logo_nav')))
					),
					
					array(
						'name'    => 'image_logo_nav',
						'label'   => __('Sticky Nav Logo Image (Optional)', 'bunyad-admin'),
						'desc'    => __('This logo image will be used in sticky navigation. Ignore this if you use a text-based logo. Recommended Size: 43x75 - PNG with transparent background.', 'bunyad-admin'),
						'type'    => 'upload',
						'options' => array(
							'type'  => 'image',
							'title' => __('Upload This Picture', 'bunyad-admin'), 
							'insert_label' => __('Use As Logo', 'bunyad-admin')
						),
						// conditionally shown by sticky_nav_logo 
					),					
				)
				
			), // end section
			
									
			array(
				'title'  => __('Top Bar', 'bunyad-admin'),
				'fields' => array(
			
					array(
						'name'  => 'disable_topbar',
						'value' => 0,
						'label' => __('Disable Top Bar', 'bunyad-admin'),
						'desc'  => __('Setting this to yes will disable the top bar element that appears above the logo area.', 'bunyad-admin'),
						'type'  => 'checkbox'
					),

					array(
						'name'  => 'topbar_style',
						'value' => '',
						'label' => __('Topbar Style', 'bunyad-admin'),
						'desc'  => __('You can use a light or a dark topbar.', 'bunyad-admin'),
						'type'  => 'select',
						'options' => array(
							''  => __('Light', 'bunyad-admin'),
							'dark' => __('Dark', 'bunyad-admin'),
						)
					),
					
					array(
						'name'  => 'disable_topbar_ticker',
						'value' => 0,
						'label' => __('Disable Top News Ticker', 'bunyad-admin'),
						'desc'  => __('Setting this to yes will disable the top bar news ticker', 'bunyad-admin'),
						'type'  => 'checkbox'
					),
					
					array(
						'name'  => 'topbar_ticker_text',
						'value' => __('Trending', 'bunyad'),
						'label' => __('Topbar Ticker Text', 'bunyad-admin'),
						'desc'  => __('Enter the text you wish to display before the headlines in the ticker.', 'bunyad-admin'),
						'type'  => 'text'
					),
					
					array(
						'name'  => 'topbar_search',
						'value' => 1,
						'label' => __('Show Search Box', 'bunyad-admin'),
						'desc'  => __('Enabling search shows a search box in the top bar.', 'bunyad-admin'),
						'type'  => 'checkbox'
					),
					
					array(
						'name'  => 'topbar_live_search',
						'value' => 1,
						'label' => __('Enable Live Search', 'bunyad-admin'),
						'desc'  => __('Live search shows results using AJAX as you type, in the top bar search.', 'bunyad-admin'),
						'type'  => 'checkbox',
						'events' => array('change' => array('value' => 'checked', 'actions' => array('show' => 'live_search_number')))
					),
					
					array(
						'name'   => 'live_search_number',
						'value'  => 4,
						'label'  => __('Live Search Results', 'bunyad-admin'),
						'desc'   => __('Set the number of results to show when using the live search.', 'bunyad-admin'),
						'type'   => 'number',
					)
				)
			), // end section 
			
												
			array(
				'title'  => __('Navigation', 'bunyad-admin'),
				'fields' => array(
			
					array(
						'name'  => 'nav_style',
						'value' => '',
						'label' => __('Navigation Style', 'bunyad-admin'),
						'desc'  => __('The default style is a dark navigation. The light variation is a completely different navigation style and is only compatible with Default and Tech skin.', 'bunyad-admin'),
						'type'  => 'select',
						'options' => array(
							'' => __('Default Dark', 'bunyad-admin'),
							'nav-light' => __('Modern Light', 'bunyad-admin'),
						),
						// light nav doesn't support half-width 
						'events' => array('change' => array('value' => 'nav-light', 'actions' => array('set' => array('nav_layout' => 'nav-full'), 'hide' => 'nav_layout')))
					),
			
					array(
						'name'  => 'nav_align',
						'value' => '',
						'label' => __('Navigation Alignment', 'bunyad-admin'),
						'desc'  => __('You can center the top-level navigation items or keep them left-aligned.', 'bunyad-admin'),
						'type'  => 'select',
						'options' => array(
							'' => __('Left', 'bunyad-admin'),
							'nav-center' => __('Centered', 'bunyad-admin'),
						),
					),
					
					array(
						'name'  => 'nav_layout',
						'value' => '',
						'label' => __('Navigation Width', 'bunyad-admin'),
						'desc'  => __('Whether to make navigation container half or full-width.', 'bunyad-admin'),
						'type'  => 'select',
						'options' => array(
							'' => __('Default', 'bunyad-admin'),
							'nav-full' => __('Full Width', 'bunyad-admin'),
						),
					),

					array(
						'name' => 'sticky_nav',
						'value' => '',
						'label' => __('Sticky Navigation', 'bunyad-admin'),
						'desc'  => __('This makes navigation fixed when the user scrolls below the fold - making nav menu always visible. Normal is the behavior described above, but Smart changes beahavior to make it sticky only when the user scrolls up.', 'bunyad-admin'),
						'type'  => 'select',
						'options' => array(
							'' => __('Disabled', 'bunyad-admin'),
							'normal' =>  __('Normal', 'bunyad-admin'),
							'smart'  => __('Smart - Show only when scrolling up', 'bunyad-admin'),
						),
					),
					
					array(
						'name'  => 'nav_search',
						'value' => 0,
						'label' => __('Search in Navigation', 'bunyad-admin'),
						'desc'  => __('Enabling it shows a search icon in the navigation bar.' , 'bunyad-admin'),
						'type'  => 'checkbox'
					),
					
					array(
						'name' => 'mobile_nav_search',
						'value' => 1,
						'label' => __('Enable Search on Mobile Menu', 'bunyad-admin'),
						'desc'  => __('Disabling this will remove the search icon from the mobile navigation menu.', 'bunyad-admin'),
						'type'  => 'checkbox',
					),
					
					array(
						'name' => 'mobile_menu_type',
						'value' => 'classic',
						'label' => __('Mobile Menu Type', 'bunyad-admin'),
						'desc'  => __('Select the mobile menu you wish to use. The classic menu expands below the mobile navigation. The off-canvas menu appears in a mobile app style at the left side.', 'bunyad-admin'),
						'type'    => 'radio',
						'options' =>  array(
							'classic' => __('Classic Menu', 'bunyad-admin'),
							'off-canvas' => __('Off-Canvas Menu', 'bunyad-admin')
						)
					),

					array(
						'name' => 'disable_breadcrumbs',
						'value' => 0,
						'label' => __('Disable Breadcrumbs', 'bunyad-admin'),
						'desc'  => __('Breadcrumbs are a hierarchy of links displayed below the main navigation. They are displayed on all pages but the home-page.', 'bunyad-admin'),
						'type'  => 'checkbox',
					),
				)
				
			), // end section

		), // end sections
	),
	
	array(
		'title' => __('Listing Layouts', 'bunyad-admin'),
		'id'    => 'options-listing-layouts',
		'icon'  => 'dashicons-list-view',
		'sections' => array(
	
			array(
				'fields' => array(
			
					array(
						'name'   => 'pagination_type',
						'value'  => '',
						'label'  => __('Pagination Type On Archives', 'bunyad-admin'),
						'desc'   => __('Sets pagination type on all archives such as category listings. Infinite scroll loads more posts as you scroll.', 'bunyad-admin'),
						'type'   => 'radio',
						'options' =>  array(
							'' => __('Normal Pagination', 'bunyad-admin'),
							'infinite' => __('Infinite Scroll', 'bunyad-admin')
						)
					),
			
					array(
						'name' => 'default_cat_template',
						'label'   => __('Default Category Style', 'bunyad-admin'),
						'value'   => 'modern',
						'desc'    => __('The style to use for listing while browsing categories. This can be overriden while creating or editing a category.', 'bunyad-admin'),
						'type'    => 'select',
						'options' =>  array(
							'modern' => __('Modern Style - 2 Column', 'bunyad-admin'),
							'modern-3' => __('Modern Style - 3 Column', 'bunyad-admin'),
							'grid-overlay' => __('Grid Overlay - 2 Column', 'bunyad-admin'),
							'grid-overlay-3' => __('Grid Overlay - 3 Column', 'bunyad-admin'),
							'tall-overlay'   => __('Tall Grid Overlay', 'bunyad-admin'),
							'alt' => __('Blog Style', 'bunyad-admin'),
							'classic'  => __('Classic - Large Blog Style', 'bunyad-admin'),
							'timeline' => __('Timeline Style', 'bunyad-admin'),	
						)
					),

					array(
						'name' => 'author_loop_template',
						'label'   => __('Author Listing Style', 'bunyad-admin'),
						'value'   => 'loop',
						'desc'    => __('This style is used while browsing author page.', 'bunyad-admin'),
						'type'    => 'select',
						'options' =>  array(
							'loop' => __('Modern Style - 2 Column', 'bunyad-admin'),
							'loop-3' => __('Modern Style - 3 Column', 'bunyad-admin'),
							'loop-grid-overlay' => __('Grid Overlay - 2 Column', 'bunyad-admin'),
							'loop-grid-overlay-3' => __('Grid Overlay - 3 Column', 'bunyad-admin'),
							'loop-tall-overlay'   => __('Tall Grid Overlay', 'bunyad-admin'),
							'loop-alt' => __('Blog Style', 'bunyad-admin'),
							'loop-classic'  => __('Classic - Large Blog Style', 'bunyad-admin'),
							'loop-timeline' => __('Timeline Style', 'bunyad-admin'),
						)
					),
				
					array(
						'name' => 'archive_loop_template',
						'label'   => __('Archive Listing Style', 'bunyad-admin'),
						'value'   => 'modern',
						'desc'    => __('This style is used while browsing tags, searching, date archives, and any other archives.', 'bunyad-admin'),
						'type'    => 'select',
						'options' =>  array(
							'modern' => __('Modern Style - 2 Column', 'bunyad-admin'),
							'modern-3' => __('Modern Style - 3 Column', 'bunyad-admin'),
							'grid-overlay' => __('Grid Overlay - 2 Column', 'bunyad-admin'),
							'grid-overlay-3' => __('Grid Overlay - 3 Column', 'bunyad-admin'),
							'tall-overlay'   => __('Tall Grid Overlay', 'bunyad-admin'),
							'alt' => __('Blog Style', 'bunyad-admin'),
							'classic'  => __('Classic - Large Blog Style', 'bunyad-admin'),
							'timeline' => __('Timeline Style', 'bunyad-admin'),
						)
					),
					
					array(
						'name'   => 'read_more',
						'value'  => 1,
						'label'  => __('Enable "Read More"', 'bunyad-admin'),
						'desc'   => __('This is global setting for read more. If this is disabled, the individual settings below will not apply.', 'bunyad-admin'),
						'type'   => 'checkbox'
					),
					
					array(
						'name' => 'meta_listing',
						'label'   => __('Meta In Listings', 'bunyad-admin'),
						'value'   => array('author' => 0, 'date' => 1, 'comments' => 1),
						'desc'    => __('Select which meta to show in post listings.', 'bunyad-admin'),
						'type'    => 'checkbox',
						'multiple' => array(
							'author'   => __('Author', 'bunyad-admin'),
							'date'     => __('Date', 'bunyad-admin'),
							'comments' => __('Comment Count', 'bunyad-admin'),
						),
					),
					
					array(
						'name' => 'meta_listing_overlay',
						'label'   => __('Meta In Overlay Listings', 'bunyad-admin'),
						'value'   => array('author' => 0, 'date' => 1, 'comments' => 0),
						'desc'    => __('Select which meta to show in post listings with overlays - Grid Overlay and Tall Overlay.', 'bunyad-admin'),
						'type'    => 'checkbox',
						'multiple' => array(
							'author' => __('Author', 'bunyad-admin'),
							'date'   => __('Date', 'bunyad-admin'),
						),
					),
					
					array(
						'name' => 'meta_listing_widgets',
						'label'   => __('Meta In Widgets', 'bunyad-admin'),
						'value'   => array('author' => 0, 'date' => 1, 'comments' => 1),
						'desc'    => __('Select which meta to show in post listings within widgets - such as latest posts.', 'bunyad-admin'),
						'type'    => 'checkbox',
						'multiple' => array(
							'author'   => __('Author', 'bunyad-admin'),
							'date'     => __('Date', 'bunyad-admin'),
							'comments' =>  __('Comment Count', 'bunyad-admin'),
						),
					),
					
					array(
						'name'  => 'meta_position',
						'value' => 'above',
						'type'  => ''
					),
				)
			), // end section
	
			array(
				'title'  => __('Modern Listing', 'bunyad-admin'),
				'fields' => array(

					array(
						'name'   => 'excerpt_length_modern',
						'value'  => 15,
						'label'  => __('Excerpt Length', 'bunyad-admin'),
						'desc'   => __('Set the excerpt length for this listing. By default, it is a word count length.', 'bunyad-admin'),
						'type'   => 'number',
					),
					
				)
			), // end section
			
			array(
				'title'  => __('Classic Large Blog Listing', 'bunyad-admin'),
				'fields' => array(

					array(
						'name'   => 'show_excerpts_classic',
						'value'  => 1,
						'label'  => __('Enable Excerpts', 'bunyad-admin'),
						'desc'   => sprintf(__('By default whole post is displayed unless %s is used in posts. When excerpts are enabled, manual or automatic excerpt is used.', 'bunyad-admin'), '&lt;!--more--&gt;'),
						'type'   => 'checkbox',
						'events' => array('change' => array('value' => 'checked', 'actions' => array('show' => 'excerpt_length_classic')))
					),
			
					array(
						'name'   => 'excerpt_length_classic',
						'value'  => 100,
						'label'  => __('Excerpt Length', 'bunyad-admin'),
						'desc'   => __('Set the excerpt length for this listing. By default, it is a word count length.', 'bunyad-admin'),
						'type'   => 'number'
					),					
					
					array(
						'name'   => 'social_icons_classic',
						'value'  => 0,
						'label'  => __('Show Social Icons', 'bunyad-admin'),
						'desc'   => __('Enabling this will show social icons in this listing style. Requires: Social icons to be enabled for single pages.', 'bunyad-admin'),
						'type'   => 'checkbox'
					),
					
				)
			), // end section
			
			array(
				'title'  => __('Traditional Blog Listing', 'bunyad-admin'),
				'fields' => array(

					array(
						'name'   => 'excerpt_length_alt',
						'value'  => 20,
						'label'  => __('Excerpt Length', 'bunyad-admin'),
						'desc'   => __('Set the excerpt length for this listing. By default, it is a word count length.', 'bunyad-admin'),
						'type'   => 'number'
					),
										
					array(
						'name'   => 'read_more_alt',
						'value'  => 1,
						'label'  => __('Show "Read More"', 'bunyad-admin'),
						'desc'   => __('Show read "More" links in listings of this type?', 'bunyad-admin'),
						'type'   => 'checkbox'
					),
					
				)
			), // end section
									
		), // end sections
	),
	
	array(
		'title' => __('Homepage & Blocks', 'bunyad-admin'),
		'id'    => 'options-homepage-blocks',
		'icon'  => 'dashicons-admin-home',
		'sections' => array(
		
			array(
				'fields' => array(

					array(
						'name'   => 'no_home_duplicates',
						'value'  => 0,
						'label'  => __('No Duplicate Posts In Homepage Blocks?', 'bunyad-admin'),
						'desc'   => __('If you have a lot of content or when you are using latest posts slider, you can see duplicate in featured area and homepage blocks. Setting this feature to Yes will remove duplicates.', 'bunyad-admin'),
						'type'   => 'checkbox'
					),					
				)
			), // end section
	
			array(
				'title'  => __('Excerpts', 'bunyad-admin'),
				'fields' => array(
			
					array(
						'name'   => 'excerpt_length_highlights',
						'value'  => 20,
						'label'  => __('Highlights Block Excerpt Length', 'bunyad-admin'),
						'desc'   => __('Set the excerpt length for this listing. By default, it is a word count length.', 'bunyad-admin'),
						'type'   => 'number'
					),
					
					array(
						'name'   => 'excerpt_length_news_focus',
						'value'  => 20,
						'label'  => __('News Focus Block Excerpt Length', 'bunyad-admin'),
						'desc'   => __('Set the excerpt length for this listing. By default, it is a word count length.', 'bunyad-admin'),
						'type'   => 'number'
					),
					
					array(
						'name'   => 'excerpt_length_focus_grid',
						'value'  => 30,
						'label'  => __('Focus Grid Block Excerpt Length', 'bunyad-admin'),
						'desc'   => __('Set the excerpt length for this listing. By default, it is a word count length.', 'bunyad-admin'),
						'type'   => 'number'
					),
					
				)
			), // end section
									
		), // end sections
	),
	
	array(
		'title' => __('Page/Post Settings', 'bunyad-admin'),
		'id'    => 'options-specific-pages',
		'icon'  => 'dashicons-admin-post',
		'sections' => array(
	
			array(
				'title'  => __('Single Post / Article Page', 'bunyad-admin'),
				'fields' => array(
					
					array(
						'name' => 'post_layout_template',
						'label'   => __('Default Posts Layout', 'bunyad-admin'),
						'value'   => 'classic',
						'desc'    => __('Default single post layout to use, unless explicitly overriden in the post options.', 'bunyad-admin'),
						'type'    => 'select',
						'options' =>  array(
							'classic' => __('Classic', 'bunyad-admin'),
							'cover' => __('Post Cover', 'bunyad-admin'),
							'classic-above' => __('Classic - Title First', 'bunyad-admin'),
						)
					),

					array(
						'name'   => 'lightbox_prettyphoto',
						'value'  => 1,
						'label'  => __('Enable prettyPhoto Lightbox', 'bunyad-admin'),
						'desc'   => __('When enabled, prettyPhoto lightbox will auto-bind to images such as featured images, WordPress galleries etc.', 'bunyad-admin'),
						'type'   => 'checkbox'
					),
			
					array(
						'name'   => 'show_featured',
						'value'  => 1,
						'label'  => __('Show Featured', 'bunyad-admin'),
						'desc'   => __('Disabling featured area will mean the featured image or video will no longer show at top of the article.', 'bunyad-admin'),
						'type'   => 'checkbox'
					),
					
					array(
						'name'   => 'css_first_paragraph',
						'value'  => '',
						'label'  => __('First Paragraph', 'bunyad-admin'),
						'desc'   => __('By default, the first paragraph is treated as a summary and thus emphasized with larger font. NOTE: Applies to classic post layout only.', 'bunyad-admin'),
						'css'    => array('selectors' => array('.post .post-content > p:first-child' => 'font-size: inherit; color: inherit;')),
						'type'   => 'radio',
						'options' => array(
							'' => __('Emphasized with larger font', 'bunyad-admin'),
							'normal' => __('Normal - same as other paragraphs', 'bunyad-admin'),
						)
					),
			
					array(
						'name'   => 'show_tags',
						'value'  => 0,
						'label'  => __('Show Tags', 'bunyad-admin'),
						'desc'   => __('Show tags below posts? We recommend using categories instead of tags.', 'bunyad-admin'),
						'type'   => 'checkbox'
					),
					
					array(
						'name'  => 'social_share',
						'value' => 1,
						'label' => __('Show Social Sharing', 'bunyad-admin'),
						'desc'  => __('Show twitter, facebook, etc. share images beneath posts?', 'bunyad-admin'),
						'type'  => 'checkbox'
					),
					
					array(
						'name'  => 'post_navigation',
						'value' => 0,
						'label' => __('Previous/Next Navigation?', 'bunyad-admin'),
						'desc'  => __('Enabling this will add a Previous and Next post link in the single post page.', 'bunyad-admin'),
						'type'  => 'checkbox'
					),
					
					array(
						'name'  => 'author_box',
						'value' => 1,
						'label' => __('Show Author Box', 'bunyad-admin'),
						'desc'  => __('Setting to No will disable author box displayed below posts on post page.', 'bunyad-admin'),
						'type'  => 'checkbox'
					),
					
				)
			), // end section
			
						
			array(
				'title'  => __('Related Posts', 'bunyad-admin'),
				'fields' => array(	
			
					array(
						'name'  => 'related_posts',
						'value' => 1,
						'label' => __('Show Related Posts', 'bunyad-admin'),
						'desc'  => __('Setting to No will disable the related posts that appear on the single post page.', 'bunyad-admin'),
						'type'  => 'checkbox'
					),
					
					
					array(
						'name'  => 'related_posts_by',
						'value' => 'cats',
						'label' => __('Related Posts By', 'bunyad-admin'),
						'desc'  => __('By default, related posts will be displayed by finding posts based on the categories of post being viewed. You can change it to tags.', 'bunyad-admin'),
						'type'  => 'select',
						'options' => array(
							'cats' => __('Categories', 'bunyad-admin'), 'tags' => __('Tags', 'bunyad-admin')
						)
					),
					
					array(
						'name'  => 'related_posts_yarpp',
						'value' => 0,
						'label' => __('Use YARPP Plugin? (Advanced)', 'bunyad-admin'),
						'desc'  => __('Enabling this will allow you to use YARPP (Yet Another Related Posts Plugin) with theme styling.', 'bunyad-admin'),
						'type'  => 'checkbox'
					),
				)
			),
			
			array(
				'title'  => __('Review Posts', 'bunyad-admin'),
				'fields' => array(					
					
					array(
						'name'  => 'review_show',
						'value' => 1,
						'label' => __('Show Rating In Listings', 'bunyad-admin'),
						'desc'  => __('On posts with reviews, show the verdict rating points in category/home-page listing?', 'bunyad-admin'),
						'type'  => 'checkbox'
					),
					
					array(
						'name'  => 'review_show_widgets',
						'value' => 1,
						'label' => __('Show Rating In Widgets/Sidebar', 'bunyad-admin'),
						'desc'  => __('On posts with reviews, show the verdict rating points in sidebar widgets?', 'bunyad-admin'),
						'type'  => 'checkbox'
					),
					
					array(
						'name'  => 'review_style',
						'value' => 'bar',
						'label' => __('Widgets & Blocks Review Style', 'bunyad-admin'),
						'desc'  => __('This setting affects review style in widgets & blocks. Bar & Points displays an overlay over the thumbnail, where as stars go next in an overlay or below title.', 'bunyad-admin'),
						'type'  => 'select',
						'options' => array(
							'bar' => __('Bar and Points Overlay', 'bunyad-admin'),
							'stars' => __('Stars', 'bunyad-admin')
						)
					),
					
					array(
						'name'  => 'user_rating',
						'value' => 1,
						'label' => __('Enable Users Ratings', 'bunyad-admin'),
						'desc'  => __('This feature adds a user rating area below criterion to allow readers to click and vote.', 'bunyad-admin'),
						'type'  => 'checkbox'
					),					
			
				)
			) // end section
						
		), // end sections
	),
	
	array(
		'title' => __('Typography', 'bunyad-admin'),
		'id'    => 'options-typography',
		'icon'  => 'dashicons-editor-spellcheck',
		'sections' => array(
	
			array(
				'title'  => __('General', 'bunyad-admin'),
				'desc'   => sprintf(__('Selecting a font will show a basic preview. Go to %s for more details. '
								. 'It is highly recommended that you choose fonts that have similar heights to '
								. 'the default fonts to maintain pleasing aesthetics.', 
								'bunyad-admin'), '<a href="http://www.google.com/webfonts" target="_blank">google fonts directory</a>'),
								
				'fields' => array(
					array(
						'name'   => 'css_main_font',
						'value' => 'Open Sans',
						'label' => __('Main Font Family', 'bunyad-admin'),
						'desc'  => __('This effects almost every element on the theme. Please use a family that has regular, semi-bold and bold style. You may '
									. 'want to set the same for "Blog Post & Pages Body" too.', 'bunyad-admin'),
						'type'  => 'typography',
						'css'   => array(
							'selectors' => 'body, .main .sidebar .widgettitle, .tabbed .tabs-list, h3.gallery-title, .comment-respond small, .main-heading, ' 
											. '.gallery-title, .section-head, .main-footer .widgettitle, .entry-title, .page-title'
						),
						'families' => true,
						'suggested' => array(
							'Open Sans' => 'Open Sans',
							'PT Sans' => 'PT Sans',
							'Lato' => 'Lato',
							'Roboto' => 'Roboto',
							'Merriweather Sans' => 'Merriweather Sans',
							'Ubuntu' => 'Ubuntu'							
						),
					),
					
					array(
						'name'   => 'css_heading_font',
						'value' => 'Roboto Slab',
						'label' => __('Contrast Font Family', 'bunyad-admin'),
						'desc'  => __('This font will apply to mainly post headlines in post pages, slider, homepage, etc.', 'bunyad-admin'),
						'type'  => 'typography',
						'css'   => array(
							'selectors' => 'h1, h2, h3, h4, h5, h6, .gallery-block .carousel .title a, .list-timeline .posts article, .posts-list .content > a, .block.posts a, 
								#bbpress-forums .bbp-topic-title, #bbpress-forums .bbp-forum-title, .bbpress.single-topic .main-heading, .navigate-posts .link'
						),
						'families' => true,
						'fallback_stack' => 'Georgia, serif',
					),
					
					array(
						'name'   => 'css_post_body_font',
						'value' => 'Open Sans:regular',
						'label' => __('Blog Post & Pages Body', 'bunyad-admin'),
						'desc'  => __('Pages and blog posts body can also use a font of your choice. Readability is cruicial. Choose wisely.', 'bunyad-admin'),
						'type'  => 'typography',
						'css'   => array('selectors' => '.post-content'),
						'size'  => array('value' => 13)
					),
					
					array(
						'name'   => 'css_navigation_font',
						'value' => 'Open Sans:regular',
						'label' => __('Navigation Font', 'bunyad-admin'),
						'desc'  => __('Change the font used in the navigation menu.', 'bunyad-admin'),
						'type'  => 'typography',
						'css'   => array('selectors' => '.navigation .menu'),
					),
					
					array(
						'name'   => 'css_listing_body_font',
						'value' => 'Open Sans:regular',
						'label' => __('Blocks & Listing Excerpts', 'bunyad-admin'),
						'desc'  => __('Affects the agebuilder blocks, and category listings\' excerpt that is displayed below the heading.', 'bunyad-admin'),
						'type'  => 'typography',
						'css'   => array('selectors' => '.highlights .excerpt, .listing-alt .content .excerpt'),
						'size'  => array('value' => 13)
					),
					
					array(
						'name'   => 'css_post_title_font',
						'value' => 'Open Sans:regular',
						'label' => __('Pages & In-Post Headings', 'bunyad-admin'),
						'desc'  => __('Changing this will affect the font used for pages heading and heading h1-h6 used within posts or default template pages.', 'bunyad-admin'),
						'type'  => 'typography',
						'css'   => array('selectors' => '.post-header h1, .post-content h1, .post-content h2, .post-content h3, .post-content h4, .post-content h5, .post-content h6')
					),
					
				),
			), // end section
			
			array(
				'title'  => __('Content Heading Sizes', 'bunyad-admin'),
				'desc'   => __('These sizes affects the heading of fonts used within posts.', 'bunyad-admin'),
								
				'fields' => array(
					array(
						'name'   => 'css_post_h1',
						'value' =>  24,
						'label' => __('H1 Size', 'bunyad-admin'),
						'desc'  => __('h1 size for in-post headings.', 'bunyad-admin'),
						'type'  => 'number',
						'css'   => array(
							'selectors' => array('.post-content h1' => 'font-size: %spx;')
						),
					),
					
					array(
						'name'   => 'css_post_h2',
						'value' => 21,
						'label' => __('H2 Size', 'bunyad-admin'),
						'desc'  => __('h2 size for in-post headings.', 'bunyad-admin'),
						'type'  => 'number',
						'css'   => array(
							'selectors' => array('.post-content h2' => 'font-size: %spx;')
						),
					),
					
					array(
						'name'   => 'css_post_h3',
						'value' => 18,
						'label' => __('H3 Size', 'bunyad-admin'),
						'desc'  => __('h3 size for in-post headings.', 'bunyad-admin'),
						'type'  => 'number',
						'css'   => array(
							'selectors' => array('.post-content h3' => 'font-size: %spx;')
						),
					),
					
					array(
						'name'   => 'css_post_h4',
						'value' => 16,
						'label' => __('H4 Size', 'bunyad-admin'),
						'desc'  => __('h4 size for in-post headings.', 'bunyad-admin'),
						'type'  => 'number',
						'css'   => array(
							'selectors' => array('.post-content h4' => 'font-size: %spx;')
						),
					),
					
					array(
						'name'   => 'css_post_h5',
						'value' => 15,
						'label' => __('H5 Size', 'bunyad-admin'),
						'desc'  => __('h5 size for in-post headings.', 'bunyad-admin'),
						'type'  => 'number',
						'css'   => array(
							'selectors' => array('.post-content h5' => 'font-size: %spx;')
						),
					),
					
					array(
						'name'   => 'css_post_h6',
						'value' => 14,
						'label' => __('H6 Size', 'bunyad-admin'),
						'desc'  => __('h6 size for in-post headings.', 'bunyad-admin'),
						'type'  => 'number',
						'css'   => array(
							'selectors' => array('.post-content h6' => 'font-size: %spx;')
						),
					),					
				),
			), // end section
			
			array(
				'title' => __('Advanced', 'bunyad-admin'),
				'fields' => array(
					array(
						'name' => 'font_charset',
						'label'   => __('Google Fonts Character Set', 'bunyad-admin'),
						'value'   => '',
						'desc'    => __('For some languages, you will need an extended character set. Please note, not all fonts will have the subset. Check the google font to make sure.', 'bunyad-admin'),
						'type'    => 'checkbox',
						'multiple' => array(
							'latin' => __('Latin', 'bunyad-admin'),
							'latin-ext' => __('Latin Extended', 'bunyad-admin'),
							'cyrillic'  => __('Cyrillic', 'bunyad-admin'),
							'cyrillic-ext'  => __('Cyrillic Extended', 'bunyad-admin'),
							'greek'  => __('Greek', 'bunyad-admin'),
							'greek-ext' => __('Greek Extended', 'bunyad-admin'),
							'vietnamese' => __('Vietnamese', 'bunyad-admin'),
						),
					),
					
					array(
						'name'  => 'font_awesome_cdn',
						'value' => 0,
						'label' => __('Use FontAwesome CDN', 'bunyad-admin'),
						'desc'  => __('FontAwesome is loaded locally by default. Using the CDN will save a the font and CSS download if the user has already visited a site that used FontAwesome.', 'bunyad-admin'),
						'type'  => 'checkbox'
					),
				),

			),
			
			array(
				'title'  => __('Adobe Typekit Fonts', 'bunyad-admin'),
				'desc'   => __('First, please get your Kit Javascript code from Typekit and add it to Theme Settings > General > Head Code.', 'bunyad-admin'),
				'fields' => array(
					array(
						'name' => 'fonts_typekit',
						'label'   => __('Font Family Names', 'bunyad-admin'),
						'value'   => '',
						'desc'    => __('A font name of "Source Sans Pro" should be source-sans-pro. You can click "Using fonts in CSS" link in Typekit Kit Editor to find exact name.', 'bunyad-admin'),
						'type'    => 'multiple',
						'sub_fields' => array(
							array('type' => 'text', 'label' => __('Font Name', 'bunyad-admin'))
						),
					),
				),
			),
			
			array(
				'title'  => __('Custom Fonts', 'bunyad-admin'),
				'desc'   => sprintf(__('You have to convert your font to the .woff format. Use <a href="%s">FontSquirrel WebFont Generator</a> to get a .woff file.', 'bunyad-admin'), 'http://www.fontsquirrel.com/tools/webfont-generator'),
				'fields' => array(
					array(
						'name' => 'fonts_custom',
						'label'   => __('Custom Fonts', 'bunyad-admin'),
						'value'   => '',
						'desc'    => __('Upload your font in woff format and enter the font name. You can upload your font file using FTP, or via Media > Add New and then paste the URL.', 'bunyad-admin'),
						'type'    => 'multiple',
						'sub_fields' => array(
							array(
								'label' => __('File URL', 'bunyad-admin'),
								'name'  => 'url',
								'type'  => 'text'
							),
							array(
								'label' =>__('Font Name', 'bunyad-admin'),
								'name'  => 'name',
								'type'  => 'text'
							),
						)
					),
				),
			),
						
		), // end sections
	),
	
	array(
		'title' => __('Skins & Styling', 'bunyad-admin'),
		'id'    => 'options-style-color',
		'icon'  => 'dashicons-admin-appearance',
		'sections' => array(
	
			array(
				//'title'  => __('Defaults', 'bunyad-admin'),
				'id' => 'defaults',
				'fields' => array(
					array(
						'name'   => 'predefined_style',
						'value' => '',
						'label' => __('Pre-defined Skin', 'bunyad-admin'),
						'desc'  => __('Select a predefined skin or create your own customized one below. <br /><br /><strong>WARNING:</strong> Changing to (and from) Tech skin requires running "Regenerate Thumbnails" after saving Settings.', 'bunyad-admin'),
						'type'  => 'select',
						'options' => array(
							'' => __('Default', ''),
							'light' => __('Light Scheme (Light Nav, Sidebar, Footer)', 'bunyad-admin'),
							'dark'  => __('Black Scheme (All Dark)', 'bunyad-admin'),
							'tech'  => __('Advanced: Tech Skin', 'bunyad-admin'),
						),
					),
					
					array(
						'label' => __('Reset Colors', 'bunyad-admin'),
						'desc'  => __('Clicking this button will reset all the color settings below to the default color settings.', 'bunyad-admin'),
						'type'  => 'html',
						'html' => "<input type='submit' class='button' id='reset-colors' name='reset-colors' data-confirm='" 
								. __('Do you really wish to reset colors to defaults?', 'bunyad-admin') . "' value='". __('Reset Colors', 'bunyad-admin') ."' />",
					),
				)
			), // end section
			
			array(
				'title' => __('General', 'bunyad-admin'),
				'fields' => array(		
					array(
						'name'  => 'css_main_color',
						'value' => '#e54e53',
						'label' => __('Theme Color', 'bunyad-admin'),
						'desc'  => __('It is the contrast color for the theme. It will be used for all links, menu, category overlays, main page and '
									. 'many contrasting elements.', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
									'::selection' => 'background: %s',
									'::-moz-selection' => 'background: %s',
									
									'.top-bar, .post-content .modern-quote, .mobile-head' => 'border-top-color: %s',

									'.trending-ticker .heading, .breadcrumbs .location, .gallery-title, .related-posts .section-head, 
									.post-content a, .comments-list .bypostauthor .comment-author a, .error-page 
									.text-404, .main-color, .section-head.prominent, .block.posts .fa-angle-right, a.bbp-author-name, .main-stars span:before,
									.main-stars, .recentcomments .url, .nav-light .menu > li:hover > a, .nav-light .menu > .current-menu-item > a, 
									.nav-light .menu > .current-menu-parent > a, .nav-light-search .active .search-icon, .nav-light-search .search-icon:hover,
									.nav-light .menu li li:hover > a, .nav-light .menu li li.current-menu-item > a, .nav-light .mega-menu .sub-nav li:hover > a, 
									.nav-light .menu .sub-nav li.current-menu-item > a' 
										=> 'color: %s',

									'.navigation .menu > li:hover > a, .navigation .menu >.current-menu-item > a, .navigation .menu > .current-menu-parent > a,
									.navigation .menu > .current-menu-ancestor > a, .tabbed .tabs-list .active a, .nav-search .search-icon:hover, 
									.nav-search .active .search-icon, .comment-content .reply, .sc-tabs .active a' 
										=> 'border-bottom-color: %s',
										
									'.main-featured .cat, .main-featured .pages .flex-active, .rate-number .progress, .highlights .rate-number .progress, 
									.main-pagination .current, .main-pagination a:hover, .cat-title, .sc-button-default:hover, .drop-caps, .review-box .bar,
									.review-box .overall, .post .read-more a, .button, .post-pagination > span' 
										=> 'background: %s',
									
									'.post-content .wpcf7-not-valid-tip, .main-heading, .review-box .heading, .post-header .post-title:before, 
									.highlights h2:before, div.bbp-template-notice, div.indicator-hint, div.bbp-template-notice.info, 
									.modal-header .modal-title, .entry-title, .page-title' 
										=> 'border-left-color: %s',

									'@media only screen and (max-width: 799px) { .navigation .mobile .fa' 
										=> 'background: %s',
							),
						)
					),
					
					
					array(
						'name'  => 'css_body_bg_color',
						'value' => '#eeeeee',
						'label' => __('Body Background Color', 'bunyad-admin'),
						'desc'  => __('Use light colors only in non-boxed layout. Setting a body background image below will override it.', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
								'body, body.boxed' => 'background-color: %s;',
							),
						)
					),
					
					array(
						'name'  => 'css_body_bg',
						'value' => '',
						'label' => __('Body Background', 'bunyad-admin'),
						'desc'  => __('Use light patterns in non-boxed layout. For patterns, use a repeating background. Use photo to fully cover the background with an image. Note that it will override the background color option.', 'bunyad-admin'),
						'css' => array(
							'selectors' => array(
								'body' => 'background-image: url(%s);',
								'body.boxed' => 'background-image: url(%s);',
							),
						),
						'type'    => 'upload',
						'options' => array(
							'type'  => 'image',
							'title' => __('Upload This Picture', 'bunyad-admin'), 
							'button_label' => __('Upload Image', 'bunyad-admin'),
							'insert_label' => __('Use as Background', 'bunyad-admin')
						),
						'bg_type' => array('value' => 'cover'),
					),
					
					array(
						'name'  => 'css_post_text_color',
						'value' => '#606569',
						'label' => __('Posts Main Text Color', 'bunyad-admin'),
						'desc'  => __('Text color applies to body text of posts and pages.', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
								'.post-content' => 'color: %s',
							),
						)
					),
					
					array(
						'name'  => 'css_listing_text_color',
						'value' => '#949697',
						'label' => __('Blocks & Listings Excerpt Color', 'bunyad-admin'),
						'desc'  => __('Text color applies to excerpt text displayed on homepage blocks and category listings.', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
								'.highlights .excerpt, .listing-alt .content .excerpt' => 'color: %s',
							),
						)
					),
					
					array(
						'name'  => 'css_headings_text_color',
						'value' => '#000000',
						'label' => __('Main Headings Color', 'bunyad-admin'),
						'desc'  => __('Applies to headings such as main post/page heading and all the in-post headings.', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
								'h1, h2, h3, h4, h5, h6' => 'color: %s',
								'.post-content h1, .post-content h2, .post-content h3, .post-content h4, .post-content h5, .post-content h6' => 'color: %s',
							),
						)
					),
					
					array(
						'name'  => 'css_links_color',
						'value' => '#e54e53',
						'label' => __('Posts Link Color', 'bunyad-admin'),
						'desc'  => __('Changes all the links color within posts and pages.', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
								'.post-content a' => 'color: %s',
							),
						)
					),
					
					array(
						'name'  => 'css_links_hover_color',
						'value' => '#19232d',
						'label' => __('Posts Link Hover Color', 'bunyad-admin'),
						'desc'  => __('This color is applied when you mouse-over a certain link.', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
								'.post-content a:hover' => 'color: %s',
							),
						)
					),
					
					array(
						'name'  => 'css_slider_bg_color',
						'value' => '#f2f2f2',
						'label' => __('Featured Slider Background Color', 'bunyad-admin'),
						'desc'  => __('Setting a body background pattern below will override it.', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
								'.main-featured' => 'background-color: %s; background-image: none;',
							),
						)
					),
					
					array(
						'name'  => 'css_slider_bg_pattern',
						'value' => '',
						'label' => __('Featured Slider Background', 'bunyad-admin'),
						'desc'  => __('Please use a background pattern that can be repeated. Note that it will override the background color option.', 'bunyad-admin'),
						'css' => array(
							'selectors' => array(
								'.main-featured' => 'background-image: url(%s);',
							),
						),
						'type'    => 'upload',
						'options' => array(
							'type'  => 'image',
							'title' => __('Upload This Picture', 'bunyad-admin'), 
							'button_label' => __('Upload Image', 'bunyad-admin'),
							'insert_label' => __('Use as Background', 'bunyad-admin')
						),
						'bg_type' => array('value' => 'repeat'),
					),
				),
			), // end section
			
			array(
				'title' => __('Header', 'bunyad-admin'),
				'fields' => array(
			
					array(
						'name'  => 'css_topbar_bg_color',
						'value' => '#f2f2f2',
						'label' => __('Top Bar Background Color', 'bunyad-admin'),
						'desc'  => __('Only applies if top bar is enabled.', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
								'.top-bar, .top-bar.dark' => 'background-color: %s;',
							),
						)
					),			
			
					array(
						'name'  => 'css_header_bg_color',
						'value' => '#ffffff',
						'label' => __('Header Background Color', 'bunyad-admin'),
						'desc'  => __('Setting a header background pattern below will override it.', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
								'.main-head' => 'background-color: %s; background-image: none;',
							),
						)
					),

					array(
						'name'  => 'css_header_bg_pattern',
						'value' => '',
						'label' => __('Header Background', 'bunyad-admin'),
						'desc'  => __('Please use a background pattern that can be repeated. Note that it will override the background color option.', 'bunyad-admin'),
						'css' => array(
							'selectors' => array(
								'.main-head' => 'background-image: url(%s);',
							),
						),
						'type'    => 'upload',
						'options' => array(
							'type'  => 'image',
							'title' => __('Upload This Picture', 'bunyad-admin'), 
							'button_label' => __('Upload Image', 'bunyad-admin'),
							'insert_label' => __('Use as Background', 'bunyad-admin')
						),
						'bg_type' => array('value' => 'repeat'),
					),
				),
			), // end section
			
			array(
				'title' => __('Navigation Menu', 'bunyad-admin'),
				'fields' => array(
			
					array(
						'name'  => 'css_menu_bg_color',
						'value' => '#19232d',
						'label' => __('Main Menu Background Color', 'bunyad-admin'),
						'desc'  => __('Menu background affects the top-level background only.', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
								'.navigation' => 'background-color: %s;',
					
								'@media only screen and (max-width: 799px) { .navigation .menu > li:hover > a, .navigation .menu > .current-menu-item > a, 
								.navigation .menu > .current-menu-parent > a' 
									=> 'background-color: %s;',
								
								'.navigation.sticky' => 'background: rgba(%s, 0.9);',
							),
						)
					),
					
					array(
						'name'  => 'css_menu_drop_bg',
						'value' => '#19232d',
						'label' => __('Menu Dropdowns Background Color', 'bunyad-admin'),
						'desc'  => __('Menu background color is only used when a background pattern is not specified below.', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
								'.navigation .mega-menu, .navigation .menu ul' => 'background-color: %s;',
					
								'@media only screen and (max-width: 799px) { .navigation .mega-menu.links > li:hover' 
									=> 'background-color: %s;',
							),
						)
					),
					
					array(
						'name'  => 'css_menu_hover_bg_color',
						'value' => '#1e2935',
						'label' => __('Menu Hover/Current Background Color', 'bunyad-admin'),
						'desc'  => __('This is the background color used when you hover a menu item.  It is not used for top-level active state.', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
								'.navigation .menu > li:hover, .navigation .menu li li:hover, .navigation .menu li li.current-menu-item,
								.navigation .mega-menu .sub-nav li:hover, .navigation .menu .sub-nav li.current-menu-item' 
										=> 'background-color: %s;',
										
								'@media only screen and (max-width: 799px) { .navigation .menu > li:hover > a, .navigation .menu > .current-menu-item > a, 
									.navigation .menu > .current-menu-parent > a, .navigation .mega-menu.links > li:hover,
									.navigation .menu > .current-menu-ancestor > a, .navigation .menu li.active' 
										=> 'background-color: %s;',
							),
						)
					),
					
					array(
						'name'  => 'css_menu_big_border_color',
						'value' => '#2f4154',
						'label' => __('Menu Border Below', 'bunyad-admin'),
						'desc'  => __('Navigation menu has a 3 pixel border below it. Changing this color will only affect that border.', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
								'.navigation' => 'border-color: %s;', 
							),
						)
					),
										
					
					array(
						'name'  => 'css_menu_borders_color',
						'value' => '#1f2c38',
						'label' => __('Menu Items Border Color', 'bunyad-admin'),
						'desc'  => __('Menu items on drop down are separated by a border.', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
								'.navigation .menu > li li a, .navigation .mega-menu.links > li > a, .navigation .mega-menu.links > li li a,
								.mega-menu .posts-list .content, .navigation .mega-menu .sub-nav li a' 
										=> 'border-color: %s;', 
										
								'@media only screen and (max-width: 799px) { .navigation .menu li a' => 'border-color: %s;',  
							),
						)
					),
					
					
					array(
						'name'  => 'css_mega_menu_borders',
						'value' => '#2f4154',
						'label' => __('Mega Menu Headings Border Color', 'bunyad-admin'),
						'desc'  => __('Mega Menu items have a distinct color on border.', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
								'.mega-menu .heading, .navigation .mega-menu.links > li > a' 
										=> 'border-color: %s;', 
							),
						)
					),
					
					array(
						'name'  => 'css_mega_menu_subnav',
						'value' => '#1e2935',
						'label' => __('Mega Menu Left Sub-Categories Background', 'bunyad-admin'),
						'desc'  => __('Mega Menu has a distinct background for its sub-navigation at the left side..', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
								'.navigation .mega-menu .sub-nav' 
										=> 'background: %s;', 
							),
						)
					),
					
					array(
						'name'  => 'css_menu_text_color',
						'value' => '#efefef',
						'label' => __('Menu Text Color', 'bunyad-admin'),
						'desc'  => __('Applies to top menu items. Does not apply to drop down.', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
								'.navigation a, .mega-menu .heading, .mega-menu .featured h2 a' => 'color: %s;',
							),
						)
					),
					
				),
			), // end section
			
			array(
				'title' => __('Main Sidebar', 'bunyad-admin'),
				'fields' => array(
			
					array(
						'name'  => 'css_sidebar_heading_bg_color',
						'value' => '#19232d',
						'label' => __('Sidebar Heading Background', 'bunyad-admin'),
						'desc'  => __('Sidebar heading background color affects all the headings in the main sidebar.', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
								'.main .sidebar .widgettitle, .tabbed .tabs-list' => 'background-color: %s;',
							),
						)
					),

					array(
						'name'  => 'css_sidebar_heading_color',
						'value' => '#efefef',
						'label' => __('Sidebar Heading Color', 'bunyad-admin'),
						'desc'  => __('Change color of headings/widget titles in the main sidebar.', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
								'.main .sidebar .widgettitle, .tabbed .tabs-list a' => 'color: %s',
							),
						)
					),					
				),
			), // end section
			
			array(
				'title' => __('Footer', 'bunyad-admin'),
				'fields' => array(
			
					array(
						'name'  => 'css_footer_bg_color',
						'value' => '#19232d',
						'label' => __('Footer Background Color', 'bunyad-admin'),
						'desc'  => __('Footer background color is only used when a background pattern is not specified below.', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
								'.main-footer' => 'background-color: %s; background-image: none;',
							),
						)
					),

					array(
						'name'  => 'css_footer_bg_pattern',
						'value' => '',
						'label' => __('Footer Background Pattern', 'bunyad-admin'),
						'desc'  => __('Please use a background pattern that can be repeated. Note that it will override the background color option.', 'bunyad-admin'),
						'css' => array(
							'selectors' => array(
								'.main-footer' => 'background-image: url(%s)',
							),
						),
						'type'    => 'upload',
						'options' => array(
							'type'  => 'image',
							'title' => __('Upload This Picture', 'bunyad-admin'), 
							'button_label' => __('Upload Pattern', 'bunyad-admin'),
							'insert_label' => __('Use as Background Pattern', 'bunyad-admin')
						),
					),

					array(
						'name'  => 'css_footer_headings_color',
						'value' => '#c5c7cb',
						'label' => __('Footer Headings Color', 'bunyad-admin'),
						'desc'  => __('Change color of headings in the footer.', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
								'.main-footer .widgettitle' => 'color: %s',
							),
						)
					),
					
					array(
						'name'  => 'css_footer_text_color',
						'value' => '#d7dade',
						'label' => __('Footer Text Color', 'bunyad-admin'),
						'desc'  => __('Affects color of text in the footer.', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
								'.main-footer, .main-footer .widget' => 'color: %s',
							),
						)
					),
					
					array(
						'name'  => 'css_footer_links_color',
						'value' => '#d7dade',
						'label' => __('Footer Links Color', 'bunyad-admin'),
						'desc'  => __('Affects color of links in the footer.', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
								'.main-footer .widget a' => 'color: %s',
							),
						)
					),
					
					array(
						'name'  => 'css_footer_lower_bg',
						'value' => '#121a21',
						'label' => __('Lower Footer Background Color', 'bunyad-admin'),
						'desc'  => __('Second footer uses this color in the background.', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
								'.lower-foot' => 'background-color: %s',
							),
						)
					),
					
					array(
						'name'  => 'css_footer_lower_text',
						'value' => '#8d8e92',
						'label' => __('Lower Footer Text Color', 'bunyad-admin'),
						'desc'  => __('Second footer uses this color for text.', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
								'.lower-foot' => 'color: %s',
							),
						)
					),
					
					array(
						'name'  => 'css_footer_lower_links',
						'value' => '#b6b7b9',
						'label' => __('Lower Footer Links Color', 'bunyad-admin'),
						'desc'  => __('Affects color of links in the footer.', 'bunyad-admin'),
						'type' => 'color',
						'css' => array(
							'selectors' => array(
								'.lower-foot a' => 'color: %s',
							),
						)
					),
					
				),
			), // end section
						
		), // end sections
	),
	
	array(
		'title' => __('Slider & Featured', 'bunyad-admin'),
		'id'    => 'options-slider',
		'icon'  => 'dashicons-format-gallery',
		'sections' => array(
	
			array(
				//'title'  => __('General', 'bunyad-admin'),
								
				'fields' => array(
					
					array(
						'name' => 'slider_animation',
						'label'   => __('Animation Type', 'bunyad-admin'),
						'value'   => 'fade',
						'desc'    => __('Set the type of animation to use for the slider. Does not apply to default slider.', 'bunyad-admin'),
						'type'    => 'select',
						'options' => array('fade' => __('Fade Animation', 'bunyad-admin'), 'slide' => __('Slide Animation', 'bunyad-admin')),
					),
					
					array(
						'name' => 'slider_slide_delay',
						'label'   => __('Slide Delay/Speed', 'bunyad-admin'),
						'value'   => 5000,
						'desc'    => __('Set the time a slide will be displayed for (in ms) before animating to the next one.', 'bunyad-admin'),
						'type'    => 'text',
					),
					
					array(
						'name' => 'slider_animation_speed',
						'label'   => __('Animation Speed', 'bunyad-admin'),
						'value'   => 600,
						'desc'    => __('Set the speed of animations in miliseconds. A valid number is required.', 'bunyad-admin'),
						'type'    => 'text',
					),
				)
					
			), // end section
			
			array(
				'title'  => __('Right Side Posts Grid (Default Slider only)', 'bunyad-admin'),
				'desc' => __('The right side posts grid consists of 3 blocks and shows the last 3 of the 8 posts marked ' 
							.'as featured. It is optional setting and does not apply to category sliders. It only applies ' 
							.'to homepage slider. This only applies to Default Slider!', 'bunyad-admin'),
								
				'fields' => array(
					
					array(
						'name' => 'featured_right_cat',
						'label'   => __('Show from a Category?', 'bunyad-admin'),
						'value'   => '',
						'desc'    => __('<strong>WARNING:</strong> If you limit by category, posts marked as featured will no longer be used for this area. Latest posts from this category will be displayed.', 'bunyad-admin'),
						'type'    => 'html',
						'html'    => wp_dropdown_categories(array(
							'show_option_all' => __('Not Limited', 'bunyad-admin'), 
							'hierarchical' => 1, 'order_by' => 'name', 'class' => 'widefat', 
							'name' => 'featured_right_cat', 'echo' => false,
							'selected' => Bunyad::options()->featured_right_cat
						))
					),
					
					array(
						'name' => 'featured_right_tag',
						'label'   => __('Show Posts by a Tag?', 'bunyad-admin'),
						'value'   => '',
						'desc'    => __('<strong>WARNING:</strong> If you limit by tag, posts marked as featured will no longer be used for this area. Only used it show by category is set to None.', 'bunyad-admin'),
						'type'    => 'text',
					),
				)
					
			), // end section
			
		), // end sections
	),
	
	array(
		'title' => __('Custom CSS', 'bunyad-admin'),
		'id'    => 'options-custom-css',
		'icon'  => 'dashicons-editor-code',
		'sections' => array(
	
			array(
				//'title'  => __('General', 'bunyad-admin'),
								
				'fields' => array(
					array(
						'name'   => 'css_custom',
						'value' => '',
						'label' => __('Custom CSS', 'bunyad-admin'),
						'desc'  => __('Custom CSS will be added at end of all other customizations and thus can be used to overwrite rules. Less chances of specificity wars.', 'bunyad-admin'),
						'type'  => 'textarea',
						'options' => array('cols' => 75, 'rows' => 15)
					),
					
					array(
						'name'   => 'css_custom_output',
						'value' => 'external',
						'label' => __('Output Method', 'bunyad-admin'),
						'desc'  => __('On-page is better for performance unless your Custom CSS is too large. If you are experiencing problem with a cache plugin or your Custom CSS is large, use external method.', 'bunyad-admin'),
						'type'  => 'select',
						'options' => array(
							'inline' => __('On-page (For few lines of Custom CSS)', 'bunyad-admin'),
							'external' => __('External (For a lot of Custom CSS)', 'bunyad-admin'),
						)
					),
					
				)
					
			), // end section
						
		), // end sections
	),
	
	array(
		'title' => __('Backup & Restore', 'bunyad-admin'),
		'id'    => 'options-backup-restore',
		'icon'  => 'dashicons-backup',
		'sections' => array(

			array(
				'fields' => array(
			
					array(
						'label'  => __('Backup / Export', 'bunyad-admin'),
						'desc'   => __('This allows you to create a backup of your options and settings. Please note, it will not backup anything else.', 'bunyad-admin'),
						'type'   => 'html',
						'html'   => "<input type='button' class='button' id='options-backup' value='". __('Download Backup', 'bunyad-admin') ."' />",
					),
					
					array(
						'label'  => __('Restore / Import', 'bunyad-admin'),
						'desc'   => __('<strong>It will override your current settings!</strong> Please make sure to select a valid backup file.', 'bunyad-admin'),
						'type'   => 'html',
						'html'   => "<input type='file' name='import_backup' id='options-restore' />",
					)
					
				),
			
			),
	
		),
	),
	

	array(
		'title' => __('Sample Import', 'bunyad-admin'),
		'id'    => 'options-sample-import',
		'icon'  => 'dashicons-download',
		'sections' => array(

			array(
				'title' => __('Import Sample Content', 'bunyad-admin'),
				'desc'  => __('Import sample content from SmartMag official demo site. It may take <strong>1-2</strong> minutes to complete.
					<strong>WARNING: Only use on an empty site and make sure you have enabled recommended plugins first! Existing widgets will NOT be deleted but it is a good idea to remove them.</strong>
					', 'bunyad-admin') . '<br /><br /><strong>Important:</strong> Once import is completed, you will have to install and run "Regenerate Thumbnails" plugin.',
				'fields' => array(
			
					array(
						'name'  => 'import_demo_type',
						'label' => __('Select Demo', 'bunyad-admin'),
						'value' => '',
						'type' => 'radio',
						'desc' => '',
						'options' => array('' => __('Default Demo', 'bunyad-admin'), 'tech' => __('Tech Demo', 'bunyad-admin')),
					),

					array(
						'name'  => 'import_media',
						'label' => __('Images & Media', 'bunyad-admin'),
						'value' => 1,
						'type' => 'radio',
						'desc' => 'When enabled, random public domain licensed (CC0) images will be imported.',
						'options' => array(0 => __('Skip images & media', 'bunyad-admin'), 1 => __('Import random images', 'bunyad-admin')),
					),

					array(
						'label'  => __('Start Import', 'bunyad-admin'),
						'desc'   => '',
						'type'   => 'html',
						'html'   => "
							<input type='hidden' name='import_demo' value='1' />
							<input type='hidden' name='import_demo_gen' value='0' />
							<p><input type='button' class='button-primary' id='options-demo-import' value='". __('Import Sample Data', 'bunyad-admin') ."' data-confirm='"
							. __('WARNING: Do not use this on site with existing content. Enable Bunyad plugins before importing. Do you really wish to import sample data?', 'bunyad-admin') . "'/></p>
						",
					),	
				),
			
			),
	
		),
	),
	
	/*array(
		'title' => 'Samples',
		'id' => 'samples',
		'sections' => array(
			array(
				'fields' => array(
				
					array(
						'name'  => 'css_heading_font',
						'value' => 'PT Sans:regular',
						'label' => __('Heading Font', 'bunyad-admin'),
						'desc'  => __('Please go to <a href="http://www.google.com/webfonts" target="_blank">google fonts directory</a> to find fonts, then make a selection.', 'bunyad-admin'),
						'type'  => 'typography',
						'css'   => array('selectors' => 'h1, h2, h3, h4, h5, h6'),
						'size'  => array('value' => '14'),
						'color' => array('value' => '#000'),
					),

					array(
						'name'  => 'css_heading_font_2',
						'value' => 'PT Sans:regular',
						'label' => __('Heading Font', 'bunyad-admin'),
						'desc'  => __('Please go to <a href="http://www.google.com/webfonts" target="_blank">google fonts directory</a> to find fonts, then make a selection.', 'bunyad-admin'),
						'type'  => 'typography'
					),
				
					// change to 'controls' to support multiple controls for an element
					array(
						'name'   => 'mega_menus',
						'label'  => __('Mega menus?', 'bunyad-admin'),
						'desc'   => __('Show mega menus with latest posts for categories present in the menu.', 'bunyad-admin'),
						'type'   => 'checkbox'
					),
					

					array(
						'name'    => 'mega_menus_radio',
						'label'   => __('Mega Menus?', 'bunyad-admin'),
						'desc'    => __('Show mega menus with latest posts for categories in the custom menu.', 'bunyad-admin'),
						'type'    => 'radio',
						'options' =>  array(0 => 'Unacceptable', 1 => 'Acceptable')
					),
					
					array(
						'name'    => 'nice_pic',
						'label'   => __('Need A Picture Here?', 'bunyad-admin'),
						'desc'    => __('This adds a header.', 'bunyad-admin'),
						'type'    => 'upload',
						'options' => array(
							'type'  => 'image',
							'title' => __('Upload This Picture', 'bunyad-admin'), 
							'insert_label' => __('Use As Header', 'bunyad-admin')
						),
					),
					
					array(
						'name'    => 'bg_color',
						'label'   => __('Background Color', 'bunyad-admin'),
						'desc'    => __('Change the background color of header.', 'bunyad-admin'),
						'type'    => 'color',
						'value'   => '#000'
					),
					
					array(
						'name'    => 'image_logo',
						'label'   => __('Upload A Logo Image (optional)', 'bunyad-admin'),
						'desc'    => __('By default, a text-based logo is created using your site title. But you can upload an image-based logo here.', 'bunyad-admin'),
						'type'    => 'upload',
						'options' => array(
							'type'  => 'image',
							'title' => __('Upload This Picture', 'bunyad-admin'), 
							'insert_label' => __('Use As Logo', 'bunyad-admin')
						),
					),
				)
			)
		),
	)*/
));