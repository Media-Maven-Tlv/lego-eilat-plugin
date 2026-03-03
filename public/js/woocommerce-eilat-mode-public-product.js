(function ($) {
  'use strict';
  $(document).ready(function () {
    // Note: The main eilat-button click handler is in woocommerce-eilat-mode-public.js
    // This file only handles product page specific functionality

    $('#toggleEilatMode').on('click', toggleEilatMode);

    // Toggle Eilat mode on and off
    function toggleEilatMode(e) {
      e.preventDefault();
      // Bail early if Eilat mode is globally disabled
      if (typeof eilat_config === 'undefined' || eilat_config.globally_enabled !== '1') {
        return;
      }
      $('.eilat_toggle_wrapper .form-check-input').addClass('loading');
      $('.eilat_toggle_wrapper .form-check-input').attr('disabled', 'disabled');
      var mode = getCookie('eilatMode') === 'true' ? 'false' : 'true';
      setCookie('eilatMode', mode, 1);
      location.reload();
    }
  });
})(jQuery);
