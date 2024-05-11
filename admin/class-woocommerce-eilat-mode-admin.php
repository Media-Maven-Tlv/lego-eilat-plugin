<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://media-maven.co.il
 * @since      1.0.0
 *
 * @package    Woocommerce_Eilat_Mode
 * @subpackage Woocommerce_Eilat_Mode/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Eilat_Mode
 * @subpackage Woocommerce_Eilat_Mode/admin
 * @author     Dor Meljon <dor@media-maven.co.il>
 */
class Woocommerce_Eilat_Mode_Admin
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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/woocommerce-eilat-mode-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
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
		wp_enqueue_script('flatpickr', '//cdn.jsdelivr.net/npm/flatpickr', array(), '4.6.6', false);
		wp_enqueue_style('flatpickr', '//cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', array(), '4.6.6');
		wp_enqueue_script('flatpickr-i18n', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/he.js', array(), '4.6.6', false);
		wp_enqueue_style('flatpickr-airbnb', '//cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css', array(), '4.6.6');
		wp_enqueue_script('choices', '//cdn.jsdelivr.net/npm/choices.js@9.0.1/public/assets/scripts/choices.min.js', array('jquery'), '4.0.13', false);
		wp_enqueue_style('choices', '//cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css', array(), '4.0.13');

		wp_enqueue_script('fullcalendar', '//cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.js', array('jquery'), '5.10.0', false);
		wp_enqueue_style('fullcalendar', '//cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.css', array(), '5.10.0');

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/woocommerce-eilat-mode-admin.js', array('jquery', 'flatpickr', 'choices', 'fullcalendar'), $this->version, false);
		wp_localize_script($this->plugin_name, 'calendarData', array(
			'ajaxurl' => admin_url('admin-ajax.php')
		));
	}
}

function eilat_settings_page()
{
	add_menu_page(
		'Eilat Settings',
		'Eilat Settings',
		'manage_options',
		'eilat-settings',
		'eilat_settings_page_html',
		'dashicons-admin-generic',
		90
	);
	//add sub page for delivery calendar
	add_submenu_page(
		'eilat-settings',
		'Delivery Calendar',
		'Delivery Calendar',
		'manage_options',
		'eilat-delivery-calendar',
		'eilat_delivery_calendar_page_html'
	);
}
add_action('admin_menu', 'eilat_settings_page');

function eilat_settings_page_html()
{
	// Check user capabilities
	if (!current_user_can('manage_options')) {
		return;
	}

?>
	<div class="wrap">
		<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields('eilat-settings');
			do_settings_sections('eilat-settings');
			submit_button('Save Changes');
			?>
		</form>
	</div>
<?php

	// HTML form for settings page will go here
}


function my_eilat_settings_init()
{
	// Register a new setting for "custom-settings" page.
	register_setting('eilat-settings', 'excluded_dates');
	register_setting('eilat-settings', 'selected_time_slots');
	register_setting('eilat-settings', 'email_to');
	register_setting('eilat-settings', 'eilat_pickup_method');
	register_setting('eilat-settings', 'delivery_date_status');

	// Register a new section in the "custom-settings" page.
	add_settings_section(
		'eilat_settings_section',
		'Eilat Settings',
		'my_eilat_settings_section_callback',
		'eilat-settings'
	);

	// Register a new field in the "custom_settings_section" section, inside the "custom-settings" page.
	add_settings_field(
		'eilat_settings_excluded_dates', // As ID
		'Excluded Dates', // Title
		'my_eilat_settings_excluded_dates_callback', // Callback
		'eilat-settings', // Page
		'eilat_settings_section' // Section
	);
	add_settings_field(
		'eilat_settings_selected_time_slots', // As ID
		'Selected Time Slots', // Title
		'my_eilat_settings_selected_time_slots_callback', // Callback
		'eilat-settings', // Page
		'eilat_settings_section' // Section
	);
	add_settings_field(
		'eilat_settings_email_to', // As ID
		'Email To', // Title
		'my_eilat_settings_email_to_callback', // Callback
		'eilat-settings', // Page
		'eilat_settings_section' // Section
	);
	add_settings_field(
		'eilat_settings_eilat_pickup_method', // As ID
		'Eilat Pickup Method', // Title
		'my_eilat_settings_eilat_pickup_method_callback', // Callback
		'eilat-settings', // Page
		'eilat_settings_section' // Section
	);
	add_settings_field(
		'eilat_settings_delivery_date_status', // As ID
		'Delivery Date Status', // Title
		'my_eilat_settings_delivery_date_status_callback', // Callback
		'eilat-settings', // Page
		'eilat_settings_section' // Section
	);

	// Repeat add_settings_field() for other settings as needed.
}
add_action('admin_init', 'my_eilat_settings_init');

function my_eilat_settings_section_callback()
{
	echo '<p>Eilat settings for your plugin.</p>';
}

