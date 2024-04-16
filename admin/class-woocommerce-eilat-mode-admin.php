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
		// wp_enqueue_script('flatpickr', '//cdn.jsdelivr.net/npm/flatpickr', array(), '4.6.6', false);
		// wp_enqueue_style('flatpickr', '//cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', array(), '4.6.6');
		// wp_enqueue_script('flatpickr-i18n', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/he.js', array(), '4.6.6', false);
		// wp_enqueue_style('flatpickr-airbnb', '//cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css', array(), '4.6.6');
		// wp_enqueue_script('choices', '//cdn.jsdelivr.net/npm/choices.js@9.0.1/public/assets/scripts/choices.min.js', array('jquery'), '4.0.13', false);
		// wp_enqueue_style('choices', '//cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css', array(), '4.0.13');
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/woocommerce-eilat-mode-admin.js', array('jquery', 'flatpickr'), $this->version, false);
	}
}

function my_custom_settings_page()
{
	add_submenu_page(
		'options-general.php',          // Parent slug
		'Eilat Mode Settings',              // Page title
		'Eilat Mode Settings',              // Menu title
		'manage_options',               // Capability
		'custom-settings',              // Menu slug
		'my_custom_settings_page_html'  // Callback function
	);
}
add_action('admin_menu', 'my_custom_settings_page');

function my_custom_settings_page_html()
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
			settings_fields('custom-settings');
			do_settings_sections('custom-settings');
			submit_button('Save Changes');
			?>
		</form>
	</div>
<?php

	// HTML form for settings page will go here
}


function my_custom_settings_init()
{
	// Register a new setting for "custom-settings" page.
	register_setting('custom-settings', 'excluded_dates');
	register_setting('custom-settings', 'selected_time_slots');

	// Register a new section in the "custom-settings" page.
	add_settings_section(
		'custom_settings_section',
		'Custom Settings',
		'my_custom_settings_section_callback',
		'custom-settings'
	);

	// Register a new field in the "custom_settings_section" section, inside the "custom-settings" page.
	add_settings_field(
		'custom_settings_excluded_dates', // As ID
		'Excluded Dates', // Title
		'my_custom_settings_excluded_dates_callback', // Callback
		'custom-settings', // Page
		'custom_settings_section' // Section
	);
	add_settings_field(
		'custom_settings_selected_time_slots', // As ID
		'Selected Time Slots', // Title
		'my_custom_settings_selected_time_slots_callback', // Callback
		'custom-settings', // Page
		'custom_settings_section' // Section
	);

	// Repeat add_settings_field() for other settings as needed.
}
add_action('admin_init', 'my_custom_settings_init');

function my_custom_settings_section_callback()
{
	echo '<p>Custom settings for your plugin.</p>';
}

function my_custom_settings_excluded_dates_callback()
{
	// HTML input for the 'excluded_dates' setting
	$value = get_option('excluded_dates');
	echo '<input type="text" id="excluded_dates" name="excluded_dates" value="' . $value . '">';

	// Add more input fields as needed

}

function my_custom_settings_selected_time_slots_callback()
{
	// HTML input for the 'selected_time_slots' setting
	$value = get_option('selected_time_slots');
	echo '<input type="select" multiple id="selected_time_slots" name="selected_time_slots" value="' . $value . '">';

	// Add more input fields as needed

}
