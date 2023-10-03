(function ($) {
  Drupal.behaviors.sec_catalog = {
    attach: function (context, settings) {

      // если содержимое страницы загружено с помощью AJAX,
      // установить новую ссылку, сответствующую содержмому
      if (settings.history) {
        window.history.pushState(null, null, settings.history.url);
      }

      // todo
      // при нажатии Назад в браузере содержимое не меняется

    }
  };
})(jQuery);
