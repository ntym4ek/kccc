(function ($) {
  Drupal.behaviors.argocalc = {
    attach: function (context, settings) {

      // tooltips
      $(".spec.description a").each(function() {
        let id = $(this).data("id");
        let content = $("#" + id).html();

        tippy(this, {
          content: content,
          arrow: true,
        });
      });

    }
  };
})(jQuery);
