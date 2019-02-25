<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://atarimtr.com
 * @since             1.0.0
 * @package           Perpetual_Order_Btn
 *
 * @wordpress-plugin
 * Plugin Name:       Perpetual order btn for Woocommerce
 * Plugin URI:        https://atarimtr.com
 * Description:       Set a predefined perpetual order for your customers that they can activate in one click. The plugin creates a shortcode that can be assigned in any part of the site (widgets or template) and displays a button for adding all the predefined order's items to the cart. You can set for each customer one of his existing orders as perpetual in the customer profile. Then a button will shoe in the shortcode's place for these customers. The customer can now click this button and all the products from the predefined order will be added to the cart. Use [perpetual-order] as the shortcode.
 * Version:           1.0.0
 * Author:            Yehuda Tiram
 * Author URI:        http://atarimtr.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       perpetual-order-btn
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-perpetual-order-btn-activator.php
 */
function activate_perpetual_order_btn() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-perpetual-order-btn-activator.php';
	Perpetual_Order_Btn_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-perpetual-order-btn-deactivator.php
 */
function deactivate_perpetual_order_btn() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-perpetual-order-btn-deactivator.php';
	Perpetual_Order_Btn_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_perpetual_order_btn' );
register_deactivation_hook( __FILE__, 'deactivate_perpetual_order_btn' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-perpetual-order-btn.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_perpetual_order_btn() {

	$plugin = new Perpetual_Order_Btn();
	$plugin->run();

}
run_perpetual_order_btn();
