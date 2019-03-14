/**
 *
 */
(function ($, Drupal, window, document, undefined) {

    Drupal.behaviors.mini_calc = {
        attach: function (context, settings) {

            // расчёт стоиомсти обработки по регламенту на странице Препарата
            function evaluate() {
                var unit_rate = $("[name='units']").val() === "ga" ? "га" : "сот";
                var unit_volume = $("#units").data("unit-volume");
                if (unit_rate !== "га") {
                    unit_volume = unit_volume === "л" ? "мл" : "г";
                }
                var mult_rate = unit_rate === "га" ? 1 : 0.01;
                var mult_volume = unit_rate === "га" ? 1 : 1000;

                $("#units").html(unit_volume + "/" + unit_rate);

                var $price0 = $('#price_0').val();
                var $price1 = $('#price_1').val();
                var $area = $('.calculation input').val();
                var $rate_str = $('.rate.active').html();
                if ($rate_str) {
                    var $rate_arr = [];
                    if ($price1 === undefined) {
                        $price1 = $price0;
                        $rate_arr[0] = $rate_str.split('-');
                    } else {
                        $rate_arr = $rate_str.split('+');
                        $rate_arr[0] = $rate_arr[0].split('-');
                        $rate_arr[1] = $rate_arr[1].split('-');
                    }

                    $rate_arr[0][0] = $rate_arr[0][0].replace(',', '.');
                    $rate_arr[0][0] = parseFloat($rate_arr[0][0]) * mult_rate;
                    var $rate_from0 = accounting.formatNumber($area * $rate_arr[0][0] * mult_volume, 0, " ");
                    var $price_from0 = accounting.formatNumber($area * $rate_arr[0][0] * $price0, 2, " ");
                    var $rate_to0 = "", $price_to0 = "";
                    if ($rate_arr[0].length > 1) {
                        $rate_arr[0][1] = $rate_arr[0][1].replace(',', '.');
                        $rate_arr[0][1] = parseFloat($rate_arr[0][1]) * mult_rate;
                        $rate_to0 = accounting.formatNumber($area * $rate_arr[0][1] * mult_volume, 0, " ");
                        $price_to0 = accounting.formatNumber($area * $rate_arr[0][1] * $price0, 2, " ");
                    }
                    var $rate0 = $rate_from0 + ($rate_to0 ? ' - ' + $rate_to0 : '');
                    var $amount0 = $price_from0 + ($price_to0 ? " - " + $price_to0 : "") + " руб.";

                    var $rate1 = "", $amount1 = "";
                    if ($rate_arr[1] !== undefined) {
                        $rate_arr[1][0] = $rate_arr[1][0].replace(',', '.');
                        $rate_arr[1][0] = parseFloat($rate_arr[1][0]) * mult_rate;
                        var $rate_from1 = accounting.formatNumber($area * $rate_arr[1][0] * mult_volume, 0, " ");
                        var $price_from1 = accounting.formatNumber($area * $rate_arr[1][0] * $price1, 2, " ");
                        var $rate_to1 = "", $price_to1 = "";
                        if ($rate_arr[1][1] !== undefined) {
                            $rate_arr[1][0] = $rate_arr[1][0].replace(',', '.');
                            $rate_arr[1][1] = parseFloat($rate_arr[1][1]);
                            $rate_to1 = accounting.formatNumber($area * $rate_arr[1][1] * mult_volume, 0, " ");
                            $price_to1 = accounting.formatNumber($area * $rate_arr[1][1] * $price1, 2, " ");
                        }
                        $rate1 = $rate_from1 + ($rate_to1 ? ' - ' + $rate_to1 : '');
                        $amount1 = $price_from1 + ($price_to1 ? ' - ' + $price_to1 : '') + ' руб.';
                    }

                    var $calc_volume = 0, $calc_cost = 0;
                    if ($rate_arr[1] !== undefined && $rate_arr[1][1] !== undefined) {
                        $calc_volume = $rate0 + '<br />+ ' + $rate1;
                        $calc_cost = $amount0 + '<br />+ ' + $amount1;
                    } else {
                        $calc_volume = $rate0 + ($rate1 ? ' + ' + $rate1 : '');
                        $calc_cost = $amount0 + ($amount1 ? ' + ' + $amount1 : '');
                    }


                    $('.calc-volume').html($calc_volume);
                    $('.calc-cost').html($calc_cost);
                }
            }
            $(document).ready( function(){
                evaluate();
            });

            $('.rate').on('click', function() {
                var $clicked = this;
                $('.rate.active').removeClass('active');
                $($clicked).addClass('active');
                evaluate();
            });
            $("[name='units']").on('change', function() {
                evaluate();
            });
            $('.calculation input').on('keyup', function() {
                evaluate();
            });
        }
    };

})(jQuery, Drupal, this, this.document);

