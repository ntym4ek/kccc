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
    if ($element['#original_link']['module'] == 'taxonomy_menu') {
        $source_tid = str_replace('taxonomy/term/', '', $element['#original_link']['link_path']);
        if ($source_term_wr = entity_metadata_wrapper('taxonomy_term', $source_tid)) {

            $href = $source_term_wr->field_link->value();
            $tid = strpos($href, 'taxonomy/term/' ) === 0 ?  str_replace('taxonomy/term/', '', $href) : null;

            // у меню второго уровня должно присутствовать изображение
            if ($depth == 2) {
                $image_uri = empty($source_term_wr->field_shop_category_image->value()) ? 'public://default_images/no_photo.png' : $source_term_wr->field_shop_category_image->file->value()->uri;
                $image_url = image_style_url('menu', $image_uri);
                $title =  '<div class="category-img"><img src="' . $image_url . '" /></div>'
                        . '<div class="category-link">' . $title . '</div>';

                // формируем при необходимости меню третьего уровня
                // тащим экземпляры заданной сущности
                if ($field_entity = empty($source_term_wr->field_entity->value()) ? '' : $source_term_wr->field_entity->value()) {
                    $field_entity_field = empty($source_term_wr->field_entity_field->value()) ? '' : $source_term_wr->field_entity_field->value();

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

                    $entities = _get_menu_entities($field_entity, $field_entity_field, $tid);
                    $chevron_color = $category_subtitle_color ? $category_subtitle_color : $category_title_color;
                    $list = _pack_entities_to_list($entities, ['color' => $chevron_color]);

                    $sub_menu =   '<div class="level-3-wrapper">'
                                    . '<div class="col-sm-12">'
                                        . '<div class="row">'
                                            . '<div class="col-sm-3">'
                                                . '<a href="' . $category_title_url . '"><h3 style="color: #' . $category_title_color . '">' . $category_title . '</h3></a>'
                                                . '<a href="' . $category_subtitle_url . '" class="show-more"><h4 ' . ($category_subtitle_color ? 'style="color: #' . $category_subtitle_color . '"' : '') . '>' . $category_subtitle . '&nbsp;&nbsp;<i class="fa fa-chevron-right"></i></h4></a>'
                                            . '</div>'
                                            . '<div class="col-sm-6">'
                                                . $list
                                            . '</div>'
                                            . '<div class="col-sm-3 menu-banner">'
                                                . ''
                                            . '</div>'
                                        . '</div>'
                                    . '</div>'
                                . '</div>';
                    }
            }

        }
    }

    if ($element['#below']) {
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
    if (isset($tid) && $tid == 15) $attributes['class'][] = 'visible';

    return '<li' . drupal_attributes($attributes) . '>' . l($title, $href, $options) . $sub_menu . "</li>";
}

function _get_menu_entities($field_entity, $field_entity_field = null, $tid = null)
{
    $query = db_select('node', 'n')->distinct();
    $query->condition('n.type', $field_entity);
    $query->condition('n.status', 1);
    $query->fields('n', array('nid'));

    // дополнительный фильтр по таксономии
    if ($field_entity_field && $tid) {
        $query->innerJoin('field_data_' . $field_entity_field, 'f', 'n.nid = f.entity_id');
        $query->condition('f.' . $field_entity_field . '_tid', $tid);
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

    return $query->execute()->fetchAll();
}

// завернуть вписок в ul
function _pack_entities_to_list($entities, $options)
{
    $list = '';
    if ($entities) {
        $list .= '<ul class="level-3">';
        foreach ($entities as $entity) {
            $color = isset($options['color']) ? ' style="color: #' . $options['color'] . '"' : '';
            $list .= '<li><i class="fa fa-chevron-right"' . $color . '></i><a href="' . url('node/' . $entity->nid) . '">' . $entity->title . '</a></li>';
        }
        $list .= '</ul>';
    }
    return $list;
}