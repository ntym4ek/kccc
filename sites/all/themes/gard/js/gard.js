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

            // dropdown вместо ссылки
          $('a').each(function(i, el) {
            if ($(el).attr('data-href')) {
              $(el).attr('href', $(el).attr('data-href'));
              $(el).attr('data-toggle', 'collapse');
            }
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
            // $("#block-cart").once(function() {
            //     $("#block-cart").each(function() {
            //         setTimeout(function() { $("#block-cart").removeClass("shake-on"); }, 1500);
            //     });
            // });

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
                $("[id^=views-bootstrap-carousel]").each(function(){
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
                        $eAddr = $eAddr.replace('%5Bs1%5D', "@");
                        $eAddr = $eAddr.replace('%5Bs2%5D', ".");
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
                        $eAddr = $eAddr.replace('%5Bs1%5D', "@");
                        $eAddr = $eAddr.replace('%5Bs2%5D', ".");
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

          /* ------------------------------------- Front Page ------------------------------------------------------- */
          // анимация стрелки
          function setArrowPos() {
            var parent_block = $('.block-arrow').closest('.block-block');
            if ($(parent_block).length) {
              var block_bottom = $(parent_block).offset().top + $(parent_block).height();
              var page_bottom = $(window).height() + $(window).scrollTop();
              if (block_bottom > page_bottom) {
                $('.block-arrow').css({bottom: (block_bottom - page_bottom) + 'px'});
              }
            }
          }

          setArrowPos();
          window.addEventListener('resize', function(event) {
            setArrowPos();
          }, true);
          window.onscroll = function () {
            setArrowPos();
          };

        }
    };

})(jQuery, Drupal, this, this.document);

