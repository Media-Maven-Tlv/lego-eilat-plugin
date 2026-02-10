(function ($) {
  'use strict';

  // Constants for easier modification and clearer references
  const eilatModeCookieName = 'eilatMode';
  const eilatShippingMethod = local_pickup;
  const regularShippingMethod = 'free_shipping:4';

  // Global variable to track the last shipping method
  let lastShippingMethod;

  // Initialize once the document is ready
  // FIX: Cloudflare Rocket Loader breaks jQuery's .ready() — the internal
  // readyList deferred is never resolved because Rocket Loader interferes with
  // jQuery's DOMContentLoaded/readyState initialization. Use a Rocket Loader-safe
  // approach: if the document is already complete, call directly via setTimeout;
  // otherwise fall back to the DOMContentLoaded event.
  function onDomReady(fn) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', fn);
    } else {
      setTimeout(fn, 0);
    }
  }

  onDomReady(function () {
    lastShippingMethod = $('select#shipping_method_0').val();
    initializeCheckout();
  });

  function initializeCheckout() {
    setupEventListeners();
    applyInitialSettings();
  }

  function setupEventListeners() {
    $(document.body).on('updated_checkout', updateCheckout);
    $(document.body).on(
      'change',
      'select#shipping_method_0',
      handleShippingMethodChange
    );
    $(document.body).on(
      'click',
      'a.woocommerce-terms-and-conditions-link',
      function (e) {
        if (getCookie(eilatModeCookieName) === 'true') {
          e.preventDefault();
          $('a.woocommerce-terms-and-conditions-link').attr(
            'href',
            'https://lego.certifiedstore.co.il/eis-eilat/'
          );
        }
      }
    );
  }

  function applyInitialSettings() {
    const isEilatMode = getCookie(eilatModeCookieName) === 'true';
    toggleEilatMode(isEilatMode);

    // Show inline pickup notice if a pickup method is already selected on load
    // (popup only shows on active user change, not on page load)
    var currentVal = $('select#shipping_method_0').val() || '';
    if (isPickupMethod(currentVal)) {
      togglePickupNotice();
    }
  }

  function updateCheckout() {
    const placeOrderText =
      getCookie(eilatModeCookieName) === 'true' ? 'הזמנה באילת' : 'המשך לתשלום';
    $('button#place_order').text(placeOrderText);

    // Ensure we have a fallback for lastShippingMethod
    const fallbackShippingMethod = lastShippingMethod || regularShippingMethod;

    $('#shipping_method_0').val(
      getCookie(eilatModeCookieName) === 'true'
        ? local_pickup
        : fallbackShippingMethod
    );

    // Re-check pickup notice after checkout fragments refresh
    setTimeout(togglePickupNotice, 200);
  }

  function toggleEilatMode(enable) {
    if (enable) {
      lastShippingMethod = $('select#shipping_method_0').val(); // Save the current method before changing
      toggleCoupon(true);
      $('#payment_method_cod').prop('checked', true).trigger('change');
      $('#billing_city').val('אילת');
      $('#billing_state').val('IL2600').trigger('change');
      toggleBillingFields(false);
      toggleExtraFields(false);
      toggleBillingTitle(false);
      termsLink(true);
      setTimeout(() => {
        $('#shipping_method_0').val(local_pickup).trigger('change');
      }, 2000);
    } else {
      $('#payment_method_cod').prop('checked', false).trigger('change');
      toggleCoupon(false);
      $('#billing_city').val('');
      $('#billing_state').val('').trigger('change');
      toggleBillingFields(true);
      toggleExtraFields(true);
      toggleBillingTitle(true);
      $('.woocommerce-billing-fields h3').text('פרטי חיוב');
      termsLink(false);
      setTimeout(() => {
        const fallbackShippingMethod = lastShippingMethod || regularShippingMethod;
        $('#shipping_method_0').val(fallbackShippingMethod).trigger('change');
      }, 2000);
    }
  }

  function toggleBillingFields(display) {
    const fields = [
      '#billing_address_1_field',
      '#billing_address_2_field',
      'p#billing_address_3_field',
      '#billing_city_field',
      '#billing_state_field',
      '#billing_postcode_field',
    ];
    fields.forEach((field) => $(field).toggle(display));
    if ($.fn.select2) { $('#billing_state').select2(); }
    $('span.optional').hide();
    fields.forEach((field) => {
      const label = $(field + ' label');
      if (label.find('abbr.required').length === 0) {
        label.append('<abbr class="required" title="נדרש">*</abbr>');
      }
    });
  }

  function toggleExtraFields(display) {
    var coupon = $('.woocommerce-form-coupon-toggle');
    var deliveryDetails = $('#custom_delivery_details');
    var eilatBanner = $('.eilat-banner');
    coupon.toggle(display);
    deliveryDetails.toggle(!display);
    eilatBanner.hide();
  }

  function toggleBillingTitle(display) {
    var title = $('.woocommerce-billing-fields h3');
    if (display) {
      title.text('פרטי חיוב');
    } else {
      title.text('פרטי הזמנה');
    }
  }

  // ── Pickup IDs from theme (set via wp_localize_script) ──
  const pickupIds = (typeof legoPickupNotice !== 'undefined') ? legoPickupNotice.pickupIds : [];

  function isPickupMethod(val) {
    // Exclude Eilat pickup — it has its own dedicated flow
    if (val === eilatShippingMethod) return false;
    return pickupIds.indexOf(val) !== -1 || (val && val.indexOf('local_pickup') === 0);
  }

  function getSelectedMethodName() {
    return $('select#shipping_method_0 option:selected').text().trim() || '';
  }

  function togglePickupNotice() {
    var val = $('select#shipping_method_0').val() || '';
    var $notice = $('#lego-pickup-notice');
    if (isPickupMethod(val)) {
      $('.lego-pickup-method-name').text(getSelectedMethodName());
      $notice.slideDown(200);
    } else {
      $notice.slideUp(200);
    }
  }

  function showPickupPopup() {
    $('.lego-pickup-method-name').text(getSelectedMethodName());
    $('#lego-pickup-popup').css('display', 'flex').hide().fadeIn(250);
  }

  function hidePickupPopup() {
    $('#lego-pickup-popup').fadeOut(200);
  }

  // Close popup — close on button click or backdrop click (but not content area)
  $(document).on('click', '#lego-pickup-popup', function (e) {
    if ($(e.target).is('#lego-pickup-popup') || $(e.target).closest('#lego-pickup-popup-close').length) {
      hidePickupPopup();
    }
  });

  // ── update_order_review AJAX (WooCommerce doesn't handle <select> natively) ──
  function refreshOrderReview() {
    if (typeof wc_checkout_params === 'undefined') return;
    var $form = $('form.checkout');
    if (!$form.length) return;

    var shipping = {};
    $('select.shipping_method, input[name^="shipping_method"][type="radio"]:checked, input[name^="shipping_method"][type="hidden"]').each(function () {
      shipping[$(this).data('index')] = $(this).val();
    });

    $('.woocommerce-checkout-payment, .woocommerce-checkout-review-order-table').block({
      message: null, overlayCSS: { background: '#fff', opacity: 0.6 }
    });

    $.post(
      wc_checkout_params.wc_ajax_url.toString().replace('%%endpoint%%', 'update_order_review'),
      {
        security: wc_checkout_params.update_order_review_nonce,
        shipping_method: shipping,
        post_data: $form.serialize()
      },
      function (data) {
        if (data && data.fragments) {
          $.each(data.fragments, function (k, v) { $(k).replaceWith(v); });
        }
        $(document.body).trigger('updated_checkout', [data]);
      },
      'json'
    );
  }

  function handleShippingMethodChange() {
    const selectedShippingMethod = $(this).val();
    const isEilatMode = selectedShippingMethod === local_pickup;

    // Store the last non-eilat shipping method for restoration later
    if (!isEilatMode && selectedShippingMethod !== local_pickup) {
      lastShippingMethod = selectedShippingMethod;
    }

    setCookie(eilatModeCookieName, isEilatMode ? 'true' : 'false', 1);
    toggleBillingFields(!isEilatMode);
    toggleExtraFields(!isEilatMode);
    toggleBillingTitle(!isEilatMode);
    termsLink(isEilatMode);
    toggleEilatBanner(isEilatMode);
    toggleCoupon(isEilatMode);
    
    // Only check stock when switching TO Eilat mode (local pickup)
    if (isEilatMode) {
      checkStock(true);
    }

    // Pickup notice popup + inline notice
    if (isPickupMethod(selectedShippingMethod)) {
      showPickupPopup();
    } else {
      hidePickupPopup();
    }
    togglePickupNotice();

    // Refresh order review (WooCommerce doesn't do it for <select> changes)
    refreshOrderReview();
  }

  function checkStock(isEilat) {
    $.ajax({
      type: 'POST',
      url: eilat_rest_api.rest_url + 'eilat/v1/check-stock',
      beforeSend: function (xhr) {
        xhr.setRequestHeader('X-WP-Nonce', eilat_rest_api.nonce);
      },
      data: { eilatMode: isEilat },
      success: function (response) {
        if (!response.success) {
          handleStockError(response.data);
        }
      },
      error: function () {
        Swal.fire({
          icon: 'error',
          title: 'טעות תקשורת',
          text: 'נראה שיש בעיה בבקשה לשרת. נסה שוב מאוחר יותר.',
          confirmButtonText: 'סגור',
        });
      },
    });
  }

  function handleStockError(errorMessage) {
    Swal.fire({
      icon: 'error',
      title: errorMessage || 'אין מלאי לפריטים בעגלה',
      confirmButtonText: 'ריקון עגלה',
      cancelButtonText: 'מעבר לעגלה',
      showCancelButton: true,
    }).then((result) => {
      if (result.isConfirmed) {
        clearCart();
      } else {
        location.href = '/cart';
      }
    });
  }

  function clearCart() {
    $.ajax({
      type: 'POST',
      url: eilat_rest_api.rest_url + 'eilat/v1/clear-cart',
      beforeSend: function (xhr) {
        xhr.setRequestHeader('X-WP-Nonce', eilat_rest_api.nonce);
      },
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

  function toggleCoupon(isEilat) {
    if (isEilat) {
      $('.woocommerce-form-coupon-toggle').hide();
      $.ajax({
        type: 'POST',
        url: eilat_rest_api.rest_url + 'eilat/v1/remove-coupon',
        beforeSend: function (xhr) {
          xhr.setRequestHeader('X-WP-Nonce', eilat_rest_api.nonce);
        },
      });
    } else {
      $('.woocommerce-form-coupon-toggle').show();
    }
  }

  function termsLink(isEilat) {
    if (isEilat) {
      $('a.woocommerce-terms-and-conditions-link').attr(
        'href',
        'https://lego.certifiedstore.co.il/eis-eilat/'
      );
    }
  }
})(jQuery);
