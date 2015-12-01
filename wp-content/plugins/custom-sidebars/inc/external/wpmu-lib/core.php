<?php
/**
Plugin Name: WPMU Dev code library
Plugin URI:  http://premium.wpmudev.org/
Description: Framework to support creating WordPress plugins and themes.
Version:     1.0.17
Author:      WPMU DEV
Author URI:  http://premium.wpmudev.org/
Textdomain:  wpmu-lib
*/

/**
 * Constants for wp-config.php
 *
 * define( 'WDEV_UNMINIFIED', true ) // Load the unminified JS/CSS files
 * define( 'WDEV_DEBUG', true ) // Activate WDev()->debug() without having to enable WP_DEBUG
 */

$version = '1.0.17'; // Remember to update the class-name in functions-wpmulib.php!!

/**
 * Load TheLib class definition if not some other plugin already loaded it.
 */
$dirname = dirname( __FILE__ ) . '/';
$class_file = 'functions-wpmulib.php';
$class_name = 'TheLib_' . str_replace( '.', '_', $version );
if ( ! class_exists( $class_name ) && file_exists( $dirname . $class_file ) ) {
	require_once( $dirname . $class_file );
}

if ( ! class_exists( 'TheLibWrap' ) ) {
	/**
	 * The wrapper class is used to handle situations when some plugins include
	 * different versions of TheLib.
	 *
	 * TheLibWrap will always keep the latest version of TheLib for later usage.
	 */
	class TheLibWrap {
		static public $version = '0.0.0';
		static public $object = null;

		static public function set_obj( $version, $obj ) {
			if ( version_compare( $version, self::$version, '>' ) ) {
				self::$version = $version;
				self::$object = $obj;
			}
		}
	};
}
$obj = new $class_name();
TheLibWrap::set_obj( $version, $obj );

if ( ! function_exists( 'WDev' ) ) {
	/**
	 * This is a shortcut function to access the latest TheLib object.
	 *
	 * Usage:
	 *   WDev()->message();
	 */
	function WDev() {
		return TheLibWrap::$object;
	}
}
