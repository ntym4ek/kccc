(function ($) {
    Drupal.behaviors.representatives = {
        attach: function (context, settings) {

            var regions_by_iso = settings.representatives.sales.regions_by_iso;

            var tooltip = function(jQueryTooltipObj, mapObject, mapsvgInstance) {
                return regions_by_iso[this.id].name;
                // return  "<div>" +
                //             "<h4>" + regions_by_iso[this.id].name + "</h4>" +
                //             "<p>2 представителя</p>" +
                //             "<span>нажмите, чтобы отфильтровать список ниже</span>" +
                //         "</div>";
            };

            var onClick = function(e){
                if (this.data.selected) {
                    this.data.selected = false;
                    $("#mapsvg").deselectRegion(this);
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
                var markers = [];
                data.regions.forEach(function(r){
                    if (regions_by_iso[r.id] && regions_by_iso[r.id].reps) {
                        var center = r.getCenter();
                        markers.push({
                            attached: true,
                            src: "/sites/all/modules/_/representatives/images/map/sprout.png",
                            width: 20, height: 48,
                            x: center[0]-300,y: center[1]+250,
                            tooltip: r.title,
                        });
                    }
                });

                this.setMarkers(markers);
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
                tooltips: {mode: tooltip, on: false, priority: "local", position: "bottom-right"},
                onClick: onClick,
                // afterLoad: afterLoad,

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


