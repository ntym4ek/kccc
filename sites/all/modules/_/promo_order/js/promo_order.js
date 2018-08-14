(function ($) {
    Drupal.behaviors.promo_order = {
        attach: function (context, settings) {
            $('input[id^="edit-volume"]').keyup(function() {
                var $id = $(this).attr('name');
                $id = $id.replace('volume-','');
                var $qty = $(this).val();
                var $price = $('#amount-' + $id).attr('price');
                $('#amount-' + $id).attr('amount', $price*$qty);
                $('#amount-' + $id).html(accounting.formatNumber($price*$qty*0.9, 0, " "));

                var $total = 0;
                $('span[id^="amount-"]').each(function(){
                    var $amount = $(this).attr('amount');
                    $amount = $amount.replace(/ /g, '');
                    $total = $total + parseInt($amount);
                });
                var $discount = $total * 0.1;
                $('#discount').html(accounting.formatNumber($discount, 0, " "));
                $('#total').html(accounting.formatNumber($total - $discount, 0, " "));
            });
        }
    };
})(jQuery);


