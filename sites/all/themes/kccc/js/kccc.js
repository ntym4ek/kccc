(function ($) {
  Drupal.behaviors.kccc = {
    attach: function (context, settings) {

      // --- Главное Меню ------------------------------------------------------
      // не переходить по ссылке на выпадающих меню в мобильной версии
      $(".header .menu-wr .expanded > a").on("click", (e) => {
        e.preventDefault();
      });

      // -- Скролл мышкой подменю на десктопах ---------------------------------
      $(".page-highlighted .sub-menu").mousedown(function(event) {
        let $this = $(this),
          startX = event.pageX - $this.offset().left,
          scrollLeft = $this.scrollLeft();

        $this.data('mouseMoveHandler', function(event) {
          event.preventDefault();
          let x = event.pageX - $this.offset().left,
            walk = (x - startX) * 2;
          $this.scrollLeft(scrollLeft - walk);
        });

        $(document).mousemove($this.data('mouseMoveHandler'));

        $(document).mouseup(function() {
          $(document).off('mousemove', $this.data('mouseMoveHandler'));
        });
      });

      // -- Обновить историю URL -----------------------------------------------
        // если содержимое страницы загружено с помощью AJAX,
        // установить новую ссылку, соОтветствующую содержмому
        // (Каталог и ПвП)
      if (settings.history) {
        window.history.pushState(null, null, settings.history.url);
      }


    }
  };
})(jQuery);
