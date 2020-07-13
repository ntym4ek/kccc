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
        '9722'  => '<i class="fas fa-home"></i>',
        '2055'  => '<i class="fas fa-newspaper"></i>',
        '10174' => '<i class="fas fa-flask"></i>',
        '2054'  => '<i class="fas fa-info-circle"></i>',
        '14'    => '<i class="fas fa-user"></i>',
        '15'    => '<i class="fas fa-sign-out-alt"></i>',
        '12858' => '<i class="fas fa-user"></i>',
        '2675'  => '<i class="fas fa-cog"></i>',
        '9459'  => '<i class="fas fa-check"></i>',
        '3819'  => '<i class="fas fa-map-marker"></i>',
        '8980'  => '<i class="fab fa-pagelines"></i>',
        '893'   => '<i class="fas fa-comment-alt"></i>',
        '9731'  => '<i class="fas fa-user-circle"></i>',
    );
    // убрать текст из пунктов меню Пользователя (будут только иконки)
    if (in_array($mlid, array(9722, 14, 15, 12858, 9459))) {
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
 *
 * Пользовательское меню
 */
function gard_menu_link__user_menu(array $variables)
{
    $element = $variables['element'];
    $mlid = $element['#original_link']['mlid'];

    // не выводить кнопку входа или личного кабинета в зависимости от наличия авторизации
    if ($GLOBALS['user']->uid) {
        if ($mlid == 14)   return '';
    } else {
        if ($mlid == 12880) return '';
    }

    // Check plain title if "html" is not set, otherwise, filter for XSS attacks.
    $title = empty($options['html']) ? check_plain($element['#title']) : filter_xss_admin($element['#title']);

    $options = !empty($element['#localized_options']) ? $element['#localized_options'] : array();
    // Ensure "html" is now enabled so l() doesn't double encode. This is now
    // safe to do since both check_plain() and filter_xss_admin() encode HTML
    // entities. See: https://www.drupal.org/node/2854978
    $options['html'] = TRUE;

    // вставить Иконки
    if (in_array($mlid, [14, 12880])) {
        $options['attributes']['class'][] = 'btn btn-header btn-s1';
        $title =  '<i class="fa fa-user">';

        // вывести бейдж с количеством уведомлений
        if (module_exists('ext_message_got')) {
            $mids = ext_message_got_get_user_ungot_messages($GLOBALS['user']->uid);
            if (count($mids)) $title .= '<span class="bubble bubble-red">' . count($mids) . '</span>';
        }

        $title .=  '</i>';
    }

    if ($mlid == 15030) {
        $mids = ext_message_got_get_user_ungot_messages($GLOBALS['user']->uid);
        if (count($mids)) $title .= ' (' . count($mids) . ')';
    }

    $href = $element['#href'];
    $attributes = !empty($element['#attributes']) ? $element['#attributes'] : array();

    $sub_menu = '';
    $depth = $element['#original_link']['depth'];
    if (!empty($element['#below'])) {
        unset($element['#below']['#theme_wrappers']);
        $sub_menu .= '<div class="dropdown-menu level-' . ($depth + 1) . '-wrapper">';
        if ($mlid == 12880 && $GLOBALS['user']->uid) {

            $sub_menu .= '<div class="user-info">' .
                            (module_exists('realname') ? realname_load($GLOBALS['user']) : $GLOBALS['user']->name) .
                            '<span>' . $GLOBALS['user']->mail . '</span>' .
                        '</div>';
        }
        // навигация
        $nav_menu = menu_tree_all_data('navigation');
        $nav_menu = menu_tree_output($nav_menu);
        $nav_menu['#theme_wrappers'] = ['menu_tree__navigation_submenu'];

        $sub_menu .=    '<ul class="level-' . ($depth + 1) . '">' .
                            drupal_render($nav_menu) .
                        '</ul>';
        $sub_menu .=    '<ul class="level-' . ($depth + 1) . '">' .
                            drupal_render($element['#below']) .
                        '</ul>' .
                    '</div>';

        $attributes['class'][] = 'dropdown';
        $attributes['id'] = 'dropdown';
        if ($GLOBALS['user']->uid) $href = 'person/' . $GLOBALS['user']->uid . '/summary';
//
//        $options['attributes']['class'][] = 'dropdown-toggle';
//        $options['attributes']['data-toggle'] = 'dropdown';
    }
    $attributes['class'][] = 'level-' . $depth . '-item';

    if ($mlid == 14) {
        $options['attributes']['class'][] = 'popup-trigger';
        $title .= '<div class="popup popup-top-left"><div class="popup-content">' . t('Sign in') . '</div></div>';
    }

    return '<li' . drupal_attributes($attributes) . '>' . l($title, $href, $options) . $sub_menu . "</li>";
}

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
function gard_menu_link__menu_main_d(array $variables) {
    $element = $variables['element'];
    $sub_menu = '';

    $options = !empty($element['#localized_options']) ? $element['#localized_options'] : array();
    $depth = $element['#original_link']['depth'];

    // Check plain title if "html" is not set, otherwise, filter for XSS attacks.
    $mlid = $element['#original_link']['mlid'];
    $title = empty($options['html']) ? check_plain($element['#title']) : filter_xss_admin($element['#title']);

    // Ensure "html" is now enabled so l() doesn't double encode. This is now
    // safe to do since both check_plain() and filter_xss_admin() encode HTML
    // entities. See: https://www.drupal.org/node/2854978
    $options['html'] = TRUE;

    $href = $element['#href'];
    $attributes = !empty($element['#attributes']) ? $element['#attributes'] : array();

    // если используется модуль taxonomy_menu
    // меняем ссылку на заданную в словаре Главное Меню
    if ($element['#original_link']['menu_name'] == 'menu-main-d') {
        $source_tid = str_replace('taxonomy/term/', '', $element['#original_link']['link_path']);
        if ($source_term_wr = entity_metadata_wrapper('taxonomy_term', $source_tid)) {

            $href = $source_term_wr->field_link->value();
            $tid = strpos($href, 'taxonomy/term/' ) === 0 ?  str_replace('taxonomy/term/', '', $href) : null;

            // панель второго уровня
            if ($depth == 2) {
                $image_uri = empty($source_term_wr->field_shop_category_image->value()) ? 'public://default_images/no_photo.png' : $source_term_wr->field_shop_category_image->file->value()->uri;
                $image_url = image_style_url('thumbnail', $image_uri);
                $title =  '<div class="category-img"><img src="' . $image_url . '" alt="' . $title . '"/></div>'
                        . '<div class="category-link">' . $title . '</div>';

                // формируем при необходимости меню третьего уровня
                // тащим экземпляры заданной сущности
                // или пункты следующего уровня
                $list = [];

                if ($field_entity = empty($source_term_wr->field_entity->value()) ? '' : $source_term_wr->field_entity->value()) {
                    $field_entity_field = empty($source_term_wr->field_entity_field->value()) ? '' : $source_term_wr->field_entity_field->value();
                    $field_tids = empty($source_term_wr->field_textfield_1->value()) ? '' : $source_term_wr->field_textfield_1->value();
                    $tids = explode(',', $field_tids);
                    if (count($tids) == 1) $tids = [$tid];
                    $list = _get_menu_entities($field_entity, $field_entity_field, $tids);
                }

                // если сущности для второй трети выше не заданы
                // проверить наличие подпунктов и вывести их
                if (empty($list) && $element['#below']) {
                    $list = _get_menu_terms($element['#below']);
                }

                // третий уровень выводить только при наличии наполнения второй трети
                if ($list) {
                    // определяем наполнение первой трети
                    // если это каталог
                    if ($tid) {
                        $parents = taxonomy_get_parents_all($tid);
                        if (empty($parents[1])) {
                            $category_title = $parents[0]->name;
                            $category_subtitle = t('View more');
                            $category_title_color = $parents[0]->field_color['und'][0]['value'];
                            $category_subtitle_color = '';
                            $category_title_url = url('taxonomy/term/' . $parents[0]->tid);
                            $category_subtitle_url = $category_title_url;
                        } else {
                            $category_title = $parents[1]->name;
                            $category_subtitle = $parents[0]->name;
                            $category_title_color = $parents[1]->field_color['und'][0]['value'];
                            $category_subtitle_color = $parents[0]->field_color['und'][0]['value'];
                            $category_title_url = url('taxonomy/term/' . $parents[1]->tid);
                            $category_subtitle_url = url('taxonomy/term/' . $parents[0]->tid);
                        }
                        $chevron_color = $category_subtitle_color ? $category_subtitle_color : $category_title_color;

                        // выводить ли прайс-лист
                        $szr = false;
                        if (in_array($parents[0]->tid, [AGRO_CATEGORY_FERT_TID, AGRO_CATEGORY_SURFACTANTS_TID, AGRO_CATEGORY_DESICCANTS_TID, AGRO_CATEGORY_HERBICIDES_TID, AGRO_CATEGORY_DISINFECTANTS_TID, AGRO_CATEGORY_FUNGICIDES_TID, AGRO_CATEGORY_INSECTICIDES_TID])) $szr = true;

                    // если ссылка на раздел
                    } else {
                        $category_title = $element['#title'];
                        $category_subtitle = t('View more');
                        $category_title_color = '';
                        $category_subtitle_color = '';
                        $category_title_url = url($href);
                        $category_subtitle_url = $category_title_url;
                    }
                    $list_html = _pack_list_to_html($list, empty($chevron_color) ? [] : ['color' => $chevron_color]);

                    // определяем наполнение последней трети
                    // подготовить баннер при наличии
                    $banner_html = '';
                    if ($element['#below']) {
                        foreach ($element['#below'] as $key => $item) {
                            if (is_numeric($key) && $banner_html = _get_banner_html($element['#below'][$key])) {
                                unset($element['#below'][$key]);
                                break;
                            }
                        }
                    }


                    $sub_menu =   '<div class="level-3-wrapper">'
                                    . '<div class="col-sm-12">'
                                        . '<div class="row fix-heights">'
                                            . '<div class="col-sm-3 col-first">'
                                                . '<a href="' . $category_title_url . '" class="category-title" ><h3 style="color: #' . $category_title_color . '">' . $category_title . '</h3></a>'
                                                . '<a href="' . $category_subtitle_url . '" class="show-more" ' . ($category_subtitle_color ? 'style="color: #' . $category_subtitle_color . '"' : '') . '><h4>' . $category_subtitle . '&nbsp;&nbsp;<i class="fa fa-chevron-right"></i></h4></a>'
                                                . (!empty($szr) ? '<div class="price-list"><a href="/catalog/agrochemicals/price-list">' . t('Price-list') . '</a></div>' : '')
                                            . '</div>'
                                            . '<div class="col-sm-6">'
                                                . $list_html
                                            . '</div>'
                                            . '<div class="col-sm-3' . ($banner_html ? ' menu-banner' : '') . '">' . $banner_html . '</div>'
                                        . '</div>'
                                    . '</div>'
                                . '</div>';

                    unset($element['#below']);
                }
            }

        }
    }

    if (!empty($element['#below'])) {
        unset($element['#below']['#theme_wrappers']);
        $sub_menu =   '<div class="dropdown-menu level-' . ($depth + 1) . '-wrapper">'
                        . '<ul class="level-' . ($depth + 1) . '">'
                            . drupal_render($element['#below'])
                        . '</ul>'
                    . '</div>';

        $attributes['class'][] = 'dropdown';
        $attributes['id'] = 'dropdown';

        $options['attributes']['class'][] = 'dropdown-toggle';
        $options['attributes']['data-toggle'] = 'dropdown';
    }
    $attributes['class'][] = 'level-' . $depth . '-item';
    if (isset($tid) && $tid == 16) $attributes['class'][] = 'visible';

    return '<li' . drupal_attributes($attributes) . '>' . l($title, $href, $options) . $sub_menu . "</li>";
}

function _get_menu_terms($menu_list)
{
    $term_tids = $terms = [];
    foreach ($menu_list as $key => $element) {
        if (is_numeric($key)) {
            $term_tids[] = str_replace('taxonomy/term/', '', $element['#original_link']['link_path']);
        }
    }

    foreach(taxonomy_term_load_multiple($term_tids) as $term) {
        $terms[] = [
            'id' => $term->tid,
            'title' => $term->name,
            'url' => url($term->field_link['und'][0]['value']),
        ];
    }
    return $terms;
}


function _get_menu_entities($field_entity, $field_entity_field = null, $tids = [])
{
    $items = [];

    $query = db_select('node', 'n')->distinct();
    $query->condition('n.type', $field_entity);
    $query->condition('n.status', 1);
    $query->fields('n', array('nid'));

    // дополнительный фильтр по таксономии
    if ($field_entity_field && $tids) {
        $query->innerJoin('field_data_' . $field_entity_field, 'f', 'n.nid = f.entity_id');
        $query->condition('f.' . $field_entity_field . '_tid', $tids, 'IN');
    }

    // подцепить имя сущности
    if (in_array($field_entity, ['product_agro', 'product_fert', 'product_chem'])) {
        $query->leftJoin('field_data_title_field', 'tf', 'n.nid = tf.entity_id');
        $query->condition(
            db_or()
                ->condition('tf.language', $GLOBALS['language']->language)
                ->condition('tf.language', 'und')
        );
        $query->addField('tf', 'title_field_value', 'title');
        $query->orderby('tf.title_field_value', 'ASC');
    }

    // формуляция
    if (in_array($field_entity, ['product_agro'])) {
        $query->leftJoin('field_data_field_pd_formulation', 'ff', 'n.nid = ff.entity_id');
        $query->leftJoin('field_data_field_tax_short_name', 'fs', 'ff.field_pd_formulation_tid = fs.entity_id');
        $query->condition(
            db_or()
                ->condition('fs.language', $GLOBALS['language']->language)
                ->condition('fs.language', 'und')
        );
        $query->addField('fs', 'field_tax_short_name_value', 'formulation');
    }

    foreach($query->execute()->fetchAll() as $item) {
        $items[] = [
            'id' => $item->nid,
            'title' => $item->title,
            'url' => url('node/' . $item->nid),
            'formulation' => empty($item->formulation) ? '' : $item->formulation,
        ];
    }

    return $items;
}

// завернуть вписок в ul
function _pack_list_to_html($items, $options)
{
    $list = '';
    if ($items) {
        $list .= '<ul class="level-3">';
        foreach ($items as $item) {
            $title = $item['title'] . (empty($item['formulation']) ? '' : ', ' . $item['formulation']);
            $color_style = isset($options['color']) ? ' style="color: #' . $options['color'] . '"' : '';
            $hover_style = isset($options['color']) ? ' onmouseover="this.style.color=\'#' . $options['color'] . '\';" onmouseleave="this.style.color=\'#585857\';"' : '';
            $list .= '<li><i class="fa fa-chevron-right"' . $color_style . '></i><a href="' . $item['url'] . '"' . $hover_style . '>' . $title . '</a></li>';
        }
        $list .= '</ul>';
    }
    return $list;
}

// сформировать разметку баннера
function _get_banner_html($element)
{
    $html = '';
    $tid = str_replace('taxonomy/term/', '', $element['#original_link']['link_path']);
    if ($term_wr = entity_metadata_wrapper('taxonomy_term', $tid)) {

        $href = url($term_wr->field_link->value());
        $lang = $GLOBALS['language']->language;
        $desc = $term_wr->language($lang)->description->value();
        $link_title = $element['#original_link']['link_title'];

        if ($image_uri = empty($term_wr->field_shop_category_image->value()) ? '' : $term_wr->field_shop_category_image->file->value()->uri) {
            $image_url = image_style_url('menu_banner', $image_uri);
            $html = '<div class="row category-banner">' .
                        '<div class="col-xs-12 banner-img"><img src="' . $image_url . '"  alt="' . $link_title . '"/></div>' .
                        '<div class="col-xs-12 banner-text">' .
                            '<p>' . $desc . '</p>' .
                            '<a href="' . $href . '">' . $link_title . '&nbsp;&nbsp;<i class="fa fa-chevron-right"></i></a>' .
                        '</div>' .
                    '</div>';
        }
    }

    return $html;
}
