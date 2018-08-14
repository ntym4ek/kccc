(function ($) {
    Drupal.behaviors.terms = {
        attach: function (context, settings) {
            // при наведении на термин показать значение
            $('body').on('click', function(){
                if (!$(this).hasClass('ru')) {
                    var tooltip = $('.terms').data('tooltip');
                    if (tooltip != 'undefined') {
                        $(tooltip).removeClass('pop')
                    }
                }
            });
            $('.close').on('click', function(){
                var tooltip = $('.terms').data('tooltip');
                if (tooltip != 'undefined') {
                    $(tooltip).removeClass('pop')
                }
            });

            $('.ru').on('click', function(e){
                var tooltip = $('.terms').data('tooltip');
                if (tooltip != 'undefined') {
                    $(tooltip).removeClass('pop')
                }
                tooltip = $(this).parent().parent().find('.mean');
                tooltip.addClass('pop');
                $('.terms').data('tooltip', tooltip);
                e.stopPropagation();
            });

            $('.letter a').bind("click", function(e){
                // погасить подстветку
                var highlight = $('.terms').data('highlight');
                if (highlight != 'undefined') {
                    $(highlight).removeClass('highlight')
                }
                var anchor = $(this);

                // включить подсветку
                highlight = $(anchor.attr('href')).parent();
                highlight.addClass('highlight');
                $('.terms').data('highlight', highlight);

                // скроллинг
                $('html, body').stop().animate({
                    scrollTop: $(anchor.attr('href')).offset().top-100
                }, 1000);
                e.preventDefault();
            });
            return false;
        }
    };
})(jQuery);


