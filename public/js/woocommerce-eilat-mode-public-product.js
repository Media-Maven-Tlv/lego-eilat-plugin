(function ($) {
  'use strict';
  $(document).ready(function () {
    // Note: The main eilat-button click handler is in woocommerce-eilat-mode-public.js
    // This file only handles product page specific functionality

    $('#toggleEilatMode').on('click', toggleEilatMode);

    // Toggle Eilat mode on and off
    function toggleEilatMode(e) {
      e.preventDefault();
      $('.eilat_toggle_wrapper .form-check-input').addClass('loading');
      $('.eilat_toggle_wrapper .form-check-input').attr('disabled', 'disabled');
      var mode = getCookie('eilatMode') === 'true' ? 'false' : 'true';
      setCookie('eilatMode', mode, 1);
      location.reload();
    }
  });
})(jQuery);
