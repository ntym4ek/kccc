(function($){
    Drupal.behaviors.SheduleAutoRefresh = {
        attach: function (context, settings) {
            // $("body").once(function () {
            //     var ajax = new Drupal.ajax(
            //             false,
            //             false,
            //             { url : "/shedule/ajax" }
            //         );
            //     var timer = window.setInterval(function(){
            //         $(document).queue(function() {
            //             ajax.eventResponse(ajax, {});
            //             $(document).dequeue();
            //         });
            //         return false; }, 5000);
            // });
        }
    };
})(jQuery);