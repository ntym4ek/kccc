<?php
/**
 * @file
 * Stub file for bootstrap_menu_link() and suggestion(s).
 */

/**
 * Overrides theme_menu_link() for book module.
 */
/**
 * Returns HTML for a menu link and submenu.
 *
 * @param array $variables
 *   An associative array containing:
 *   - element: Structured array data for a menu link.
 *
 * @return string
 *   The constructed HTML.
 *
 * @see theme_menu_link()
 *
 * @ingroup theme_functions
 */
function gard_menu_link(array $variables) {
    $element = $variables['element'];
    $sub_menu = '';

    $options = !empty($element['#localized_options']) ? $element['#localized_options'] : array();

    // Check plain title if "html" is not set, otherwise, filter for XSS attacks.
    $mlid = $element['#original_link']['mlid'];
    $title = empty($options['html']) ? check_plain($element['#title']) : filter_xss_admin($element['#title']);
    $icons = array(
        '9722' => '<i class="fas fa-home"></i>',
        '2055' => '<i class="fas fa-newspaper"></i>',
        '10174' => '<i class="fas fa-flask"></i>',
        '2054' => '<i class="fas fa-info-circle"></i>',
        '14' => '<i class="fas fa-sign-in-alt"></i>',
        '15' => '<i class="fas fa-sign-out-alt"></i>',
        '3' => '<i class="fas fa-user"></i>',
        '2675' => '<i class="fas fa-cog"></i>',
        '9459' => '<i class="fas fa-check"></i>',
        '3819' => '<i class="fas fa-map-marker"></i>',
        '8980' => '<i class="fab fa-pagelines"></i>',
        '893' => '<i class="fas fa-comment-alt"></i>',
        '9731' => '<i class="fas fa-user-circle"></i>',
    );
    // убрать текст из пунктов меню Пользователя (будут только иконки)
    if (in_array($mlid, array(9722, 14, 15, 3, 9459))) {
        $options['attributes']['title'] = $title;
        $title = '';
    }
    // вставить Иконки
    if (!empty($icons[$mlid])) {
        if ($title && $element['#original_link']['depth'] == 1) $element['#attributes']['class'][] = 'with-icon';
        $title = $icons[$mlid] . $title;
    }

    // Ensure "html" is now enabled so l() doesn't double encode. This is now
    // safe to do since both check_plain() and filter_xss_admin() encode HTML
    // entities. See: https://www.drupal.org/node/2854978
    $options['html'] = TRUE;

    $href = $element['#href'];
    $attributes = !empty($element['#attributes']) ? $element['#attributes'] : array();

    if ($element['#below']) {
        // Prevent dropdown functions from being added to management menu so it does not affect the navbar module.
        if (($element['#original_link']['menu_name'] == 'management') && (module_exists('navbar'))) {
            $sub_menu = drupal_render($element['#below']);
        }
        elseif ((!empty($element['#original_link']['depth']))) {
            // собственная обёртка
            unset($element['#below']['#theme_wrappers']);
            $sub_menu = '<div id="dropdown-' . $mlid . '-' . $element['#original_link']['depth'] . '" class="panel-collapse collapse">'
                            .'<div class="panel-body">'
                              .'<ul class="nav navbar-nav">'
                                .drupal_render($element['#below'])
                              .'</ul>'
                       .'</div></div>';

            // аттрибуты li
            $attributes['class'][] = 'panel panel-default';
            $attributes['id'] = 'dropdown';

            $title .= ' <a href="#dropdown-' . $mlid . '-' . $element['#original_link']['depth'] . '" class="dropdown-link collapsed" data-toggle="collapse"><span class="glyphicon glyphicon-menu-down dropdown-caret"></span></a>';

            // заменить сссылку /catalog на /catalog/agrochemicals
            if ($href == 'catalog') {
                $href = 'catalog/agrochemicals';
            }
        }
    }

    return '<li' . drupal_attributes($attributes) . '>' . l($title, $href, $options) . $sub_menu . "</li>\n";
}