(function ($) {
  'use strict';

  $(document).ready(function () {
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
      // plugins: [new confirmDatePlugin({
      //   confirmText: "אישור",
      //   showAlways: false,
      //   theme: "dark"
      // })]
      // onChange: function (selectedDates, dateStr, instance) {
      //   console.log(dateStr);
      // },
    });

    var timeSlots = [];
    for (var i = 0; i < 24; i++) {
      var time = i + ':00 - ' + (i + 1) + ':00';
      timeSlots.push({ id: time, text: time });
    }

    const selected_time_slots = document.getElementById('selected_time_slots');
    const choices = new Choices(selected_time_slots, {
      removeItemButton: true,
      searchEnabled: true,
      placeholder: true,
      placeholderValue: 'Select time slots',
      placeholder: true,
      shouldSort: false,
      choices: timeSlots,
      //add time slots options to the select, each block 1 hour
    });

    $(document).on('change', '#delivery_date', function () {
      var dateSelected = $(this).val();
      console.log(dateSelected);
      $.ajax({
        url: checkout_params.ajaxurl,
        type: 'POST',
        data: {
          action: 'get_available_time_slots',
          date: dateSelected,
        },
        success: function (result) {
          // Assuming result is an array of time slots
          var $timeSlotSelect = $('#delivery_time_slot');
          $timeSlotSelect.empty().removeAttr('disabled');
          $.each(result, function (index, timeSlot) {
            $timeSlotSelect.append(
              $('<option></option>')
                .attr('value', timeSlot.value)
                .text(timeSlot.text)
            );
          });
        },
      });
    });
  });

  //append dates after input
})(jQuery);
