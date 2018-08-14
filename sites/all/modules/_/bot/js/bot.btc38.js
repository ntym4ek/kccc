
(function($){
    Drupal.behaviors.btc38 = {
        attach: function (context, settings) {

            $('#start-button').once('start-button', function() {
                var element_settings = {
                    url: window.location.protocol + '//' + window.location.hostname +  settings.basePath + settings.pathPrefix + 'bot/btc38/nojs',
                    event: 'click'
                };
                var ajax = new Drupal.ajax(false, this, element_settings);

                $(document).ajaxComplete(function(e, xhr, settings){
                    if (!$('#stop-button').hasClass('stop')) {
                        ajax.eventResponse(ajax, {});
                    } else {
                        $('#stop-button').removeClass('stop');
                    }
                });
            });

            // $('#start-button').once('start-button', function() {
            //     var base = $(this).attr('id');
            //     var element_settings = {
            //         url: window.location.protocol + '//' + window.location.hostname +  settings.basePath + settings.pathPrefix + 'bot/btc38/nojs',
            //         event: 'click',
            //         success: function (response, status) {
            //             Drupal.ajax.prototype.success.apply(this, arguments);
            //             if (!$('#stop-button').hasClass('stop')) {
            //                 this.eventResponse(this, {});
            //             } else {
            //                 $('#stop-button').removeClass('stop');
            //             }
            //         }
            //     };
            //     Drupal.ajax[base] = new Drupal.ajax(base, this, element_settings);
            // });




            $('#stop-button').once('stop-button', function() {
                $(this).on('click', function() {
                    $(this).addClass('stop');
                });
            });
        }
    };
}
)(jQuery);