(function ($) {
  Drupal.ext_block = Drupal.ext_block || {};

  // Определение функции рендера пагинатора для блока История
  Drupal.ext_block.renderBulletHistory = function (index, className) {
    let name = $("#carousel-history .swiper-wrapper .swiper-slide").eq(this[0]).data("name");
    return "<span class=\"milestone " + this[1] + "\"><b>" + name + "</b></span>";
  };

})(jQuery);

