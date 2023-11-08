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

      // -- Аккордеон пунктов в мобильном меню
      class MobileMenuAccordion {
        constructor(target, config) {
          this._el = typeof target === "string" ? document.querySelector(target) : target;
          const defaultConfig = {
            alwaysOpen: true,
            duration: 350
          };
          this._config = Object.assign(defaultConfig, config);
          this.addEventListener();
        }
        addEventListener() {
          this._el.addEventListener("click", (e) => {
            const elHeader = e.target.closest(".expanded > a");
            if (!elHeader) {
              return;
            }
            if (!this._config.alwaysOpen) {
              const elOpenItem = this._el.querySelector(".show");
              if (elOpenItem) {
                elOpenItem !== elHeader.parentElement ? this.toggle(elOpenItem) : null;
              }
            }
            this.toggle(elHeader.parentElement);
          });
        }
        show(el) {
          const elBody = el.querySelector(".expanded .sub-menu");
          if (elBody.classList.contains("collapsing") || el.classList.contains("show")) {
            return;
          }
          elBody.style.display = "block";
          const height = elBody.offsetHeight;
          elBody.style.height = 0;
          elBody.style.overflow = "hidden";
          elBody.style.transition = `height ${this._config.duration}ms ease`;
          elBody.classList.add("collapsing");
          el.classList.add("slidedown");
          elBody.offsetHeight;
          elBody.style.height = `${height}px`;
          window.setTimeout(() => {
            elBody.classList.remove("collapsing");
            el.classList.remove("slidedown");
            elBody.classList.add("collapse");
            el.classList.add("show");
            elBody.style.display = "";
            elBody.style.height = "";
            elBody.style.transition = "";
            elBody.style.overflow = "";
          }, this._config.duration);
        }
        hide(el) {
          const elBody = el.querySelector(".expanded .sub-menu");
          if (elBody.classList.contains("collapsing") || !el.classList.contains("show")) {
            return;
          }
          elBody.style.height = `${elBody.offsetHeight}px`;
          elBody.offsetHeight;
          elBody.style.display = "block";
          elBody.style.height = 0;
          elBody.style.overflow = "hidden";
          elBody.style.transition = `height ${this._config.duration}ms ease`;
          elBody.classList.remove("collapse");
          el.classList.remove("show");
          elBody.classList.add("collapsing");
          window.setTimeout(() => {
            elBody.classList.remove("collapsing");
            elBody.classList.add("collapse");
            elBody.style.display = "";
            elBody.style.height = "";
            elBody.style.transition = "";
            elBody.style.overflow = "";
          }, this._config.duration);
        }
        toggle(el) {
          el.classList.contains("show") ? this.hide(el) : this.show(el);
        }
      }
      new MobileMenuAccordion(document.querySelector(".nav-mobile .main-menu"), {
        alwaysOpen: false
      });
      new MobileMenuAccordion(document.querySelector(".nav-mobile .secondary-menu"), {
        alwaysOpen: false
      });

    }
  };
})(jQuery);
