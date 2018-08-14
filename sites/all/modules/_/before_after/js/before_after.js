(function ($) {
    Drupal.behaviors.Before_After = {
        attach: function (context, settings) {

            // развернуть/свернуть текст
            $('.c1 .s1').on('click', function() {
                var flag = $(this).parent().parent().data('flag');
                if (flag) {
                    $(this).parent().parent().find('.cc1').css('height', '55px');
                    $(this).parent().parent().find('.cc2').css('height', '0');
                    $(this).parent().parent().data('flag', 0);
                } else {
                    $(this).parent().parent().find('.cc1').css('height', '0');
                    $(this).parent().parent().find('.cc2').css('height', 'initial');
                    $(this).parent().parent().data('flag', 1);
                }
            });
        }
    };
})(jQuery);