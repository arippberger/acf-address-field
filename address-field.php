<?php
/*
* Plugin Name: Advanced Custom Fields - Address Field add-on
* Plugin URI:  https://github.com/GCX/acf-address-field
* Description: Adds an Address Field to Advanced Custom Fields. Pick and choose the components and layout of the address.
* Author:      Brian Zoetewey
* Author URI:  https://github.com/GCX
* Version:     1.0.1
* Text Domain: acf-address-field
* Domain Path: /languages/
* License:     Modified BSD
*/
?>

<?php
/*
 * Copyright (c) 2012, CAMPUS CRUSADE FOR CHRIST
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * 
 *     Redistributions of source code must retain the above copyright notice, this
 *         list of conditions and the following disclaimer.
 *     Redistributions in binary form must reproduce the above copyright notice,
 *         this list of conditions and the following disclaimer in the documentation
 *         and/or other materials provided with the distribution.
 *     Neither the name of CAMPUS CRUSADE FOR CHRIST nor the names of its
 *         contributors may be used to endorse or promote products derived from this
 *         software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 */
?>
<?php


if( !class_exists( 'ACF_Address_Field_Plugin' ) ) :

/**
 * Advanced Custom Fields - Address Field Plugin
 * 
 * This class is a Plugin for the ACF_Address_Field class.
 * 
 * It provides:
 * Localization support and registering the textdomain with WordPress.
 * Registering the address field with Advanced Custom Fields. There is no need in your plugin or theme
 * to manually call the register_field() method, just include this file.
 * <code> include_once( rtrim( dirname( __FILE__ ), '/' ) . '/acf-address-field/address-field.php' ); </code>
 * 
 * @author Brian Zoetewey <brian.zoetewey@ccci.org>
 * @todo Provide shortcode support for address fields
 */
class ACF_Address_Field_Plugin {
	/**
	* WordPress Localization Text Domain
	*
	* Used in wordpress localization and translation methods.
	* @var string
	*/
	const L10N_DOMAIN = 'acf-address-field';
	
	/**
	 * Singleton instance
	 * @var ACF_Address_Field_Plugin
	 */
	private static $instance;

	/**
	 * The URL of the plugin
	 * @var string
	 */
	public $base_url;
	
	/**
	 * Returns the ACF_Address_Field_Plugin singleton
	 * 
	 * <code>$obj = ACF_Address_Field_Plugin::singleton();</code>
	 * @return ACF_Address_Field_Plugin
	 */
	public static function singleton() {
		if( !isset( self::$instance ) ) {
			$class = __CLASS__;
			self::$instance = new $class();
		}
		return self::$instance;
	}
	
	/**
	 * Prevent cloning of the ACF_Address_Field_Plugin object
	 * @internal
	 */
	private function __clone() {
	}
	
	/**
	 * Language directory path
	 * 
	 * Used to build the path for WordPress localization files.
	 * @var string
	 */
	private $lang_dir;
	
	/**
	 * Constructor
	 */
	private function __construct() {
		$this->lang_dir = rtrim( dirname( realpath( __FILE__ ) ), '/' ) . '/languages';

		$this->load_textdomain();

		// version 4+
		add_action( 'acf/register_fields', array( $this, 'register_fields' ) );

		// version 3-
		add_action( 'init', array( $this, 'register_address_field' ), 5, 0 );
	}

	public function register_fields() {
		include_once( 'address-field-v4.php' );
	}

	/**
	 * Registers the Address Field with Advanced Custom Fields
	 */
	public function register_address_field() {
		if( function_exists( 'register_field' ) ) {
			register_field( 'ACF_Address_Field', __FILE__ );
		}
	}
	
	/**
	* Loads the textdomain for the current locale if it exists
	*/
	public function load_textdomain() {
		$locale = get_locale();
		$mofile = $this->lang_dir . '/' . self::L10N_DOMAIN . '-' . $locale . '.mo';
		load_textdomain( self::L10N_DOMAIN, $mofile );
	}
}
endif; //class_exists 'ACF_Address_Field_Plugin'

//Instantiate the Addon Plugin class
ACF_Address_Field_Plugin::singleton();