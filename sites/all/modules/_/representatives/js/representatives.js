(function ($) {
    Drupal.behaviors.representatives = {
        attach: function (context, settings) {
            // перенесено в common.js
            // $('.popup-trigger-js').on("hover", function(e){
            //     var win = $(this).parent().find('.popup');
            //     if ($(win).hasClass('pop')) { $(win).removeClass('pop'); }
            //     else { $(win).addClass('pop'); }
            // });
            // $('.popup .close').on("click", function(e){
            //     var win = $(this).parent();
            //     $(win).removeClass('pop');
            // });
            return false;
        }
    };
})(jQuery);


