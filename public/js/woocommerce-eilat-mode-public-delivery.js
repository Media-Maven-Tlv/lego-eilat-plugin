(function ($) {
  'use strict';

  $(document).ready(function () {
    // Disable the time selection initially
    $('#order_delivery_time').prop('disabled', true);
  
    function disableClosestTwoHours() {
      var now = new Date();
      var hours = now.getHours();
      var minutes = now.getMinutes();
      var timeSlots = [
        '11:00 - 11:30', '11:30 - 12:00', '12:00 - 12:30', '12:30 - 13:00',
        '13:00 - 13:30', '13:30 - 14:00', '14:00 - 14:30', '14:30 - 15:00',
        '15:00 - 15:30', '15:30 - 16:00', '16:00 - 16:30', '16:30 - 17:00',
        '17:00 - 17:30', '17:30 - 18:00', '18:00 - 18:30', '18:30 - 19:00',
        '19:00 - 19:30', '19:30 - 20:00', '20:00 - 20:30', '20:30 - 21:00'
      ];
  
      var currentTimeSlot = (hours - 11) * 2 + (minutes >= 30 ? 1 : 0); // Adjust to match the start time 11:00
  
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
  
      $('#order_delivery_time').select2({ // Refresh the select2 dropdown
        placeholder: 'בחר שעה',
        allowClear: true,
        width: '100%',
        style: 'border: 1px solid #ccc',
      });
    }
  
    $('#order_delivery_date').flatpickr({
      locale: 'he',
      minDate: 'tomorrow', // Disable today by setting minDate to tomorrow
      enable: [
        function (date) {
          var today = new Date();
          var dayOfWeek = today.getDay();
          var threeDaysFromToday = new Date(
            today.getFullYear(),
            today.getMonth(),
            today.getDate() + 3
          );
          date.setHours(0, 0, 0, 0); // Reset time to midnight
    
          var formattedDate =
            date.getDate().toString().padStart(2, '0') +
            '/' +
            (date.getMonth() + 1).toString().padStart(2, '0') +
            '/' +
            date.getFullYear().toString();
    
          // Determine if today is Wednesday and handle Saturday closure
          if (dayOfWeek === 3) { // Wednesday is 3
            var nextSunday = new Date(today);
            nextSunday.setDate(today.getDate() + (7 - today.getDay()));
    
            // Ensure next Sunday is open if today is Wednesday
            if (date.getTime() === nextSunday.setHours(0, 0, 0, 0)) {
              return true;
            }
          }
    
          return (
            date > new Date().setHours(0, 0, 0, 0) && // Disable today
            date <= threeDaysFromToday &&
            date.getDay() !== 6 && // Disable Saturdays
            !excluded_dates.includes(formattedDate) // Exclude specific dates
          );
        },
      ],
      dateFormat: 'd/m/Y',
      altInput: true,
      altFormat: 'd/m/Y',
      enableTime: false,
      time_24hr: true,
      theme: 'airbnb',
      onChange: function (selectedDates, dateStr, instance) {
        // Clear the time slot value on date change
        $('#order_delivery_time').val(null).trigger('change');
    
        if (selectedDates.length > 0) {
          var selectedDate = selectedDates[0];
          var today = new Date();
          selectedDate.setHours(0, 0, 0, 0);
          today.setHours(0, 0, 0, 0);
    
          // Enable the time selection when a valid date is chosen
          $('#order_delivery_time').prop('disabled', false);
    
          // If today is selected, disable the closest two hours
          if (selectedDate.getTime() === today.getTime()) {
            disableClosestTwoHours();
          } else {
            // Enable all time slots if a future date is selected
            $('#order_delivery_time option').prop('disabled', false).css('color', '');
            $('#order_delivery_time').select2({ // Refresh the select2 dropdown
              placeholder: 'בחר שעה',
              allowClear: true,
              width: '100%',
              style: 'border: 1px solid #ccc',
            });
          }
        } else {
          // Disable the time selection if no date is selected
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
