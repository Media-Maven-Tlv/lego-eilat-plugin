(function ($) {
  'use strict';

  $(document).ready(function () {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',

      events: function (fetchInfo, successCallback, failureCallback) {
        jQuery.ajax({
          url: calendarData.ajaxurl,
          type: 'POST',
          data: {
            action: 'load_eilat_orders',
          },
          success: function (response) {
            successCallback(response);
          },
          error: function () {
            failureCallback();
          },
        });
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
            // var formattedDate =
            //   date.getDate().toString().padStart(2, '0') +
            //   '/' +
            //   (date.getMonth() + 1).toString().padStart(2, '0') +
            //   '/' +
            //   date.getFullYear().toString();

            return (
              // excluded_dates.includes(formattedDate) || // Check if date is in excluded_dates
              date.getDay() === 6 || // Disable Saturdays
              date.getDay() === 5 // Disable Fridays
            );
          },
        ],
        // plugins: [new confirmDatePlugin({
        //   confirmText: "אישור",
        //   showAlways: false,
        //   theme: "dark"
        // })]
        // onChange: function (selectedDates, dateStr, instance) {
        //   console.log(dateStr);
        // },
      });
      // var timeSlots = [];
      // for (var i = 0; i < 24; i++) {
      //   var time = i + ':00 - ' + (i + 1) + ':00';
      //   timeSlots.push({ id: time, text: time });
      // }

      // const selected_time_slots = document.getElementById(
      //   'selected_time_slots'
      // );
      // const choices = new Choices(selected_time_slots, {
      //   removeItemButton: true,
      //   searchEnabled: true,
      //   placeholder: true,
      //   placeholderValue: 'Select time slots',
      //   placeholder: true,
      //   shouldSort: false,
      //   choices: timeSlots,
      //   //add time slots options to the select, each block 1 hour
      // });
    }

    // $(document).on('change', '#delivery_date', function () {
    //   var dateSelected = $(this).val();
    //   console.log(dateSelected);
    //   $.ajax({
    //     url: checkout_params.ajaxurl,
    //     type: 'POST',
    //     data: {
    //       action: 'get_available_time_slots',
    //       date: dateSelected,
    //     },
    //     success: function (result) {
    //       // Assuming result is an array of time slots
    //       var $timeSlotSelect = $('#delivery_time_slot');
    //       $timeSlotSelect.empty().removeAttr('disabled');
    //       $.each(result, function (index, timeSlot) {
    //         $timeSlotSelect.append(
    //           $('<option></option>')
    //             .attr('value', timeSlot.value)
    //             .text(timeSlot.text)
    //         );
    //       });
    //     },
    //   });
    // });
  });

  //append dates after input
})(jQuery);
