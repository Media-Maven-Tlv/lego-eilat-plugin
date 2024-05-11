(function ($) {
  'use strict';
  $(document).ready(function () {
    const addedToCart = false;
    $('.eilat-button').on('click', function (e) {
      e.preventDefault();
      addedToCart = true;
      // showLoadingIndicator();
      $(this).prop('disabled', true);
      $(this).addClass('loading');
      // $(this).text('מוסיף לעגלה...');
      var button = $(this);
      var product_id = button.data('product_id');
      var product_sku = button.data('product_sku');
      var quantity = button.data('quantity');
      if (!addedToCart) {
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
            button.prop('disabled', false);
            $(document.body).trigger('added_to_cart', [
              response.fragments,
              response.cart_hash,
              button,
            ]);
          },
        });
      }
    });

    $('#toggleEilatMode').on('click', toggleEilatMode);

      // Toggle Eilat mode on and off
  function toggleEilatMode(e) {
    e.preventDefault();
    $('.eilat_toggle_wrapper .form-check-input').addClass('loading');
    $('.eilat_toggle_wrapper .form-check-input').attr('disabled', 'disabled');
    var mode = getCookie('eilatMode') === 'true' ? 'false' : 'true';
    setCookie('eilatMode', mode, 1);
    // showLoadingIndicator();
    location.reload();
  };

    if (getCookie('eilatMode') === 'true') {
      $('#toggleEilatModeLabel').text('הזמנה מאילת');
    } else {
      $('#toggleEilatModeLabel').text('הזמנה מאילת');
    }
  });
})(jQuery);
