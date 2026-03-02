(function ($) {
  'use strict';

  // Constants for easier modification and clearer references
  const eilatModeCookieName = 'eilatMode';
  const eilatShippingMethod = local_pickup;
  const regularShippingMethod = 'free_shipping:4';

  // Global variable to track the last shipping method
  let lastShippingMethod;
  let currentShippingMethod;

  // ── Branch stock check config (injected by theme via wp_localize_script) ──
  const branchStockConfig = (typeof legoBranchStock !== 'undefined') ? legoBranchStock : null;

  function isBranchStockCheckMethod(val) {
    return branchStockConfig && branchStockConfig.stockCheckMethods.hasOwnProperty(val);
  }

  function getBranchStoreName(val) {
    return (branchStockConfig && branchStockConfig.stockCheckMethods[val]) || '';
  }

  var legoSwalBase = {
    background: '#fff',
    color: '#000',
    confirmButtonColor: '#e3000b',
    cancelButtonColor: '#FFD700',
    customClass: {
      popup: 'lego-swal-popup',
      title: 'lego-swal-title',
      htmlContainer: 'lego-swal-html',
      confirmButton: 'lego-swal-confirm',
      cancelButton: 'lego-swal-cancel',
    },
  };

  function onDomReady(fn) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', fn);
    } else {
      setTimeout(fn, 0);
    }
  }

  onDomReady(function () {
    lastShippingMethod = $('select#shipping_method_0').val();
    currentShippingMethod = lastShippingMethod;
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

    var currentVal = $('select#shipping_method_0').val() || '';
    if (isPickupMethod(currentVal)) {
      togglePickupNotice();
    }
  }

  function updateCheckout() {
    const placeOrderText =
      getCookie(eilatModeCookieName) === 'true' ? 'הזמנה באילת' : 'המשך לתשלום';
    $('button#place_order').text(placeOrderText);

    const fallbackShippingMethod = lastShippingMethod || regularShippingMethod;

    var newVal = getCookie(eilatModeCookieName) === 'true'
        ? local_pickup
        : fallbackShippingMethod;
    $('#shipping_method_0').val(newVal);
    currentShippingMethod = newVal;

    setTimeout(togglePickupNotice, 200);
  }

  function toggleEilatMode(enable) {
    if (enable) {
      lastShippingMethod = $('select#shipping_method_0').val();
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

    if (selectedShippingMethod === currentShippingMethod) {
      return;
    }
    currentShippingMethod = selectedShippingMethod;

    const isEilatMode = selectedShippingMethod === local_pickup;

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
    
    if (isEilatMode) {
      checkStock(true);
    }

    if (!isEilatMode && isBranchStockCheckMethod(selectedShippingMethod)) {
      checkBranchStock(selectedShippingMethod, getBranchStoreName(selectedShippingMethod));
    }

    if (isPickupMethod(selectedShippingMethod)) {
      showPickupPopup();
    } else {
      hidePickupPopup();
    }
    togglePickupNotice();

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

  // ── Branch stock check (non-Eilat pickup methods) ──

  function checkBranchStock(shippingMethod, storeName) {
    Swal.fire(Object.assign({}, legoSwalBase, {
      title: 'מוודאים שיש לנו הכל בסניף ' + escapeHtml(storeName),
      allowOutsideClick: false,
      allowEscapeKey: false,
      showConfirmButton: false,
      didOpen: function () { Swal.showLoading(); },
    }));

    $.ajax({
      type: 'POST',
      url: branchStockConfig.restUrl + 'branch-stock-check',
      beforeSend: function (xhr) {
        xhr.setRequestHeader('X-WP-Nonce', branchStockConfig.nonce);
      },
      data: { shipping_method: shippingMethod },
      success: function (response) {
        if (response.success) {
          if (response.stock_check_active) {
            Swal.fire(Object.assign({}, legoSwalBase, {
              icon: 'success',
              title: 'הכל במלאי!',
              text: 'כל המוצרים זמינים בסניף ' + storeName,
              timer: 2000,
              showConfirmButton: false,
            }));
          } else {
            Swal.close();
          }
          return;
        }
        handleBranchOutOfStock(response.out_of_stock || [], storeName);
      },
      error: function () {
        Swal.fire(Object.assign({}, legoSwalBase, {
          icon: 'error',
          title: 'שגיאת תקשורת',
          text: 'לא ניתן לבדוק את המלאי כרגע. נסו שוב מאוחר יותר.',
          confirmButtonText: 'סגור',
        }));
      },
    });
  }

  function handleBranchOutOfStock(items, storeName) {
    if (!items.length) { Swal.close(); return; }

    var listHtml = '<div style="text-align:center;direction:rtl;margin:0.5em 0;">';
    listHtml += '<p style="color:#000;margin-bottom:0.8em;font-size:18px;">המוצרים הבאים לא זמינים בסניף ' + escapeHtml(storeName) + ':</p>';
    items.forEach(function (item) {
      listHtml += '<div style="padding:0.5em 0;border-bottom:1px solid #eee;color:#000;">' + escapeHtml(item.name) + '</div>';
    });
    listHtml += '</div>';

    Swal.fire(Object.assign({}, legoSwalBase, {
      icon: 'warning',
      title: 'מוצרים חסרים במלאי הסניף',
      html: listHtml,
      confirmButtonText: 'הסר מוצרים חסרים',
      cancelButtonText: 'מעבר לעגלה',
      showCancelButton: true,
      reverseButtons: true,
    })).then(function (result) {
      if (result.isConfirmed) {
        removeBranchOutOfStockItems(items);
      } else {
        window.location.href = '/cart';
      }
    });
  }

  function removeBranchOutOfStockItems(items) {
    var keys = items.map(function (item) { return item.cart_item_key; });

    Swal.fire(Object.assign({}, legoSwalBase, {
      title: 'מסיר מוצרים...',
      allowOutsideClick: false,
      allowEscapeKey: false,
      showConfirmButton: false,
      didOpen: function () { Swal.showLoading(); },
    }));

    $.ajax({
      type: 'POST',
      url: branchStockConfig.restUrl + 'remove-cart-items',
      beforeSend: function (xhr) {
        xhr.setRequestHeader('X-WP-Nonce', branchStockConfig.nonce);
      },
      contentType: 'application/json',
      data: JSON.stringify({ items: keys }),
      success: function (response) {
        if (response.success) {
          currentShippingMethod = '';
          refreshOrderReview();
          $(document.body).trigger('wc_fragment_refresh');
          Swal.fire(Object.assign({}, legoSwalBase, {
            icon: 'success',
            title: 'המוצרים הוסרו בהצלחה',
            timer: 1500,
            showConfirmButton: false,
          }));
        } else {
          Swal.fire(Object.assign({}, legoSwalBase, {
            icon: 'error',
            title: 'שגיאה',
            text: 'לא הצלחנו להסיר את המוצרים. נסו לעדכן את העגלה ידנית.',
            confirmButtonText: 'מעבר לעגלה',
          })).then(function () {
            window.location.href = '/cart';
          });
        }
      },
      error: function () {
        Swal.fire(Object.assign({}, legoSwalBase, {
          icon: 'error',
          title: 'שגיאת תקשורת',
          confirmButtonText: 'מעבר לעגלה',
        }).then(function () {
          window.location.href = '/cart';
        }));
      },
    });
  }

  function escapeHtml(str) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
  }
})(jQuery);
