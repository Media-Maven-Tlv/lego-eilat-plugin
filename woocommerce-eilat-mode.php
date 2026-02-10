<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://media-maven.co.il
 * @since             1.0.0
 * @package           Woocommerce_Eilat_Mode
 *
 * @wordpress-plugin
 * Plugin Name:       Woocommerce Eilat Mode
 * Plugin URI:        https://media-maven.co.il
 * Description:       Adds custom functionalities for handling Eilat mode, including a custom order status, product stock validation, checkout modifications, and applying zero tax in Eilat mode.
 * Version:           2.2.87
 * Author:            Dor Meljon
 * Author URI:        https://media-maven.co.il/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-eilat-mode
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('WOOCOMMERCE_EILAT_MODE_VERSION', '2.2.87');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woocommerce-eilat-mode-activator.php
 */
function activate_woocommerce_eilat_mode()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-woocommerce-eilat-mode-activator.php';
	Woocommerce_Eilat_Mode_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woocommerce-eilat-mode-deactivator.php
 */
function deactivate_woocommerce_eilat_mode()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-woocommerce-eilat-mode-deactivator.php';
	Woocommerce_Eilat_Mode_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_woocommerce_eilat_mode');
register_deactivation_hook(__FILE__, 'deactivate_woocommerce_eilat_mode');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-woocommerce-eilat-mode.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woocommerce_eilat_mode()
{

	$plugin = new Woocommerce_Eilat_Mode();
	$plugin->run();
}
run_woocommerce_eilat_mode();
