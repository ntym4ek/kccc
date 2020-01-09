(function ($) {
    Drupal.behaviors.representatives = {
        attach: function (context, settings) {

            $('.popup-trigger-js').on("click", function(e){
                if ($(this).hasClass('selected')) {
                    $(this).removeClass('selected');
                    $('.rep-item').show();
                    $('.rep-list .clearfix').show();
                }
                else {
                    $('.popup-trigger-js').removeClass('selected');
                    $('.rep-item').show();

                    $(this).addClass('selected');
                    var region = $(this).data('region');
                    $('.rep-item').each(function(){
                        if(!$(this).hasClass(region)) {
                            $(this).hide();
                            $('.rep-list .clearfix').hide();
                        }
                    });
                }
            });
            return false;
        }
    };
})(jQuery);


