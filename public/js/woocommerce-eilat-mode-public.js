(function ($) {
  ('use strict');

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

  // Revert to Eilat mode settings
  window.revertToEilatMode = function () {
    $('#shipping_method_0').val(local_pickup).change();
    setCookie('eilatMode', 'true', 1);
    toggleBillingFields(false);
    checkStock(true);
  };

  // Add to cart with optional override
  window.addProductToEilat = function (button, override = false) {
    // Prevent double calls - check if already processing
    if (button.hasClass('processing') || button.prop('disabled')) {
      return false;
    }
    
    // Check if eilat_rest_api is defined
    if (typeof eilat_rest_api === 'undefined') {
      console.error('eilat_rest_api is not defined');
      alert('אירעה שגיאה בטעינת הדף. אנא רענן את הדף ונסה שנית.');
      return false;
    }
    
    button.addClass('loading processing');
    button.attr('disabled', 'disabled');

    var product_id = button.data('product_id');
    var product_sku = button.data('product_sku');
    var quantity = button.data('quantity') || 1;

    $.ajax({
      type: 'POST',
      url: eilat_rest_api.rest_url + 'eilat/v1/add-to-cart',
      beforeSend: function (xhr) {
        xhr.setRequestHeader('X-WP-Nonce', eilat_rest_api.nonce);
      },
      data: {
        product_id: product_id,
        product_sku: product_sku,
        quantity: quantity,
        override: override
      },
      success: function (response) {
        button.removeClass('loading processing');
        button.removeAttr('disabled');

        // Check if product exists (needs confirmation) - but only if not already processing override
        if (response.product_exists && !response.success && !override) {
          if (confirm('המוצר "' + response.product_name + '" כבר קיים בעגלה. האם ברצונך להחליף את הנתונים?')) {
            addProductToEilat(button, true);
          }
          return;
        } 
        // Check if we have success flag or fragments
        if (response.success === true || response.fragments || response.cart_hash) {
          // Update fragments if available
          if (response.fragments) {
            $.each(response.fragments, function(key, value) {
              $(key).replaceWith(value);
            });
          }
          // Trigger WooCommerce added to cart event
          $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, button]);
          // Open side cart if available
          $(document.body).trigger('wc_fragment_refresh');
          if ($('.xoo-wsc-container').length) {
            $('.xoo-wsc-container').addClass('xoo-wsc-active');
          }
          return;
        } 
        // Check for error
        if (response.error) {
          alert(response.error);
          return;
        }
        // If we got here with no clear response, reload as fallback
        window.location.reload();
      },
      error: function (xhr, status, error) {
        button.removeClass('loading processing');
        button.removeAttr('disabled');
        console.error('Eilat add to cart error:', status, error);
        alert('אירעה שגיאה. אנא נסה שנית מאוחר יותר.');
      }
    });
  };

  // Initialize page-specific functionalities
  $(document).ready(function () {
    if (getCookie('eilatMode') === 'true') {
      $('body').addClass('eilat-mode');

      $('.eilat-button').each(function () {
        var eilatStock = $(this).attr('eilat-stock');

        if (eilatStock === 'true') {
          $(this).text('הזמנה באילת');
        } else {
          $(this).attr('href', '#');
          $(this).addClass('disabled');
          $(this).text('מוצר זה לא זמין באילת');
        }
      });

      toggleEilatBanner(true);
    } else {
      $('body').removeClass('eilat-mode');
      toggleEilatBanner(false);
    }

    $('#exit-eilat-mode').on('click', exitEilatMode);

    // Handle eilat-button click events (for carousels and other non-product pages)
    $(document).on('click', '.eilat-button', function (e) {
      e.preventDefault();
      
      var button = $(this);
      
      // Prevent double clicks - check if button is already processing or disabled
      if (button.hasClass('processing') || button.hasClass('loading') || button.prop('disabled')) {
        return false;
      }

      // If the addProductToEilat function is available, use it
      if (typeof window.addProductToEilat === 'function') {
        window.addProductToEilat(button);
      }
    });
  });

  window.toggleEilatBanner = function (display) {
    var bannerHtml = `<div class="eilat-banner d-flex flex-column flex-sm-row justify-content-center align-items-center p-2 gap-1 gap-sm-3">
			<p class="text-center text-light mb-0">
הנכם נמצאים בתהליך הזמנה בסניף ביג אילת
			</p>
			<button class="btn btn-danger text-light btn-sm" id="exit-eilat-mode">
				ליציאה מתהליך זה
			</button>
		</div>`;
    var eilatBanner = $('.eilat-banner');
    if (display) {
      $('body').prepend(bannerHtml);
    } else {
      eilatBanner.remove();
    }
  };

  window.exitEilatMode = function (e) {
    e.preventDefault();
    setCookie('eilatMode', 'false', 1);
    location.reload();
  };
})(jQuery);
