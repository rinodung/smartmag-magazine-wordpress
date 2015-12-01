<?php

// Based on Jigsaw plugin by Jared Novack (http://jigsaw.upstatement.com/)
class TheLib_1_0_17 {

	// --- Start of 5.2 compatibility functions

	/**
	 * Internal data collection used to pass arguments to callback functions.
	 * Only used for 5.2 version as alternative to closures.
	 * @var array
	 */
	protected $data = array();

	protected function _have( $key ) {
		return isset( $this->data[ $key ] );
	}

	protected function _add( $key, $value ) {
		if ( ! is_array( @$this->data[ $key ] ) ) {
			$this->data[ $key ] = array();
		}
		$this->data[ $key ][] = $value;
	}

	protected function _get( $key ) {
		if ( ! is_array( @$this->data[ $key ] ) ) {
			$this->data[ $key ] = array();
		}
		return $this->data[ $key ];
	}

	protected function _clear( $key ) {
		$this->data[ $key ] = array();
	}


	// --- End of 5.2 compatibility functions

	// --- Start of Session access

	protected function _sess_have( $key ) {
		return isset( $_SESSION[ '_lib_persist_' . $key ] );
	}

	protected function _sess_add( $key, $value ) {
		if ( ! is_array( @$_SESSION[ '_lib_persist_' . $key ] ) ) {
			$_SESSION[ '_lib_persist_' . $key ] = array();
		}
		$_SESSION[ '_lib_persist_' . $key ][] = $value;
	}

	protected function _sess_get( $key ) {
		if ( ! is_array( @$_SESSION[ '_lib_persist_' . $key ] ) ) {
			$_SESSION[ '_lib_persist_' . $key ] = array();
		}
		return $_SESSION[ '_lib_persist_' . $key ];
	}

	protected function _sess_clear( $key ) {
		unset( $_SESSION[ '_lib_persist_' . $key ] );
	}

	// --- End of Session access

	public function __construct() {
		if ( ! session_id() ) {
			if ( ! headers_sent() ) {
				session_start();
			}
		}

		// Check for persistent data from last request that needs to be processed.
		$this->check_persistent_data();
	}

	/**
	 * Returns the full URL to an internal CSS file of the code library.
	 *
	 * @since  1.0.0
	 * @private
	 * @param  string $file The filename, relative to this plugins folder.
	 * @return string
	 */
	protected function _css_url( $file ) {
		static $Url = null;

		if ( defined( 'WDEV_UNMINIFIED' ) && WDEV_UNMINIFIED ) {
			$file = str_replace( '.min.css', '.css', $file );
		}
		if ( null === $Url ) {
			$Url = plugins_url( 'css/', __FILE__ );
		}
		return $Url . $file;
	}

	/**
	 * Returns the full URL to an internal JS file of the code library.
	 *
	 * @since  1.0.0
	 * @private
	 * @param  string $file The filename, relative to this plugins folder.
	 * @return string
	 */
	protected function _js_url( $file ) {
		static $Url = null;

		if ( defined( 'WDEV_UNMINIFIED' ) && WDEV_UNMINIFIED ) {
			$file = str_replace( '.min.js', '.js', $file );
		}
		if ( null === $Url ) {
			$Url = plugins_url( 'js/', __FILE__ );
		}
		return $Url . $file;
	}

	/**
	 * Returns the full path to an internal php partial of the code library.
	 *
	 * @since  1.0.0
	 * @private
	 * @param  string $file The filename, relative to this plugins folder.
	 * @return string
	 */
	protected function _include_path( $file ) {
		static $Path = null;
		if ( null === $Path ) {
			$basedir = dirname( __FILE__ ) . '/';
			$Path = $basedir . 'inc/';
		}
		return $Path . $file;
	}



