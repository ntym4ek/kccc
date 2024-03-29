(function ($) {
  Drupal.behaviors.ext_user = {
    attach : function(context, settings) {
      // панель добавления Видов деятельности
      // при клике на Добавить, показать
      $('#activity-add-pane', context).once('pane', function () {
        $('#activity-add-control').on('click', function () {
          $(this).hide();
          $('#activity-add-pane').animate({height: "show"}, 300);
          $('#activity-add-pane').css({overflow: "visible"});
        });
      });


      // при нажатии на крест, убрать адрес и показать ссылку на Адресную Книгу
      $('.address .close').on('click', function() {
        $(this).parent().replaceWith('');
        $('#address-add-control').show();
      });

      // Смена пароля
      // при нажатии на Изменить пароль заменить ссылку на текст и показать панель
      $('.form-item-password').on('click', '.pass-show', function() {
        $('.pass-text').html('<a class="pass-close btn-link">' + 'свернуть' + '</a>');
        $('.password-pane').show();
      });
      // при нажатии на Свернуть закрыть панель, вставить ссылку
      $('.form-item-password').on('click', '.pass-close', function() {
        $('.pass-text').html('<a class="pass-show btn-link">' + 'изменить' + '</a>');
        $('.password-pane').hide();
      });

      // скрыть сообщение при нажатии на крестик
      $('#messages-wrap .close').on('click', function() {
        $('#messages-wrap').animate({opacity: "hide", height: "hide"}, 500);
      });
    }
  };
})(jQuery);
