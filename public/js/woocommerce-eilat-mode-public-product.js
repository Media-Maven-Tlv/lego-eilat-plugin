(function ($) {
  'use strict';
  $(document).ready(function () {
    $('.eilat-button').on('click', function (e) {
      e.preventDefault();
      showLoadingIndicator();
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
          hideLoadingIndicator();
          $(document.body).trigger('added_to_cart', [
            response.fragments,
            response.cart_hash,
            button,
          ]);
        },
      });
    });

    if (getCookie('eilatMode') === 'true') {
      $('#toggleEilatModeLabel').text('הזמנה מחוץ לאילת');
    } else {
      $('#toggleEilatModeLabel').text('הזמנה מאילת');
    }
  });
})(jQuery);
