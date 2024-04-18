(function ($) {
  'use strict';

  $(document).ready(function () {
    function toggleBillingFields(display) {
      var fields = [
        '#billing_address_1_field',
        '#billing_address_2_field',
        'p#billing_address_3_field',
        '#billing_city_field',
        '#billing_state_field',
        '#billing_postcode_field',
        '#billing_country_field',
      ];
      fields.forEach(function (field) {
        if (display) {
          $(field).show();
        } else {
          $(field).hide();
        }
      });
    }

    function checkStock(mode) {
      $.ajax({
        type: 'POST',
        url: '/wp-admin/admin-ajax.php',
        data: {
          action: 'check_stock',
          eilatMode: mode,
        },
        success: function (response) {
          // console.log(response); // Handle the response
          if (response.success) {
            //
          } else {
            Swal.fire({
              icon: 'error',
              title: 'אין מלאי',
              text: response.data,
              confirmButtonText: 'ריקון עגלה',
              cancelButtonText: 'חזרה להזמנה אילת',
              showCancelButton: true,
            }).then((result) => {
              if (result.isConfirmed) {
                $.ajax({
                  type: 'POST',
                  url: '/wp-admin/admin-ajax.php',
                  data: {
                    action: 'clear_cart',
                  },
                  success: function (response) {
                    $(document.body).trigger('update_checkout');
                    Swal.fire({
                      icon: 'success',
                      title: 'העגלה רוקנה בהצלחה',
                      showConfirmButton: false,
                      timer: 1500,
                    });
                    window.location.href = '/';
                  },
                });
              } else {
                jQuery('#shipping_method_0').val('local_pickup:13').change();
                setCookie('eilatMode', 'true', 1); // Expires in 1 day
                $('#billing_city').val('אילת');
                $('#billing_city').attr('disabled', true);

                $('#billing_state').val('IL2600').change();
                $('#billing_state').select2({
                  disabled: true,
                });
                toggleBillingFields(false);
                // toggleCheckoutButton(false);
                checkStock(true);
              }
              $(document.body).trigger('update_checkout');
            });
          }
        },
        error: function (errorThrown) {
          console.log(errorThrown); // Handle any errors
        },
      });
    }

    $(document.body).on('change', 'select#shipping_method_0', function () {
      var selectedShippingMethod = $(this).val();
      toggle_delivery_details();

      // Toggle Eilat mode based on shipping method selection
      if (selectedShippingMethod === 'local_pickup:13') {
        setCookie('eilatMode', 'true', 1); // Expires in 1 day
        $('#billing_city').val('אילת');
        $('#billing_city').attr('disabled', true);
        $('#billing_state').val('IL2600').change();
        $('#billing_state').select2({
          disabled: true,
        });
        toggleBillingFields(false);
        // toggleCheckoutButton(false);
        // $('.orddd-checkout-fields').show();
        checkStock(true);
        // $('#order_delivery_date_fields').show();
      } else {
        setCookie('eilatMode', 'false', 1); // Expires in 1 day
        $('#billing_city').val('');
        $('#billing_city').attr('disabled', false);

        $('#billing_state').val('').change();
        $('#billing_state').select2({
          disabled: false,
        });
        toggleBillingFields(true);
        // toggleCheckoutButton(true);
        // $('.orddd-checkout-fields').hide();
        checkStock(false);
        // $('#order_delivery_date_fields').hide();
      }

      // Trigger checkout update to reflect changes
      $(document.body).trigger('update_checkout');
    });

    // Set payment method to Cash on Delivery if Eilat mode is active
    if (getCookie('eilatMode') === 'true') {
      $('#payment_method_cod').prop('checked', true).change();
      $('select#shipping_method_0').val('local_pickup:13').change();
      $('#billing_city').val('אילת');
      $('#billing_state').val('IL2600').change();
      toggleBillingFields(false);
      // setTimeout(function () {
      //   toggleCheckoutButton(false);
      // }, 800);
    } else {
      $('#payment_method_cod').prop('checked', false).change();
      $('select#shipping_method_0').val('flat_rate:12').change();
      $('#billing_city').val('');
      $('#billing_state').val('').change();
      toggleBillingFields(true);
      // setTimeout(function () {
      //   toggleCheckoutButton(true);
      // }, 800);
    }

    function customValidationPasses() {
      var requiredFields = $('form.checkout').find(
        'input[required], select[required]'
      );
      var isValid = true;
      requiredFields.each(function () {
        if ($(this).val() === '') {
          isValid = false;
        }
      });
      return isValid;
    }

    $(document.body).on('updated_checkout', function () {
      if (document.cookie.indexOf('eilatMode=true') !== -1) {
        $('button#place_order').text('הזמנה מאילת');
        $('button#place_order').attr('id', 'eilat_place_order');
        $('button#eilat_place_order').attr('type', 'button');
        var status = false;
        $('#eilat_place_order').on('click', function (e) {
          if (status) {
            return;
          }
          e.preventDefault();
          if (customValidationPasses() && status === false) {
            var orderData = $('form.checkout').serialize();

            $.ajax({
              type: 'POST',
              url: '/wp-admin/admin-ajax.php',
              data: {
                action: 'eilat_process_order',
                order_data: orderData,
              },
              success: function (response) {
                // Handle response here. Redirect to thank you page or show message
                status = true;
                if (response.success) {
                  status = true;
                  window.location.href = response.data.redirect_url;
                } else {
                  // Handle failure
                  console.log(response);
                  // $(document.body).trigger('update_checkout', [
                  //   response.fragments,
                  //   response.cart_hash,
                  // ]);
                  const message = response.data.message;
                  const error = response.data.error;
                  status = false;

                  Swal.fire({
                    icon: 'error',
                    title: message,
                    text: error,
                  });
                }
              },
              error: function () {
                Swal.fire({
                  icon: 'error',
                  title: 'אירעה שגיאה',
                  text: 'אנא נסה שוב מאוחר יותר',
                });
              },
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'שדות חובה חסרים',
              text: 'אנא מלא את כל השדות החובה',
            });
          }
        });
      }
    });

    // Function to toggle delivery details
    function toggle_delivery_details() {
      var chosen_shipping_method = $('#shipping_method_0').val();
      var display =
        chosen_shipping_method == 'local_pickup:13' ? 'block' : 'none';

      $('#custom_delivery_details').css('display', display);

      // Make fields required if local pickup is selected
      $('#order_delivery_date, #order_delivery_time').prop(
        'required',
        chosen_shipping_method == 'local_pickup:13'
      );
    }

    // Check on initial load
    toggle_delivery_details();
  });
})(jQuery);
