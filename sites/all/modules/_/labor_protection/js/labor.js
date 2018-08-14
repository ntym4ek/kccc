(function ($) {
    Drupal.behaviors.labor = {
        attach: function (context, settings) {
            $('.labor-protection').once(function() {
                if ($('.switch').html()) { $('.switch').attr('text', $('.switch').html()); }
                $('.switch').val('05:00').html('05:00').attr('disabled', '').addClass('form-button-disabled');


                // функция, отображающая таймер
                function start() {
                    var remain_bv = 300;

                    function parseTime_bv(timestamp) {
                        if (timestamp < 0) { timestamp = 0; }

                        var mins = Math.floor((timestamp) / 60);
                        var secs = Math.floor(timestamp - mins * 60);

                        mins = (String(mins).length > 1) ? mins : "0" + mins;
                        secs = (String(secs).length > 1) ? secs : "0" + secs;
                        if ($('.switch').val() !== $('.switch').attr('text')) {
                            $('.switch').val(mins + ":" + secs).html(mins + ":" + secs);
                        }
                    }

                    var t = setInterval(function () {
                        remain_bv = remain_bv - 1;
                        parseTime_bv(remain_bv);
                        if (remain_bv <= 0) {
                            if ($('.switch').hasClass('form-button-disabled')) {
                                $('.switch').val($('.switch').attr('text')).html($('.switch').attr('text')).removeAttr('disabled', '').removeClass('form-button-disabled');
                            }
                            clearInterval(t);
                        }
                    }, 1000);
                }

                // мониторим клик по iframe с видео
                var monitor = setInterval(function () {
                    var elem = document.activeElement;
                    if (elem && elem.tagName === 'IFRAME') {
                        start();
                        clearInterval(monitor);
                    }
                }, 100);
            });

        }
    };
})(jQuery);


