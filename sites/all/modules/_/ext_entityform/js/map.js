(function ($) {
    Drupal.behaviors.ext_entityform = {
        attach: function (context, settings) {

          var contact = settings.ext_entityform.contact;
            ymaps.ready(function () {
                var myMap = new ymaps.Map("map", {
                        center: [contact.coords.x, contact.coords.y],
                        zoom: 13
                    }, {
                        searchControlProvider: "yandex#search"
                    }),

                    // Создаём макет содержимого.
                    MyIconContentLayout = ymaps.templateLayoutFactory.createClass(
                        "<div style=\"color: #000; background: #fff; border: 2px solid #0DB02B; border-radius: 5px; padding: 5px; \">$[properties.iconContent]</div>"
                    ),

                    myPlacemarkWithContent = new ymaps.Placemark([contact.coords.x, contact.coords.y], {
                        hintContent: "Административное здание",
                        balloonContent: contact.schedule + "<br />" + contact.phone_txt,
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

                myMap.geoObjects
                    .add(myPlacemarkWithContent);
            });

        }
    };
})(jQuery);
