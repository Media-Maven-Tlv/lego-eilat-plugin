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
			$this->version = '2.0.82';
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

		// Pickup inline notice (before Place Order button on checkout, before Proceed to Checkout on cart)
		$this->loader->add_action('woocommerce_review_order_before_submit', $plugin_public, 'pickup_notice_html');
		$this->loader->add_action('woocommerce_proceed_to_checkout', $plugin_public, 'pickup_notice_html');

		// Pickup popup in wp_footer (outside checkout form so fragment replacement won't destroy it)
		$this->loader->add_action('wp_footer', $plugin_public, 'pickup_popup_html');

		// Exclude checkout JS from Cloudflare Rocket Loader
		$this->loader->add_filter('script_loader_tag', $plugin_public, 'exclude_script_from_rocket_loader', 10, 2);
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



// Register custom order statuses
function register_custom_order_status()
{
	$statuses = [
		'wc-eilat-pickup' => __('אילת הזמנה חדשה', 'textdomain'),
		'wc-eilat-partial' => __('אילת אספקה חלקית', 'textdomain'),
		'wc-eilat-full' => __('אילת אספקה מלאה', 'textdomain'),
		'wc-eilat-no-stock' => __('אילת ללא אספקה', 'textdomain'),
		'wc-eilat-cancelled' => __('אילת בוטל על ידי הלקוח', 'textdomain'),
	];

	foreach ($statuses as $status => $label) {
		register_post_status($status, array(
			'label'                     => $label,
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop("{$label} <span class=\"count\">(%s)</span>", "{$label} <span class=\"count\">(%s)</span>", 'textdomain')
		));
	}
}
add_action('init', 'register_custom_order_status');

// Add custom order statuses to WooCommerce
function add_custom_order_status_to_woocommerce($order_statuses)
{
	$custom_statuses = [
		'wc-eilat-pickup' => __('אילת הזמנה חדשה', 'textdomain'),
		'wc-eilat-partial' => __('אילת אספקה חלקית', 'textdomain'),
		'wc-eilat-full' => __('אילת אספקה מלאה', 'textdomain'),
		'wc-eilat-no-stock' => __('אילת ללא אספקה', 'textdomain'),
		'wc-eilat-cancelled' => __('אילת בוטל על ידי הלקוח', 'textdomain'),
	];

	// Insert custom statuses after 'wc-pending'
	$new_order_statuses = [];
	foreach ($order_statuses as $key => $status) {
		$new_order_statuses[$key] = $status;
		if ('wc-pending' === $key) {
			foreach ($custom_statuses as $custom_key => $custom_label) {
				$new_order_statuses[$custom_key] = $custom_label;
			}
		}
	}
	return $new_order_statuses;
}
add_filter('wc_order_statuses', 'add_custom_order_status_to_woocommerce');

// Add custom bulk actions to the bulk actions dropdown
function custom_dropdown_bulk_actions_shop_order($actions)
{
	$actions['mark_wc-eilat-pickup'] = __('שינוי הסטטוס לאיסוף מאילת', 'woocommerce');
	$actions['mark_wc-eilat-partial'] = __('שינוי הסטטוס לאיסוף מאילת חלקי', 'woocommerce');
	$actions['mark_wc-eilat-full'] = __('שינוי הסטטוס לאספקה מלאה מאילת', 'woocommerce');
	$actions['mark_wc-eilat-no-stock'] = __('שינוי הסטטוס לאילת ללא אספקה', 'woocommerce');
	$actions['mark_wc-eilat-cancelled'] = __('שינוי הסטטוס לאילת בוטל על ידי הלקוח', 'woocommerce');
	return $actions;
}
add_filter('bulk_actions-edit-shop_order', 'custom_dropdown_bulk_actions_shop_order', 20, 1);

// Handle custom bulk actions and update order statuses with debugging
function handle_custom_bulk_actions_shop_order($redirect_url, $action, $post_ids)
{
	$status_map = [
		'mark_wc-eilat-pickup' => 'wc-eilat-pickup',
		'mark_wc-eilat-partial' => 'wc-eilat-partial',
		'mark_wc-eilat-full' => 'wc-eilat-full',
		'mark_wc-eilat-no-stock' => 'wc-eilat-no-stock',
		'mark_wc-eilat-cancelled' => 'wc-eilat-cancelled',
	];

	if (isset($status_map[$action])) {
		foreach ($post_ids as $post_id) {
			$order = wc_get_order($post_id);
			if ($order) {
				// Log before updating status
				error_log("Updating order #{$post_id} to status {$status_map[$action]}");
				$order->update_status($status_map[$action], __('Status updated via custom bulk action.', 'textdomain'));
				// Log after updating status
				error_log("Order #{$post_id} status updated to {$order->get_status()}");
			} else {
				error_log("Order not found: #{$post_id}");
			}
		}
		$redirect_url = add_query_arg('bulk_custom_status', count($post_ids), $redirect_url);
	}

	return $redirect_url;
}
add_filter('handle_bulk_actions-edit-shop_order', 'handle_custom_bulk_actions_shop_order', 10, 3);

// Add admin notices for bulk actions
function custom_bulk_action_admin_notice()
{
	if (!empty($_REQUEST['bulk_custom_status'])) {
		$count = intval($_REQUEST['bulk_custom_status']);
		printf('<div id="message" class="updated fade"><p>%s %d.</p></div>', __('Successfully updated', 'textdomain'), $count);
	}
}
add_action('admin_notices', 'custom_bulk_action_admin_notice');

// Add custom status to WooCommerce email actions
function custom_email_actions($actions)
{
	$actions[] = 'woocommerce_order_status_wc-eilat-pickup';
	$actions[] = 'woocommerce_order_status_wc-eilat-partial';
	$actions[] = 'woocommerce_order_status_wc-eilat-full';
	$actions[] = 'woocommerce_order_status_wc-eilat-no-stock';
	$actions[] = 'woocommerce_order_status_wc-eilat-cancelled';
	return $actions;
}
add_filter('woocommerce_email_actions', 'custom_email_actions', 20, 1);


//insert new order as wc-eilat-pickup
function set_custom_order_status_based_on_eilat_mode($order, $data)
{
	if (isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'true') {
		$order->set_status('eilat-pickup');
	}
}

add_action('woocommerce_checkout_create_order', 'set_custom_order_status_based_on_eilat_mode', 20, 2);



// get cookie value with static caching for performance
function get_cookie_eilat_mode()
{
	static $result = null;
	if ($result === null) {
		$result = isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'true';
	}
	return $result;
}

// Cache eilat_min_stock option
function get_eilat_min_stock_cached()
{
	static $min_stock = null;
	if ($min_stock === null) {
		$min_stock = get_option('eilat_min_stock') ? (int) get_option('eilat_min_stock') : 0;
	}
	return $min_stock;
}

