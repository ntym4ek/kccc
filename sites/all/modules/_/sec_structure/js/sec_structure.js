(function ($) {
  Drupal.behaviors.Structure = {
    attach: function (context, settings) {

      function getHeight(el) {
        var h = el.outerHeight();
        var box_wr = el.find(".box-wrapper.open");
        if (box_wr.length) {
          h += getHeight(box_wr.find(".s-level"));
        }

        return h;
      }

      $(".box").on("mouseover", function() {
        var wrapper = $(this).closest(".box-wrapper");
        wrapper.css("z-index", 12);
      });
      $(".box").on("mouseleave", function() {
        var wrapper = $(this).closest(".box-wrapper");
        if (wrapper.hasClass("open")) { wrapper.attr("style", ""); }
        else { wrapper.css("z-index", 1); }
      });
      $(".box").on("click", function() {
        var wrapper = $(this).closest(".box-wrapper");
        var s_level = $(this).closest(".s-level");
        var box = wrapper.children(".box");
        var delta = 0;

        if (wrapper.hasClass("open")) {
          wrapper.removeClass("open");
          s_level.find(".box-wrapper").each(function () {
            $(this).removeClass("shade");
          });
        } else {
          s_level.find(".box-wrapper").each(function () {
            $(this).removeClass("open").removeClass("shade");
          });
          s_level.children(".s-boxes").children(".box-wrapper").each(function () {
            $(this).addClass("shade");
          });
          wrapper.addClass("open").removeClass("shade");

          // положение открываемого подуровня
          var height = wrapper.children(".box-sublevel").children(".s-level").outerHeight();
          wrapper.find(".box-sublevel").css("bottom", (height+15)*-1);

          // вычислить высоту box-trace
          var box_bottom = box.offset().top + box.outerHeight();
          var parent_bottom = wrapper.closest(".s-level").offset().top + wrapper.closest(".s-level").outerHeight();
          delta = parent_bottom - box_bottom;
        }

        // высота box-trace
        box.find(".box-trace").css("bottom", delta*-1);

        // высота страницы
        var page_height = getHeight($(".structure .s-level"));
        if (page_height > $(".structure").height()) {
          $(".structure").css("height", page_height);
        }
      });

      // trace для верхнего бокса
      var box = $(".structure > .s-level > .s-boxes > .box-wrapper > .box");
      var wrapper = box.closest(".box-wrapper");
      var box_bottom = box.offset().top + box.outerHeight();
      var parent_bottom = wrapper.closest(".s-level").offset().top + wrapper.closest(".s-level").outerHeight();
      var delta = parent_bottom - box_bottom;
      box.children(".box-trace").css("bottom", delta*-1);

    }
  };
})(jQuery);