	/**
	 * Enqueue core UI files (CSS/JS).
	 *
	 * Defined modules:
	 *  - core
	 *  - scrollbar
	 *  - select
	 *  - vnav
	 *
	 * @since  1.0.0
	 * @param  string $modules The module to load.
	 * @param  string $onpage A page hook; files will only be loaded on this page.
	 */
	public function add_ui( $module = 'core', $onpage = null ) {
		switch ( $module ) {
			case 'core':
				$this->add_css( $this->_css_url( 'wpmu-ui.min.css' ), $onpage );
				$this->add_js( $this->_js_url( 'wpmu-ui.min.js' ), $onpage );
				break;

			case 'scrollbar':
				$this->add_js( $this->_js_url( 'tiny-scrollbar.min.js' ), $onpage );
				break;

			case 'select':
				$this->add_css( $this->_css_url( 'select2.min.css' ), $onpage );
				$this->add_js( $this->_js_url( 'select2.min.js' ), $onpage );
				break;

			case 'vnav':
				$this->add_css( $this->_css_url( 'wpmu-vnav.min.css' ), $onpage );
				$this->add_js( $this->_js_url( 'wpmu-vnav.min.js' ), $onpage );
				break;

			case 'media':
				$this->add_js( 'wpmu:media', $onpage );
				break;

			default:
				$ext = strrchr( $module, '.' );

				if ( defined( 'WDEV_UNMINIFIED' ) && WDEV_UNMINIFIED ) {
					$module = str_replace( '.min' . $ext, $ext, $module );
				}
				if ( '.css' === $ext ) {
					$this->add_css( $module, $onpage, 20 );
				} else if ( '.js' === $ext ) {
					$this->add_js( $module, $onpage, 20 );
				}
		}
	}

	/**
	 * Adds a variable to javascript.
	 *
	 * @since 1.0.7
	 * @param string $name Name of the variable
	 * @param mixed $data Value of the variable
	 */
	public function add_data( $name, $data, $onpage = null ) {
		if ( did_action( 'wp_enqueue_scripts' ) || did_action( 'admin_enqueue_scripts' ) ) {
			// Javascript sources already enqueued:
			// Directly output the data right now.
			printf(
				'<script>window.%1$s = %2$s;</script>',
				sanitize_html_class( $name ),
				json_encode( $data )
			);
		} else {
			// Enqueue the data for output with javascript sources.
			$this->_add( 'js_data', array( $name, $data ) );
			$this->_prepare_js_or_css( 'jquery', 'js', $onpage, 1 );
		}
	}

	/**
	 * Enqueue a javascript file.
	 *
	 * @since  1.0.0
	 * @param  string $url Full URL to the javascript file.
	 * @param  string $onpage A page hook; files will only be loaded on this page.
	 * @param  int $priority Loading order. The higher the number, the later it is loaded.
	 */
	public function add_js( $url, $onpage = null, $priority = 10 ) {
		$this->_prepare_js_or_css( $url, 'js', $onpage, $priority );
	}

	/**
	 * Enqueue a css file.
	 *
	 * @since  1.0.0
	 * @param  string $url Full URL to the css filename.
	 * @param  string $onpage A page hook; files will only be loaded on this page.
	 * @param  int $priority Loading order. The higher the number, the later it is loaded.
	 */
	public function add_css( $url, $onpage = null, $priority = 10 ) {
		$this->_prepare_js_or_css( $url, 'css', $onpage, $priority );
	}

	/**
	 * Prepare to enqueue a javascript or css file.
	 *
	 * @since  1.0.7
	 * @private
	 * @param  string $url Full URL to the javascript/css file.
	 * @param  string $type 'css' or 'js'
	 * @param  string $onpage A page hook; files will only be loaded on this page.
	 * @param  int $priority Loading order. The higher the number, the later it is loaded.
	 */
	protected function _prepare_js_or_css( $url, $type, $onpage, $priority ) {
		$hooked = $this->_have( 'js_or_css' );
		$this->_add( 'js_or_css', compact( 'url', 'type', 'onpage', 'priority' ) );

		if ( ! did_action( 'init' ) ) {
			$hooked || add_action(
				'init',
				array( $this, '_add_js_or_css' )
			);
		} else {
			$this->_add_js_or_css();
		}
	}

	/**
	 * Returns the JS/CSS handle of the item.
	 * This is a private helper function used by array_map()
	 *
	 * @since  1.0.7
	 * @private
	 */
	public function _get_script_handle( $item ) {
		return @$item->handle;
	}

