<?php
// add_action('woocommerce_review_order_before_payment', 'add_eilat_mode_calender');
function add_eilat_mode_calender()
{
  if (isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] == 'true' || WC()->session->get('chosen_shipping_methods')[0] == 'local_pickup:13') {
?>
    <div class="eilat-mode-calendar">
      <h5><?php _e('בחירת מועד לאיסוף', 'textdomain'); ?></h5>
      <div class="form-group eilat-mode-delivery-date-time">
        <input type="text" id="eilat-mode-date" name="eilat_mode_date" placeholder="Select a date" required>
        <select name="eilat_mode_time" class="form-control eilat-mode-time mt-2" id="eilat-mode-time" required disabled>
          <option value="" selected disabled><?php _e('בחרו שעה', 'textdomain'); ?></option>
          <option value="9:00-10:00">9:00-10:00</option>
          <option value="10:00-11:00">10:00-11:00</option>
          <option value="11:00-12:00">11:00-12:00</option>
          <option value="12:00-13:00">12:00-13:00</option>
          <option value="13:00-14:00">13:00-14:00</option>
        </select>
      </div>
    </div>
    <script>
      jQuery(function($) {
        $("#eilat-mode-date").flatpickr({
          locale: 'he',
          minDate: 'today',
          dateFormat: 'd/m/Y',
          altInput: true,
          altFormat: 'F j, Y',
          enableTime: false,
          time_24hr: true,
          // plugins: [new confirmDatePlugin({
          //   confirmText: "אישור",
          //   showAlways: false,
          //   theme: "dark"
          // })]
        });

        // Enable time selection when a date is selected
        $('#eilat-mode-date').on('change', function() {
          $('#eilat-mode-time').prop('disabled', false);
          // Trigger checkout update to reflect changes
          $(document.body).trigger('update_checkout');
        });

        $('select#shipping_method_0').change(function(e) {
          if ($(this).val() == 'local_pickup:13') {
            $('.eilat-mode-delivery-date-time').show();
            $('#eilat-mode-date').prop('required', true);
            $('#eilat-mode-time').prop('required', true);
          } else {
            $('.eilat-mode-delivery-date-time').hide();
            $('#eilat-mode-date').prop('required', false);
            $('#eilat-mode-time').prop('required', false);
          }
        });

        if ($('select#shipping_method_0').val() == 'local_pickup:13') {
          $('.eilat-mode-delivery-date-time').show();
          $('#eilat-mode-date').prop('required', true);
          $('#eilat-mode-time').prop('required', true);
        } else {
          $('.eilat-mode-delivery-date-time').hide();
          $('#eilat-mode-date').prop('required', false);
          $('#eilat-mode-time').prop('required', false);
        }
      });
    </script>
<?php

  }
}








function add_custom_checkout_fields($checkout)
{

  // Condition for showing fields
  if (isset($_COOKIE['eilatMode']) && $_COOKIE['eilatMode'] == 'true' || WC()->session->get('chosen_shipping_methods')[0] == 'local_pickup:13') {
    echo '<div id="custom_checkout_fields"><h3>' . __('Delivery Date and Time') . '</h3>';

    // Date field with a data attribute for AJAX URL
    woocommerce_form_field('delivery_date', array(
      'type'          => 'text',
      'class'         => array('flatpickr-field form-row-wide'),
      'label'         => __('Select Delivery Date'),
      'placeholder'   => __('Select a date'),
      'custom_attributes' => array('data-ajax_url' => admin_url('admin-ajax.php')),
      'required'      => true,
    ), $checkout->get_value('delivery_date'));

    // Time slot field initially disabled
    woocommerce_form_field('delivery_time_slot', array(
      'type'          => 'select',
      'class'         => array('form-row-wide'),
      'label'         => __('Select Delivery Time Slot'),
      'options'       => array('' => __('Please select a date first')),
      'custom_attributes' => array('disabled' => 'disabled'),
      'required'      => true,
    ), $checkout->get_value('delivery_time_slot'));

    echo '</div>';
  }
}
add_action('woocommerce_after_order_notes', 'add_custom_checkout_fields', 10, 1);

function validate_custom_checkout_fields()
{
  if (isset($_POST['delivery_date']) && empty($_POST['delivery_date'])) {
    wc_add_notice(__('Please select a delivery date.'), 'error');
  }

  if (isset($_POST['delivery_time_slot']) && empty($_POST['delivery_time_slot'])) {
    wc_add_notice(__('Please select a delivery time slot.'), 'error');
  }
}
add_action('woocommerce_checkout_process', 'validate_custom_checkout_fields');


function save_custom_checkout_fields($order_id)
{
  if (isset($_POST['delivery_date']) && !empty($_POST['delivery_date'])) {
    update_post_meta($order_id, '_delivery_date', sanitize_text_field($_POST['delivery_date']));
  }

  if (isset($_POST['delivery_time_slot']) && !empty($_POST['delivery_time_slot'])) {
    update_post_meta($order_id, '_delivery_time_slot', sanitize_text_field($_POST['delivery_time_slot']));
  }
}
add_action('woocommerce_checkout_update_order_meta', 'save_custom_checkout_fields');


function display_custom_fields_in_admin_order($order)
{
  echo '<p><strong>' . __('Delivery Date') . ':</strong> ' . get_post_meta($order->get_id(), '_delivery_date', true) . '</p>';
  echo '<p><strong>' . __('Delivery Time Slot') . ':</strong> ' . get_post_meta($order->get_id(), '_delivery_time_slot', true) . '</p>';
}
add_action('woocommerce_admin_order_data_after_billing_address', 'display_custom_fields_in_admin_order', 10, 1);

function include_custom_fields_in_order_emails($order, $sent_to_admin, $plain_text = false)
{
  $delivery_date = get_post_meta($order->get_id(), '_delivery_date', true);
  $delivery_time_slot = get_post_meta($order->get_id(), '_delivery_time_slot', true);

  if ($plain_text) {
    echo __('Delivery Date') . ': ' . $delivery_date . "\n";
    echo __('Delivery Time Slot') . ': ' . $delivery_time_slot . "\n";
  } else {
    echo '<p><strong>' . __('Delivery Date') . ':</strong> ' . $delivery_date . '</p>';
    echo '<p><strong>' . __('Delivery Time Slot') . ':</strong> ' . $delivery_time_slot . '</p>';
  }
}
add_action('woocommerce_email_order_meta', 'include_custom_fields_in_order_emails', 10, 3);



function get_available_time_slots()
{
  $date = sanitize_text_field($_POST['date']);
  // Fetch time slots from ACF options or your custom source based on $date
  $time_slots = get_field('time_slots', 'option'); // Example: fetching from ACF options

  // Filter or modify your $time_slots based on $date if necessary

  // Assume $time_slots is an array of arrays with 'value' and 'text' keys
  wp_send_json($time_slots);
}
add_action('wp_ajax_get_available_time_slots', 'get_available_time_slots');
add_action('wp_ajax_nopriv_get_available_time_slots', 'get_available_time_slots');
