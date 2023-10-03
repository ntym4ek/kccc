(function ($) {
    // отследить клик по карте и за её пределами
    var mapClick = false;

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
                if (this.mapsvg.selectedRegion && this.id === this.mapsvg.selectedRegion) {
                    this.mapsvg.deselectRegion(this);
                    $(".rep-item").show();
                    $(".rep-list .clearfix").show();
                    this.mapsvg.selectedRegion = null;
                }
                else {
                    $(".rep-item").hide();
                    $(".rep-item." + this.id).show();
                    $(".rep-list .clearfix").hide();
                    this.mapsvg.selectedRegion = this.id;
                }
                mapClick = true;
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


            $("#mapsvg").once(function() {
              $("#mapsvg").mapSvg({
                markerLastID: 1,
                source: "/sites/all/modules/_/sec_representatives/images/map/russia.svg",
                title: "Russia",
                width: 1224.449, height: 760.6203,
                colors: {baseDefault: "#000000", background: "transparent", directory: "#fafafa", status: {}, stroke: "#DECEC1", hover: "#B0EB28", selected: "#134A07"},
                viewBox: [0,0,1224.449,760.6203],
                zoom: {on: true, limit: [0, 10], delta: 2, buttons: {on: true, location: "left"}, mousewheel: true},
                scroll: {on: true, limit: false, background: false, spacebar: false},
                cursor: "pointer",
                responsive: true,
                tooltips: {mode: tooltip, on: false, priority: "local", position: "top-right"},
                onClick: onClick,
                afterLoad: afterLoad,
              });

              // клик за пределами карты
              $("#mapsvg").bind("mousedown touchstart", function() {
                if (!mapClick) {
                  // var mapsvg = MapSVG.get(0);
                  var mapsvg = $(this).eq(0).mapSvg();
                  mapsvg.deselectAllRegions();
                  $(".rep-item").show();
                  $(".rep-list .clearfix").show();
                  mapsvg.selectedRegion = null;
                }
                mapClick = false;
              });
            });


            return false;
        }
    };
})(jQuery);


