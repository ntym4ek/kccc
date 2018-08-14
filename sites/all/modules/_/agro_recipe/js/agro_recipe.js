(function ($) {
    Drupal.behaviors.argo_recipe = {
        attach: function (context, settings) {
            // при изменеии списка выбранных сорняков, сохранить его в кукис
            $('select[name=field_culture], select[name=field_period], select[name=field_pp], input[name=dessic]').on('change', function(){
                var culture = $('select[name=field_culture]').val();    $.cookie('selected_culture', culture);
                var period = $('select[name=field_period]').val();
                if (typeof period == 'undefined') period = '';          $.cookie('selected_period', period);
                var area = $('input[name=field_area]').val();           $.cookie('selected_area', area);
                var seeding = $('input[name=field_seeding]').val();     $.cookie('selected_seeding', seeding);
                var weeds = $('input[name=weeds]').val();               $.cookie('selected_weeds', weeds);
                var fungis = $('input[name=fungis]').val();             $.cookie('selected_fungis', fungis);
                var pests = $('input[name=pests]').val();               $.cookie('selected_pests', pests);
                var dessic = $('input[name=dessic]').val();
                if (typeof dessic == 'undefined') dessic = '';          $.cookie('selected_dessic', dessic);
                var ppid = $('select[name=field_pp]').val();            $.cookie('selected_ppid', ppid);
            });
        }
    };
})(jQuery);


