<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://media-maven.co.il
 * @since      1.0.0
 *
 * @package    Woocommerce_Eilat_Mode
 * @subpackage Woocommerce_Eilat_Mode/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Woocommerce_Eilat_Mode
 * @subpackage Woocommerce_Eilat_Mode/includes
 * @author     Dor Meljon <dor@media-maven.co.il>
 */
class Woocommerce_Eilat_Mode_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'woocommerce-eilat-mode',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
