(function ($) {
  'use strict';

  $(document).ready(function () {
    var config = (typeof eilat_delivery_config !== 'undefined')
      ? eilat_delivery_config
      : { opening_time: '11:00', closing_time: '19:00', closed_days: ['5', '6'] };

    var openingParts = config.opening_time.split(':');
    var closingParts = config.closing_time.split(':');
    var openingHour = parseInt(openingParts[0], 10);
    var openingMin  = parseInt(openingParts[1], 10);
    var closingHour = parseInt(closingParts[0], 10);
    var closingMin  = parseInt(closingParts[1], 10);
    var closedDays  = config.closed_days.map(Number);

    function generateTimeSlots() {
      var slots = [];
      var h = openingHour, m = openingMin;
      while (h < closingHour || (h === closingHour && m < closingMin)) {
        var startH = h.toString().padStart(2, '0');
        var startM = m.toString().padStart(2, '0');
        m += 30;
        if (m >= 60) { m -= 60; h += 1; }
        var endH = h.toString().padStart(2, '0');
        var endM = m.toString().padStart(2, '0');
        slots.push(startH + ':' + startM + ' - ' + endH + ':' + endM);
      }
      return slots;
    }

    var timeSlots = generateTimeSlots();

    $('#order_delivery_time').prop('disabled', true);

    function disableClosestTwoHours() {
      var now = new Date();
      var nowMinutes = now.getHours() * 60 + now.getMinutes();
      var openMinutes = openingHour * 60 + openingMin;
      var currentTimeSlot = Math.floor((nowMinutes - openMinutes) / 30);

      if (currentTimeSlot < 0) {
        currentTimeSlot = 0;
      }

      var slotsToDisable = [];
      for (var i = currentTimeSlot; i < currentTimeSlot + 4 && i < timeSlots.length; i++) {
        slotsToDisable.push(timeSlots[i]);
      }

      $('#order_delivery_time option').each(function () {
        if (slotsToDisable.includes($(this).val())) {
          $(this).prop('disabled', true).css('color', '#ccc');
        } else {
          $(this).prop('disabled', false).css('color', '');
        }
      });

      $('#order_delivery_time').select2({
        placeholder: 'בחר שעה',
        allowClear: true,
        width: '100%',
        style: 'border: 1px solid #ccc',
      });
    }

    function getNextOpenDates(count) {
      var dates = [];
      var d = new Date();
      d.setHours(0, 0, 0, 0);
      d.setDate(d.getDate() + 1);

      var maxLookahead = 14;
      while (dates.length < count && maxLookahead > 0) {
        var formatted =
          d.getDate().toString().padStart(2, '0') + '/' +
          (d.getMonth() + 1).toString().padStart(2, '0') + '/' +
          d.getFullYear().toString();

        var excludedArr = (typeof excluded_dates === 'string')
          ? excluded_dates.split(',').map(function (s) { return s.trim(); })
          : (Array.isArray(excluded_dates) ? excluded_dates : []);

        if (closedDays.indexOf(d.getDay()) === -1 && excludedArr.indexOf(formatted) === -1) {
          dates.push(new Date(d));
        }
        d.setDate(d.getDate() + 1);
        maxLookahead--;
      }
      return dates;
    }

    var allowedDates = getNextOpenDates(3);

    $('#order_delivery_date').flatpickr({
      locale: 'he',
      minDate: 'tomorrow',
      enable: [
        function (date) {
          date.setHours(0, 0, 0, 0);
          return allowedDates.some(function (d) {
            return d.getTime() === date.getTime();
          });
        },
      ],
      dateFormat: 'd/m/Y',
      altInput: true,
      altFormat: 'd/m/Y',
      enableTime: false,
      time_24hr: true,
      theme: 'airbnb',
      onChange: function (selectedDates, dateStr, instance) {
        $('#order_delivery_time').val(null).trigger('change');

        if (selectedDates.length > 0) {
          var selectedDate = selectedDates[0];
          var today = new Date();
          selectedDate.setHours(0, 0, 0, 0);
          today.setHours(0, 0, 0, 0);

          $('#order_delivery_time').prop('disabled', false);

          if (selectedDate.getTime() === today.getTime()) {
            disableClosestTwoHours();
          } else {
            $('#order_delivery_time option').prop('disabled', false).css('color', '');
            $('#order_delivery_time').select2({
              placeholder: 'בחר שעה',
              allowClear: true,
              width: '100%',
              style: 'border: 1px solid #ccc',
            });
          }
        } else {
          $('#order_delivery_time').prop('disabled', true);
        }
      }
    });

    $('#order_delivery_time').select2({
      placeholder: 'בחר שעה',
      allowClear: true,
      width: '100%',
      style: 'border: 1px solid #ccc',
    });
  });

})(jQuery);