	/**
	 * Enqueues either a css or javascript file
	 *
	 * @since  1.0.0
	 * @private
	 */
	public function _add_js_or_css() {
		global $wp_styles, $wp_scripts;

		$scripts = $this->_get( 'js_or_css' );
		$this->_clear( 'js_or_css' );

		// Prevent adding the same URL twice.
		$done_urls = array();

		foreach ( $scripts as $script ) {
			extract( $script ); // url, type, onpage, priority

			if ( 'front' === $onpage && is_admin() ) { continue; }

			// Prevent adding the same URL twice.
			if ( in_array( $url, $done_urls ) ) { continue; }
			$done_urls[] = $url;

			$type = ( 'css' === $type || 'style' === $type ? 'css' : 'js' );

			// The $handle values are intentionally not cached:
			// Any plugin/theme could add new handles at any moment...
			$handles = array();
			if ( 'css' == $type ) {
				if ( ! is_a( $wp_styles, 'WP_Styles' ) ) {
					$wp_styles = new WP_Styles();
				}
				$handles = array_values(
					array_map(
						array( $this, '_get_script_handle' ),
						$wp_styles->registered
					)
				);
				$type_callback = '_enqueue_style_callback';
			} else {
				if ( ! is_a( $wp_scripts, 'WP_Scripts' ) ) {
					$wp_scripts = new WP_Scripts();
				}
				$handles = array_values(
					array_map(
						array( $this, '_get_script_handle' ),
						$wp_scripts->registered
					)
				);
				$type_callback = '_enqueue_script_callback';
			}

			if ( in_array( $url, $handles ) ) {
				$alias = $url;
				$url = '';
			} else {
				// Get the filename from the URL, then sanitize it and prefix "wpmu-"
				$urlparts = explode( '?', $url, 2 );
				$alias = 'wpmu-' . sanitize_title( basename( $urlparts[0] ) );
			}
			$onpage = empty( $onpage ) ? '' : $onpage;

			if ( 'front' === $onpage && ! is_admin() ) {
				$hook = 'wp_enqueue_scripts';
			} else {
				$hook = 'admin_enqueue_scripts';
			}

			$item = compact( 'url', 'alias', 'onpage' );
			$hooked = $this->_have( $hook . $type );
			$this->_add( $type, $item );
			$this->_add( $hook . $type, true );

			if ( ! did_action( $hook ) ) {
				$hooked || add_action(
					$hook,
					array( $this, $type_callback ),
					100 + $priority // Load custom styles a bit later than core styles.
				);
			} else {
				$this->$type_callback();
			}
		}
	}

	/**
	 * Action hook for enqueue style (for PHP <5.3 only)
	 *
	 * @since  1.0.1
	 * @private
	 * @param  string $hook The current admin page that is rendered.
	 */
	public function _enqueue_style_callback( $hook = '' ) {
		$items = $this->_get( 'css' );

		if ( empty( $hook ) ) { $hook = 'front'; }

		foreach ( $items as $item ) {
			extract( $item ); // url, alias, onpage
			if ( '' !== $onpage && $hook !== $onpage ) { continue; }

			if ( empty( $url ) ) {
				wp_enqueue_style( $alias );
			} else {
				wp_enqueue_style( $alias, $url );
			}
		}
	}

	/**
	 * Action hook for enqueue script (for PHP <5.3 only)
	 *
	 * @since  1.0.1
	 * @private
	 * @param  string $hook The current admin page that is rendered.
	 */
	public function _enqueue_script_callback( $hook = '' ) {
		$items = $this->_get( 'js' );

		$data = $this->_get( 'js_data' );
		$this->_clear( 'js_data' );

		if ( empty( $hook ) ) { $hook = 'front'; }

		foreach ( $items as $item ) {
			extract( $item ); // url, alias, onpage

			if ( '' !== $onpage && $hook !== $onpage ) { continue; }

			// Load the Media-library functions.
			if ( 'wpmu:media' === $url ) {
				wp_enqueue_media();
				continue;
			}

			// Register script if it has an URL.
			if ( ! empty( $url ) ) {
				wp_register_script( $alias, $url, array( 'jquery' ), false, true );
			}

			// Append javascript data to the script output.
			if ( ! empty( $data ) ) {
				foreach ( $data as $item ) {
					wp_localize_script( $alias, $item[0], $item[1] );
				}
				$data = false;
			}

			// Enqueue the script for output in the page footer.
			wp_enqueue_script( $alias );
		}
	}



