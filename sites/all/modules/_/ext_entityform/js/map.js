(function ($) {
    Drupal.behaviors.ext_entityform = {
        attach: function (context, settings) {

          var contacts = settings.ext_entityform.contact;
            ymaps.ready(function () {

              // Создаём макет содержимого.
              var MyIconContentLayout = ymaps.templateLayoutFactory.createClass(
                "<div style=\"color: #000; background: #fff; border: 2px solid #0DB02B; border-radius: 5px; padding: 5px; \">$[properties.iconContent]</div>"
              );

              var myPlacemarksWithContent = [];
              var center_x = 0, center_y = 0;
              contacts.forEach(function (el) {
                if (!center_x) {
                  center_x = el.coords.x;
                  center_y = el.coords.y;
                }
                var myPlacemarkWithContent = new ymaps.Placemark([el.coords.x, el.coords.y], {
                  hintContent: "Административное здание",
                  balloonContent: el.schedule + "<br />" + el.address,
                  iconContent: Drupal.t("OOO Trade House") + "<br />" + Drupal.t("Kirovo-Chepetsk Chemical Company")
                }, {
                  iconLayout: "default#imageWithContent",
                  iconImageHref:"/sites/all/modules/_/ext_entityform/images/map_logo.png",
                  iconImageSize: [42,47],
                  iconImageOffset: [-30, -30],
                  iconContentSize: [275,33],
                  iconContentOffset: [45, 2],
                  iconContentLayout: MyIconContentLayout
                });
                myPlacemarksWithContent.push(myPlacemarkWithContent);
              });

              var myMap = new ymaps.Map("map",
                {
                  center: [center_x, center_y],
                  zoom: 13
                },
                { searchControlProvider: "yandex#search" }
              );

              myPlacemarksWithContent.forEach(function (el) {
                myMap.geoObjects.add(el);
              });
            });

        }
    };
})(jQuery);
