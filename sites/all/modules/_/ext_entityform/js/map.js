(function ($) {
    Drupal.behaviors.Map = {
        attach: function (context, settings) {

            ymaps.ready(function () {
                var myMap = new ymaps.Map('map', {
                        center: [58.538202, 50.009723],
                        zoom: 13
                    }, {
                        searchControlProvider: 'yandex#search'
                    }),

                    // Создаём макет содержимого.
                    MyIconContentLayout = ymaps.templateLayoutFactory.createClass(
                        '<div style="color: #000; background: #fff; border: 2px solid #0DB02B; border-radius: 5px; padding: 5px; ">$[properties.iconContent]</div>'
                    ),

                    myPlacemarkWithContent = new ymaps.Placemark([58.540547, 49.976694], {
                        hintContent: 'Административное здание',
                        balloonContent: 'Пн-Пт  8:00-17:00<br />+7 (8332) 76-15-20',
                        iconContent: Drupal.t("OOO Trade House") + "<br />" + Drupal.t('"Kirovo-Chepetsk Chemical Company"')
                    }, {
                        iconLayout: 'default#imageWithContent',
                        iconImageHref: '/sites/all/modules/_/ext_entityform/images/map_logo.png',
                        iconImageSize: [45,47],
                        iconImageOffset: [-30, -30],
                        iconContentSize: [260,33],
                        iconContentOffset: [50, 2],
                        iconContentLayout: MyIconContentLayout
                    });

                myMap.geoObjects
                    .add(myPlacemarkWithContent);
            });

        }
    };
})(jQuery);