	/**
	 * Displays a WordPress pointer on the current admin screen.
	 *
	 * @since  1.0.0
	 * @param  string $pointer_id Internal ID of the pointer, make sure it is unique!
	 * @param  string $html_el HTML element to point to (e.g. '#menu-appearance')
	 * @param  string $title The title of the pointer.
	 * @param  string $body Text of the pointer.
	 */
	public function pointer( $pointer_id, $html_el, $title, $body ) {
		if ( ! is_admin() ) {
			return;
		}

		$this->_have( 'init_pointer' ) || add_action(
			'init',
			array( $this, '_init_pointer' )
		);
		$this->_add( 'init_pointer', compact( 'pointer_id', 'html_el', 'title', 'body' ) );
	}

	/**
	 * Action handler for plugins_loaded. This decides if the pointer will be displayed.
	 *
	 * @since  1.0.2
	 * @private
	 */
	public function _init_pointer() {
		$items = $this->_get( 'init_pointer' );
		foreach ( $items as $item ) {
			extract( $item );

			// Find out which pointer IDs this user has already seen.
			$seen = (string) get_user_meta(
				get_current_user_id(),
				'dismissed_wp_pointers',
				true
			);
			$seen_list = explode( ',', $seen );

			// Handle our first pointer announcing the plugin's new settings screen.
			if ( ! in_array( $pointer_id, $seen_list ) ) {
				$this->_have( 'pointer' ) || add_action(
					'admin_print_footer_scripts',
					array( $this, '_pointer_print_scripts' )
				);
				$this->_have( 'pointer' ) || add_action(
					'admin_enqueue_scripts',
					array( $this, '_enqueue_pointer' )
				);
				$this->_add( 'pointer', $item );
			}
		}
	}

	/**
	 * Enqueue wp-pointer (for PHP <5.3 only)
	 *
	 * @since  1.0.1
	 * @private
	 */
	public function _enqueue_pointer() {
		// Load the JS/CSS for WP Pointers
		wp_enqueue_script( 'wp-pointer' );
		wp_enqueue_style( 'wp-pointer' );
	}

	/**
	 * Action hook for admin footer scripts (for PHP <5.3 only)
	 *
	 * @since  1.0.1
	 * @private
	 */
	public function _pointer_print_scripts() {
		$items = $this->_get( 'pointer' );
		foreach ( $items as $item ) {
			extract( $item ); // pointer_id, html_el, title, body
			include $this->_include_path( 'pointer.php' );
		}
	}



	/**
	 * Display an admin notice.
	 *
	 * @since  1.0.0
	 * @param  string $text Text to display.
	 * @param  string $class Message-type [updated|error]
	 * @param  string $screen Limit message to this screen-ID
	 */
	public function message( $text, $class = '', $screen = '' ) {
		if ( 'red' == $class || 'err' == $class || 'error' == $class ) {
			$class = 'error';
		} else {
			$class = 'updated';
		}

		// Check if the message is already queued...
		$items = $this->_sess_get( 'message' );
		foreach ( $items as $key => $data ) {
			if (
				$data['text'] == $text &&
				$data['class'] == $class &&
				$data['screen'] == $screen
			) {
				return; // Don't add duplicate message to queue.
			}
		}

		$this->_sess_add( 'message', compact( 'text', 'class', 'screen' ) );

		if ( did_action( 'admin_notices' ) ) {
			$this->_admin_notice_callback();
		} else {
			$this->_have( '_admin_notice' ) || add_action(
				'admin_notices',
				array( $this, '_admin_notice_callback' ),
				1
			);
			$this->_add( '_admin_notice', true );
		}
	}

