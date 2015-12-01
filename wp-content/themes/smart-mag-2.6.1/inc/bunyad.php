<?php

/**
 * Bunyad framework factory extension
 * 
 * The main reason it's being extended is to offer shorter syntax that's not 
 * possible to offer on versions older than PHP 5.3 (lack of __callStatic).
 * 
 * This also aids in better code completion for most IDEs.
 * 
 * Most methods here are simply a wrapper for Bunyad_Base::get() method.
 * 
 * @see Bunyad_Base
 * @see Bunyad_Base::get()
 */
class Bunyad extends Bunyad_Base {
	
	/**
	 * Helper methods for page blocks
	 *  
	 * @return Bunyad_Theme_Blocks
	 */
	public static function blocks() {
		return self::get('blocks');
	}
}