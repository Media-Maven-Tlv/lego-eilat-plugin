(function ($) {
  ('use strict');

  // Create and append the loading indicator to the body
  // window.createLoadingIndicator = function () {
  //   var loadingHtml = '<div id="loadingIndicator" >טוען...</div>';
  //   $('body').prepend(loadingHtml);
  // };

  // Utility functions for cookie management
  window.setCookie = function (name, value, days) {
    var expires = '';
    if (days) {
      var date = new Date();
      date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
      expires = '; expires=' + date.toUTCString();
    }
    document.cookie = name + '=' + (value || '') + expires + '; path=/';
  };

  window.getCookie = function (name) {
    var nameEQ = name + '=';
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i].trim();
      if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length);
    }
    return null;
  };

  // window.showLoadingIndicator = function () {
  //   $('#loadingIndicator').css('display', 'flex');
  // };

  // window.hideLoadingIndicator = function () {
  //   $('#loadingIndicator').hide();
  // };

  // Revert to Eilat mode settings
  window.revertToEilatMode = function () {
    $('#shipping_method_0').val(local_pickup).change();
    setCookie('eilatMode', 'true', 1);
    toggleBillingFields(false);
    checkStock(true);
  };

  // Initialize page-specific functionalities
  $(document).ready(function () {
    // createLoadingIndicator(); // Call to create the loading indicator when document is ready

    $('#exit-eilat-mode').on('click', exitEilatMode);
    if (getCookie('eilatMode') === 'true') {
      $('body').addClass('eilat-mode');

      $('.eilat-button').each(function () {
        var eilatStock = $(this).attr('eilat-stock'); // Get the value of the 'eilat-stock' attribute

        if (eilatStock === 'true') {
          // Do something if eilat-stock is true
          console.log('Eilat stock is available');
          // Example action: enable the button
          $(this).text('הזמנה מאילת');
          $(this).on('click', function (e) {
            e.preventDefault();
            $(this).addClass('loading');
            // $(this).text('מוסיף לעגלה...');
            $(this).attr('disabled', 'disabled');
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
                // hideLoadingIndicator();
                button.removeClass('loading');
                // button.text('הזמנה מאילת');
                button.removeAttr('disabled');
                $(document.body).trigger('added_to_cart', [
                  response.fragments,
                  response.cart_hash,
                  button,
                ]);
              },
            });
          });
        } else {
          // Do something if eilat-stock is false
          console.log('Eilat stock is not available');
          // Example action: disable the button
          $(this).attr('href', '#');
          $(this).addClass('disabled');
          $(this).text('מוצר זה לא זמין באילת');
        }
      });
    } else {
      $('body').removeClass('eilat-mode');
    }
  });

  window.exitEilatMode = function (e) {
    e.preventDefault();
    setCookie('eilatMode', 'false', 1);
    // showLoadingIndicator();
    location.reload();
  };


})(jQuery);