// Register REST API endpoints for better performance
add_action('rest_api_init', 'eilat_register_rest_routes');
function eilat_register_rest_routes()
{
	register_rest_route('eilat/v1', '/add-to-cart', array(
		'methods' => 'POST',
		'callback' => 'eilat_rest_add_to_cart',
		'permission_callback' => '__return_true'
	));
	
	register_rest_route('eilat/v1', '/check-stock', array(
		'methods' => 'POST',
		'callback' => 'eilat_rest_check_stock',
		'permission_callback' => '__return_true'
	));
	
	register_rest_route('eilat/v1', '/remove-coupon', array(
		'methods' => 'POST',
		'callback' => 'eilat_rest_remove_coupon',
		'permission_callback' => '__return_true'
	));
	
	register_rest_route('eilat/v1', '/clear-cart', array(
		'methods' => 'POST',
		'callback' => 'eilat_rest_clear_cart',
		'permission_callback' => '__return_true'
	));
}

// Helper function to ensure WooCommerce cart is initialized
function eilat_ensure_wc_cart()
{
	if (is_null(WC()->cart)) {
		wc_load_cart();
	}
}

// REST API: Add to cart
function eilat_rest_add_to_cart($request)
{
	// Start output buffering to catch any unwanted output from filters/actions
	ob_start();
	
	// Ensure WooCommerce cart is initialized
	eilat_ensure_wc_cart();
	
	$product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
	$quantity = isset($_POST['quantity']) ? absint($_POST['quantity']) : 1;
	$override = isset($_POST['override']) ? filter_var($_POST['override'], FILTER_VALIDATE_BOOLEAN) : false;
	$cart_item_data = array('eilat' => true);

	try {
		$product_data = wc_get_product($product_id);

		if ($quantity <= 0 || !$product_data || 'trash' === $product_data->get_status()) {
			ob_end_clean();
			return new WP_REST_Response(array('success' => false, 'error' => 'Invalid product'), 400);
		}

		$cart_item_data = (array) apply_filters('woocommerce_add_cart_item_data', $cart_item_data, $product_id, 0, $quantity);
		$cart = WC()->cart;
		$cart_id = $cart->generate_cart_id($product_id, 0, array(), $cart_item_data);
		$cart_item_key = $cart->find_product_in_cart($cart_id);

		if ($cart_item_key && !$override) {
			ob_end_clean();
			return new WP_REST_Response(array(
				'success' => false,
				'product_exists' => true,
				'product_name' => $product_data->get_name()
			), 200);
		}

		if (!check_if_product_is_in_eilat_stock($product_data)) {
			$message = sprintf(__('You cannot add &quot;%s&quot; to the cart because the product is out of stock.', 'woocommerce'), $product_data->get_name());
			$message = apply_filters('woocommerce_cart_product_out_of_stock_message', $message, $product_data);
			ob_end_clean();
			return new WP_REST_Response(array('success' => false, 'error' => $message), 200);
		}

		if ($cart_item_key) {
			$new_quantity = $quantity + $cart->cart_contents[$cart_item_key]['quantity'];
			$cart->set_quantity($cart_item_key, $new_quantity, false);
		} else {
			$cart_item_key = $cart_id;
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
		do_action('woocommerce_ajax_added_to_cart', $product_id);
		
		// Get fragments manually instead of using WC_AJAX::get_refreshed_fragments() which outputs directly
		$fragments = apply_filters('woocommerce_add_to_cart_fragments', array());
		$cart_hash = WC()->cart->get_cart_hash();
		
		// Clear any output buffer
		ob_end_clean();
		
		return new WP_REST_Response(array(
			'success' => true,
			'fragments' => $fragments,
			'cart_hash' => $cart_hash
		), 200);
	} catch (Exception $e) {
		ob_end_clean();
		return new WP_REST_Response(array('success' => false, 'error' => $e->getMessage()), 200);
	}
}

// REST API: Check stock
function eilat_rest_check_stock($request)
{
	// Ensure WooCommerce cart is initialized
	eilat_ensure_wc_cart();
	
	$eilatMode = get_cookie_eilat_mode();
	$cartItems = WC()->cart->get_cart();
	$gwpInCart = false;
	
	// Prime meta cache for all products at once
	$product_ids = wp_list_pluck($cartItems, 'product_id');
	if (!empty($product_ids)) {
		update_meta_cache('post', $product_ids);
	}
	
	$min_stock = get_eilat_min_stock_cached();

	foreach ($cartItems as $cart_item) {
		$productID = $cart_item['product_id'];
		$quantity = $cart_item['quantity'];
		$product = $cart_item['data'];

		if ($product && has_term('gwp', 'product_cat', $productID)) {
			$gwpInCart = true;
			continue;
		}

		if ($eilatMode) {
			$eilatStock = (int) get_post_meta($productID, 'eilat_stock', true);
			if ($eilatStock <= $min_stock || $eilatStock < $quantity) {
				return new WP_REST_Response(array(
					'success' => false,
					'data' => 'למוצר ' . $product->get_name() . ' אין מספיק יחידות במלאי באילת, יש לעדכן את הכמות בעגלת הקניות.'
				), 200);
			}
		} else {
			if ($product && !$product->is_in_stock()) {
				return new WP_REST_Response(array(
					'success' => false,
					'data' => 'מצטערים, אין מספיק מלאי למוצר "' . $product->get_name() . '".'
				), 200);
			}
		}
	}

	if ($gwpInCart) {
		return new WP_REST_Response(array(
			'success' => true,
			'data' => array('message' => 'קבלת המתנה מותנית במלאי המוצר בסניף אילת', 'gwpInCart' => true)
		), 200);
	}

	return new WP_REST_Response(array('success' => true, 'data' => 'All items in stock.'), 200);
}

// REST API: Remove coupon
function eilat_rest_remove_coupon($request)
{
	// Ensure WooCommerce cart is initialized
	eilat_ensure_wc_cart();
	
	if (get_cookie_eilat_mode()) {
		if (WC()->cart && WC()->cart->has_discount()) {
			WC()->cart->remove_coupons();
			wc_clear_notices();
			return new WP_REST_Response(array('success' => true), 200);
		}
	}
	return new WP_REST_Response(array('success' => false), 200);
}

// REST API: Clear cart
function eilat_rest_clear_cart($request)
{
	// Ensure WooCommerce cart is initialized
	eilat_ensure_wc_cart();
	
	WC()->cart->empty_cart();
	return new WP_REST_Response(array('success' => true, 'message' => 'Cart cleared successfully.'), 200);
}

// Conditionally register tax filters only when in Eilat mode
add_action('init', 'eilat_conditionally_register_tax_filters', 1);
function eilat_conditionally_register_tax_filters()
{
	if (get_cookie_eilat_mode()) {
		add_filter('woocommerce_product_get_tax_class', 'apply_zero_tax_class_for_eilat_mode', 10, 2);
		add_filter('woocommerce_shipping_tax_class', 'set_zero_shipping_tax_for_eilat_mode', 10, 3);
	}
}

function apply_zero_tax_class_for_eilat_mode($tax_class, $product)
{
	return 'Zero Rate';
}

function set_zero_shipping_tax_for_eilat_mode($tax_class, $shipping_rate, $package)
{
	return 'Zero Rate';
}

add_filter('woocommerce_package_rates', 'disable_tax_for_specific_shipping_method', 10, 2);

function disable_tax_for_specific_shipping_method($rates, $package)
{
	foreach ($rates as $rate_key => $rate) {
		// Check if the shipping method is the one for which we want to disable tax
		if ($rate->method_id === 'flat_rate' && strpos($rate->id, 'flat_rate:3') !== false) {
			$rates[$rate_key]->taxes = array();  // Set taxes to an empty array
			$rates[$rate_key]->taxable = false;  // Mark the rate as not taxable
		}
	}
	return $rates;
}



// Conditionally register product tax class filters
add_action('init', 'eilat_conditionally_register_product_tax_filters', 2);
function eilat_conditionally_register_product_tax_filters()
{
	if (get_cookie_eilat_mode()) {
		add_filter('woocommerce_product_get_tax_class', 'eilat_zero_tax_class_for_product', 10, 2);
		add_filter('woocommerce_product_variation_get_tax_class', 'eilat_zero_tax_class_for_product', 10, 2);
	}
}

function eilat_zero_tax_class_for_product($tax_class, $product)
{
	// Make sure WC()->cart is initialized
	if (is_null(WC()->cart)) {
		return $tax_class;
	}

	// Use static cache to avoid repeated cart iterations
	static $eilat_product_ids = null;
	if ($eilat_product_ids === null) {
		$eilat_product_ids = array();
		foreach (WC()->cart->get_cart() as $cart_item) {
			if (!empty($cart_item['eilat'])) {
				$eilat_product_ids[$cart_item['product_id']] = true;
			}
		}
	}

	// Check if this product is an eilat product
	if (isset($eilat_product_ids[$product->get_id()])) {
		return 'Zero Rate';
	}

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


// add_filter('woocommerce_checkout_fields', 'custom_toggle_checkout_field_requirements_based_on_cookie');

// function custom_toggle_checkout_field_requirements_based_on_cookie($fields) {
//     // Determine if the cookie 'eilatMode' is set to 'true'
//     $is_eilat_mode = isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'true';

//     // List of billing fields to modify
//     $billing_fields_to_modify = [
//         'billing_address_1',
//         'billing_address_2',
//         'billing_address_3',
//         'billing_city',
//         'billing_state',
//         'billing_postcode',
//         // 'billing_country'
//     ];

//     // Loop through each field and adjust the required status based on eilat mode
//     foreach ($billing_fields_to_modify as $field) {
//         if (isset($fields['billing'][$field])) {
//             $fields['billing'][$field]['required'] = !$is_eilat_mode; // Required if not in eilat mode
//         }
//     }

//     return $fields;
// }


add_action('woocommerce_after_checkout_validation', 'custom_checkout_field_validation', 20, 2);

function custom_checkout_field_validation($data, $errors)
{
	if (isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'true') {
		// Define the fields that you want to ignore during validation
		$fields_to_ignore = [
			'billing_address_1',
			'billing_address_2',
			'billing_address_3',
			'billing_city',
			'billing_state',
			'billing_postcode',
			// 'billing_country'
		];

		// Loop through each field and remove errors related to them
		foreach ($fields_to_ignore as $field) {
			if ($errors->get_error_code('billing_' . $field)) {
				$errors->remove('billing_' . $field);
			}
		}
	}
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
	} else {
		unset($available_gateways['cod']); // Unset COD if not in Eilat mode
	}

	return $available_gateways;
}

add_action('woocommerce_before_cart', 'check_and_remove_coupons_based_on_cookie');
add_action('woocommerce_before_checkout_form', 'check_and_remove_coupons_based_on_cookie');

function check_and_remove_coupons_based_on_cookie()
{
	if (isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'true') {
		// Check if there are any coupons applied
		if (!is_admin() && WC()->cart && WC()->cart->has_discount()) {
			WC()->cart->remove_coupons();

			// Add a notice to inform the user
			wc_add_notice(__('הקופון הוסר מההזמנה, קופוני האתר אינם תקפים בהזמנת איסוף מאילת', 'woocommerce'), 'notice');
		}
	}
}

add_filter('woocommerce_coupons_enabled', 'disable_coupons_based_on_cookie');
function disable_coupons_based_on_cookie($enabled)
{
	if (isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'true') {
		$enabled = false;
	}
	return $enabled;
}

//ajax toggleCoupon
add_action('wp_ajax_remove_coupon', 'remove_coupon');
add_action('wp_ajax_nopriv_remove_coupon', 'remove_coupon');
function remove_coupon()
{
	//remove all coupons
	if (isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'true') {
		if (WC()->cart->has_discount()) {
			WC()->cart->remove_coupons();
			wc_clear_notices();
			wc_add_notice(__('הקופון הוסר מההזמנה, קופוני האתר אינם תקפים בהזמנת איסוף מאילת', 'woocommerce'), 'success');
			// wp_send_json_success('הקופון הוסר מההזמנה, קופוני האתר אינם תקפים בהזמנת איסוף מאילת');
			wp_die();
		}
	}

	wp_send_json_error();
	wp_die();
}


add_filter('woocommerce_get_terms_and_conditions_checkbox_text', 'custom_terms_conditions_text');

function custom_terms_conditions_text($text)
{
	if (isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'true') {
		$new_url = 'https://lego.certifiedstore.co.il/eis-eilat'; // Change this to your desired URL
		$new_text = '<a href="' . esc_url($new_url) . '" class="woocommerce-terms-and-conditions-link" target="_blank">קראתי ואני מסכים לתנאי השימוש באתר ולתנאי איסוף באילת</a>'; // Change this to your desired text
		return $new_text;
	}
	return $text;
}


//check eilat inventory on checkout process
add_action('woocommerce_checkout_process', 'validate_product_categories_during_checkout', 1);
function validate_product_categories_during_checkout()
{
	if (!get_cookie_eilat_mode()) {
		return;
	}
	
	$cart_items = WC()->cart->get_cart();
	
	// Prime meta cache for all products at once
	$product_ids = wp_list_pluck($cart_items, 'product_id');
	if (!empty($product_ids)) {
		update_meta_cache('post', $product_ids);
		// Prime term cache
		update_object_term_cache($product_ids, 'product');
	}
	
	$min_stock = get_eilat_min_stock_cached();
	
	foreach ($cart_items as $cart_item) {
		$product_id = $cart_item['product_id'];
		$product_categories = wp_get_post_terms($product_id, 'product_cat', ['fields' => 'slugs']);

		if (in_array('coming-soon', $product_categories)) {
			wc_add_notice(__('מוצר זה לא ניתן לרכישה.', 'woocommerce'), 'error');
		}

		// Existing stock validation logic
		$eilat_stock = get_post_meta($product_id, 'eilat_stock', true);
		if (($eilat_stock <= $min_stock || $eilat_stock < $cart_item['quantity']) && !has_term('gwp', 'product_cat', $product_id)) {
			wc_add_notice(sprintf(__('למוצר %s אין מספיק יחידות במלאי באילת, יש לעדכן את הכמות בעגלת הקניות.', 'woocommerce'), $cart_item['data']->get_name()), 'error');
		}
	}

	$fields_to_unset = [
		'billing_address_1',
		'billing_address_2',
		'billing_address_3',
		'billing_city',
		'billing_state',
		'billing_postcode',
	];

	foreach ($fields_to_unset as $field) {
		if (isset($_POST[$field])) {
			unset($_POST[$field]);
		}
	}
}


add_filter('woocommerce_checkout_fields', 'custom_override_checkout_fields');
function custom_override_checkout_fields($fields)
{
	if (isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'true') {
		$fields_to_disable = [
			'billing_address_1',
			'billing_address_2',
			'billing_address_3',
			'billing_city',
			'billing_state',
			'billing_postcode',
			// 'billing_country'
		];

		foreach ($fields_to_disable as $field_key) {
			if (isset($fields['billing'][$field_key])) {
				$fields['billing'][$field_key]['required'] = false;
			}
		}
	}
	return $fields;
}


function get_eilat_min_stock()
{
	return get_eilat_min_stock_cached();
}

add_filter('woocommerce_loop_add_to_cart_args', 'filter_woocommerce_loop_add_to_cart_args', 10, 2);
function filter_woocommerce_loop_add_to_cart_args($args, $product)
{
	if (isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'true') {
		$args['class'] = 'eilat-button';
		$args['attributes']['eilat-stock'] = $product->get_meta('eilat_stock') > get_eilat_min_stock() ? 'true' : 'false';
	}
	return $args;
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
// add_action('woocommerce_before_add_to_cart_form', 'display_eilat_stock_on_product_page');
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
	if (isset($_COOKIE['eilatMode']) && 'true' === $_COOKIE['eilatMode']) {
		if (!empty($values['eilat_stock'])) {
			$item->add_meta_data('eilat_stock', $values['eilat_stock']);
		}
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

// Saving Eilat Mode to Order Meta - HPOS compatible
add_action('woocommerce_checkout_update_order_meta', function ($order_id) {
	if (isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'true') {
		$order = wc_get_order($order_id);
		if ($order) {
			$order->update_meta_data('_eilat_mode', 'yes');
			$order->save();
		}
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
	$eilat_stock = $product->get_meta('eilat_stock') > get_eilat_min_stock() ? 'true' : 'false';
	if ($product->get_meta('eilat_stock') < get_eilat_min_stock()) {
		$disabled = 'disabled';
	} else {
		$disabled = '';
	}
	echo <<<HTML
                <button
                    type="button"
                    data-product_id="$product_id" eilat-stock="$eilat_stock"
                    data-product_sku="$sku" data-quantity="1"
                    class="eilat-button btn btn-danger col-10 fw-bold pb-3 pe-5 ps-5 pt-3 rounded rounded-1 text-center w-100 wp-block-button__link" $disabled
                >
                    הזמנה בחנות לגו אילת
                </button>
         HTML;

	if ($product->get_meta('eilat_stock') < get_eilat_min_stock()) {
		echo '<div class="text-danger w-100 d-block">מוצר זה אינו זמין להזמנה מסניף אילת</div>';
	}
}

// check if product is in eilat stock function
function check_if_product_is_in_eilat_stock($product)
{
	$eilat_stock = $product->get_meta('eilat_stock');
	if (!empty($eilat_stock) && $eilat_stock > get_eilat_min_stock()) {
		return true;
	}
	return false;
}

// add product to eilat cart ajax function
function add_product_to_eilat()
{
	$product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
	$quantity = isset($_POST['quantity']) ? absint($_POST['quantity']) : 1;
	$override = isset($_POST['override']) ? filter_var($_POST['override'], FILTER_VALIDATE_BOOLEAN) : false;
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

		// Check if product exists and the override flag wasn't set
		if ($cart_item_key && !$override) {
			// Product already exists in cart and no override flag
			// Return a special response asking for confirmation
			wp_send_json(array(
				'success' => false,
				'product_exists' => true,
				'product_name' => $product_data->get_name()
			));
			return;
		}

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
		
		// Trigger AJAX added to cart action for side cart compatibility
		do_action('woocommerce_ajax_added_to_cart', $product_id);
		
		// Get refreshed fragments
		$fragments_data = WC_AJAX::get_refreshed_fragments();
		
		// Return successful response with fragments and cart_hash
		wp_send_json(array(
			'success' => true,
			'fragments' => $fragments_data['fragments'],
			'cart_hash' => $fragments_data['cart_hash']
		));
		return;
	} catch (Exception $e) {
		if ($e->getMessage()) {
			wp_send_json(array(
				'success' => false,
				'error' => $e->getMessage()
			));
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
	if (isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'true') {
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
	}


	return $error;
}

// Process the order when the custom checkout button is clicked
// add_action('wp_ajax_eilat_process_order', 'process_eilat_order');
// add_action('wp_ajax_nopriv_eilat_process_order', 'process_eilat_order');
// function process_eilat_order()
// {
// 	if (WC()->cart->is_empty()) {
// 		wp_send_json_error('Cart is empty.');
// 		wp_die();
// 	}

// 	if (!isset($_POST['order_data'])) {
// 		wp_send_json_error(['message' => 'Order data is missing.']);
// 		wp_die();
// 	}

// 	parse_str($_POST['order_data'], $order_data);
// 	// Set billing from the $order_data array
// 	$billing_address = [
// 		'first_name' => $order_data['billing_first_name'],
// 		'last_name'  => $order_data['billing_last_name'],
// 		'email'      => $order_data['billing_email'],
// 		'phone'      => $order_data['billing_phone'],
// 		'address_1'  => $order_data['billing_address_1'],
// 		'address_2'  => $order_data['billing_address_2'],
// 		'city'       => $order_data['billing_city'],
// 		'state'      => $order_data['billing_state'],
// 		// 'h_deliverydate_0' => $order_data['h_deliverydate_0'],
// 		'e_deliverydate_0' => $order_data['e_deliverydate_0'],
// 		'orddd_time_slot_0' => $order_data['orddd_time_slot_0'],
// 		'_orddd_timestamp' => $order_data['_orddd_timestamp'],
// 	];

// 	if (get_option('delivery_date_status') == 'on') {
// 		$billing_address['order_delivery_date'] = $order_data['order_delivery_date'];
// 		$billing_address['order_delivery_time'] = $order_data['order_delivery_time'];
// 	}

// 	//check if terms_field is checked
// 	if (!isset($order_data['terms'])) {
// 		wp_send_json_error(['message' => 'יש לאשר את תנאי השימוש ומדיניות הפרטיות.']);
// 		wp_die();
// 	}

// 	//validate billing_address	
// 	$required_fields = ['first_name', 'last_name', 'email', 'phone', 'e_deliverydate_0', 'orddd_time_slot_0'];
// 	if (get_option('delivery_date_status') == 'on') {
// 		$required_fields[] = 'order_delivery_date';
// 		$required_fields[] = 'order_delivery_time';
// 	}
// 	$empty_fields = [];
// 	foreach ($required_fields as $field) {
// 		if (empty($billing_address[$field])) {
// 			$empty_fields[] = $field;
// 		}
// 	}
// 	if (!empty($empty_fields)) {
// 		$field_names = [
// 			'first_name' => 'שם פרטי',
// 			'last_name' => 'שם משפחה',
// 			'email' => 'אימייל',
// 			'phone' => 'טלפון',
// 			'e_deliverydate_0' => 'תאריך איסוף',
// 			'orddd_time_slot_0' => 'שעת איסוף',
// 			'order_delivery_date' => 'תאריך איסוף',
// 			'order_delivery_time' => 'שעת איסוף'
// 		];
// 		$missing_fields_list = array_map(function ($field) use ($field_names) {
// 			return $field_names[$field] ?? $field;  // This will use the custom field name if available, or default to the key
// 		}, $empty_fields);

// 		$error_message = 'יש למלא את השדות הבאים:';
// 		wp_send_json_error(['message' => $error_message, 'error' =>  implode(', ', $missing_fields_list)]);		// return WC_AJAX::get_refreshed_fragments();
// 	}

// 	$order = wc_create_order();

// 	$cart_items = WC()->cart->get_cart();

// 	foreach ($cart_items as $cart_item_key => $cart_item) {
// 		$product = $cart_item['data'];
// 		$quantity = $cart_item['quantity'];

// 		if (is_a($product, 'WC_Product')) {
// 			$order->add_product($product, $quantity);
// 		}
// 	}

// 	$order->set_address($billing_address, 'billing');

// 	$order->set_payment_method('cod'); // Assuming cash on delivery
// 	$order->calculate_totals(false);
// 	$order->update_meta_data('order_delivery_date', date('d/m/Y', strtotime($order_data['e_deliverydate_0'])));
// 	$order->update_meta_data('e_deliverydate_0', date('d/m/Y', strtotime($order_data['e_deliverydate_0'])));
// 	$order->update_meta_data('order_delivery_time', $order_data['orddd_time_slot_0']);
// 	$order->update_meta_data('_orddd_timestamp', $order_data['orddd_time_slot_0']);
// 	$order->update_status('eilat-pickup', 'הזמנה לאיסוף מאילת');
// 	$order->save();

// 	$redirect_url = $order->get_checkout_order_received_url();

// 	wp_send_json_success(['order_id' => $order->get_id(), 'redirect_url' => $redirect_url]);
// 	wp_die();
// }
function send_eilat_order_email($order_id)
{
	$order = wc_get_order($order_id);
	$billing_address = $order->get_address('billing');
	$delivery_date = $order->get_meta('order_delivery_date');
	$delivery_time = $order->get_meta('order_delivery_time');

	$subject = 'הזמנה חדשה מאילת #' . $order->get_order_number();
	$headers = 'From: ' . get_bloginfo('name') . ' <' . get_bloginfo('admin_email') . '>' . PHP_EOL;
	$headers .= 'Content-Type: text/html; charset=UTF-8' . PHP_EOL;

	// Enhanced HTML email template
	ob_start(); // Start output buffering to capture the template
?>
	<!DOCTYPE html>
	<html dir="rtl">

	<head>
		<meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<title>התקבלה הזמנה חדשה לאיסוף מאילת</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="https://fonts.googleapis.com/css2?family=Arial&display=swap" rel="stylesheet">
		<style type="text/css">
			body,
			table,
			td,
			a {
				-ms-text-size-adjust: 100%;
				-webkit-text-size-adjust: 100%;
			}

			table,
			td {
				mso-table-rspace: 0pt;
				mso-table-lspace: 0pt;
			}

			img {
				-ms-interpolation-mode: bicubic;
			}

			body {
				width: 100% !important;
				height: 100% !important;
				padding: 0 !important;
				margin: 0 !important;
				font-family: 'Arial', sans-serif;
				direction: rtl;
			}

			table {
				border-collapse: collapse !important;
				direction: rtl;
			}

			a {
				color: #1a82e2;
			}

			img {
				height: auto;
				line-height: 100%;
				text-decoration: none;
				border: 0;
				outline: none;
			}

			body {
				background-color: #e9ecef;
			}
		</style>
	</head>

	<body>
		<table border="0" cellpadding="0" cellspacing="0" width="100%" dir="rtl">
			<tr>
				<td align="center" bgcolor="#e9ecef">
					<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;">
						<tr>
							<td align="center" valign="top" style="padding: 36px 24px;">
								<a href="https://lego.certifiedstore.co.il/" target="_blank" style="display: inline-block;">
									<img src="https://lego.certifiedstore.co.il/wp-content/uploads/2022/07/fav.png" alt="Logo" border="0" width="100" style="display: block; width: 100px; max-width: 100px; min-width: 100px;">
								</a>
							</td>
						</tr>
						<tr>
							<td align="center" bgcolor="#ffffff" style="padding: 36px 24px 0; font-family: 'Arial', sans-serif; border-top: 3px solid #d4dadf;">
								<h1 style="margin: 0; font-size: 32px; font-weight: 700; letter-spacing: -1px; line-height: 48px;">
									התקבלה הזמנה חדשה לאיסוף מאילת
								</h1>
							</td>
						</tr>
						<tr>
							<td align="center" bgcolor="#ffffff" style="padding: 24px; font-family: 'Arial', sans-serif; font-size: 16px; line-height: 24px;">
								<p style="margin: 0;">שם: <?php echo $billing_address['first_name'] . ' ' . $billing_address['last_name']; ?></p>
								<p style="margin: 0;">טלפון: <?php echo $billing_address['phone']; ?></p>
								<p style="margin: 0;">אימייל: <?php echo $billing_address['email']; ?></p>
								<p style="margin: 0;">תאריך איסוף: <?php echo $order->get_meta('order_delivery_date'); ?></p>
								<p style="margin: 0;">שעת איסוף: <?php echo $delivery_time; ?></p>
							</td>
						</tr>
						<tr>
							<td align="center" bgcolor="#ffffff" style="padding: 24px; font-family: 'Arial', sans-serif; font-size: 16px; line-height: 24px;">
								<h2 style="margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -1px; line-height: 36px;">
									פרטי הזמנה מס׳ #<?php echo $order->get_order_number() ?>
								</h2>
							</td>
						</tr>
						<tr>
							<td align="center" bgcolor="#ffffff" style="padding: 24px; font-family: 'Arial', sans-serif; font-size: 16px; line-height: 24px;">
								<table border="0" cellpadding="0" cellspacing="0" width="100%" dir="rtl">
									<thead>
										<tr>
											<th style="text-align: right;">מוצר</th>
											<th style="text-align: right;">מק״ט</th>
											<th style="text-align: right;">כמות</th>
											<th style="text-align: right;">מחיר</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($order->get_items() as $item_id => $item) : ?>
											<tr>
												<td><?php echo $item->get_name(); ?></td>
												<td><?php echo $item->get_product()->get_sku(); ?></td>
												<td><?php echo $item->get_quantity(); ?></td>
												<td><?php echo wc_price($item->get_total()); ?></td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</td>
						<tr>
							<td align="center" bgcolor="#ffffff" style="padding: 24px; font-family: 'Arial', sans-serif; font-size: 16px; line-height: 24px; border-bottom: 3px solid #d4dadf">
								<p style="margin: 0;margin-bottom: 20px;">סה"כ: <?php echo $order->get_formatted_order_total(); ?></p>
								<a href="<?php echo $order->get_edit_order_url(); ?>" style="text-decoration: none; color: #ffffff;">
									<button type="button" class="btn btn-primary" style="margin: 0; padding: 12px 24px; font-size: 16px; font-weight: 700; letter-spacing: -1px; line-height: 24px; background-color: #E3000B; color: #ffffff; border: none; border-radius: 4px; cursor: pointer;">צפה בהזמנה</button>
								</a>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</body>

	</html>
	<?php
	$message = ob_get_clean(); // Store the contents of the buffer in $message
	// Send the email
	$to = get_option('email_to');
	$to = explode(',', $to);

	wp_mail($to, $subject, $message, $headers);
}


//custom thank you message
add_action('woocommerce_thankyou', 'custom_thankyou_page');
function custom_thankyou_page($order_id)
{
	$order = wc_get_order($order_id);
	$delivery_date = $order->get_meta('order_delivery_date');
	$delivery_time = $order->get_meta('order_delivery_time');
	if ($order->get_status() === 'eilat-pickup') {
		echo '<h3 class="mb-4">יש להמתין למסרון המודיע על כך שההזמנה מוכנה לאיסוף בסניף</h3>';
		echo '<h3>פרטי איסוף</h3>';
		echo '<table class="shop_table order_details">
		<tbody>
			<tr class="order">
				<th>תאריך איסוף:</th>
				<td>' . $delivery_date . '</td>
			</tr>
			<tr class="order">
				<th>שעת איסוף:</th>
				<td>' . $delivery_time . '</td>
			</tr>';
		echo '</tbody></table>';
		send_eilat_order_email($order_id);
	}
}

// add_action('woocommerce_order_details_after_customer_details', 'display_custom_field_after_customer_details', 10, 1);
// function display_custom_field_after_customer_details($order)
// {
// 	if ($order->get_status() === 'eilat-pickup') {
// 		$delivery_date = $order->get_meta('order_delivery_date');
// 		$delivery_time = $order->get_meta('order_delivery_time');
// 			echo '<h3>פרטי איסוף</h3>';
// 			echo '<p>תאריך איסוף: ' . $delivery_date . '</p>';
// 			echo '<p>שעת איסוף: ' . $delivery_time . '</p>';
// 	}
// }

// Remove the "Pay with cash upon delivery" text from the thank you page
add_action('wp', 'remove_cod_payment_text_thank_you_page');

function remove_cod_payment_text_thank_you_page()
{
	if (is_wc_endpoint_url('order-received')) {
		remove_action('woocommerce_thankyou_cod', 'woocommerce_cod_thankyou', 10);
	}
}


// check stock ajax function (legacy - kept for backwards compatibility, prefer REST API)
add_action('wp_ajax_check_stock', 'check_stock');
add_action('wp_ajax_nopriv_check_stock', 'check_stock');

function check_stock()
{
	$eilatMode = get_cookie_eilat_mode();
	$cartItems = WC()->cart->get_cart();
	$gwpInCart = false;
	
	// Prime meta cache for all products at once
	$product_ids = wp_list_pluck($cartItems, 'product_id');
	if (!empty($product_ids)) {
		update_meta_cache('post', $product_ids);
	}
	
	$min_stock = get_eilat_min_stock_cached();

	foreach ($cartItems as $cart_item) {
		$productID = $cart_item['product_id'];
		$quantity = $cart_item['quantity'];
		$product = $cart_item['data']; // Use data from cart instead of fetching again

		if ($product && has_term('gwp', 'product_cat', $productID)) {
			$gwpInCart = true;
			continue;
		}

		if ($eilatMode) {
			$eilatStock = (int) get_post_meta($productID, 'eilat_stock', true);
			if ($eilatStock <= $min_stock || $eilatStock < $quantity) {
				wp_send_json_error('למוצר ' . $product->get_name() . ' אין מספיק יחידות במלאי באילת, יש לעדכן את הכמות בעגלת הקניות.');
				wp_die();
			}
		} else {
			if ($product && !$product->is_in_stock()) {
				wp_send_json_error('מצטערים, אין מספיק מלאי למוצר "' . $product->get_name() . '".');
				wp_die();
			}
		}
	}

	if ($gwpInCart) {
		wp_send_json_success(['message' => 'קבלת המתנה מותנית במלאי המוצר בסניף אילת', 'gwpInCart' => true]);
		wp_die();
	}

	wp_send_json_success('All items in stock.');
	wp_die();
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


// add_action('wp_footer', 'add_eilat_banner');
function add_eilat_banner()
{
	if (isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'true') : ?>
		<div class="eilat-banner d-flex justify-content-center align-items-center p-2 gap-3">
			<p class="text-center text-light mb-0">
				הנכם נמצאים בתהליך הזמנה בסניף ביג אילת
			</p>
			<button class="btn btn-danger text-light btn-sm" id="exit-eilat-mode">
				ליציאה מתהליך זה
			</button>
		</div>
<?php endif;
}


function add_custom_checkout_fields_between_shipping_and_payment($checkout)
{
	if (!is_a($checkout, 'WC_Checkout')) {
		$checkout = WC()->checkout();
	}
	echo '<div id="custom_delivery_details" style="display:none;"><h3>' . __('בחירת מועד איסוף') . '</h3>';

	// Delivery Date Field
	woocommerce_form_field('order_delivery_date', array(
		'type'          => 'text',
		'class'         => array('order-delivery-date form-row-wide'),
		'label'         => __('תאריך איסוף'),
		'required'      => true,  // Initially not required
	), $checkout->get_value('order_delivery_date'));

	// Delivery Time Field
	woocommerce_form_field('order_delivery_time', array(
		'type'          => 'select',
		'class'         => array('order-delivery-time form-row-wide'),
		'options'				=> array(
			'' => 'שעת איסוף',
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
			'18:00 - 18:30' => '18:00 - 18:30',
			'18:30 - 19:00' => '18:30 - 19:00',
			'19:00 - 19:30' => '19:00 - 19:30',
			'19:30 - 20:00' => '19:30 - 20:00',
			'20:00 - 20:30' => '20:00 - 20:30',
			'20:30 - 21:00' => '20:30 - 21:00',
		),
		'label'         => '',
		'placeholder'     => '',
		'required'      => true,  // Initially not required
	), $checkout->get_value('order_delivery_time'));

	echo '</div>';
}

add_action('woocommerce_checkout_process', 'custom_checkout_field_process');
function custom_checkout_field_process()
{
	$eilatMode = isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] === 'true';
	if (isset($_POST['order_delivery_date']) && empty($_POST['order_delivery_date']) && $eilatMode) {
		wc_add_notice(__('יש לבחור תאריך איסוף'), 'error');
	}
	if (isset($_POST['order_delivery_time']) && empty($_POST['order_delivery_time']) && $eilatMode) {
		wc_add_notice(__('יש לבחור שעת איסוף'), 'error');
	}
}

function save_custom_checkout_fields($order_id)
{
	$order = wc_get_order($order_id);
	if (!$order) {
		return;
	}
	
	$updated = false;
	
	if (isset($_POST['order_delivery_date']) && !empty($_POST['order_delivery_date'])) {
		$order->update_meta_data('order_delivery_date', sanitize_text_field($_POST['order_delivery_date']));
		$updated = true;
	}

	if (isset($_POST['order_delivery_time']) && !empty($_POST['order_delivery_time'])) {
		$order->update_meta_data('order_delivery_time', sanitize_text_field($_POST['order_delivery_time']));
		$updated = true;
	}
	
	if ($updated) {
		$order->save();
	}
}

function display_editable_custom_fields_in_order_admin($order)
{
	wp_nonce_field('update_order_delivery_time', 'custom_fields_nonce');

	// Display a text input for delivery time - HPOS compatible
	$delivery_time = $order->get_meta('order_delivery_time', true);
	$delivery_date = $order->get_meta('order_delivery_date', true);

	echo '<div class="form-field form-field-wide"><h4>בחירת מועד איסוף</h4>';
	echo '<input type="text" id="order_delivery_date" name="order_delivery_date" placeholder="בחירת תאריך איסוף" value="' . esc_attr($delivery_date) . '">';
	echo '</div>';

	echo '<div class="form-field form-field-wide"><h4>Delivery Time</h4>';
	echo '<input type="text" id="order_delivery_time" name="order_delivery_time" value="' . esc_attr($delivery_time) . '">';
	echo '</div>';
}

function save_custom_fields_on_admin_order_save($order_id, $post = null)
{
	// Check user capabilities
	if (!current_user_can('edit_shop_orders', $order_id)) {
		return;
	}

	// Check if our nonce is set (you should create a nonce field in your form to verify).
	if (!isset($_POST['custom_fields_nonce']) || !wp_verify_nonce($_POST['custom_fields_nonce'], 'update_order_delivery_time')) {
		return;
	}

	// HPOS compatible - get order object
	$order = wc_get_order($order_id);
	if (!$order) {
		return;
	}
	
	$updated = false;

	// Sanitize and save the field if it's set
	if (isset($_POST['order_delivery_time'])) {
		$order->update_meta_data('order_delivery_time', sanitize_text_field($_POST['order_delivery_time']));
		$updated = true;
	}
	if (isset($_POST['order_delivery_date'])) {
		$order->update_meta_data('order_delivery_date', sanitize_text_field($_POST['order_delivery_date']));
		$updated = true;
	}
	
	if ($updated) {
		$order->save();
	}
}

// Always register these hooks for Eilat mode delivery date/time
add_action('woocommerce_process_shop_order_meta', 'save_custom_fields_on_admin_order_save', 10, 2);
add_action('woocommerce_admin_order_data_after_billing_address', 'display_editable_custom_fields_in_order_admin');
add_action('woocommerce_checkout_update_order_meta', 'save_custom_checkout_fields');
add_action('woocommerce_review_order_before_payment', 'add_custom_checkout_fields_between_shipping_and_payment');


function my_custom_plugin_override_template($template, $template_name, $template_path)
{
	$plugin_path = untrailingslashit(plugin_dir_path(__FILE__)) . '/woocommerce/';

	// Check if the template is 'simple.php' and the template is within the 'templates' directory.
	if ('single-product/add-to-cart/simple.php' === $template_name) {
		$override_path = $plugin_path . $template_name;
		if (file_exists($override_path)) {
			return $override_path;
		}
	}
	if ('single-product/price.php' === $template_name) {
		$override_path = $plugin_path . $template_name;
		if (file_exists($override_path)) {
			return $override_path;
		}
	}

	// Return default template
	return $template;
}
// add_filter('woocommerce_locate_template', 'my_custom_plugin_override_template', 10, 3);


// add custom order item checkbox
add_action('woocommerce_admin_order_item_headers', 'add_custom_order_item_header');
function add_custom_order_item_header($order)
{
	if ($order->get_status() !== 'eilat-pickup') {
		return;
	}
	echo '<th class="eilat_order_item_checkbox">במלאי אילת</th>'; // Add your custom column header
}

// Add the custom field value to the order items
add_action('woocommerce_admin_order_item_values', 'add_custom_order_item_value', 10, 3);
function add_custom_order_item_value($product, $item, $item_id)
{
	$order = wc_get_order($item->get_order_id());
	$checked = wc_get_order_item_meta($item_id, '_eilat_order_item_checkbox', true) === 'yes' ? 'checked' : '';
	if ($order->get_status() !== 'eilat-pickup') {
		return;
	}
	if (is_object($product)) {
		echo '<td class="eilat_order_item_checkbox"><input type="checkbox" name="eilat_order_item_checkbox[' . esc_attr($item_id) . ']" ' . $checked . ' /></td>';
	}
}

// Save the custom field value to the order items
add_action('woocommerce_process_shop_order_meta', 'save_custom_order_meta', 10, 2);
add_action('woocommerce_saved_order_items', 'save_custom_order_meta', 10, 2);
function save_custom_order_meta($post_id, $post)
{
	$order = wc_get_order($post_id);
	if ($order->get_status() !== 'eilat-pickup') {
		return;
	}
	if (isset($_POST['eilat_order_item_checkbox'])) {
		foreach ($_POST['eilat_order_item_checkbox'] as $item_id => $value) {
			// Check if checkbox is checked, consider using 'on' or true as checked values
			$checked = !empty($value) ? 'yes' : 'no';
			wc_update_order_item_meta($item_id, '_eilat_order_item_checkbox', $checked);
		}
	} else {
		// Important to handle the case where no checkboxes are checked
		$order = wc_get_order($post_id);
		foreach ($order->get_items() as $item_id => $item) {
			wc_update_order_item_meta($item_id, '_eilat_order_item_checkbox', 'no');
		}
	}
}

// Add custom order action
add_filter('woocommerce_order_actions', 'add_custom_order_action');
function add_custom_order_action($actions)
{
	global $theorder;
	if (!$theorder) {
		$theorder = wc_get_order(get_the_ID());
	}
	if ($theorder->get_status() === 'eilat-pickup') {
		$actions['send_to_third_party'] = __('עדכון אספקה מאילת עם מלאי חלקי', 'textdomain'); // Change 'textdomain' to your theme's or plugin's text domain
	}
	return $actions;
}

function process_order_send_to_third_party($order)
{
	if (!is_a($order, 'WC_Order')) {
		return;
	}

	// Process only if the order status is 'eilat-pickup'
	if ($order->get_status() !== 'eilat-pickup') {
		return;
	}

	$logger = wc_get_logger();
	$context = ['source' => 'eilat'];
	$order_data = ['order_id' => $order->get_id(), 'total' => $order->get_total(), 'items' => []];

	// Iterate through each item in the order
	foreach ($order->get_items() as $item_id => $item) {
		if (!$item instanceof WC_Order_Item_Product) {
			continue;
		}

		$product = $item->get_product();
		$checkbox_state = isset($_POST['eilat_order_item_checkbox'][$item_id]) ? 'yes' : 'no';
		wc_update_order_item_meta($item_id, '_eilat_order_item_checkbox', $checkbox_state);

		$order_data['items'][] = [
			'product_id'    => $item->get_product_id(),
			'product_image' => get_the_post_thumbnail_url($item->get_product_id(), 'thumbnail'),
			'product_name'  => $product ? $product->get_name() : '',
			'quantity'      => $item->get_quantity(),
			'checkbox_meta' => $checkbox_state,
		];
	}

	// Filter unchecked items
	$unchecked_items = array_filter($order_data['items'], function ($item) {
		return $item['checkbox_meta'] === 'no';
	});

	// Prepare the API request payload
	$api_payload = [
		"message" => [
			"template" => "t9qzd5ehw", // Replace with your actual template ID
			"subject" => "הזמנה מספר {$order->get_order_number()} במלאי חלקי באילת",
			"from" => [
				"name" => "LEGO® Certified Store",
				"email" => "info@certifiedstore.co.il",
			],
			"to" => [
				"name" => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
				"email" => $order->get_billing_email(),
			],
			"vars" => [
				"order_id" => $order->get_order_number(),
				"name" => $order->get_billing_first_name(),
				"pickup_date" => $order->get_meta('order_delivery_date'),
				"pickup_time" => $order->get_meta('order_delivery_time'),
				"items" => $unchecked_items,
			],
		],
	];

	// Log the entire body being sent
	$logger->info('API Request Payload:', ['payload' => json_encode($api_payload)]);

	// Send data to the third-party service
	$api_response = wp_remote_post('https://api.flashy.app/messages/email', [
		'method'    => 'POST',
		'headers'   => [
			'Content-Type' => 'application/json',
			'x-api-key'    => 'hbve08i5ykevufwdjxhpkuuy4k9onbxu', // Replace with your Flashy API key
		],
		'body'      => json_encode($api_payload),
	]);

	if (is_wp_error($api_response)) {
		$logger->error('Error sending email via Flashy API: ' . $api_response->get_error_message(), $context);
	} else {
		$response_code = wp_remote_retrieve_response_code($api_response);
		$response_body = wp_remote_retrieve_body($api_response);
		$logger->info('Flashy API Response: ' . $response_code . ' - ' . $response_body, $context);
	}

	// Update the order status
	$order->update_status('eilat-partial');
	$order->save();
}
add_action('woocommerce_order_action_send_to_third_party', 'process_order_send_to_third_party');


// add_action('woocommerce_init', 'custom_function_using_wc_get_order_statuses');


function get_order_info_for_flashy($order)
{
	$orderObj = new WC_Order($order);
	$order_id = $orderObj->get_order_number();
	$pickup_date = $orderObj->get_meta('order_delivery_date');
	$pickup_time = $orderObj->get_meta('order_delivery_time');
	$first_name = $orderObj->get_billing_first_name();
	$items = $orderObj->get_items();
	$logger = wc_get_logger();
	$context = ['source' => 'eilat'];
	$items = array_map(function ($item) {
		// Ensure $item is an instance of WC_Order_Item_Product
		if (!$item instanceof WC_Order_Item_Product) {
			return null;
		}

		$product = $item->get_product();
		return [
			'product_id' => $item->get_product_id(),
			'product_image' => get_the_post_thumbnail_url($item->get_product_id(), 'thumbnail'),
			'product_name' => $product ? $product->get_name() : '',
			'quantity' => $item->get_quantity(),
			'checkbox_meta' => wc_get_order_item_meta($item->get_id(), '_eilat_order_item_checkbox', true),
		];
	}, $items);

	$logger->info('Items:', ['items' => $items]);

	// Remove null values (non-product items) from the array
	$items = array_filter($items);
	$logger->info('Filtered Items:', ['items' => $items]);

	$unchecked_items = array_filter($items, function ($item) {
		return $item['checkbox_meta'] === 'no';
	});
	$logger->info('Unchecked Items:', ['items' => $unchecked_items]);

	return [
		"order_id" => $order_id,
		"pickup_date" => $pickup_date,
		"pickup_time" => $pickup_time,
		"first_name" => $first_name,
		"items" => $unchecked_items,
	];
}
add_filter('flashy_get_order_context', 'get_order_info_for_flashy', 10, 1);



//send email to admin when order is placed in eilat
// add_action('woocommerce_order_status_eilat-pickup', 'send_eilat_order_email', 10, 1);

//round coupon discount
// function filter_woocommerce_coupon_get_discount_amount(
// 	$discount,
// 	$discounting_amount,
// 	$cart_item,
// 	$single,
// 	$instance
// ) {
// 	// round up to nearest 1
// 	$discount = round($discount);
// 	return $discount;
// }

// add_filter(
// 	'woocommerce_coupon_get_discount_amount',
// 	'filter_woocommerce_coupon_get_discount_amount',
// 	10,
// 	5
// );
