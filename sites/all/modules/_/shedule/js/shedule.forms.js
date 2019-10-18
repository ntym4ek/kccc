(function($){
    Drupal.behaviors.SheduleForms = {
        attach: function (context, settings) {
            $("[name^=field_period]").datetimepicker({
                lang: 'ru',
                format: 'd.m.Y - H:i',
                dayOfWeekStart: 1,
                allowBlank: true,
                validateOnBlur: false
            });
        }
    };
})(jQuery);