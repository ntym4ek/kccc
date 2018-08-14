(function ($) {
    Drupal.behaviors.Datepicker = {
        attach: function (context, settings) {
            $('.datepicker', context).datepicker({
                dateFormat: 'dd.mm.yy'
            });
        }
    };
})(jQuery);