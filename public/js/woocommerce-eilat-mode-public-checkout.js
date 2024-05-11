(function ($) {
  ('use strict');
  $(document).ready(function () {

    // function showLoadingIndicator() {
    //   var loadingIndicator = $('<div class="loading-indicator">LOADING</div>');
    //   $('body').append(loadingIndicator);
    // }

    $(document.body).on('updated_checkout', function () {
      if (getCookie('eilatMode') === 'true') {
        $('button#place_order').text('הזמנה מאילת');
      } else {
        $('button#place_order').text('הזמנה רגילה');
      }
    });

    if (getCookie('eilatMode') === 'true') {
      $('.woocommerce-form-coupon-toggle').hide();
      $('#payment_method_cod').prop('checked', true).change();
      $('#billing_city').val('אילת');
      $('#billing_state').val('IL2600').change();
      // $('#shipping_method_0').val(local_pickup).change();
      toggleBillingFields(false);
    } else {
      $('#payment_method_cod').prop('checked', false).change();
      $('#billing_city').val('');
      $('#billing_state').val('').change();
      // $('select#shipping_method_0').val(currentShippingMethod).change();
      toggleBillingFields(true);
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
        // '#billing_country_field',
      ];
      fields.forEach(function (field) {
        $(field).toggle(display);
      });
    }


    function handleShippingMethodChange() {
      var selectedShippingMethod = $(this).val();
      var isLocalPickup = selectedShippingMethod === local_pickup;
      setCookie('eilatMode', isLocalPickup ? 'true' : 'false', 1);
      toggleBillingFields(!isLocalPickup);
      checkStock(true);
    
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
              cancelButtonText: 'יציאה ממצב אילת',
              showCancelButton: true,
            }).then((result) => {
              if (result.isConfirmed) {
                clearCart();
              } else {
                // revertToEilatMode();
                // showLoadingIndicator();
                setCookie('eilatMode', 'false', 1);
                $('#shipping_method_0').val('free_shipping:4').change();
                
              }
              // $(document.body).trigger('update_checkout');
              // $(document.body).on('updated_checkout', function () {
              //     location.reload();
              //   });
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

    $(document.body).on(
      'change',
      'select#shipping_method_0',
      handleShippingMethodChange
    );
    
    $('input#e_deliverydate_0').attr('placeholder', '');
    setTimeout(() => {
      $('span#select2-orddd_time_slot_0-container').text('בחר זמן איסוף');
    } , 1000);

    // function customValidationPasses() {
    //   var requiredFields = $('form.checkout').find(
    //     'input[required], select[required]'
    //   );
    //   var isValid = true;
    //   requiredFields.each(function () {
    //     if ($(this).val() === '') {
    //       isValid = false;
    //     }
    //   });
    //   return isValid;
    // }

    // $('#eilat_place_order').on('click', function (e) {
    //   alert('הזמנה מאילת');
    //   $('#eilat_place_order').prop('disabled', true);
    //   e.preventDefault();
    //   if (customValidationPasses()) {
    //     var orderData = $('form.checkout').serialize();

    //     $.ajax({
    //       type: 'POST',
    //       url: '/wp-admin/admin-ajax.php',
    //       data: {
    //         action: 'eilat_process_order',
    //         order_data: orderData,
    //       },
    //       success: function (response) {
    //         // Handle response here. Redirect to thank you page or show message
    //         if (response.success) {
    //           window.location.href = response.data.redirect_url;
    //         } else {
    //           // Handle failure
    //           // console.log(response);
    //           const message = response.data.message;
    //           const error = response.data.error;
    //           Swal.fire({
    //             icon: 'error',
    //             title: message,
    //             text: error,
    //           });
    //         }
    //         $('#eilat_place_order').prop('disabled', false);
    //       },
    //       error: function () {
    //         Swal.fire({
    //           icon: 'error',
    //           title: 'אירעה שגיאה',
    //           text: 'אנא נסה שוב מאוחר יותר',
    //         });
    //         $('#eilat_place_order').prop('disabled', false);
    //       },
    //     });
    //   } else {
    //     Swal.fire({
    //       icon: 'error',
    //       title: 'שדות חובה חסרים',
    //       text: 'אנא מלא את כל השדות החובה',
    //     });
    //     $('#eilat_place_order').prop('disabled', false);
    //   }
    // });
  });
})(jQuery);
