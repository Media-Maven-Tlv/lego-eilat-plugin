(function ($) {
  'use strict';

  // Create and append the loading indicator to the body
  function createLoadingIndicator() {
    var loadingHtml =
      '<div id="loadingIndicator" style="position: fixed;width: 100%;height: 100%;top: 0;left: 0;z-index: 9999;color:white;background: rgba(0, 0, 0, 0.75) url(\'path_to_spinner.gif\') center no-repeat;align-items: center;justify-content: center;font-size: 50px;">טוען...</div>';
    $('body').prepend(loadingHtml);
  }

  // Utility functions for cookie management
  function setCookie(name, value, days) {
    var expires = '';
    if (days) {
      var date = new Date();
      date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
      expires = '; expires=' + date.toUTCString();
    }
    document.cookie = name + '=' + (value || '') + expires + '; path=/';
  }

  function getCookie(name) {
    var nameEQ = name + '=';
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i].trim();
      if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length);
    }
    return null;
  }

  function showLoadingIndicator() {
    $('#loadingIndicator').css('display', 'flex');
  }

  function hideLoadingIndicator() {
    $('#loadingIndicator').hide();
  }

  function toggleCouponField(isLocalPickup) {
    var couponField = $('.woocommerce-form-coupon-toggle');
    couponField.toggle(!isLocalPickup);
  }

  // Toggle visibility of billing fields
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
      $(field).toggle(display);
    });
  }

  // Handle changes in shipping method
  function handleShippingMethodChange() {
    var selectedShippingMethod = $(this).val();
    var isLocalPickup = selectedShippingMethod === local_pickup;
    setCookie('eilatMode', isLocalPickup ? 'true' : 'false', 1);
    toggleBillingFields(!isLocalPickup);
    toggleCouponField(isLocalPickup);
    $(document.body).trigger('update_checkout');
    checkStock(isLocalPickup);
  }

  // AJAX request to check stock availability
  function checkStock(isEilat) {
    $.ajax({
      type: 'POST',
      url: '/wp-admin/admin-ajax.php',
      data: { action: 'check_stock', eilatMode: isEilat },
      success: function (response) {
        if (!response.success) {
          Swal.fire({
            icon: 'error',
            title: 'אין מלאי',
            text: response.data,
            confirmButtonText: 'ריקון עגלה',
            cancelButtonText: 'חזרה להזמנה אילת',
            showCancelButton: true,
          }).then((result) => {
            if (result.isConfirmed) {
              clearCart();
            } else {
              revertToEilatMode();
            }
          });
        }
      },
      error: function (errorThrown) {
        console.error(errorThrown);
      },
    });
  }

  // Clear the shopping cart via AJAX
  function clearCart() {
    $.ajax({
      type: 'POST',
      url: '/wp-admin/admin-ajax.php',
      data: { action: 'clear_cart' },
      success: function () {
        Swal.fire({
          icon: 'success',
          title: 'העגלה רוקנה בהצלחה',
          timer: 1500,
        }).then(() => {
          window.location.href = '/';
        });
      },
    });
  }

  // Revert to Eilat mode settings
  function revertToEilatMode() {
    $('#shipping_method_0').val(local_pickup).change();
    setCookie('eilatMode', 'true', 1);
    toggleBillingFields(false);
    checkStock(true);
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
  // Initialize page-specific functionalities
  $(document).ready(function () {
    createLoadingIndicator(); // Call to create the loading indicator when document is ready

    if ($('body').hasClass('woocommerce-checkout')) {
      initializeCheckoutPage();
    }

    if ($('body').hasClass('single-product')) {
      initializeProductPage();
    }

    $('#toggleEilatMode').on('click', toggleEilatMode);
    $('#exit-eilat-mode').on('click', exitEilatMode);
  });

  // Setup checkout page functionality
  function initializeCheckoutPage() {
    $(document.body).on(
      'change',
      'select#shipping_method_0',
      handleShippingMethodChange
    );
    $(document.body).on('updated_checkout', function () {
      if (getCookie('eilatMode') === 'true') {
        $('button#place_order')
          .text('הזמנה מאילת')
          .attr('id', 'eilat_place_order')
          .attr('type', 'button');
        $('#eilat_place_order').on('click', function (e) {
          $('#eilat_place_order').prop('disabled', true);
          e.preventDefault();
          if (customValidationPasses()) {
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
                $('#eilat_place_order').prop('disabled', false);
              },
              error: function () {
                Swal.fire({
                  icon: 'error',
                  title: 'אירעה שגיאה',
                  text: 'אנא נסה שוב מאוחר יותר',
                });
                $('#eilat_place_order').prop('disabled', false);
              },
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'שדות חובה חסרים',
              text: 'אנא מלא את כל השדות החובה',
            });
            $('#eilat_place_order').prop('disabled', false);
          }
        });
      } else {
        $('button#place_order')
          .text('הזמנה רגילה')
          .attr('id', 'regular_place_order')
          .attr('type', 'submit');
      }
    });

    if (getCookie('eilatMode') === 'true') {
      $('#shipping_method_0').val(local_pickup).change();
      toggleBillingFields(false);
      checkStock(true);
    } else {
      toggleBillingFields(true);
    }
  }

  // Setup product page functionality
  function initializeProductPage() {
    $('.eilat-button').on('click', function (e) {
      e.preventDefault();
      var button = $(this);
      var product_id = button.data('product_id');
      var product_sku = button.data('product_sku');
      var quantity = button.data('quantity');
      $.ajax({
        type: 'POST',
        url: '/wp-admin/admin-ajax.php',
        data: {
          action: 'add_product_to_eilat',
          product_id: product_id,
          product_sku: product_sku,
          quantity: quantity,
        },
        success: function (response) {
          $(document.body).trigger('added_to_cart', [
            response.fragments,
            response.cart_hash,
            button,
          ]);
        },
      });
    });
  }

  function exitEilatMode(e) {
    e.preventDefault();
    setCookie('eilatMode', 'false', 1);
    showLoadingIndicator();
    location.reload();
  }

  // Toggle Eilat mode on and off
  function toggleEilatMode(e) {
    e.preventDefault();
    var mode = getCookie('eilatMode') === 'true' ? 'false' : 'true';
    setCookie('eilatMode', mode, 1);
    showLoadingIndicator();
    location.reload();
  }
})(jQuery);
