var Area = 1;
var Seeding = 1;

(function ($) {
    Drupal.behaviors.argocalc = {
        attach: function (context, settings) {

            Area = $("[name=area]").val() ? $("[name=area]").val() : 1;
            Seeding = $("[name=seed]").val() ? $("[name=seed]").val() : 1;

            function _switch_flip(flip) {
                try {
                    if ($(flip).attr("id") === "switch_all") {
                        $("#protection-program [id^=switch_]").each(function (index, fl) {
                            if ($(fl).prop("checked") !== $(flip).prop("checked")) { $(fl).trigger("click"); }
                        });
                        return;
                    }

                    var category = $("#category-" + $(flip).data("tid")),
                        cnt = $(category).data("cnt"),
                        reglament = $(flip).closest(".reglament");
                    if ($(flip).prop("checked")) {
                        $(reglament).addClass("is-active");
                        $("[data-print=" + $(reglament).attr("id") + "]").removeClass("hidden-print");
                        $(category).data("cnt", cnt + 1);
                        if (!cnt) {
                            $(category).addClass("is-active");
                        }
                    } else {
                        if ($("#switch_all").prop("checked")) { $("#switch_all").prop("checked", false); }

                        $(reglament).removeClass("is-active");
                        $("[data-print=" + $(reglament).attr("id") + "]").addClass("hidden-print");
                        cnt = cnt - 1;
                        if (!cnt) {
                            $(category).removeClass("is-active");
                            $(category).find("folder").html();
                        }
                        $(category).data("cnt", cnt);
                        $(reglament).find(".amountByItem").html("");
                    }
                }
                catch (error) { console.log("_switch_flip - " + error); }
            }

            function recalculate() {
                try {
                    var calc_arr = {};
                    $(".reglament").each(function (key, item) {
                        if ($(item).find("[id^=switch_]").prop("checked")) {
                            // обновить Запрос
                            var tid = $(item).find("[id^=switch_]").data("tid");
                            // посчитать стоимость
                            var amountByItem = 0;
                            $(item).find("[id^=range_]").each(function (index, slider) {
                                var price = $(item).find("[id^=switch_]").data("price" + index);
                                var rate = $(slider).val();
                                amountByItem += amountByItem + rate * price;
                            });
                            // для протравителей умножить на норму высева
                            if (tid == 71533) { amountByItem = amountByItem * Seeding/1000; }
                            var price_html = amountByItem ? accounting.formatNumber(amountByItem, 0, " ") + " руб." + " x " + Area + " га = " + accounting.formatNumber(amountByItem * Area, 0, " ") + " руб." : "цена не задана";
                            $(item).find(".amountByItem").html(price_html);

                            var cat_id = $(item).data("cat");
                            if (!calc_arr[cat_id]) { calc_arr[cat_id] = 0; }
                            if (!calc_arr.total) { calc_arr.total = 0; }
                            calc_arr[cat_id] += amountByItem;
                            calc_arr.total += amountByItem;
                        }
                    });

                    for (var index in calc_arr) {
                        $("#" + index).find(".amountByCat .amount").html(accounting.formatNumber(calc_arr[index], 0, " ") + " руб.");
                        $("#" + index).find(".amountByCat .total").html(accounting.formatNumber(calc_arr[index] * Area, 0, " ") + " руб.");
                    }
                    $(".amountByProgram .amount").html(accounting.formatNumber(calc_arr.total, 0, " ") + " руб.");
                    $(".amountByProgram .total").html(accounting.formatNumber(calc_arr.total * Area, 0, " ") + " руб.");

                    if (calc_arr.total) {
                        $(".calculation-total > p.note").css("display", "block");
                        $(".calculation-total > p.choose-one").css("display", "none");
                    }
                    else {
                        $(".calculation-total > p.note").css("display", "none");
                        $(".calculation-total > p.choose-one").css("display", "block");
                    }
                }
                catch (error) { console.log("recalculate - " + error); }
            }
            
            $("[id^=switch_]").on("change", function() {
                _switch_flip(this);
                recalculate();
            });

            $("[id^=range_]").on("change", function() {
                if (!$(this).closest(".reglament").find("[id^=switch_]").prop("checked")) {
                    $(this).closest(".reglament").find("[id^=switch_]").trigger("click");
                } else {
                    recalculate();
                }
            });

            // init range sliders on page load
            $("[id^=range_]").ionRangeSlider();
            recalculate();

            // smooth scroll program into the view
            if ($("#protection-program").length) {
                if ($("#protection-program").offset().top - $(window).height() > 100) {
                    var offset = $("#protection-program").offset().top - 250;
                    $("html, body").animate({scrollTop: offset + "px"});
                }
            }

            // remove program on parameters change
            $("[name=phase], [name=area], [name=seed], [name^=weeds], [name^=pests], [name^=diseases], [name=desic], [name=fert]").on("change", function() {
                $("#protection-program").remove();
                $("#request").remove();
            });
        }
    };
})(jQuery);