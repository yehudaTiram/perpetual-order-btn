<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://atarimtr.com
 * @since      1.0.0
 *
 * @package    Perpetual_Order_Btn
 * @subpackage Perpetual_Order_Btn/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Perpetual_Order_Btn
 * @subpackage Perpetual_Order_Btn/includes
 * @author     Yehuda Tiram <yehuda@atarimtr.co.il>
 */
class Perpetual_Order_Btn_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'perpetual-order-btn',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