	/**
	 * Action hook for admin notices (for PHP <5.3 only)
	 *
	 * @since  1.0.1
	 * @private
	 */
	public function _admin_notice_callback() {
		$items = $this->_sess_get( 'message' );
		$this->_sess_clear( 'message' );
		$screen_info = get_current_screen();
		$screen_id = $screen_info->id;

		foreach ( $items as $item ) {
			extract( $item ); // text, class, screen
			if ( empty( $screen ) || $screen_id == $screen ) {
				echo '<div class="' . esc_attr( $class ) . '"><p>' . $text . '</p></div>';
			}
		}
	}



	/**
	 * Short way to load the textdomain of a plugin.
	 *
	 * @since  1.0.0
	 * @param  string $domain Translations will be mapped to this domain.
	 * @param  string $rel_dir Path to the dictionary folder; relative to ABSPATH.
	 */
	public function translate_plugin( $domain, $rel_dir ) {
		$hooked = $this->_have( 'textdomain' );

		$this->_add( 'textdomain', compact( 'domain', 'rel_dir' ) );

		if ( ! did_action( 'plugins_loaded' ) ) {
			$hooked || add_action(
				'plugins_loaded',
				array( $this, '_translate_plugin_callback' )
			);
		} else {
			$this->_translate_plugin_callback();
		}
	}

	/**
	 * Create function callback for load textdomain (for PHP <5.3 only)
	 *
	 * @since  1.0.1
	 * @private
	 */
	public function _translate_plugin_callback() {
		$items = $this->_get( 'textdomain' );
		foreach ( $items as $item ) {
			extract( $item ); // domain, rel_dir
			load_plugin_textdomain( $domain, false, $rel_dir );
		}
	}



	/**
	 * Checks the DB for persistent data from last request.
	 * If persistent data exists the appropriate hooks are set to process them.
	 *
	 * @since  1.0.7
	 */
	public function check_persistent_data() {
		// $this->message()
		if ( $this->_sess_have( 'message' ) ) {
			$this->_have( '_admin_notice' ) || add_action(
				'admin_notices',
				array( $this, '_admin_notice_callback' ),
				1
			);
			$this->_add( '_admin_notice', true );
		}
	}

	/**
	 * Returns the current URL.
	 * This URL is not guaranteed to look exactly same as the user sees it.
	 * E.g. Hashtags are missing ("index.php#section-a")
	 *
	 * @since  1.0.7
	 * @return string Full URL to current page.
	 */
	public function current_url() {
		$Url = null;

		if ( null === $Url ) {
			if ( isset( $_SERVER['HTTPS'] ) && strtolower( $_SERVER['HTTPS'] ) == 'on' ) {
				$Url .= 'https://';
			} else {
				$Url = 'http://';
			}

			if ( $_SERVER['SERVER_PORT'] != '80' ) {
				$Url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
			} else {
				$Url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
			}
			$Url = trailingslashit( $Url );
		}

		return $Url;
	}

	/**
	 * Adds a value to the data collection in the user session.
	 *
	 * @since  1.0.15
	 * @param  string $key The key of the value.
	 * @param  mixed $value Value to store.
	 */
	public function store_add( $key, $value ) {
		$this->_sess_add( 'store:' . $key, $value );
	}

	/**
	 * Returns the current data array of the specified value from user session.
	 *
	 * @since  1.0.15
	 * @param  string $key The key of the value.
	 * @return array The value, or an empty array if no value was assigned yet.
	 */
	public function store_get( $key ) {
		$vals = $this->_sess_get( 'store:' . $key );
		foreach ( $vals as $key => $val ) {
			if ( null === $val ) { unset( $vals[ $key ] ); }
		}
		$vals = array_values( $vals );
		return $vals;
	}

	/**
	 * Returns the current data array of the specified value from user session
	 * and then clears the values from the session.
	 *
	 * @since  1.0.15
	 * @param  string $key The key of the value.
	 * @return array The value, or an empty array if no value was assigned yet.
	 */
	public function store_get_clear( $key ) {
		$val = $this->store_get( $key );
		$this->_sess_clear( 'store:' . $key );
		return $val;
	}

