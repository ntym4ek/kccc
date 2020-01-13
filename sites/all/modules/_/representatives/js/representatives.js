(function ($) {
    Drupal.behaviors.representatives = {
        attach: function (context, settings) {

            var regions_by_iso = settings.representatives.sales.regions_by_iso;

            var tooltip = function(jQueryTooltipObj, mapObject, mapsvgInstance) {
                var content = "<p>" + Drupal.t("There are no representatives") + "</p>";
                if (regions_by_iso[this.id].reps) {
                    content = "<p>" + Drupal.formatPlural(regions_by_iso[this.id].reps, "@count representative", "@count representatives") + "</p>" +
                                "<span>" + Drupal.t("Click to filter representatives list below") + "</span>";
                }
                return  "<div>" +
                            "<h4>" + regions_by_iso[this.id].name + "</h4>" +
                            content +
                        "</div>";
            };

            var onClick = function(e){
                if (this.data.selected) {
                    this.data.selected = false;
                    this.mapsvg.deselectRegion(this);
                    $(".rep-item").show();
                    $(".rep-list .clearfix").show();
                }
                else {
                    this.data.selected = true;

                    $(".rep-item").show();

                    var region_iso = this.id;
                    $(".rep-item").each(function(index, el){
                        if(!$(el).hasClass(region_iso)) {
                            $(el).hide();
                            $(".rep-list .clearfix").hide();
                        }
                    });
                }
            };

            var afterLoad = function () {
                // данные карты, включая регионы
                var data = this.getData();
                data.regions.forEach(function(r){
                    if (regions_by_iso[r.id] && regions_by_iso[r.id].reps) {
                        // закрасить регионы присутствия
                        r.setFill("#3a9027");
                    }
                });
            };

            $("#mapsvg").mapSvg({
                markerLastID: 1,
                source: "/sites/all/modules/_/representatives/images/map/russia.svg",
                title: "Russia",
                width: 1224.449, height: 760.6203,
                colors: {baseDefault: "#000000", background: "transparent", directory: "#fafafa", status: {}, stroke: "#ac9d8f", hover: "rgba(187,230,87,0.5)", selected: "#bbe657"},
                viewBox: [0,0,1224.449,760.6203],
                zoom: {on: true, limit: [0, 10], delta: 2, buttons: {on: true, location: "left"}, mousewheel: true},
                scroll: {on: true, limit: false, background: false, spacebar: false},
                cursor: "pointer",
                responsive: true,
                tooltips: {mode: tooltip, on: false, priority: "local", position: "top-right"},
                onClick: onClick,
                afterLoad: afterLoad,

                // markers: [
                //     {
                //         attached: true,
                //         src: "/sites/all/modules/_/representatives/images/map/sprout.png",
                //         width: 20, height: 48,
                //         x: 536,y: 462,
                //     }
                // ]
            });



            return false;
        }
    };
})(jQuery);


