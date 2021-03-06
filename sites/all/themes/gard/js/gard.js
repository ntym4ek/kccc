/**
 *
 */
(function ($, Drupal, window, document, undefined) {

    Drupal.behaviors.gard = {
        attach: function (context, settings) {

            /* ------------------------------------------ Main Menu ------------------------------------------------- */
            $("ul.level-2 > li").hover(function () {
                var hoveredEl = this;
                $(this).closest("ul.level-2").find("li").each(function(index, el) {
                    if (el !== hoveredEl) {
                        $(el).removeClass("visible");
                    }
                });
                $(this).addClass("visible");
            });
            // затемнение
            $(".level-1 > li").on("show.bs.dropdown", function () {
                var docHeight = $(document).height() + 50;
                var docWidth = $(document).width();
                var winHeight = $(window).height();
                if( docHeight < winHeight ) { docHeight = winHeight; }
                $("#modalBackdrop").css("top", 0).css("height", docHeight + "px").css("width", docWidth + "px").show();
            });
            $(".level-1 > li").on("hidden.bs.dropdown", function () {
                $("#modalBackdrop").hide();
            });

            /* ------------------------------------------ Right  Menu ----------------------------------------------- */
            $(".user-menu .dropdown").hover(function() {
                    $(this).addClass("open");
                },
                function() {
                    $(this).removeClass("open");
                }
            );
            $(".call_back_post").on("click", function () {
                TalkMe("openTab", 1);
            });


            /* ------------------------------------------ Animated Elements ----------------------------------------- */
            $("[data-animate=true]").each(function(){
                if ($(this).data("a-effect")) {
                    $(this).addClass($(this).data("a-effect"));
                } else {
                    $(this).addClass("slide-up");
                }
                $(this).addClass("animate-processed");
            });

            /* ------------------------------------------ блок корзины ---------------------------------------------- */
            /* - при обновлении блока корзины через секунду убрать класс с эффектом тряски - */
            /* - (класс добавляется в модуле checkout - checkout_commerce_fast_ajax_atc_commands_alter()) - */
            $("#block-cart").once(function() {
                $("#block-cart").each(function() {
                    setTimeout(function() { $("#block-cart").removeClass("shake-on"); }, 1500);
                });
            });

            /* ------------------------------------------  swipe events init ---------------------------------------- */
            function toggleMenu() {
                $("#navbar").toggleClass("slide-in");
                $(".menu-container").toggleClass("slide-in");
                $(".content-container > .content").toggleClass("body-slide-in");
            }

            $("body", context).once(function () {
                /* -----------------------------------  slide меню -------------------------------------------------- */
                $(".btn-s4").click(function () { toggleMenu(); });

                if (!device.desktop()) {
                    // свайп реализован на основе jquery.touchSwipe.min.js
                    // http://labs.rampinteractive.co.uk/touchSwipe/docs/index.html
                    $(".content-container > .content").swipe( {
                        threshold: Math.min($(document).width() / 2, 160),
                        swipeRight:function() {
                            toggleMenu();
                        },
                        excludedElements:$.fn.swipe.defaults.excludedElements+", .horizontal-tabs-list"
                    });
                    $(".side-menu").swipe( {
                        swipeLeft:function() {
                            toggleMenu();
                        },
                    });
                }

                /* ------------------------------------ панель поиска, Search --------------------------------------- */
                $(".sp3, .btn-s3").on("click", function () {
                    $("#search-pane").toggleClass("hide");
                });

                /* ------------------------------------ Prevent disabled link from following its href --------------- */
                $(".disabled").click(function(event){
                    event.preventDefault();
                });

                /* ------------------------------------ Слайдер - */
                $("#bootstrap-slider, [id^=views-bootstrap-carousel]").each(function(){
                    $(this).swipe( {
                        swipeLeft:function() { $(".carousel").carousel("next"); },
                        swipeRight:function(event) {
                            $(".carousel").carousel("prev");
                            event.stopPropagation();
                        },
                    });
                });
            });

            /* ------------------------------------ popup ----------------------------------------------------------- */
            $(".popup-trigger-js").on({
                mouseenter: function () {
                    $(this).find(".popup").addClass("pop");
                },
                mouseleave: function () {
                    $(this).find(".popup").removeClass("pop");
                }
            });
            $(".popup .close").on("click", function(){
                var win = $(this).parent();
                $(win).removeClass("pop");
            });

            /* ------------------------------------- print & share -------------------------------------------------- */
            $(".btn-print").on("click", function() {
                window.print();
            });
            $(".btn-share").on("click", function() {
                $(".header-share").toggleClass("closed");
            });

            /* ------------------------------------- e-address decode ----------------------------------------------- */
            /* <a href="e(supp/ort[s1]kcc/c[s2]ru)" class="eAddr-encoded"></a> */
            $("a.eAddr-encoded").each(function() {
                var $href = $(this).attr("href");
                var $pattern = /e\((.*)\)/;
                var $match = $pattern.exec($href);
                if ($match) {
                    var $eAddr = $match[1];
                    if ($eAddr) {
                        $eAddr = $eAddr.replace(/\//g, "");
                        $eAddr = $eAddr.replace(/\[s1\]/, "@");
                        $eAddr = $eAddr.replace(/\[s2\]/, ".");
                        $(this).attr("href", "mailto:" + $eAddr).removeClass("eAddr-encoded");
                        if ($(this).hasClass("eAddr-html")) {
                            $(this).html($eAddr).removeClass("eAddr-html");
                        }
                    }
                }
            });
            /* <tag class="eAddr-encoded">.*e(supp/ort[s1]kcc/c[s2]ru).*</tag> */
            $(".eAddr-encoded").each(function() {
                if (this.tagName !== "A") {
                    var $html = $(this).html();
                    var $pattern = /^(.*)e\((.*)\)(.*)$/;
                    var $match = $pattern.exec($html);
                    var $eAddr = $match[2];

                    if ($eAddr) {
                        $eAddr = $eAddr.replace(/\//g, "");
                        $eAddr = $eAddr.replace(/\[s1\]/, "@");
                        $eAddr = $eAddr.replace(/\[s2\]/, ".");
                        $(this).html($match[1] + $eAddr + $match[3]);
                    }
                }
            });

            /* ------------------------------------- MagicLine Menu ------------------------------------------------- */
            // https://css-tricks.com/jquery-magicline-navigation/
            var $el, leftPos, newWidth,
                $mainNav = $(".magic-line-menu");

            if ($mainNav.length > 0) {
                if ($mainNav.width() > 767) {
                    $mainNav.append("<li id='magic-line'></li>");
                    var $magicLine = $("#magic-line");

                    $magicLine
                        .width($mainNav.find("li.active").width())
                        .css("left", $mainNav.find("li.active").position().left + 15)
                        .data("origLeft", $magicLine.position().left)
                        .data("origWidth", $magicLine.width());

                    $(".magic-line-menu li").click(function () {
                        $el = $(this);
                        $magicLine
                            .width($el.width())
                            .css("left", $el.position().left + 15)
                            .data("origLeft", $magicLine.position().left)
                            .data("origWidth", $magicLine.width());
                    });

                    $(".magic-line-menu li").hover(function () {
                        $el = $(this);
                        leftPos = $el.position().left;
                        newWidth = $el.width() + 30;
                        $magicLine.stop().animate({
                            left: leftPos,
                            width: newWidth
                        });
                    }, function () {
                        $magicLine.stop().animate({
                            left: $magicLine.data("origLeft"),
                            width: $magicLine.data("origWidth")
                        });
                    });
                }
            }

            /* ------------------------------------- Gallery Formatter ---------------------------------------------- */
            // добавлен функционал адаптивности, ширина зависит от ширины страницы
            // дополнение функции prepare
            if (typeof (Drupal.galleryformatter) !== "undefined") {
                Drupal.galleryformatter.prepare = (function (oldPrepare) {
                    function extendsPrepare(el) {

                        var setSize = function (el) {
                            var $el = $(el);
                            var $slides = $("li.gallery-slide", $el);
                            var $slideContainer = $("div.gallery-slides", $el);

                            // установить размер галереи по родительскому окну
                            var slideContainerWidth = $slideContainer.outerWidth();
                            var slideHeight = 0;
                            $($slides).each(function () {
                                $(this).width(slideContainerWidth + "px");
                                if (slideHeight < $(this).find("img").height()) {
                                    slideHeight = $(this).find("img").height();
                                }
                            });
                            $slideContainer.height(slideHeight + "px");
                        };

                        oldPrepare(el);
                        setSize(el);

                        // повесить установку размера на ресайз окна
                        $(window).bind("resize", function () {
                            $(".galleryformatter", context).each(function () {
                                setSize(this);
                            });

                        });
                    }
                    return extendsPrepare;
                })(Drupal.galleryformatter.prepare);
            }

          /* ------------------------------------- checkout/* ------------------------------------------------------- */
          $(".checkout-contact").once(function() {
            checkButton();
            $(".form-item-checkout-contact-request-forma-region select").on("keyup", checkButton);
            $(".form-item-checkout-contact-request-forma-name input").on("keyup", checkButton);
            $(".form-item-checkout-contact-request-forma-email input").on("keyup", checkButton);
            $(".form-item-checkout-contact-request-forma-phone input").on("keyup", checkButton);
          });
          function checkButton() {
            var region = $(".form-item-checkout-contact-request-forma-region select");
            var name = $(".form-item-checkout-contact-request-forma-name input");
            var email = $(".form-item-checkout-contact-request-forma-email input");
            var phone = $(".form-item-checkout-contact-request-forma-phone input");
            var q1 = region.val();
            var q2 = name.val();
            var q3 = email.val();
            var q4 = phone.val();
            if (region.val() === "All" || !name.val() || ! email.val() || !phone.val()) {
              if (!$(".checkout-continue").prop( "disabled")) {
                $(".checkout-continue").prop( "disabled", true ).after("<p class='button-notice'>Заполните поля выше прежде чем продолжить.</p>");
              }
            } else {
              $(".checkout-continue").prop("disabled", false);
              $(".checkout-buttons .button-notice").remove();
            }
          }
        }
    };

})(jQuery, Drupal, this, this.document);

