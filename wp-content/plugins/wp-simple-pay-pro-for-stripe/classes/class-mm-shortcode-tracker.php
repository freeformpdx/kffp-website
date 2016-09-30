<?php

/**
 * Class to keep track of shortcodes and their parent/child relationship - SP Pro & add-ons (not Lite)
 */

class Shortcode_Tracker {
	
	private static $shortcodes = array();
	private static $parent_id = 0;
	private static $base;
	private static $prev_parent = null;
	private static $current_parent = null;
	private static $error_count = 0;
	
	protected static $instance = null;

	
	// Class constructor
	public function __construct() {}
	
	/*
	 * Gets or sets instance of this class
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
	
	/*
	 * Set the base shortcode
	 * Used as the main array holder index
	 */
	public static function set_as_base( $id, $attr = array() ) {
		
		self::$base = $id . '_' . self::$parent_id;
		self::$shortcodes[self::$base]['attributes'] = $attr;
		
		self::$parent_id++;
	}
	
	/*
	 * Set the parent ID starting point
	 * Needed to keep all shortcodes from one parent in the same array
	 */
	public static function set_parent_id( $uid ) {
		self::$parent_id = $uid;
		
	}
	
	/*
	 * Output the shortcodes array
	 * Mostly used for testing
	 */
	public static function print_shortcodes() {
		echo '<pre>' . print_r( self::$shortcodes, true ) . '</pre>';
	}
	
	/*
	 * Add a new shortcode to the array
	 */
	public static function add_new_shortcode( $id, $type = null, $attr = array(), $is_child = false ) {
		$index = self::$base;
		$arr   = self::$shortcodes;
		
		// Set current parent
		self::$current_parent = $id;
		
		if( $is_child ) {
			$prev_parent = self::$prev_parent;
			
			$arr[$index][$prev_parent]['children'][$id] = array();
			$arr[$index][$prev_parent]['children'][$id]['attr'] = $attr;
			$arr[$index][$prev_parent]['children'][$id]['type'] = $type;
			
		} else {
			// Set prev_parent in case next item is a child element
			

			$arr[$index][$id] = array();
		
			$arr[$index][$id]['attr'] = $attr;
			$arr[$index][$id]['type'] = $type;
			
			self::$prev_parent = $id;
		}
		
		// Update our shortcodes array
		self::$shortcodes = $arr;
		
	}
	
	// Checks current shortcode for specific child shortcode
	// returns the chunk of array for the matching found id
	public static function shortcode_exists_current( $id ) {
		$arr = self::$shortcodes;
		$base = self::$base;
		
		if ( isset( $arr[$base] ) ) {
			
			foreach( $arr[$base] as $k => $v ) {
				
				// Skip the stored attributes in the base array
				if ( $k == 'attributes' ) {
					continue;
				}
				
				foreach( $v as $v2 ) {
					if( $v2 == $id ) {
						return $v;
					}
				}
			}
		}
		
		return false;
	}
	
	public static function get_base_attributes() {
		$arr = self::$shortcodes;
		$base = self::$base;
		
		if( isset( $arr[$base] ) ) {
			return $arr[$base]['attributes'];
		}
		
		return false;
	}
	
	public static function update_error_count() {
		self::$error_count++;
	}
	
	public static function get_error_count() {
		return self::$error_count;
	}
	
	public static function add_error_message( $message ) {
		$index = self::$base;
		$arr   = self::$shortcodes;
		
		$arr[$index]['errors'][] = $message;
		
		self::$shortcodes = $arr;
	}
	
	public static function reset_error_count() {
		self::$error_count = 0;
	}
	
	public static function print_errors() {
		$arr = self::$shortcodes;
		$base = self::$base;
		$html = '';
		
		if( ! isset( $arr[$base]['errors'] ) || empty( $arr[$base]['errors'] ) ) {
			$html = __( 'There are no errors to display.', 'stripe' );
		} else {
			foreach( $arr[$base]['errors'] as $err ) {
				$html .= $err;
			}
		}
		
		return $html;
	}
}
