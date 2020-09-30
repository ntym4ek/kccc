/**
 *
 */
(function ($, Drupal, window, document, undefined) {

    Drupal.behaviors.ya = {
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
                $("#modalBackdrop").css("top", 0).css("height", docHeight + "px").css("width", docWidth + "px").addClass("show");
            });
            $(".level-1 > li").on("hidden.bs.dropdown", function () {
                $("#modalBackdrop").removeClass("show");
            });

            /* ------------------------------------------ Right  Menu ----------------------------------------------- */
            $(".user-menu .dropdown").hover(function() {
                    $(this).addClass("open");
                },
                function() {
                    $(this).removeClass("open");
                }
            );


            /* ------------------------------------------ Animated Elements ----------------------------------------- */
            $("[data-animate=true]").each(function(){
                if ($(this).data("a-effect")) {
                    $(this).addClass($(this).data("a-effect"));
                } else {
                    $(this).addClass("slide-up");
                }
                $(this).addClass("animate-processed");
            });



            $("body", context).once(function () {

                /* ------------------------------------ панель поиска, Search --------------------------------------- */
                $(".sp3, .btn-s3").on("click", function () {
                    $("#search-pane").toggleClass("hide");
                });

                /* ------------------------------------ Prevent disabled link from following its href --------------- */
                $(".disabled").click(function(event){
                    event.preventDefault();
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
        }
    };

})(jQuery, Drupal, this, this.document);

