<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://media-maven.co.il
 * @since      1.0.0
 *
 * @package    Woocommerce_Eilat_Mode
 * @subpackage Woocommerce_Eilat_Mode/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Woocommerce_Eilat_Mode
 * @subpackage Woocommerce_Eilat_Mode/includes
 * @author     Dor Meljon <dor@media-maven.co.il>
 */
class Woocommerce_Eilat_Mode
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Woocommerce_Eilat_Mode_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('WOOCOMMERCE_EILAT_MODE_VERSION')) {
			$this->version = WOOCOMMERCE_EILAT_MODE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'woocommerce-eilat-mode';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Woocommerce_Eilat_Mode_Loader. Orchestrates the hooks of the plugin.
	 * - Woocommerce_Eilat_Mode_i18n. Defines internationalization functionality.
	 * - Woocommerce_Eilat_Mode_Admin. Defines all hooks for the admin area.
	 * - Woocommerce_Eilat_Mode_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-woocommerce-eilat-mode-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-woocommerce-eilat-mode-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-woocommerce-eilat-mode-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-woocommerce-eilat-mode-public.php';

		// require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-woocommerce-eilat-mode-delivery.php';

		$this->loader = new Woocommerce_Eilat_Mode_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Woocommerce_Eilat_Mode_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{

		$plugin_i18n = new Woocommerce_Eilat_Mode_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{

		$plugin_admin = new Woocommerce_Eilat_Mode_Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
	}



	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{

		$plugin_public = new Woocommerce_Eilat_Mode_Public($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Woocommerce_Eilat_Mode_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}

require 'plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/Media-Maven-Tlv/lego-eilat-plugin',
	__FILE__,
	'woocommerce-eilat-mode'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('main');


// register custom order status
function register_custom_order_status()
{
	register_post_status('wc-eilat-pickup', array(
		'label'                     => _x('איסוף מאילת', 'Order status', 'textdomain'),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop('איסוף מאילת <span class="count">(%s)</span>', 'איסוף מאילת <span class="count">(%s)</span>', 'textdomain')
	));
}
add_action('init', 'register_custom_order_status');

//add custom order status to WooCommerce
function add_custom_order_status_to_woocommerce($order_statuses)
{
	$order_statuses['wc-eilat-pickup'] = _x('איסוף מאילת', 'Order status', 'textdomain');
	return $order_statuses;
}
add_filter('wc_order_statuses', 'add_custom_order_status_to_woocommerce');

// get cookie value
function get_cookie_eilat_mode()
{
	if (isset($_COOKIE['eilatMode'])) {
		return $_COOKIE['eilatMode'] === 'true';
	}
	return false;
}

// add_filter('woocommerce_product_get_tax_class', 'apply_zero_tax_class_for_eilat_mode', 10, 2);
function apply_zero_tax_class_for_eilat_mode($tax_class, $product)
{
	if (get_cookie_eilat_mode()) {
		return 'Zero Rate';
	}
	return $tax_class;
}

// add_filter('woocommerce_shipping_tax_class', 'set_zero_shipping_tax_for_eilat_mode', 10, 3);
function set_zero_shipping_tax_for_eilat_mode($tax_class, $shipping_rate, $package)
{
	if (get_cookie_eilat_mode()) {
		return 'Zero Rate';
	}
	return $tax_class;
}

add_filter('woocommerce_product_get_tax_class', 'eilat_zero_tax_class_for_product', 10, 2);
add_filter('woocommerce_product_variation_get_tax_class', 'eilat_zero_tax_class_for_product', 10, 2);
function eilat_zero_tax_class_for_product($tax_class, $product)
{
	// Early return if we're not in the cart or no cart is available yet
	if (!did_action('woocommerce_before_calculate_totals')) {
		return $tax_class;
	}

	$eilatMode = isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'true';
	if (!$eilatMode) return; // Exit if not in Eilat mode

	// Get the cart
	$cart = WC()->cart->get_cart();
	foreach ($cart as $cart_item) {
		// Check if the 'eilat' flag is set for the cart item
		$isEilatProduct = $cart_item['eilat'];

		if ($isEilatProduct) {
			return 'Zero Rate'; // Make sure this matches the name of your zero tax rate class
		}
	}

	// Return the original tax class if conditions are not met
	return $tax_class;
}


// add_action('woocommerce_before_calculate_totals', 'adjust_eilat_prices', 10, 1);
function adjust_eilat_prices($cart)
{
	if (is_admin() && !defined('DOING_AJAX')) return;

	// Check if Eilat mode is active
	$eilatMode = isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'true';

	if (!$eilatMode) return; // Exit if not in Eilat mode

	foreach ($cart->get_cart() as $cart_item) {
		$isEilatProduct = $cart_item['eilat'];

		if ($isEilatProduct) {
			// Calculate the new price by dividing the product's original price by 1.17
			$originalPrice = $cart_item['data']->get_price(); // Get the original price
			$newPrice = $originalPrice / 1.17; // Adjust the price

			// Set the new price
			$cart_item['data']->set_price($newPrice);
		}
	}
}


//add custom checkout fields
add_filter('woocommerce_checkout_fields', 'custom_override_checkout_fields2');
function custom_override_checkout_fields2($fields)
{

	if (isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'true') {
		// List of billing fields to unset
		$unset_fields = [
			'billing_address_1',
			'billing_address_2',
			'billing_address_3',
			'billing_city',
			'billing_state',
			'billing_postcode',
			'billing_country',
		];

		foreach ($unset_fields as $field) {
			unset($fields['billing'][$field]);
			$fields['billing'][$field]['required'] = false;
		}
	}
	return $fields;
}

// limit payment methods for Eilat mode
add_filter('woocommerce_available_payment_gateways', 'limit_payment_methods_for_eilat_mode');
function limit_payment_methods_for_eilat_mode($available_gateways)
{
	// Check if Eilat mode is on based on the cookie
	if (isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'true') {
		foreach ($available_gateways as $gateway_id => $gateway) {
			if ($gateway_id !== 'cod') {
				unset($available_gateways[$gateway_id]); // Unset all gateways except COD


			}
		}
	}

	return $available_gateways;
}

//check eilat inventory on checkout process
add_action('woocommerce_checkout_process', 'validate_eilat_stock_during_checkout');
function validate_eilat_stock_during_checkout()
{
	if (isset($_COOKIE['eilatMode']) && 'true' === $_COOKIE['eilatMode']) {
		foreach (WC()->cart->get_cart() as $cart_item) {
			$eilat_stock = get_post_meta($cart_item['product_id'], 'eilat_stock', true);

			if ($eilat_stock <= 0 || $eilat_stock < $cart_item['quantity']) {
				wc_add_notice(sprintf(__('Sorry, there is not enough Eilat stock for "%s".', 'textdomain'), $cart_item['data']->get_name()), 'error');
			}
		}
	}
}

//add eilat stock custom field
add_action('woocommerce_product_options_inventory_product_data', 'add_eilat_stock_custom_field');
function add_eilat_stock_custom_field()
{
	woocommerce_wp_text_input(array(
		'id' => 'eilat_stock',
		'label' => __('Eilat Stock', 'textdomain'),
		'desc_tip' => 'true',
		'description' => __('Enter the product\'s inventory in Eilat.', 'textdomain'),
		'type' => 'number',
		'custom_attributes' => array(
			'step' => '1',
			'min' => '0',
		),
	));
}

// Save the custom field value
add_action('woocommerce_admin_process_product_object', 'save_eilat_stock_custom_field');
function save_eilat_stock_custom_field($product)
{
	$eilat_stock = isset($_POST['eilat_stock']) ? $_POST['eilat_stock'] : '';
	$product->update_meta_data('eilat_stock', sanitize_text_field($eilat_stock));
}

// Display the custom field value on the product page
add_action('woocommerce_before_add_to_cart_form', 'display_eilat_stock_on_product_page');
function display_eilat_stock_on_product_page()
{
	global $product;
	$eilat_stock = $product->get_meta('eilat_stock');
	if (!empty($eilat_stock)) {
		echo '<div class="eilat-stock">' . sprintf(__('Eilat Stock: %s', 'textdomain'), esc_html($eilat_stock)) . '</div>';
	}
}

// Save eilat_stock as Order Item Meta
add_filter('woocommerce_add_cart_item_data', 'add_eilat_stock_to_cart_item', 10, 3);
function add_eilat_stock_to_cart_item($cart_item_data, $product_id, $variation_id)
{
	$eilat_stock = get_post_meta($product_id, 'eilat_stock', true);
	if (!empty($eilat_stock)) {
		$cart_item_data['eilat_stock'] = $eilat_stock;
	}
	return $cart_item_data;
}

// Save the custom field value to the order item
add_action('woocommerce_checkout_create_order_line_item', 'save_eilat_stock_order_item_meta', 10, 4);
function save_eilat_stock_order_item_meta($item, $cart_item_key, $values, $order)
{
	if (!empty($values['eilat_stock'])) {
		$item->add_meta_data('eilat_stock', $values['eilat_stock']);
	}
}

// Display the custom field value on the order edit page
// add_action('woocommerce_admin_order_item_values', 'display_eilat_stock_admin_order_item', 10, 3);
function display_eilat_stock_admin_order_item($product, $item, $item_id)
{
	if ($eilat_stock = $item->get_meta('eilat_stock', true)) {
		echo '<div><strong>' . __('Eilat Stock:', 'textdomain') . '</strong> ' . esc_html($eilat_stock) . '</div>';
	}
}

// Saving Eilat Mode to Order Meta
add_action('woocommerce_checkout_update_order_meta', function ($order_id) {
	if (isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'true') {
		update_post_meta($order_id, '_eilat_mode', 'yes');
	}
});

/**
 * 
 *   Ran 
 * Addition
 * 
 */
add_action('eilat_add_to_cart_button', 'eilat_add_to_cart_button');
function eilat_add_to_cart_button($product)
{
	global $product;
	$product_id = $product->get_id();
	$sku = $product->get_sku();
	echo <<<HTML
                <button
                    type="button"
                    data-product_id="$product_id"
                    data-product_sku="$sku" data-quantity="1"
                    class="eilat-button btn btn-danger col-10 fw-bold pb-3 pe-5 ps-5 pt-3 rounded rounded-1 text-md-center text-start wp-block-button__link"
                >
                    רכישה מאילת
                </button>
         HTML;
}

// check if product is in eilat stock function
function check_if_product_is_in_eilat_stock($product)
{
	$eilat_stock = $product->get_meta('eilat_stock');
	if (!empty($eilat_stock) && $eilat_stock > 0) {
		return true;
	}
	return false;
}

// add product to eilat cart ajax function
function add_product_to_eilat()
{
	$product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
	$quantity = isset($_POST['quantity']) ? absint($_POST['quantity']) : 1;
	$cart_item_data = array(
		'eilat' => true
	);

	try {
		$product_data = wc_get_product($product_id);

		if ($quantity <= 0 || !$product_data || 'trash' === $product_data->get_status()) {
			return false;
		}

		// Load cart item data - may be added by other plugins.
		$cart_item_data = (array) apply_filters('woocommerce_add_cart_item_data', $cart_item_data, $product_id, 0, $quantity);

		// Generate a ID based on product ID, variation ID, variation data, and other cart item data.
		$cart = WC()->cart;
		$cart_id = $cart->generate_cart_id($product_id, 0, array(), $cart_item_data);

		// Find the cart item key in the existing cart.
		$cart_item_key = $cart->find_product_in_cart($cart_id);



		if (!check_if_product_is_in_eilat_stock($product_data)) {
			/* translators: %s: product name */
			$message = sprintf(__('You cannot add &quot;%s&quot; to the cart because the product is out of stock.', 'woocommerce'), $product_data->get_name());

			/**
			 * Filters message about product being out of stock.
			 *
			 * @since 4.5.0
			 * @param string     $message Message.
			 * @param WC_Product $product_data Product data.
			 */
			$message = apply_filters('woocommerce_cart_product_out_of_stock_message', $message, $product_data);
			throw new Exception($message);
		}

		// If cart_item_key is set, the item is already in the cart.
		if ($cart_item_key) {
			$new_quantity = $quantity + $cart->cart_contents[$cart_item_key]['quantity'];
			$cart->set_quantity($cart_item_key, $new_quantity, false);
		} else {
			$cart_item_key = $cart_id;

			// Add item after merging with $cart_item_data - hook to allow plugins to modify cart item.
			$cart->cart_contents[$cart_item_key] = apply_filters(
				'woocommerce_add_cart_item',
				array_merge(
					$cart_item_data,
					array(
						'key' => $cart_item_key,
						'product_id' => $product_id,
						'variation_id' => 0,
						'variation' => array(),
						'quantity' => $quantity,
						'data' => $product_data,
						'data_hash' => wc_get_cart_item_data_hash($product_data),
					)
				),
				$cart_item_key
			);
		}

		$cart->cart_contents = apply_filters('woocommerce_cart_contents_changed', $cart->cart_contents);

		do_action('woocommerce_add_to_cart', $cart_item_key, $product_id, $quantity, 0, array(), $cart_item_data);
		// setcookie('eilatMode', 'true', time() + HOUR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
		return WC_AJAX::get_refreshed_fragments();
	} catch (Exception $e) {
		if ($e->getMessage()) {
			// wc_add_notice($e->getMessage(), 'error');
		}
		return false;
	}
	wp_die();
}
add_action('wp_ajax_add_product_to_eilat', 'add_product_to_eilat');
add_action('wp_ajax_nopriv_add_product_to_eilat', 'add_product_to_eilat');

// add eilat badge to cart item
add_action('xoo_wsc_product_summary_col_end', 'add_eilat_badge_to_cart_item', 10, 2);
function add_eilat_badge_to_cart_item($_product, $cart_item_key)
{
	// get cart cart_item_data
	$cart = WC()->cart;
	$cart_item_data = $cart->get_cart_item($cart_item_key);

	// check if the product is added to cart as eilat product
	if (isset($cart_item_data['eilat'])) {
		echo '<div class="eilat-product">מוצר אילת</div>';
	}
}

// change stock check in cart page and checkout page
add_filter('woocommerce_product_is_in_stock', 'check_stock_for_eilat_product', 10, 2);
function check_stock_for_eilat_product($is_in_stock, $product)
{
	// if is in cart or checkout page
	if (is_cart() || is_checkout()) {
		if (check_if_product_is_in_eilat_stock($product)) {
			return true;
		}
	}

	return $is_in_stock;
}

// disable woocommerce check_cart_item_stock
add_filter('woocommerce_check_cart_item_stock', 'disable_check_cart_item_stock', 10, 3);
function disable_check_cart_item_stock($check, $cart_item, $cart_item_key)
{
	$product = $cart_item['data'];
	if (check_if_product_is_in_eilat_stock($product)) {

		return false;
	}

	return $check;
}

add_filter('woocommerce_add_error', 'remove_out_of_stock_error', 10, 1);
function remove_out_of_stock_error($error)
{

	if ((strpos($error, __('מצטערים, אין לנו מספיק', 'woocommerce')) !== false || strpos($error, __('is not in stock', 'woocommerce')) !== false) || strpos($error, __('אין מספיק יחידות', 'woocommerce')) !== false || strpos($error, __('לא במלאי', 'woocommerce')) !== false) {
		$cart = WC()->cart;
		$cart_items = $cart->get_cart();
		foreach ($cart_items as $cart_item) {
			if (isset($cart_item['eilat'])) {
				$name = $cart_item['data']->get_name();
				if (strpos($error, $name) !== false) {
					return '';
				}
			}
		}
	}


	return $error;
}

// Process the order when the custom checkout button is clicked
add_action('wp_ajax_eilat_process_order', 'process_eilat_order');
add_action('wp_ajax_nopriv_eilat_process_order', 'process_eilat_order');
function process_eilat_order()
{
	if (WC()->cart->is_empty()) {
		wp_send_json_error('Cart is empty.');
		wp_die();
	}

	if (!isset($_POST['order_data'])) {
		wp_send_json_error(['message' => 'Order data is missing.']);
		wp_die();
	}

	parse_str($_POST['order_data'], $order_data);
	// Set billing from the $order_data array
	$billing_address = [
		'first_name' => $order_data['billing_first_name'],
		'last_name'  => $order_data['billing_last_name'],
		'email'      => $order_data['billing_email'],
		'phone'      => $order_data['billing_phone'],
		'address_1'  => $order_data['billing_address_1'],
		'address_2'  => $order_data['billing_address_2'],
		'city'       => $order_data['billing_city'],
		'state'      => $order_data['billing_state'],
		// 'pickup_date_field' => $order_data['h_deliverydate_0'],
		// 'pickup_time_field' => $order_data['orddd_time_slot_0'],
		'order_delivery_date' => $order_data['order_delivery_date'],
		'order_delivery_time' => $order_data['order_delivery_time'],
	];

	//check if terms_field is checked
	if (!isset($order_data['terms'])) {
		wp_send_json_error(['message' => 'יש לאשר את תנאי השימוש ומדיניות הפרטיות.']);
		wp_die();
	}

	//validate billing_address	
	$required_fields = ['first_name', 'last_name', 'email', 'phone', 'order_delivery_date', 'order_delivery_time'];
	$empty_fields = [];
	foreach ($required_fields as $field) {
		if (empty($billing_address[$field])) {
			$empty_fields[] = $field;
		}
	}
	if (!empty($empty_fields)) {
		wp_send_json_error(['message' => 'יש למלא את כל השדות הנדרשים.', 'error' => $empty_fields]);
		return WC_AJAX::get_refreshed_fragments();
	}

	$order = wc_create_order();

	$cart_items = WC()->cart->get_cart();

	foreach ($cart_items as $cart_item_key => $cart_item) {
		$product = $cart_item['data'];
		$quantity = $cart_item['quantity'];

		if (is_a($product, 'WC_Product')) {
			$order->add_product($product, $quantity);
		}
	}

	$order->set_address($billing_address, 'billing');

	$order->set_payment_method('cod'); // Assuming cash on delivery
	$order->calculate_totals(false);
	$order->update_status('eilat-pickup', 'הזמנה לאיסוף מאילת');
	$order->update_meta_data('order_delivery_date', $order_data['order_delivery_date']);
	$order->update_meta_data('order_delivery_time', $order_data['order_delivery_time']);
	$order->save();

	$redirect_url = $order->get_checkout_order_received_url();

	wp_send_json_success(['order_id' => $order->get_id(), 'redirect_url' => $redirect_url]);
	wp_die();
}


//custom thank you message
add_action('woocommerce_thankyou', 'custom_thankyou_page');
function custom_thankyou_page($order_id)
{
	$order = wc_get_order($order_id);
	if ($order->get_status() === 'eilat-pickup') {
		echo '<h2>הזמנתך נקלטה בהצלחה וממתינה לאיסוף באילת</h2>';
	}
}

// check stock ajax function
add_action('wp_ajax_check_stock', 'check_stock');
add_action('wp_ajax_nopriv_check_stock', 'check_stock');
function check_stock()
{
	$eilatMode = $_COOKIE['eilatMode'] === 'true';
	// $eilatMode = false;
	$cartItems = WC()->cart->get_cart();
	foreach ($cartItems as $cart_item) {

		// Common data for both modes.
		$productID = $cart_item['product_id'];
		$quantity = $cart_item['quantity'];
		if ($eilatMode) {

			// Eilat-specific stock check.
			$eilatStock = (int) get_post_meta($productID, 'eilat_stock', true);
			if ($eilatStock <= 0 || $eilatStock < $quantity) {
				wp_send_json_error('מצטערים, אין מספיק מלאי למוצר זה באילת.');
				wp_die(); // Ensure execution stops after sending error.
			}
			wp_send_json_success('Eilat stock is available.');
		} else {
			// Regular stock check now validated by SKU.

			$product = wc_get_product($productID);
			if ($product) {

				$sku = $product->get_sku();
				$productBySKU = wc_get_product_id_by_sku($sku);
				if ($productBySKU) {
					$productToCheck = wc_get_product($productBySKU);
					if (!$productToCheck->is_in_stock()) {
						wp_send_json_error('מצטערים, אין מספיק מלאי למוצר ' . $productToCheck->get_name() . ' באילת.');
						wp_die(); // Ensure execution stops after sending error.
					}
				} else {
					// Handle case where no product is found by SKU.
					wp_send_json_error('Sorry, no product found with the given SKU.');
					wp_die();
				}
			} else {
				// Handle case where product ID does not return a product.
				wp_send_json_error('Sorry, the product could not be found.');
				wp_die();
			}
		}
	}

	wp_send_json_success('All items in stock.');

	wp_die(); // If no issues were found, safely end execution.
}

add_filter('orddd_disable_delivery_for_user_roles', 'orddd_disable_delivery_for_user_roles_function');
function orddd_disable_delivery_for_user_roles_function($roles)
{
	if (isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'true') {
		$roles = array('');
	} else {
		$roles = array('subscriber', 'author', 'customer', 'administrator', 'editor', 'shop_manager', 'eilat_customer');
	}
	return $roles;
}

// clear cart ajax function
add_action('wp_ajax_clear_cart', 'clear_cart');
add_action('wp_ajax_nopriv_clear_cart', 'clear_cart');
function clear_cart()
{
	WC()->cart->empty_cart();
	wp_send_json_success('Cart cleared successfully.');
	return WC_AJAX::get_refreshed_fragments();

	wp_die();
}


add_action('wp_footer', 'add_eilat_banner');
function add_eilat_banner()
{
	if (isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'true') : ?>
		<div class="eilat-banner d-flex justify-content-center align-items-center p-2 gap-3" style="
    position: fixed;
    z-index: 99999999999;
    left: 0;
    bottom: 0;
    width: 100%;
    background-color: green;
">
			<p class="text-center text-light mb-0">מצב הזמנה מאילת</p>
		</div>
<?php endif;
}


function add_custom_checkout_fields_between_shipping_and_payment($checkout)
{
	if (!is_a($checkout, 'WC_Checkout')) {
		$checkout = WC()->checkout();
	}
	echo '<div id="custom_delivery_details" style="display:none;"><h3>' . __('Delivery Details') . '</h3>';

	// Delivery Date Field
	woocommerce_form_field('order_delivery_date', array(
		'type'          => 'text',
		'class'         => array('order-delivery-date form-row-wide'),
		'label'         => __('Delivery Date'),
		'required'      => false,  // Initially not required
	), $checkout->get_value('order_delivery_date'));

	// Delivery Time Field
	woocommerce_form_field('order_delivery_time', array(
		'type'          => 'select',
		'class'         => array('order-delivery-time form-row-wide'),
		'options'				=> array(
			'' => 'בחר זמן משלוח',
			'9:00 - 9:30' => '9:00 - 9:30',
			'9:30 - 10:00' => '9:30 - 10:00',
			'10:00 - 10:30' => '10:00 - 10:30',
			'10:30 - 11:00' => '10:30 - 11:00',
			'11:00 - 11:30' => '11:00 - 11:30',
			'11:30 - 12:00' => '11:30 - 12:00',
			'12:00 - 12:30' => '12:00 - 12:30',
			'12:30 - 13:00' => '12:30 - 13:00',
			'13:00 - 13:30' => '13:00 - 13:30',
			'13:30 - 14:00' => '13:30 - 14:00',
			'14:00 - 14:30' => '14:00 - 14:30',
			'14:30 - 15:00' => '14:30 - 15:00',
			'15:00 - 15:30' => '15:00 - 15:30',
			'15:30 - 16:00' => '15:30 - 16:00',
			'16:00 - 16:30' => '16:00 - 16:30',
			'16:30 - 17:00' => '16:30 - 17:00',
			'17:00 - 17:30' => '17:00 - 17:30',
			'17:30 - 18:00' => '17:30 - 18:00',
		),
		'label'         => '',
		'placeholder'     => '',
		'required'      => false,  // Initially not required
	), $checkout->get_value('order_delivery_time'));

	echo '</div>';
}

add_action('woocommerce_review_order_before_payment', 'add_custom_checkout_fields_between_shipping_and_payment');

function save_custom_checkout_fields($order_id)
{
	if (isset($_POST['order_delivery_date']) && !empty($_POST['order_delivery_date'])) {
		update_post_meta($order_id, 'Order Delivery Date', sanitize_text_field($_POST['order_delivery_date']));
	}

	if (isset($_POST['order_delivery_time']) && !empty($_POST['order_delivery_time'])) {
		update_post_meta($order_id, 'Order Delivery Time', sanitize_text_field($_POST['order_delivery_time']));
	}
}
add_action('woocommerce_checkout_update_order_meta', 'save_custom_checkout_fields');




function display_editable_custom_fields_in_order_admin($order)
{
	wp_nonce_field('update_order_delivery_time', 'custom_fields_nonce');

	// Display a text input for delivery time
	$delivery_time = get_post_meta($order->get_id(), 'order_delivery_time', true);
	$delivery_date = get_post_meta($order->get_id(), 'order_delivery_date', true);

	echo '<div class="form-field form-field-wide"><h4>Delivery Date</h4>';
	echo '<input type="text" id="order_delivery_date" name="order_delivery_date" value="' . esc_attr($delivery_date) . '">';
	echo '</div>';

	echo '<div class="form-field form-field-wide"><h4>Delivery Time</h4>';
	echo '<input type="text" id="order_delivery_time" name="order_delivery_time" value="' . esc_attr($delivery_time) . '">';
	echo '</div>';
}
add_action('woocommerce_admin_order_data_after_billing_address', 'display_editable_custom_fields_in_order_admin');

function save_custom_fields_on_admin_order_save($post_id, $post)
{
	// Check user capabilities
	if (!current_user_can('edit_shop_orders', $post_id)) {
		return;
	}

	// Check if our nonce is set (you should create a nonce field in your form to verify).
	if (!isset($_POST['custom_fields_nonce']) || !wp_verify_nonce($_POST['custom_fields_nonce'], 'update_order_delivery_time')) {
		return;
	}

	// Sanitize and save the field if it's set
	if (isset($_POST['order_delivery_time'])) {
		update_post_meta($post_id, 'order_delivery_time', sanitize_text_field($_POST['order_delivery_time']));
	}
	if (isset($_POST['order_delivery_date'])) {
		update_post_meta($post_id, 'order_delivery_date', sanitize_text_field($_POST['order_delivery_date']));
	}
}
add_action('woocommerce_process_shop_order_meta', 'save_custom_fields_on_admin_order_save', 10, 2);