function my_eilat_settings_excluded_dates_callback()
{
	// HTML input for the 'excluded_dates' setting
	$value = get_option('excluded_dates');
	echo '<input type="text" id="excluded_dates" name="excluded_dates" value="' . $value . '">';

	// Add more input fields as needed

}

function my_eilat_settings_selected_time_slots_callback()
{
	// HTML input for the 'selected_time_slots' setting
	$value = get_option('selected_time_slots');
	echo '<input type="select" multiple id="selected_time_slots" name="selected_time_slots" value="' . $value . '">';

	// Add more input fields as needed

}

function my_eilat_settings_email_to_callback()
{
	// HTML input for the 'email_to' setting
	$value = get_option('email_to');
	echo '<input type="text" id="email_to" name="email_to" value="' . $value . '">';

	// Add more input fields as needed

}

function my_eilat_settings_eilat_pickup_method_callback()
{
	// HTML input for the 'eilat_pickup_method' setting
	$value = get_option('eilat_pickup_method');
	echo '<input type="text" id="eilat_pickup_method" name="eilat_pickup_method" value="' . $value . '">';

	// Add more input fields as needed

}

function my_eilat_settings_delivery_date_status_callback()
{
	// HTML input for the 'delivery_date_status' setting
	$value = get_option('delivery_date_status');
	echo '<input type="checkbox" id="delivery_date_status" name="delivery_date_status" ' . checked($value, 'on', false) . '>';

	// Add more input fields as needed

}


function eilat_delivery_calendar_page_html()
{
	// Check user capabilities
	if (!current_user_can('manage_options')) {
		return;
	}

?>
	<div class="wrap">
		<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields('eilat-delivery-calendar');
			do_settings_sections('eilat-delivery-calendar');
			submit_button('Save Changes');
			?>
		</form>
	</div>
<?php

	// HTML form for settings page will go here
}

function my_eilat_delivery_calendar_init()
{
	// Register a new setting for "custom-settings" page.
	register_setting('eilat-delivery-calendar', 'delivery_calendar');

	// Register a new section in the "custom-settings" page.
	add_settings_section(
		'eilat_delivery_calendar_section',
		'Delivery Calendar',
		'my_eilat_delivery_calendar_section_callback',
		'eilat-delivery-calendar'
	);

	// Register a new field in the "custom_settings_section" section, inside the "custom-settings" page.
	add_settings_field(
		'eilat_delivery_calendar', // As ID
		'Delivery Calendar', // Title
		'my_eilat_delivery_calendar_callback', // Callback
		'eilat-delivery-calendar', // Page
		'eilat_delivery_calendar_section' // Section
	);

	// Repeat add_settings_field() for other settings as needed.
}
add_action('admin_init', 'my_eilat_delivery_calendar_init');

function my_eilat_delivery_calendar_section_callback()
{
	echo '<p>Delivery Calendar settings for your plugin.</p>';
}

function my_eilat_delivery_calendar_callback()
{
	// HTML input for the 'delivery_calendar' setting
	echo '<div class="wrap">';
	echo '<div id="calendar"></div>';  // Calendar container
	echo '</div>';

	// Add more input fields as needed

}

function load_eilat_orders_for_calendar()
{

	$args = array(
		'status'       => 'eilat-pickup',
		'return'       => 'ids',
		'meta_key'     => 'order_delivery_date', // Assuming this meta key is set correctly
		'orderby'      => 'meta_value',
		'order'        => 'ASC',
		'date_query'   => array(
			array(
				'year'  => current_time('Y'),
				'month' => current_time('m'),
			),
		),
	);
	$orders = wc_get_orders($args);
	$events = array();

	foreach ($orders as $order_id) {
		$order = wc_get_order($order_id);
		$delivery_date = get_post_meta($order_id, 'order_delivery_date', true);
		$delivery_time = get_post_meta($order_id, 'order_delivery_time', true);
		$delivery_date = strtr($delivery_date, '/', '-');
		$delivery_date = date('Y-m-d', strtotime($delivery_date));

		list($start_time, $end_time) = explode(' - ', $delivery_time);
		$start_datetime = date('Y-m-d H:i:s', strtotime("$delivery_date $start_time"));
		$end_datetime = date('Y-m-d H:i:s', strtotime("$delivery_date $end_time"));

		$customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
		$total = $order->get_total();
		$status = $order->get_status();

		$events[] = array(
			'title' => sprintf('Order #%s', $order_id),
			'start' => $start_datetime,
			'end'   => $end_datetime,
			'url'   => admin_url('post.php?post=' . $order_id . '&action=edit'),
			'allDay' => false, // This is important to show time in the calendar
			'extendedProps' => array(
				'customerName' => $customer_name,
				'total' => $total,
				'status' => $status,
				'email' => $order->get_billing_email(),
			)
		);
	}

	wp_send_json($events);
}
add_action('wp_ajax_load_eilat_orders', 'load_eilat_orders_for_calendar');
