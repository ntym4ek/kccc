(function ($) {
    Drupal.behaviors.AddressBook = {
        attach: function (context, settings) {
            // добавить класс 'active' при клике на radiobutton в адресной книге
            $('.ab-list input.form-radio').change(function(){
                $('.ab-list .form-type-radio').removeClass('active');
                $(this).parent().addClass('active');
            });
        }
    }

    /**
    * Обновление ссылки запроса соответствующих полей для работы зависимымых полей с автодополнением
    */
    Drupal.behaviors.refreshURI = {
        attach: function (context, settings) {
            // первые заглавные в полях
            $('input[name="surname"], input[name="name"], input[name="name2"], input[name="region"], input[name="area"], input[name="city"], input[name="street"]').on('keyup', function(){
                var text = $(this).val();
                $(this).val(ucfirst(text));
            });

            //
            $('input[name="region"]').on('keyup', function(){
                $('input[name="region_kladr"]').val('');
                $('input[name="zipcode]').val('');
            });

            $('input[name="area"]').on('keyup', function(){
                $('input[name="area_kladr"]').val('');
                $('input[name="zipcode]').val('');
            });

            $('input[name="city"]').on('keyup', function(){
                $('input[name="city_kladr"]').val('');
                $('input[name="zipcode]').val('');
            });

            $('input[name="street"]').on('keyup', function(){
                $('input[name="street_kladr"]').val('');
            });

            $('input[name="house"]').on('keyup', function(){
                $('input[name="house_kladr"]').val('');
            });

            // переопределение стандартной функции автозаполнения (misc/autocomplete.js)
            // перед осуществлением поиска установить ссылку для запроса в соответствии с предыдущими полями
            Drupal.jsAC.prototype.select = function(_super){
                return function() {
                    // убрать кладр из названия и записать его в скрытое поле
                    var src = $(arguments[0]).data('autocompleteValue');
                    if(src.search(/\[/) != -1) {
                        var city = src.match(/(.*?)\[/i);
                        var regexp = /\[(\d+)\]/g;
                        var code = regexp.exec(src);
                        var zip = regexp.exec(src);
                        $(arguments[0]).data('autocompleteValue', city[1]);
                        if (code[1] != '') {
                            $(this.input).removeClass('kladr-error');
                            $('#aform_message').html('');
                            if(this.db.uri.indexOf('autocomplete/region') != -1)  $('input[name="region_kladr"]').val(code[1]);
                            if(this.db.uri.indexOf('autocomplete/area') != -1)    $('input[name="area_kladr"]').val(code[1]);
                            if(this.db.uri.indexOf('autocomplete/city') != -1)    $('input[name="city_kladr"]').val(code[1]);
                            if(this.db.uri.indexOf('autocomplete/street') != -1)  $('input[name="street_kladr"]').val(code[1]);
                            if(this.db.uri.indexOf('autocomplete/house') != -1)   $('input[name="house_kladr"]').val(code[1]);
                        }
                        if ((zip !=null) && (zip[1] != '')) {
                            $('input[name="zipcode"]').val(zip[1]);
                        }
                    }

                    // очистить регион, если выбирается индекс
                    if(this.db.uri.indexOf('autocomplete/region') != -1) {
                        $('input[name="area"]').val('');
                        $('input[name="area_kladr"]').val('');
                    }

                    // очистить район и город, если выбирается регион
                    if(this.db.uri.indexOf('autocomplete/region') != -1
                        || this.db.uri.indexOf('autocomplete/area') != -1) {
                        $('input[name="city"]').val('');
                        $('input[name="city_kladr"]').val('');
                    }

                    // очистить улицу и дом, если выбирается город
                    if(this.db.uri.indexOf('autocomplete/region') != -1
                        || this.db.uri.indexOf('autocomplete/area') != -1
                        || this.db.uri.indexOf('autocomplete/city') != -1) {
                        $('input[name="street"]').val('');
                        $('input[name="street_kladr"]').val('');
                        $('input[name="house"]').val('');
                        $('input[name="house_kladr"]').val('');
                    }

                    return _super.apply(this, arguments);
                }
            }(Drupal.jsAC.prototype.select);

            Drupal.ACDB.prototype.search = function(_super){
                return function() {
                    var db = this;

                    var region_uri = $('input[name="region_kladr"]').val();
                    if(!region_uri) region_uri = "0";
                    var area_uri = $('input[name="area_kladr"]').val();
                    if(!area_uri) area_uri = region_uri;
                    var city_uri = $('input[name="city_kladr"]').val();
                    if(!city_uri) city_uri = "0";
                    var strt_uri = $('input[name="street_kladr"]').val();
                    if(!strt_uri) strt_uri = "0";

                    // для района
                    if(db.uri.indexOf('autocomplete/area') != -1) {
                        db.uri = Drupal.settings.basePath + 'abook/autocomplete/area/' + region_uri;
                    }
                    // для города
                    if(db.uri.indexOf('autocomplete/city') != -1) {
                        db.uri = Drupal.settings.basePath + 'abook/autocomplete/city/' + area_uri;
                    }
                    // для улицы
                    if(db.uri.indexOf('autocomplete/street') != -1) {
                        db.uri = Drupal.settings.basePath + 'abook/autocomplete/street/' + city_uri;
                    }
                    // для номера дома
                    else if(db.uri.indexOf('autocomplete/house') != -1) {
                        db.uri = Drupal.settings.basePath + 'abook/autocomplete/house/' + strt_uri;
                    }
                    return _super.apply(this, arguments);
                }
            }(Drupal.ACDB.prototype.search);
        }
    };

    function ucfirst(str) {
        var first = str.charAt(0).toUpperCase();
        return first + str.substr(1);
    }

})(jQuery);