	/**
	 * If the specified variable is an array it will be returned. Otherwise
	 * an empty array is returned.
	 *
	 * @since  1.0.14
	 * @param  mixed $val1 Value that maybe is an array.
	 * @param  mixed $val2 Optional, Second value that maybe is an array.
	 * @return array
	 */
	public function get_array( &$val1, $val2 = array() ) {
		if ( is_array( $val1 ) ) {
			return $val1;
		} else if ( is_array( $val2 ) ) {
			return $val2;
		} else {
			return array();
		}
	}

	/**
	 * Checks if the given array contains all the specified fields.
	 * If fields are not defined then they will be added to the source array
	 * with the boolean value false.
	 *
	 * This function is used to initialize optional fields.
	 * It is optimized and tested to yield best performance.
	 *
	 * @since  1.0.14
	 * @param  Array|Object $arr The array or object to check.
	 * @param  strings|Array $fields List of fields to check for.
	 * @return int Number of missing fields that were initialized.
	 */
	public function load_fields( &$arr, $fields ) {
		$missing = 0;
		$is_obj = false;

		if ( is_object( $arr ) ) { $is_obj = true; }
		else if ( ! is_array( $arr ) ) { return -1; }

		if ( ! is_array( $fields ) ) {
			$fields = func_get_args();
			array_shift( $fields ); // Remove $arr from the field list.
		}

		foreach ( $fields as $field ) {
			if ( $is_obj ) {
				if ( ! isset( $arr->$field ) ) {
					$arr->$field = false;
					$missing += 1;
				}
			} else {
				if ( ! isset( $arr[ $field ] ) ) {
					$arr[ $field ] = false;
					$missing += 1;
				}
			}
		}

		return $missing;
	}

	/**
	 * Short function for WDev()->load_fields( $_POST, ... )
	 *
	 * @since  1.0.14
	 * @param  strings|Array <param list>
	 * @return int Number of missing fields that were initialized.
	 */
	public function load_post_fields( $fields ) {
		$fields = is_array( $fields ) ? $fields : func_get_args();
		return $this->load_fields( $_POST, $fields );
	}

	/**
	 * Short function for WDev()->load_fields( $_REQUEST, ... )
	 *
	 * @since  1.0.14
	 * @param  strings|Array <param list>
	 * @return int Number of missing fields that were initialized.
	 */
	public function load_request_fields( $fields ) {
		$fields = is_array( $fields ) ? $fields : func_get_args();
		return $this->load_fields( $_REQUEST, $fields );
	}

	/**
	 * Displays a debug message at the current position on the page.
	 *
	 * @since  1.0.14
	 * @param mixed <dynamic> Each param will be dumped
	 */
	public function debug() {
		static $Need_styles = true;

		if ( ( ! defined( 'WDEV_DEBUG' ) || ! WDEV_DEBUG )
			&& ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG )
		) { return; }

		if ( $Need_styles ) {
			?>
			<style>
			.wdev-debug {
				break: both;
				border: 1px solid #C00;
				background: rgba(255, 200, 200, 0.8);
				padding: 10px;
				margin: 10px;
				position: relative;
				z-index: 99999;
				box-shadow: 0 1px 5px rgba(0,0,0,0.3);
				font-size: 12px;
			}
			.wdev-debug:before {
				content: 'DEBUG';
				font-size: 11px;
				position: absolute;
				right: 0;
				top: 0;
				color: #FFF;
				background-color: #D88;
				padding: 2px 8px;
			}
			.wdev-debug .wdev-debug-wrap {
				box-shadow: 0 1px 5px rgba(0,0,0,0.18);
			}
			.wdev-debug pre {
				font-size: 12px !important;
				margin: 1px 0 !important;
				background: rgba(255, 200, 200, 0.8);
			}
			</style>
			<?php
			$Need_styles = false;
		}

		echo '<div class="wdev-debug"><div class="wdev-debug-wrap">';
		foreach ( func_get_args() as $param ) {
			var_dump( $param );
		}
		echo '<table class="wdev-trace" cellspacing="0" cellpadding="2" border="1">';
		foreach ( debug_backtrace() as $id => $item ) {
			printf(
				'<tr><td>%1$s</td><td>%2$s : %3$s</td></tr>',
				$id,
				@$item['file'],
				@$item['line']
			);
		}
		echo '</table>';
		echo '</div></div>';
	}
};
