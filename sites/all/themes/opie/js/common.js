/**
 *
 */
(function ($, Drupal, window, document, undefined) {

    Drupal.behaviors.opie = {
        attach: function (context, settings) {

            /* ------------------------------------------  выпадающее меню ------------------------------------------ */
            $('#fold-2055 > a, #fold-272 > a, #fold-2054 > a').removeAttr("href");

            $('body').off('click', '#fold-2055 > a, #fold-272 > a, #fold-2054 > a'); // без этого click срабатывает 9 раз вместо одного
            $('body').on('click', '#fold-2055 > a, #fold-272 > a, #fold-2054 > a', function () {
                // закрыть, если другие открыты
                var clicked = this;
                var clickedUnfolded = false;
                var anyWasUnfolded = false;
                $('.foldout').each(function () {
                    if ($(this).parent().hasClass('unfolded')) {
                        anyWasUnfolded = true;
                        $(this).parent().removeClass('unfolded');
                        $(this).parent().find('.foldout').slideUp(200);
                        $(this).parent().children('i').css({transform: 'rotate(0deg)'});
                        // $('.header-pane').css({'border-bottom-left-radius': '4px','border-bottom-right-radius': '4px'});

                        if ($(this).parent().attr('id') == $(clicked).parent().attr('id')) clickedUnfolded = true;
                    }
                });

                // убрать фон, если кликнули по открытой
                if (clickedUnfolded) {
                    $('#modalBackdrop').animate({opacity: '0', easing: "swing"}, 500, function () {
                        $(this).css({width : "0", height: "0"});
                    });
                }
                // если не было открытых или кликнули по соседней, открыть панель
                else {
                    $(clicked).parent().addClass('unfolded');

                    doc_w = $(document).width(); doc_h = $(document).height();
                    // если ранее была хоть одна открыта, открыть другую без вывода фона
                    if (anyWasUnfolded) {
                        $(clicked).parent().find('.foldout').slideDown(450, function () {
                            $(this).css({'overflow': 'visible'});
                        });
                        $(clicked).parent().children('i').css({transform: 'rotate(-180deg)'});
                        // $('.header-pane').css({'border-bottom-left-radius': '0','border-bottom-right-radius': '0'});
                    }
                    // если не было открытых, открыть панель с фоном
                    else {
                        $('#modalBackdrop').css({'width': doc_w + 'px', 'height': doc_h + 'px'}).animate({opacity: '0.6', easing: "swing"}, 500);
                        $(clicked).parent().find('.foldout').slideDown(450, function () {
                            $(this).css({'overflow': 'visible'});
                        });
                        $(clicked).parent().children('i').css({transform: 'rotate(-180deg)'});
                        // $('.header-pane').css({'border-bottom-left-radius': '0','border-bottom-right-radius': '0'});
                    }
                }

                // Закрыть при клике за пределами меню и по кнопке Закрыть
                $('#modalBackdrop, .foldout-close').on('click', function () {
                    $('.foldout').each(function () {
                        if ($(this).parent().hasClass('unfolded')) {
                            $(this).slideUp(200);
                            $('#modalBackdrop').animate({opacity: '0', easing: "swing"}, 500, function () {
                                $(this).css({width : "0", height: "0"});
                            });
                            $(this).parent().children('i').css({transform: 'rotate(0deg)'});
                            $(this).parent().removeClass('unfolded');
                            // $('.header-pane').css({'border-bottom-left-radius': '4px','border-bottom-right-radius': '4px'});
                        }
                    });
                });
            });

            /* ------------------------------------ панель поиска, Search -------------------------------------------- */
            $('#search i').on('click', function () {
                if ($('#search-pane').hasClass('hide-fade')) {
                    $('#search-pane').removeClass('hide-fade');
                    $(this).removeClass('icon-search'); $(this).addClass('icon-close');
                } else {
                    $('#search-pane').addClass('hide-fade');
                    $(this).removeClass('icon-close'); $(this).addClass('icon-search');
                }
            });

            /* выпадающее меню пользователя, User Menu */
            $('.user-menu ul li').hover(function() {
                    $(this).children('ul').fadeIn(300);
                    // $('.user-menu').css({'border-bottom-left-radius': '0'});

                },
                function() {
                    $(this).children('ul').hide();
                    // $('.user-menu').css({'border-bottom-left-radius': '4px'});
                });
            
            /* плавающий блок Меню */
            $(window).scroll(function () {
                var top = $(document).scrollTop();
                var offset = parseInt($("body").css('padding-top'));
                if (top < 50) {
                    if ($("#float-wrap").hasClass('float')) {
                        $("#float-wrap").css({top: '0', position: 'relative'});
                        $("#cart").remove();
                        $("#float-wrap").removeClass('float cart');
                        $(".utility").css({'margin-bottom': '0'});
                    }
                } else {
                    $("#float-wrap").css({top: offset + 'px', position: 'fixed'});
                    if (!$("#float-wrap").hasClass('float')) {
                        $(".utility").css({'margin-bottom': '80px'});
                        $("#float-wrap").addClass('float');
                        // добавление Корзины в меню
                        var $qty = '';
                        if (!$(".cart-empty-block").length) {
                            $qty = $('#cart-qty').attr('qty');
                        }
                        if ($qty != undefined && !$('div').is('#cart')) {
                            $("#float-wrap").addClass('cart');
                            setTimeout(function () {
                                var $href = $('#cart-qty').attr('href');
                                $("#services").append('<div id="cart"><a href="' + $href + '"><span>' + $qty + '</span></a></div>');
                            }, 400);
                        }
                    }
                }
            });

            /* ------------------------------------- e-address decode ----------------------------------------------- */
                /* <a href="e(supp/ort[s1]kcc/c[s2]ru)" class="eAddr-encoded"></a> */
            $('a.eAddr-encoded').each(function() {
                var $href = $(this).attr('href');
                var $pattern = /e\((.*)\)/;
                var $match = $pattern.exec($href);
                var $eAddr = $match[1];

                if ($eAddr) {
                    $eAddr = $eAddr.replace(/\//g, '');
                    $eAddr = $eAddr.replace(/\[s1\]/, '@');
                    $eAddr = $eAddr.replace(/\[s2\]/, '.');
                    $(this).attr('href', 'mailto:' + $eAddr);
                    if ($(this).hasClass('eAddr-html')) $(this).html($eAddr);
                }
            });
                /* <tag class="eAddr-encoded">.*e(supp/ort[s1]kcc/c[s2]ru).*</tag> */
            $('.eAddr-encoded').each(function() {
                if (this.tagName != 'A') {
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

            /* ----------------------- Главная, установка расстояния от меню до блоков ------------------------------ */
            var $win_w = $(window).width();
            var $win_h = $(window).height();
            var $height = ($win_h - 300 > 800) ? 800 : ($win_h - 300);
            $('#block-views-front-slider-block .views_slideshow_cycle_main').css('line-height', $height + 'px');
            set_bg_size();

            /* ------------------------------------------ Главная, размера фона ------------------------------------- */
            $( window ).resize(function() {
                set_bg_size();
            });

            function set_bg_size() {
                $win_w = $(window).width()+100;
                $win_h = $(window).height()+100;

                if ($win_w / $win_h >= 1.33) {
                    $('#bg1').css('width', $win_w + 'px'); $('#bg2').css('width', $win_w + 'px');
                    $('#bg1').css('height', $win_w / 1.33 + 'px'); $('#bg2').css('height', $win_w / 1.33 + 'px');
                } else {
                    $('#bg1').css('height', $win_h + 'px'); $('#bg2').css('height', $win_h + 'px');
                    $('#bg1').css('width', $win_h * 1.33 + 'px'); $('#bg2').css('width', $win_h * 1.33 + 'px');
                }
            }

            /* ------------------------------------ popup ------------------------------------ */
            $('.popup-trigger-js').on("hover", function(){
                var win = $(this).parent().find('.popup');
                if ($(win).hasClass('pop')) { $(win).removeClass('pop'); }
                else { $(win).addClass('pop'); }
            });
            $('.popup .close').on("click", function(){
                var win = $(this).parent();
                $(win).removeClass('pop');
            });

        }
};

    Drupal.behaviors.autoUpload = {
        attach: function (context, settings) {
            // если ошибка при загрузке файла, то перенести рамку с прозрачного input на обёртку .file-input
            if ($('.image-widget-data .form-file.error').length) {
                $('.image-widget-data .file-input').addClass('error');
            }

            // автосабмит при загрузке фото
            $('.form-managed-file input.form-submit[name*="upload"]', context).hide();
            $('.form-managed-file input.form-file', context).change(function () {
                var $parent = $(this).closest('.form-item');
                
                setTimeout(function () {
                    if (!$('.messages.error', $parent).length) {
                        $('.form-managed-file input.form-submit', $parent).mousedown();
                    }
                }, 100);
            });
        }
    };


})(jQuery, Drupal, this, this.document);

