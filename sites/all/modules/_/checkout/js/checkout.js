/**
 * Created by ntym on 16.11.2015.
 */

(function ($) {
    Drupal.behaviors.Checkout = {
        attach: function (context, settings) {


            $('#checkout-cart').once(function(){
                /** ---- Изменение количества товара --------------------- */
                $(':input[name=update-cart]').hide();

                $('.qty-plus').on('click', function () {
                    var $input = $(this).closest('.cc-qty').find('input');
                    $input.val(parseInt($input.val()) + 1);
                    show_update_button(this);
                    return false;
                });

                $('.qty-minus').on('click', function () {
                    var $input = $(this).closest('.cc-qty').find('input');
                    var $qty = parseInt($input.val()) - 1;
                    $input.val($qty > 0 ? $qty : 1);
                    show_update_button(this);
                    return false;
                });

                /** ---- при изменении количества в корзине, вывести кнопку 'обновить' - */
                $('#checkout-cart').on('keyup', ':input[id^="edit-checkout-cart-list-item-"]', function(){
                    show_update_button(this);
                });


                /** ---- при клике на 'обновить' убрать кнопку и нажать submit - */
                $('#checkout-cart').on('click', '.cc-update', function(){
                    $(':input[name=update-cart]').click();
                });
            });

            function show_update_button(e) {
                var $cc_item = $(e).closest('.cc-item');
                if($cc_item.find('.cc-update').length === 0) {
                    var $cc_action = $cc_item.find('.cc-action');
                    $cc_action.append('<a class="btn btn-info btn-sm cc-update"><i class="fa fa-refresh" aria-hidden="true"></i></a>');
                }
            }
        }
    }
})(jQuery);