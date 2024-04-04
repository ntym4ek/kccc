(function ($) {
  Drupal.behaviors.swiper = {
    attach: function (context, settings) {

      // --- Инициализировать слайдеры и карусели ------------------------------
      // навигация и пейджер вынесены за пределы слайдера,
      // поэтому классы указываются принудительно
      $(".slider").each(function() {

        var swiper = $(this).find(".swiper");
        if (swiper) {
          var pager = $(this).find("> .swiper-pagination");
          var nextEl = $(this).find("> .swiper-button-next");
          var prevEl = $(this).find("> .swiper-button-prev");

          var options = {
            speed: 400,
            slidesPerView: 1,
            spaceBetween: 0,
            pagination: {
              el: pager ? pager[0] : null,
              clickable: true,
            },
            navigation: {
              nextEl: nextEl ? nextEl[0] : null,
              prevEl: prevEl ? prevEl[0] : null,
            },
          };

          var autoHeight = $(this).data("autoheight");
          if (autoHeight) {
            options.autoHeight = true;
          }


          // Кастомный пагинатор.
          // Требуется передать:
          // - параметр pagination-render-bullet = имя рендер функции (пример имени в блоке История компании)
          // в renderBullet ниже функция вызова кастомной рендер-функции по текстовому имени с использованием пространства имён.
          let dynamicBulletsFunc = $(this).data("pagination-render-bullet");
          if (dynamicBulletsFunc) {
            options.pagination.renderBullet = function () {
              var args = Array.prototype.slice.call(arguments, 0);
              var namespaces = dynamicBulletsFunc.split(".");
              var func = namespaces.pop();
              var context = window;
              for(var i = 0; i < namespaces.length; i++) {
                context = context[namespaces[i]];
              }
              return context[func].apply(args);
            };
          }

          new Swiper(swiper[0], options);
        }
      });

      $(".carousel").each(function() {
        var swiper = $(this).find(".swiper");

        if (swiper.length) {
          var nextEl = $(this).find("> .swiper-button-next");
          var prevEl = $(this).find("> .swiper-button-prev");
          var pager = $(this).find("> .swiper-pagination");

          var options = {
            speed: 400,
            touchMoveStopPropagation: true,
            breakpoints: {
              320: {
                spaceBetween: 15
              },
            },
            navigation: {
              nextEl: nextEl ? nextEl[0] : null,
              prevEl: prevEl ? prevEl[0] : null,
            },
            pagination: {
              el: pager ? pager[0] : null,
              clickable: true,
            },
          };

          var autoHeight = $(this).data("autoheight");
          if (autoHeight) {
            options.autoHeight = true;
          }

          var autoplay = $(this).data("autoplay");
          if (autoplay) {
            options.autoplay = {
              delay: autoplay
            };
          }

          var slidesperview = $(this).data("slidesperview");
          if (slidesperview) {
            options.slidesPerView = slidesperview;
            options.spaceBetween = 30;
          }

          var slidesperview_xs = $(this).data("slidesperview-xs");
          if (slidesperview_xs) {
            options.breakpoints[320] = {
              slidesPerView: slidesperview_xs,
              spaceBetween: 15,
            };
          }

          var slidesperview_md = $(this).data("slidesperview-md");
          if (slidesperview_md) {
            options.breakpoints[768] = {
              slidesPerView: slidesperview_md,
              spaceBetween: 30,
            };
          }
          else {
            options.breakpoints[768] = {
              spaceBetween: 30,
            };
          }

          var slidesperview_lg = $(this).data("slidesperview-lg");
          if (slidesperview_lg) {
            options.breakpoints[1024] = {
              slidesPerView: slidesperview_lg,
              spaceBetween: 30,
            };
          }

          var slidesperview_xl = $(this).data("slidesperview-xl");
          if (slidesperview_xl) {
            options.breakpoints[1366] = {
              slidesPerView: slidesperview_xl,
              spaceBetween: 30,
            };
          }

          // Кастомный пагинатор.
          // Требуется передать:
          // - параметр pagination-render-bullet = имя рендер функции (пример имени в блоке История компании)
          // в renderBullet ниже функция вызова кастомной рендер-функции по текстовому имени с использованием пространства имён.
          let dynamicBulletsFunc = $(this).data("pagination-render-bullet");
          if (dynamicBulletsFunc) {
            options.pagination.renderBullet = function () {
              var args = Array.prototype.slice.call(arguments, 0);
              var namespaces = dynamicBulletsFunc.split(".");
              var func = namespaces.pop();
              var context = window;
              for (var i = 0; i < namespaces.length; i++) {
                context = context[namespaces[i]];
              }
              return context[func].apply(args);
            };
          }

          new Swiper(swiper[0], options);
        }
      });



    }
  };
})(jQuery);
