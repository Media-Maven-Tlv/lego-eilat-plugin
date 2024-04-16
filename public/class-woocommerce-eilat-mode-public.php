<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://media-maven.co.il
 * @since      1.0.0
 *
 * @package    Woocommerce_Eilat_Mode
 * @subpackage Woocommerce_Eilat_Mode/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Woocommerce_Eilat_Mode
 * @subpackage Woocommerce_Eilat_Mode/public
 * @author     Dor Meljon <dor@media-maven.co.il>
 */
class Woocommerce_Eilat_Mode_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Eilat_Mode_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Eilat_Mode_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/woocommerce-eilat-mode-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Eilat_Mode_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Eilat_Mode_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// if (is_checkout() && isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'true') {
		// wp_enqueue_script('flatpickr', '//cdn.jsdelivr.net/npm/flatpickr', array(), '4.6.6', false);
		// wp_enqueue_style('flatpickr', '//cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', array(), '4.6.6');
		// wp_enqueue_style('flatpickr-airbnb', '//cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css', array(), '4.6.6');
		// wp_enqueue_script('flatpickr-i18n', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/he.js', array(), '4.6.6', false);


		// }

		wp_enqueue_script('sweetalert2', '//cdn.jsdelivr.net/npm/sweetalert2@11', array(), '11', false);

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/woocommerce-eilat-mode-public.js', array(
			'jquery', 'sweetalert2'
		), $this->version, false);

		// wp_localize_script($this->plugin_name, 'checkout_params', array('ajaxurl' => admin_url('admin-ajax.php')));
		// $excluded_dates = get_option('excluded_dates');

		// wp_localize_script($this->plugin_name, 'excluded_dates', $excluded_dates);
	}
}
