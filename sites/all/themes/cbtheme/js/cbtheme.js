(function ($) {
  Drupal.behaviors.cbtheme = {
    attach: function (context, settings) {

      // --- Меню --------------------------------------------------------------
      $(".expanded > a").on("click", (e) => {
        // не переходить по ссылке на выпадающих меню
        e.preventDefault();
      });

      // --- Мобильное меню --------------------------------------------------------
      function showMobileNav() {
        $("body").data("nav-mobile-opened", true).addClass("nav-mobile-opened");
      }
      function hideMobileNav() {
        $("body").data("nav-mobile-opened", false).removeClass("nav-mobile-opened");
      }
      function toggleMobileNav() {
        if ($("body").data("nav-mobile-opened")) {
          hideMobileNav();
        } else {
          showMobileNav();
        }
      }

        // если < 1280, то выводится боковое меню
        // повесить обработчик свайпа
      if ($(window).width() < 1280) {
        // клик по иконке Меню
        $(".nav-mobile-label").on("click", (e) => {
          toggleMobileNav();
          e.stopPropagation();
          // e.stopPropagation().preventDefault();
        });

        $(".nav-mobile-left .page, .nav-mobile-left .nav-mobile-label").on("swiped-right", (e) => {
          // если свайп вправо на Свайпере или блоке с классом main-menu-disabled, то не показываем меню
          let is_swiper = $(e.target).closest(".main-menu-disabled, .swiper").length > 0;
          if (!is_swiper) { showMobileNav(); }
        });
        $(".nav-mobile-right .page, .nav-mobile-right .nav-mobile-label").on("swiped-left", (e) => {
          // если свайп вправо на Свайпере, то не показываем меню
          let is_swiper = $(e.target).closest(".swiper").length > 0;
          if (!is_swiper) { showMobileNav(); }
        });
        $(".nav-mobile-left .page, .nav-mobile-left .nav-mobile, .nav-mobile-left .nav-mobile-label").on("swiped-left", () => {
          hideMobileNav();
        });
        $(".nav-mobile-right .page, .nav-mobile-right .nav-mobile, .nav-mobile-right .nav-mobile-label").on("swiped-right", () => {
          hideMobileNav();
        });
        $(".page").on("click", () => {
          hideMobileNav();
        });
      }
    }
  };
})(jQuery);
