(function ($) {
  Drupal.behaviors.Checkout = {
    attach: function (context, settings) {

      /* ------------------------------------- checkout/* ------------------------------------------------------- */
      // $(".checkout-contact").once(function() {
      //   checkButton();
      //   $(".form-item-checkout-contact-request-forma-region select").on("keyup", checkButton);
      //   $(".form-item-checkout-contact-request-forma-name input").on("keyup", checkButton);
      //   $(".form-item-checkout-contact-request-forma-email input").on("keyup", checkButton);
      //   $(".form-item-checkout-contact-request-forma-phone input").on("keyup", checkButton);
      // });
      // function checkButton() {
      //   var region = $(".form-item-checkout-contact-request-forma-region select");
      //   var name = $(".form-item-checkout-contact-request-forma-name input");
      //   var email = $(".form-item-checkout-contact-request-forma-email input");
      //   var phone = $(".form-item-checkout-contact-request-forma-phone input");
      //   var q1 = region.val();
      //   var q2 = name.val();
      //   var q3 = email.val();
      //   var q4 = phone.val();
      //   if (region.val() === "All" || !name.val() || ! email.val() || !phone.val()) {
      //     if (!$(".checkout-continue").prop( "disabled")) {
      //       $(".checkout-continue").prop( "disabled", true ).after("<p class='button-notice'>Заполните поля выше прежде чем продолжить.</p>");
      //     }
      //   } else {
      //     $(".checkout-continue").prop("disabled", false);
      //     $(".checkout-buttons .button-notice").remove();
      //   }
      // }


      $("[name=update-cart]").hide();

      $('input[name*=edit_quantity]', context).each(function () {
        var $input = $(this);

        // сохранить колво для последующего сравнения
        $(this).data("value", $(this).val());

        // обновлять только если значение отличается
        $(this).on("change", function () {
          if ($(this).data("value") !== $(this).val()) {
            Drupal.ext_commerce.quantityChanged(this);
          }
        });

        $input.keyup(function () {
          if ($(this).val()) {
            Drupal.ext_commerce.quantityChanged(this);
          }
        });

      });
    }
  };

  Drupal.ext_commerce = {
    timer: false,

    quantityChanged: function(element) {
      clearTimeout(Drupal.ext_commerce.timer);
      Drupal.ext_commerce.timer = setTimeout(function() {
        var $input = $(element);

        $input.closest('.view-commerce-cart-form').addClass('view-commerce-cart-form-loading');
        $input.trigger('quantityChanged');

        if ($input.hasClass('quantity-input-spinner')) {
          $input.spinner('disable');
        }
      }, 400);
    }
  };

})(jQuery);
