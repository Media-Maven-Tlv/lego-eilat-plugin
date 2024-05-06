(function ($) {
  'use strict';

  // Create and append the loading indicator to the body
  window.createLoadingIndicator = function () {
    var loadingHtml =
      '<div id="loadingIndicator" style="position: fixed;width: 100%;height: 100%;top: 0;left: 0;z-index: 9999;color:white;background: rgba(0, 0, 0, 0.75) url(\'path_to_spinner.gif\') center no-repeat;align-items: center;justify-content: center;font-size: 50px;">טוען...</div>';
    $('body').prepend(loadingHtml);
  };

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

  window.showLoadingIndicator = function () {
    $('#loadingIndicator').css('display', 'flex');
  };

  window.hideLoadingIndicator = function () {
    $('#loadingIndicator').hide();
  };

  // Revert to Eilat mode settings
  window.revertToEilatMode = function () {
    $('#shipping_method_0').val(local_pickup).change();
    setCookie('eilatMode', 'true', 1);
    toggleBillingFields(false);
    checkStock(true);
  };

  // Initialize page-specific functionalities
  $(document).ready(function () {
    createLoadingIndicator(); // Call to create the loading indicator when document is ready

    $('#toggleEilatMode').on('click', toggleEilatMode);
    $('#exit-eilat-mode').on('click', exitEilatMode);
    if (getCookie('eilatMode') === 'true') {
      $('body').addClass('eilat-mode');
    } else {
      $('body').removeClass('eilat-mode');
    }
  });

  window.exitEilatMode = function (e) {
    e.preventDefault();
    setCookie('eilatMode', 'false', 1);
    showLoadingIndicator();
    location.reload();
  };

  // Toggle Eilat mode on and off
  window.toggleEilatMode = function (e) {
    e.preventDefault();
    var mode = getCookie('eilatMode') === 'true' ? 'false' : 'true';
    setCookie('eilatMode', mode, 1);
    showLoadingIndicator();
    location.reload();
  };
})(jQuery);
