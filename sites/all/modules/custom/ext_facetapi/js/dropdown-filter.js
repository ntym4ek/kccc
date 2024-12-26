/**
 * Input filter for Bootstrap Dropdown
 * @param  {jQuery} $ Global jQuery instance
 * @return {void}
 *
 * @todo Navigation with the keyboard Up and Down
 * @todo Display Not Found msg to user
 * ------------------------------------------------------
 * @done Auto-focus on input when dropdown is opened
 * @done Add scrollbar to large lists
 * @done Set the input placeholder label by HTML data-filter-label attribute or plugin option
 */
(function($) {

    $.fn.bsDropDownFilter = function(options) {
        return this.filter(".dropdown-menu").each(function() {
            var opts = $.extend({}, $.fn.bsDropDownFilter.defaults, options);
            var $this, $li, $search, $droplist;
            $this = $(this);

            opts.label = $this.data('filter-label') || opts.label;

            $this.parent().on('shown.bs.dropdown', function(e) {
                $this = $(this);
                $this.find('.dropdown-filter input').focus();
                $this.find('li').show();
            }).on('hide.bs.dropdown', function(e) {
                $(this).find('.dropdown-filter input').val('');
            });

            $li = $('<div role="presentation" class="dropdown-filter"></div>').prependTo($this.parent());

            $search = $('<input type="text" class="form-text filter-input" placeholder="' + opts.label + '" style="font-size: 0.8rem; font-family: Montserrat; width:100%;" />')
                .data('dropdownList', $this)
                .bind('click', function(e) {
                    e.stopPropagation();
                })
                .bind('keyup', function() {
                    $droplist = $(this).data('dropdownList');
                    $droplist.find('li').show();
                    $droplist.find('li:not(:filter("' + this.value + '"))').not('.dropdown-filter').hide();
                })
                .prependTo($li);
        });
    };

    $.fn.bsDropDownFilter.defaults = {
        label: Drupal.t('Filter by')
    };

    $(document).ready(function() {
        $('[data-filter], .dropdown-filter').bsDropDownFilter();

        // Create a FILTER pseudo class. Like CONTAINS, but case insensitive
        $.expr[":"].filter = $.expr.createPseudo(function(arg) {
            return function(elem) {
                /*global Diacritics*/
                return Diacritics.clean($(elem).text()).toUpperCase().indexOf(Diacritics.clean(arg).toUpperCase()) >= 0;
            };
        });

    });
}(jQuery));
