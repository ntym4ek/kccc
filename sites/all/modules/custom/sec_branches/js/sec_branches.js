(function ($) {
  Drupal.behaviors.sec_branches = {
    attach: function (context, settings) {

      var regions = settings.sec_branches.regions;
      var iso_start = settings.sec_branches.iso_start;

      ymaps.ready(function () {
        // создание макета содержимого
        var MyIconContentLayout = ymaps.templateLayoutFactory.createClass(
          "<div style=\"color: #000; background: #fff; border: 2px solid #0DB02B; border-radius: 5px; padding: 5px; \">$[properties.iconContent]</div>"
        );

        var myPlacemarksWithContent = [];
        var center_x = 0, center_y = 0;
        $.each(regions, function(iso, branches) {
          $.each(branches, function(index, branch) {
            // начальные координаты карты
            if (iso === iso_start) {
              center_x = branch.coords.x;
              center_y = branch.coords.y;
            }
            // формирование метки с собственным оформлением
            var myPlacemarkWithContent = new ymaps.Placemark(
              [branch.coords.x, branch.coords.y],
              {
                hintContent: branch.target,
                balloonContent: branch.schedule + "<br />" + branch.address,
                iconContent: "ООО Торговый Дом<br />Кирово-Чепецкая Химическая Компания"
              },
              {
                iconLayout: "default#imageWithContent",
                iconImageHref:"/sites/default/files/images/logo/logo.png",
                iconImageSize: [42,47],
                iconImageOffset: [-30, -30],
                iconContentSize: [275,33],
                iconContentOffset: [45, 2],
                iconContentLayout: MyIconContentLayout
            });
            // уст. ID для ссылки центрирования филиала
            myPlacemarkWithContent.properties.set("linkID", iso + index);
            // добавление в коллекцию объектов
            myPlacemarksWithContent.push(myPlacemarkWithContent);
          });
        });

        // создание карты
        var myMap = new ymaps.Map("map",
          { center: [center_x, center_y], zoom: 13 },
          { searchControlProvider: "yandex#search" }
        );

        // размещаем метки на карте
        // и добавляем обработку клика по ссылке с центрированием филиала на карте
        myPlacemarksWithContent.forEach(function (branch) {
          myMap.geoObjects.add(branch);
          var linkID = branch.properties.get("linkID");
          $("#" + linkID).bind("click", function(e) {
            myMap.setCenter(branch.geometry.getCoordinates());
            // e.preventDefault();
          });
        });
      });

    }
  };
})(jQuery);
