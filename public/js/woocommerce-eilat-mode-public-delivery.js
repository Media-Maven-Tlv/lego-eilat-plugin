(function ($) {
  'use strict';

  $(document).ready(function () {
    $('#order_delivery_date').flatpickr({
      locale: 'he',
      minDate: 'today',
      disable: [
        function (date) {
          var formattedDate =
            date.getDate().toString().padStart(2, '0') +
            '/' +
            (date.getMonth() + 1).toString().padStart(2, '0') +
            '/' +
            date.getFullYear().toString();

          return (
            excluded_dates.includes(formattedDate) || // Check if date is in excluded_dates
            date.getDay() === 6 || // Disable Saturdays
            date.getDay() === 5 // Disable Fridays
          );
        },
      ],
      dateFormat: 'd/m/Y',
      altInput: true,
      altFormat: 'd/m/Y',
      enableTime: false,
      time_24hr: true,
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

    $('#order_delivery_time').select2({
      placeholder: 'בחר שעה',
      allowClear: true,
      width: '100%',
      style: 'border: 1px solid #ccc',
    });
  });
})(jQuery);
