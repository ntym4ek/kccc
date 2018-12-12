/**
 *
 */
(function ($, Drupal, window, document, undefined) {

    Drupal.behaviors.gard = {
        attach: function (context, settings) {

            /* ------------------------------------------ блок корзины ---------------------------------------------- */
            /* - при обновлении блока корзины через секунду убрать класс с эффектом тряски - */
            /* - (класс добавляется в модуле checkout - checkout_commerce_fast_ajax_atc_commands_alter()) - */
            $('#block-cart').once(function() {
                $('#block-cart').each(function() {
                    setTimeout(function() { $('#block-cart').removeClass('shake-on'); }, 1500);
                });
            });

            /* ------------------------------------------  swipe events init ---------------------------------------- */
            function toggleMenu() {
                $('#navbar').toggleClass('slide-in');
                $('.menu-container').toggleClass('slide-in');
                $('.content-container > .content').toggleClass('body-slide-in');
            }

            $('body', context).once(function () {
                /* -----------------------------------  slide меню -------------------------------------------------- */
                $('.btn-s4').click(function () { toggleMenu(); });

                if (!device.desktop()) {
                    // свайп реализован на основе jquery.touchSwipe.min.js
                    // http://labs.rampinteractive.co.uk/touchSwipe/docs/index.html
                    $('.content-container > .content').swipe( {
                        threshold: Math.min($(document).width() / 2, 160),
                        swipeRight:function() {
                            toggleMenu();
                        },
                        excludedElements:$.fn.swipe.defaults.excludedElements+", .horizontal-tabs-list"
                    });
                    $('.side-menu').swipe( {
                        swipeLeft:function() {
                            toggleMenu();
                        },
                    });
                }

                /* ------------------------------------ панель поиска, Search --------------------------------------- */
                $('.sp3, .btn-s3').on('click', function () {
                    $('#search-pane').toggleClass('hide');
                });

                /* ------------------------------------ Prevent disabled link from following its href --------------- */
                $('.disabled').click(function(event){
                    event.preventDefault();
                });

                /* ------------------------------------ Слайдер - */
                $('#bootstrap-slider, [id^=views-bootstrap-carousel]').each(function(){
                    $(this).swipe( {
                        swipeLeft:function() { $('.carousel').carousel('next'); },
                        swipeRight:function(event) {
                            $('.carousel').carousel('prev');
                            event.stopPropagation();
                        },
                    });
                });
            });

            /* ------------------------------------ popup ----------------------------------------------------------- */
            $(".popup-trigger-js").on({
                mouseenter: function () {
                    $(this).find('.popup').addClass('pop');
                },
                mouseleave: function () {
                    $(this).find('.popup').removeClass('pop');
                }
            });
            $('.popup .close').on("click", function(){
                var win = $(this).parent();
                $(win).removeClass('pop');
            });

            /* ------------------------------------- print & share -------------------------------------------------- */
            $('.btn-print').on('click', function() {
                window.print();
            });
            $('.btn-share').on('click', function() {
                $('.header-share').toggleClass('closed');
            });

            /* ------------------------------------- e-address decode ----------------------------------------------- */
            /* <a href="e(supp/ort[s1]kcc/c[s2]ru)" class="eAddr-encoded"></a> */
            $('a.eAddr-encoded').each(function() {
                var $href = $(this).attr('href');
                var $pattern = /e\((.*)\)/;
                var $match = $pattern.exec($href);
                if ($match) {
                    var $eAddr = $match[1];
                    if ($eAddr) {
                        $eAddr = $eAddr.replace(/\//g, '');
                        $eAddr = $eAddr.replace(/\[s1\]/, '@');
                        $eAddr = $eAddr.replace(/\[s2\]/, '.');
                        $(this).attr('href', 'mailto:' + $eAddr).removeClass('eAddr-encoded');
                        if ($(this).hasClass('eAddr-html')) {
                            $(this).html($eAddr).removeClass('eAddr-html');
                        }
                    }
                }
            });
            /* <tag class="eAddr-encoded">.*e(supp/ort[s1]kcc/c[s2]ru).*</tag> */
            $('.eAddr-encoded').each(function() {
                if (this.tagName !== 'A') {
                    var $html = $(this).html();
                    var $pattern = /^(.*)e\((.*)\)(.*)$/;
                    var $match = $pattern.exec($html);
                    var $eAddr = $match[2];

                    if ($eAddr) {
                        $eAddr = $eAddr.replace(/\//g, '');
                        $eAddr = $eAddr.replace(/\[s1\]/, '@');
                        $eAddr = $eAddr.replace(/\[s2\]/, '.');
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
                        .width($mainNav.find('li.active').width())
                        .css("left", $mainNav.find('li.active').position().left + 15)
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
                            var $slides = $('li.gallery-slide', $el);
                            var $slideContainer = $('div.gallery-slides', $el);

                            // установить размер галереи по родительскому окну
                            var slideContainerWidth = $slideContainer.outerWidth();
                            var slideHeight = 0;
                            $($slides).each(function () {
                                $(this).width(slideContainerWidth + 'px');
                                if (slideHeight < $(this).find('img').height()) {
                                    slideHeight = $(this).find('img').height();
                                }
                            });
                            $slideContainer.height(slideHeight + 'px');
                        };

                        oldPrepare(el);
                        setSize(el);

                        // повесить установку размера на ресайз окна
                        $(window).bind('resize', function () {
                            $('.galleryformatter', context).each(function () {
                                setSize(this);
                            });

                        });
                    }
                    return extendsPrepare;
                })(Drupal.galleryformatter.prepare);
            }
        }
    };

})(jQuery, Drupal, this, this.document);

