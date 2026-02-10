(function ($) {
  'use strict';

  $(document).ready(function () {
    var calendarEl = document.getElementById('calendar');
    var loadingOverlay = $('#loading-overlay');

    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      showNonCurrentDates: false,

      events: function (fetchInfo, successCallback, failureCallback) {
        loadingOverlay.fadeIn();

        jQuery.ajax({
          url: calendarData.ajaxurl,
          type: 'POST',
          data: {
            action: 'load_eilat_orders',
            start: fetchInfo.startStr,
            end: fetchInfo.endStr,
          },
          success: function (response) {
            successCallback(response);
            loadingOverlay.fadeOut();
          },
          error: function () {
            failureCallback();
            loadingOverlay.fadeOut();
          },
        });
      },
      eventDidMount: function (info) {
        var orderDetails = `
            <strong>Order #${info.event.title.replace(
              'Order #',
              ''
            )}</strong><br>
            Customer: ${info.event.extendedProps.customerName}<br>
            Total: ₪${info.event.extendedProps.total}<br>
            Status: ${info.event.extendedProps.status}
        `;

        $(info.el).attr('title', orderDetails);

        tippy(info.el, {
          content: orderDetails,
          allowHTML: true,
          theme: 'light',
          placement: 'top',
        });
      },
      eventContent: function (info) {
        var statusColor = info.event.backgroundColor || '#000000';
        var label = $('<span class="event-status-label"></span>').css({
          'background-color': statusColor,
          width: '10px',
          height: '10px',
          'border-radius': '50%',
          display: 'inline-block',
          'margin-right': '5px',
        });

        var titleEl = $('<span></span>').text(info.event.title);
        var containerEl = $('<div></div>').append(label).append(titleEl);

        return { domNodes: [containerEl[0]] };
      },
    });

    if (window.location.href.indexOf('page=eilat-delivery-calendar') > -1) {
      calendar.render();
    }

    if (window.location.href.indexOf('page=eilat-settings') > -1) {
      $('#excluded_dates').flatpickr({
        locale: 'he',
        mode: 'multiple',
        minDate: 'today',
        dateFormat: 'd/m/Y',
        altInput: true,
        altFormat: 'd/m/Y',
        enableTime: false,
        time_24hr: true,
        inline: true,
        theme: 'airbnb',
        disable: [
          function (date) {
            return (
              date.getDay() === 6 ||
              date.getDay() === 5
            );
          },
        ],
      });
    }
  });
})(jQuery);
