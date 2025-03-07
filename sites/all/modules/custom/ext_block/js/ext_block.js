(function ($) {
  Drupal.ext_block = Drupal.ext_block || {};

  // Определение функции рендера пагинатора для блока История
  Drupal.ext_block.renderBulletHistory = function (index, className) {
    let name = $("#carousel-history .swiper-wrapper .swiper-slide").eq(this[0]).data("name");
    return "<span class=\"milestone " + this[1] + "\"><b>" + name + "</b></span>";
  };

  Drupal.behaviors.ext_block = {
    attach: function (context, settings) {

      // --- Popup Telegram ----------------------------------------------------
      setTimeout(()=> {
        var popupCookie = "popup-tg2";
        var popupTg = $.cookie(popupCookie, { path: '/' });
        if (!popupTg) {
          $(".popup-tg-close").closest(".block-popup-tg").fadeIn(200);
          $.cookie(popupCookie, true, { path: '/', expires: 30 }); // 30 days
        }
      }, 5000);

      $(".popup-tg-close, .block-popup-tg a").click(() => {
        $(".popup-tg-close").closest(".block-popup-tg").fadeOut(200);
      });
    }
  };

})(jQuery);

