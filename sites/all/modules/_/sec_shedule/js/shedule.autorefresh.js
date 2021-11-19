(function($){
    Drupal.behaviors.SheduleAutoRefresh = {
        attach: function (context, settings) {

            $("body").once(function () {

                // обновление страницы с интервалом 5 сек
                var ajax = new Drupal.ajax(
                        false,
                        false,
                        { url : "/shedule/ajax" }
                    );
                $(document).queue(function() {
                    ajax.eventResponse(ajax, {});
                    $(document).dequeue();
                });

                var timer = window.setInterval(function(){
                    $(document).queue(function() {
                        ajax.eventResponse(ajax, {});
                        $(document).dequeue();
                    });
                    return false; }, 50000);

                // часы
                setInterval(function () {
                    var date = new Date(),
                        h = date.getHours(),
                        // h = date.getHours()-2,
                        m = date.getMinutes(),
                        s = date.getSeconds();
                    h = (h < 10) ? "0" + h : h;
                    m = (m < 10) ? "0" + m : m;
                    s = (s < 10) ? "0" + s : s;
                    $(".clock").html("<span class=\"hours\">" + h + "</span><span class=\"minutes\">:" + m + "</span><span class=\"seconds\"> " + s + "</span>");
                }, 1000);

            });

        }
    };
})(jQuery);
