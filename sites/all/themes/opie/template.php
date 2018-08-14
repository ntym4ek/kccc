<?php
/**
 * @file
 * Here you can use drupal hooks.
 *
 * Write hook on your own or just uncomment predefined common hooks.
 * Don't forget to replace THEMENAME.
 */


/**
 * Preprocess function for theme_captcha().
 */
function opie_preprocess_captcha(&$vars) {
    if ($vars['element']['#captcha_type'] == 'image_captcha/Image' && isset($vars['element']['captcha_widgets'])) {
        $vars['element']['captcha_widgets']['captcha_response']['#field_prefix'] = drupal_render($vars['element']['captcha_widgets']['captcha_image']);
        $vars['element']['captcha_widgets']['captcha_image']['#access'] = FALSE;
    }
}

/**
 * Preprocess variables for commerce-cart-block.tpl.php
 */
function opie_preprocess_commerce_cart_block(&$vars) {
    $order_wrapper = entity_metadata_wrapper('commerce_order', $vars['order']);
    $quantity = commerce_line_items_quantity($order_wrapper->commerce_line_items, commerce_product_line_item_types());
    $quantity_text = format_plural($quantity, '1 item', '@count items', array(), array('context' => 'product count on a Commerce order'));
    $total = $order_wrapper->commerce_order_total->value();
    $total_text = commerce_currency_format($total['amount'], $total['currency_code']);

    $vars['contents_view'] = '<a href="/checkout/' . $vars['order']->order_number . '" id="cart-qty" qty="' . $quantity . '">' . $quantity_text . ' - ' . $total_text . '</a>';
}

/**
 * Implements hook_preprocess_html().
 */

function opie_preprocess_html(&$vars, $hook) {

    // подключение стилей в зависимости от раздела
    if ($vars['is_front']) {
        drupal_add_css(drupal_get_path('theme', 'opie') . '/css/content_front.css');
    } else {
        drupal_add_css(drupal_get_path('theme', 'opie') . '/css/content_all.css');
    }

    // прописать слоган в заголовке на главной
    if ($vars['is_front'] && $vars['language']->language != 'ru') {
        $vars['head_title'] = 'Trade House Kirovo-Chepetsk Chemical Company | Production and sale of fertilizers, plant protection and fire fighting';
    }

    // подключение постраничных библиотек, стилей и скриптов (прописаны в пoле files страницы )
    if (arg(0) == 'node' && is_numeric(arg(1)) && isset($vars['page']['content']['system_main']['nodes'][arg(1)])) {
        $node = $vars['page']['content']['system_main']['nodes'][arg(1)];
        if ($node['#bundle'] == 'information') {
            if (isset($node['field_page_files']['#items'])) {
                foreach ($node['field_page_files']['#items'] as $item) {
                    if ($item['filemime'] == 'text/css') {
                        drupal_add_css($item['uri']);
                    }
                    // js файлы для этого поля загружаются в обход стандартного core-смены расширения js в js.txt
                    // обход сделан в функции chibs_file_presave
                    if ($item['filemime'] == 'application/javascript') {
                        drupal_add_js($item['uri']);
                    }
                }
            }
            if (isset($node['field_page_lib'])) {
                drupal_add_library($node['field_page_lib'][0]['#markup'], $node['field_page_lib'][0]['#markup']);
            }
        }
    }


    // добавление иконок для apple
    //regular apple-touch-icon
    $apple = array(
        '#tag' => 'link', // The #tag is the html tag - <link />
        '#attributes' => array( // Set up an array of attributes inside the tag
            'rel' => 'apple-touch-icon',
            'href' => url(path_to_theme() . '/images/icons/apple/apple-touch-icon.png', array('absolute' => true)),
        ),
    );
    drupal_add_html_head($apple, 'apple-touch');

    $apple_icon_sizes = array(57, 72, 76, 114, 120, 144, 152);

    foreach ($apple_icon_sizes as $size) {
        $apple = array(
            '#tag' => 'link',
            '#attributes' => array(
                'href' => url(path_to_theme() . '/images/icons/apple/apple-touch-icon-' . $size . 'x' . $size . '.png', array('absolute' => true)),
                'rel' => 'apple-touch-icon',
                'sizes' => $size . 'x' . $size,
            ),
        );
        drupal_add_html_head($apple, 'apple-touch-icon-' . $size);
    }
}


function opie_preprocess_node(&$vars) {

    // для Вакансий подготовить форму и обработать сообщения
    if ($vars['node']->type == 'vacancy') {
        module_load_include('inc', 'entityform', 'entityform.admin');
        $entityform_name = 'contact_vacancy';
        $vars['vacancy_form'] = entityform_form_wrapper(entityform_empty_load($entityform_name), 'submit', 'embedded');
        $vars['vacancy_form'] = drupal_render($vars['vacancy_form']);
        if ($messages = theme('status_messages')) {
            $vars['vacancy_form'] = $messages . $vars['vacancy_form'];
        }
    }

    // для Крупной фасовки подключить js библиотеку для форматирования суммы и расчётов? сформировать url на прайс
    if ($vars['view_mode'] == 'full' && in_array($vars['node']->type, array('product_agro', 'product_mix', 'product_fert'))) {
        drupal_add_js(drupal_get_path('theme', 'opie') . '/js/mini_calc.js');
        drupal_add_js(drupal_get_path('theme', 'opie') . '/js/accounting.js');
        $vars['price_link'] = '/catalog/agrochemicals/price-list';
    }


    // если страница для Сотрудников, заменить шаблон
    if ($vars['node']->nid == 460) {
        $vars['theme_hook_suggestions'][] = 'page__print';
    }

    // страница Finfire
    if ($vars['node']->nid == 5933) {
        chibs_include_modal(array('class' => 'dry-chemicals'));
        drupal_add_css('sites/default/files/info/promchem/finfire/page.css');
    }
    if  ($vars['node']->nid == 6063) {
        drupal_add_css('sites/default/files/info/promchem/finfire/page.css');
    }

    // добавить popup на страницу о необходимости регистрации
    if  ($vars['node']->nid == 409) {
        $title = 'Раздел закрыт';
        $body = 'Раздел находится в разработке, следите за обновлениями.<br /><br />'
            . 'Чтобы получить информацию по услуге свяжитесь с нашим менеджером по телефонам 8 (83361) 3-53-08, 8 (83361) 9-28-23.<br /><br />'
            . 'Приносим извинения за неудобства.<br /><br />'
            . '<a href="/">На главную</a>';
        $popup_message_parameters = array(
            'title' => $title,
            'body' => $body,
            'check_cookie' => FALSE,
            'width' => 550,
            'height' => 215,
            'close' => FALSE,
        );

        popup_message_show_message($popup_message_parameters);
    }

}

/**
 * Implements hook_preprocess_page().
 */
function opie_preprocess_page(&$vars) {
    global $user;

    // вывести сообщение о новом магазине JOY в каталоге
    $path = drupal_get_path_alias($_GET['q']);
    if (strpos($path, 'catalog/joy') === 0) {
        drupal_set_message('Для заказа продукции из раздела Дача.Сад.Огород воспользуйтесь нашим новым интернет-магазином <a href="https://joy-magazin.ru">Joy-Magazin.ru</a>');
    }

    // определить текущий язык
    $lang = ($vars['language']->language == 'ru') ? '' : $vars['language']->language;

    // переменная вывода страницы без Заголовка
    $vars['no_title'] = false;
    $vars['search_input_placeholder'] = t('Enter your search query');
    $vars['logo'] = file_create_url('public://images/logo/logo.svg');

    // Add template suggestions for 404 and 403 errors.
    $status = drupal_get_http_header("status");
    if ($status == "404 Not Found") {
        $vars['theme_hook_suggestions'][] = 'page__404';
    }


    // рендер главного меню
    $menu = menu_tree_all_data('main-menu');
    $menu_tree = menu_tree_output($menu);
    $vars['primary_nav'] = drupal_render($menu_tree);

    $url = drupal_get_path_alias(current_path());
    $url = ($url == 'node') ? '' : $url;

    // рендер вспомогательного меню
    $sup_menu = menu_tree('menu-supmenu');
    $vars['sup_nav'] = drupal_render($sup_menu);

    // цвет title
    if (strpos($url, 'tolling') !== false) {
        $vars['title_color_style'] = 'f2a500';
    }
    if (strpos($url, 'promchem') !== false) {
        $vars['title_color_style'] = 'de242a';
    }


    // описание страницы
    if (!isset($vars['title_description'])) $vars['title_description'] = '';

        // для нод
    if (arg(0) == 'node' && isset($vars['node'])) {
        $field = field_get_items('node', $vars['node'], 'body');
        $vars['title_description'] = $field[0]['summary'];

        // подключить скрипт Яндекса "Поделиться" и убрать заголовок для Новостей, Блогов
        if (in_array($vars['node']->type, array('news', 'agenda', 'blog', 'review', 'main_cultures', 'harmful_objects', 'weed', 'pest', 'ad', 'vacancy'))) {
            drupal_add_js('https://yastatic.net/share2/share.js');
            $vars['no_title'] = true;
        }

        switch($vars['node']->type) {
            case 'ad':
                // включить функции для модального окна Отправки сообщения
                chibs_include_modal();
                break;
            
            case 'news':
                $vars['title_new'] = true;
                $vars['title_description'] = '';
                break;
            case 'agrodocument':
                $doc_name = field_get_items('node', $vars['node'], 'field_doc_name');
                $vars['title'] = $doc_name[0]['value'];
                drupal_set_title($doc_name[0]['value']);
                break;
            case 'review':
                $vars['title_description'] = t('Review from user');
                break;
            case 'blog':
                $vars['title_description'] = t('Blog post from user');
                break;
            case 'agenda':
                $vars['title_description'] = t('Event with participation of the company');
                break;
            case 'disease':
                $vars['title_description'] = 'Болезнь, поражающая сельскохозяйственные культуры';
                break;
        }
    }

        // страницы entityform
    if (strpos($_GET['q'], 'eform/submit/') === 0) {
        // перевести заголовок, так как для entityform локализацию я не нашел
        $temp = drupal_get_title();
        $vars['title'] = t($temp);
    }


        // для Ограниченный доступ
    if (drupal_get_title() == 'Ограниченный доступ') {
        $vars['title_description'] = t('You have to register account to have access to this page.');
    }

        // для таксономии
    if (arg(0) == 'taxonomy' && arg(1) == 'term') {
        $tid = (int) arg(2);
        $term_wrapper = entity_metadata_wrapper('taxonomy_term', $tid);
        $vars['title_new'] = false;

        // если новое офрмление страницы таксономии
        if (isset($term_wrapper->field_category_image2) && $term_wrapper->field_category_image2->value()) {
            $vars['title_new'] = true;
            $file = $term_wrapper->field_category_image2->value();
            $vars['title_image_url'] = file_create_url($file['uri']);
        }
        $vars['title_description'] = $term_wrapper->description->value();
    }

        // различные страницы
    $path = request_path();
    if (strpos($path, 'en/') === 0) $path = str_replace('en/', '', request_path());
    switch ($path) {
        case 'info/agrocast':
            $vars['title_description'] = t('Detailed weather forecast for agrobusiness');
            break;
        case 'support/contact_us':
            $vars['title_new'] = true;
            $vars['title_description'] = '';
            break;
        case 'info/harmful-objects':
            $vars['title_description'] = t('Catalog of harmful objects, that hinder agriculture');
            break;
        case 'info/main-cultures':
            $vars['title_description'] = t('Catalog of main cultures for agriculture');
            break;
        case 'info/representatives':
            $vars['title_new'] = true;
            $vars['title_description'] = '';
            break;
        case 'info/strategy':
            $vars['title_new'] = true;
            $vars['title_description'] = '';
            break;
        case 'info/contacts':
            $vars['title_new'] = true;
            $vars['title_description'] = '';
            break;
        case 'blogs':
            $vars['title_description'] = t('Last posts in user blogs');
            break;
        case 'reviews':
            $vars['title_description'] = t('Last reviews posted by clients');
            break;
        case 'agro-recipe':
            $vars['title_description'] = t('Find sollution for your field problems');
            break;
        case 'handbook/harmful-objects':
            $vars['no_title'] = true;
            break;
        case 'catalog/promchem':
            $vars['no_title'] = true;
            $vars['title_description'] = '';
            break;
        case 'info/privacy-policy':
            $vars['title_new'] = true;
            $vars['title_description'] = '';
            break;
    }
    if (strpos($path, 'node/add/') === 0 || (arg(0) == 'node' && arg(2) == 'edit')) {
        $vars['title_new'] = true;
        $vars['title_description'] = '';
    }




    // переключатель языка
    if ($lang) {
        $translate_url = '/' . $url;
        $translate_lang = 'Ru';
    }
    else {
        $translate_url = '/en/' . $url;
        $translate_lang = 'En';
    }


    // формируем меню пользователя согласно правам
    if ($vars['logged_in']) {
        $user_menu = array();
        $n_menu = menu_tree_all_data('navigation');
        $n_menu_tree = menu_tree_output($n_menu);
        $u_menu = menu_tree_all_data('user-menu');
        $u_menu_tree = menu_tree_output($u_menu);

        // объединяем в один массив
        if (isset($n_menu_tree)) {
            $user_menu = array_merge($user_menu, $n_menu_tree);
        }
        if (isset($u_menu_tree)) {
            $user_menu = array_merge($user_menu, $u_menu_tree);
        }
        $vars['user_menu'] = render($user_menu);
    }
    else {
        $lang = ($lang) ? '/' . $lang : '';
        $vars['user_menu'] = t("<a href='@user_login'>Log in</a>  <a href='@user_register'>Sign up</a>",
            array(
                '@user_login' => $lang . '/user/login',
                '@user_register' => $lang . '/user/register',
            )
        );
    }
    $vars['user_menu'] .= '<a href="' . $translate_url . '" title="" class="menu__link">' . $translate_lang . '</a>';

    // добавить bubble в меню
    if ($bubble = getBubbleHTML(array('uid' => $GLOBALS['user']->uid),true)) $vars['user_menu'] .= $bubble;

    // добавить всплывающее окно на страницу
    if (in_array($_GET['q'], array(
        'info/labor-protection',
        'eform/submit/contact-joy',
        'node/421',
    ))) {
        // выводим если не зарегистрирован или не подтвержден
        if ((isset($user->roles[1])) || (isset($user->roles[7]))) {
            // если
            if (isset($user->roles[1])) {
                $title = t('Registration required');
                $body = '<div>'
                    . '<a href="/user/register?destination=' . $_GET['q'] . '">' . t('Signing up') . '</a> ' . t('will allow you:')
                    . '<div style="font-size: 13px; line-height: 18px; margin:5px 0 10px 5px; ">'
                    . '  <div>1. ' . t('have a full access to information, photo and documents;') . '</div>'
                    . '  <div>2. ' . t('track the state and position of your orders;') . '</div>'
                    . '  <div>3. ' . t('timely get news about new topics and functions on our website.') . '</div>'
                    . '</div>' . t('If you already signed up,') . ' ' . '<a href="/user/login?destination=' . $_GET['q'] . '">' . t('sign in') . '</a>, ' . t('please') . '.</div>';
            }
            else {
                $title = t('E-Mail confirmation required');
                $body = t('You have to confirm your E-Mail address. Please, click by link we sent you in e-mail after you signed up.<br><br><a href="/" class="fright mr30">Back to homepage</a>');
            }

            $popup_message_parameters = array(
                'title' => $title,
                'body' => $body,
                'check_cookie' => FALSE,
                'width' => 550,
                'height' => 215,
                'close' => FALSE,
            );

            popup_message_show_message($popup_message_parameters);
        }
    }

}

/**
 * Override or insert variables into the block templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("block" in this case.)
 */
function opie_preprocess_block(&$variables, $hook) {
    // Use a template with no wrapper for the page's main content.
    if ($variables['block_html_id'] == 'block-system-main') {
        $variables['theme_hook_suggestions'][] = 'block__no_wrapper';
    }
}

/**
 * Override or insert variables into the block templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("block" in this case.)
 */
function opie_preprocess_node_vacancy(&$variables, $hook) {
    // Use a template with no wrapper for the page's main content.
    if ($variables['block_html_id'] == 'block-system-main') {
        $variables['theme_hook_suggestions'][] = 'block__no_wrapper';
    }
}
function opie_preprocess_node__vacancy(&$variables, $hook) {
    // Use a template with no wrapper for the page's main content.
    if ($variables['block_html_id'] == 'block-system-main') {
        $variables['theme_hook_suggestions'][] = 'block__no_wrapper';
    }
}


/**
 * Implements hook_theme().
 */
function opie_theme() {
    $theme = array();

    // Contact form.
    $theme['contact_us_entityform_edit_form'] = array(
        'arguments' => array('form' => NULL),
        'template' => 'templates/entityform/contact-us-form',
        'render element' => 'form',
    );
    $theme['contact_agro_entityform_edit_form'] = array(
        'arguments' => array('form' => NULL),
        'template' => 'templates/entityform/contact-form',
        'render element' => 'form',
    );
    $theme['contact_joy_entityform_edit_form'] = array(
        'arguments' => array('form' => NULL),
        'template' => 'templates/entityform/contact-form',
        'render element' => 'form',
    );
    // Comment form.
    $theme['comment_form'] = array(
        'arguments' => array('form' => NULL),
        'render element' => 'form',
        'template' => 'templates/comments/comment-form',
    );

    return $theme;
}


/**
 * Замена в меню ссылки Главная на картинку
 */
function opie_menu_tree__main_menu($variables) {
    return '<ul>' . $variables['tree'] . '</ul>';
}

function opie_menu_link__main_menu($variables) {
    $element = $variables['element'];
    $sub_menu = '';

    if ($element['#below']) {
        unset($element['#below']['#theme_wrappers']);
        $element['#below']['#theme_wrappers'][] = 'menu_tree__main_menu_inner_' . $element['#original_link']['mlid'];
        foreach ($element['#below'] as $key => $val) {
            if (is_numeric($key)) {
                $element['#below'][$key]['#theme'] = 'menu_link__main_menu_inner';
            }
        }
        //dpm($element);
        $sub_menu = drupal_render($element['#below']);
    }
    $output = l($element['#title'], $element['#href'], $element['#localized_options']);
    return '<li id="fold-' . $element['#original_link']['mlid'] . '"><i class="icon-arrow_down"></i>' . $output . $sub_menu . "</li>\n";

}

function opie_menu_tree__menu_supmenu($variables) {
    return $variables['tree'];
}

function opie_menu_link__menu_supmenu($variables) {
    $element = $variables['element'];

    if ($element['#title'] == 'Заказать звонок') {
        return '<a href="#" class="menu__link" onClick="supportAPI.openTab(2); return false;">Заказать звонок</a>';
    }
    else {
        $output = l($element['#title'], $element['#href'], $element['#localized_options']);
        return $output;
    }
}

function opie_menu_tree__main_menu_inner_2055($variables) {
    return '<div id="foldout-news" class="foldout" ><ul>' . $variables['tree'] . '</ul><div class="clear"></div><div class="foldout-close"><i class="fa fa-chevron-up" aria-hidden="true"></i></div></div>';
}

function opie_menu_tree__main_menu_inner_272($variables) {
    return '<div id="foldout-catalog" class="foldout" ><ul>' . $variables['tree'] . '</ul><div class="clear"></div><div class="foldout-close"><i class="fa fa-chevron-up" aria-hidden="true"></i></div></div>';
}

function opie_menu_tree__main_menu_inner_2054($variables) {
    return '<div id="foldout-support" class="foldout" ><ul>' . $variables['tree'] . '</ul><div class="clear"></div><div class="foldout-close"><i class="fa fa-chevron-up" aria-hidden="true"></i></div></div>';
}

// связать меню с таксономией каталога продукции
function opie_menu_link__main_menu_inner($variables) {
    $element = $variables['element'];
    $tid = 0;
    $img_url = '';

    // содержимое для выпадающего меню Продукция
    if ($element['#original_link']['mlid'] == 796) {
        $tid = 1;
    }
    if ($element['#original_link']['mlid'] == 797) {
        $tid = 2;
    }
    if ($element['#original_link']['mlid'] == 1481) {
        $tid = 3;
    }
    if ($element['#original_link']['mlid'] == 799) {
        $tid = 4;
    }
    if ($element['#original_link']['mlid'] == 890) {
        $tid = 40;
    }
    if ($element['#original_link']['mlid'] == 1047) {
        $tid = 6;
    }
    if ($element['#original_link']['mlid'] == 1048) {
        $tid = 7;
    }
    // мин. удобрения
    if ($element['#original_link']['mlid'] == 1737) {
        $tid = 93;
    }
    if ($tid != 0) {
        $term = taxonomy_term_load($tid);
        //$element['#localized_options']['attributes']['style'][] = 'background:#' . $term->field_color['und'][0]['value'];
        $img_url = file_create_url($term->field_shop_category_image['und'][0]['uri']);
    }

    $path_alias = drupal_get_path_alias($element['#href']);
    $path_alias = ($path_alias == '<front>') ? '/' : $path_alias;
    $link = l($element['#title'], $element['#href'], $element['#localized_options']);
    $c = '<div class="sub-box">';
    if ($img_url) {
        $c .= '<a href="/' . $path_alias . '"><img src="' . $img_url . '"/></a>';
    }
    $c .= '<ul>';

    $c .= '<li>' . $link . '</li>';

    $below = $element['#below'];
    foreach ($below as $key => $value) {
        if (is_numeric($key)) {
            $link = l($value['#title'], $value['#href'], $value['#localized_options']);
            $c .= '<li>' . $link . '</li>';
        }
    }
    $c .= '</ul></div>';

    //dpm($element);

    return '<li id="sub-' . $element['#original_link']['mlid'] . '">' . $c . "</li>\n";
}


/**
 * перенос закладок в контекстное меню
 */
// убрать Secondary tabs на странице редактирования пользователя
//function opie_menu_local_tasks_alter(&$data, $router_item) {
//    if (strpos($router_item['path'], 'user/%/edit') !== false) {
//        unset($data['tabs'][1]);
//    }
//}

function opie_menu_local_task($variables) {
    $link = $variables['element']['#link'];
    if (isset($link['path']) && ($link['path'] == 'node/%/view')) {
        return FALSE;
    }
    $link['localized_options']['html'] = TRUE;
    return '<li>' . l($link['title'], $link['href'], $link['localized_options']) . '</li>';
}

function opie_menu_local_tasks($variables) {
    $output = '';

    $has_access = user_access('access contextual links');
    if (!empty($variables['primary']) && $has_access) {

        $variables['primary']['#prefix'] = '<div class="contextual-links-wrapper"><ul class="contextual-links">';
        $variables['primary']['#suffix'] = '</ul></div>';
        $output .= drupal_render($variables['primary']);
        drupal_add_library('contextual', 'contextual-links');
    }
    if (!empty($variables['secondary']) && $has_access) {
        $variables['secondary']['#prefix'] = '<ul class="tabs secondary">';
        $variables['secondary']['#suffix'] = '</ul>';
        $output .= drupal_render($variables['secondary']);
    }

    return $output;
}

/**
 * Implements hook_block_view_alter().
 */
function opie_block_view_alter(&$data, $block) {
    // Alter views block for search api to theme.
    // This is exposed filter (block) from search page view.
    if ($block->module == 'views' && $block->delta == '-exp-search_all-page') {
        // Add grid classes, to make form 100%;
        $data['content']['#markup'] = str_replace('views-widget-filter-search_api_views_fulltext', 'views-widget-filter-search_api_views_fulltext', $data['content']['#markup']);
        $data['content']['#markup'] = str_replace('views-submit-button', 'views-submit-button', $data['content']['#markup']);
        $data['content']['#markup'] = str_replace('views-reset-button', 'views-reset-button', $data['content']['#markup']);
    }
}

/**
 * Process variables for comment.tpl.php.
 *
 * @see comment.tpl.php
 */
function opie_preprocess_comment(&$variables) {

    $comment = $variables['elements']['#comment'];

    $variables['submitted'] = t('!username replied on !datetime', array(
      '!username' => theme('username', array('account' => $comment)),
      '!datetime' => format_date($comment->created, 'date_time'),
    ));

    $profile = profile2_load_by_user($comment->uid, 'main');
    $variables['picture'] = theme('image_style', array(
      'style_name' => 'user_photo',
      'path' => empty($profile->field_profile_photo['und'][0]['uri']) ? 'default_images/no_photo.png' : $profile->field_profile_photo['und'][0]['uri'],
    ));

}

/**
 * Implements theme_file_link().
 */
function opie_file_link($variables) {
    $file = $variables['file'];

    $file_extension = pathinfo($file->filename, PATHINFO_EXTENSION);
    $file_description = !empty($file->description) ? $file->description : str_replace('.' .$file_extension, '', $file->filename);
    $icon_directory = $variables['icon_directory'];
    //$icon_directory = drupal_get_path('theme', 'opie') . 'images/file-icons';

    $url = file_create_url($file->uri);
    $mimetype = file_get_mimetype($file->uri);
    $icon = theme('file_icon', array(
        'file' => $file,
        'icon_directory' => $icon_directory,
        'alt' => !empty($mime_name[$mimetype]) ? $mime_name[$mimetype] : t('File'),
    ));

    // Set options as per anchor format described at
    // http://microformats.org/wiki/file-format-examples
    $output = '';
    $output .= '<div class="file-info">';
    $output .=      $icon;
    $output .= '    <div>';
    $output .= '        <a href="' . $url . '" target="_blank" title="' . $file_description . '">' . $file_description . '</a><br>';
    $output .= '        <span>' . $file_extension . ' - ' . format_size($file->filesize) . '</span>';
    $output .= '    </div>';
    $output .= '</div>';

    // добавить снопку на скачивание
    if (!empty($file->display)) {
        $output .= '<div class="file-download"><a href="' . $url . '" class="submit-button">' . t('Download') . '</a></div>';
    }

    return $output;
}

/**
 * перекрытие стандартной функции с добавлениеь обёртки для кнопки Удаления
 */
function opie_file_managed_file($variables) {
    $element = $variables['element'];

    $attributes = array();
    if (isset($element['#id'])) {
        $attributes['id'] = $element['#id'];
    }
    if (!empty($element['#attributes']['class'])) {
        $attributes['class'] = (array) $element['#attributes']['class'];
    }
    $attributes['class'][] = 'form-managed-file';

    // This wrapper is required to apply JS behaviors and CSS styling.
    $output = '';
    $output .= '<div' . drupal_attributes($attributes) . '>';

    // изменить метку кнопки Удалить ********************
    $element['remove_button']['#value'] = '';
    $element['remove_button']['#prefix'] = '<div class="remove-button">';
    $element['remove_button']['#suffix'] = '</div>';
    // **************************************************

    $output .= drupal_render_children($element);
    $output .= '</div>';
    return $output;
}
/**
 * перекрытие стандартной функции с добавлениеь обёртки для кнопки Удаления
 */
function opie_file_widget($variables) {
    $element = $variables['element'];
    $output = '';

    // The "form-managed-file" class is required for proper Ajax functionality.
    $output .= '<div class="file-widget form-managed-file clearfix">';
//*    if ($element['fid']['#value'] != 0) {
//*        // Add the file size after the file name.
//*        $element['filename']['#markup'] .= ' <span class="file-size">(' . format_size($element['#file']->filesize) . ')</span> ';
//*    }

    // изменить метку кнопки Удалить ********************
    $element['remove_button']['#value'] = '';
    $element['remove_button']['#prefix'] = '<div class="remove-button">';
    $element['remove_button']['#suffix'] = '</div>';
    // **************************************************

    $output .= drupal_render_children($element);
    $output .= '</div>';

    return $output;
}
/**
 * Returns HTML for an image field widget.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: A render element representing the image field widget.
 *
 * @ingroup themeable
 */
function opie_image_widget($variables) {
    $element = $variables['element'];
    $output = '';
    $output .= '<div class="image-widget form-managed-file clearfix">';

    // изменить метку кнопки Удалить ********************
    $element['remove_button']['#value'] = '';
    $element['remove_button']['#prefix'] = '<div class="remove-button">';
    $element['remove_button']['#suffix'] = '</div>';
    // **************************************************

//    if (isset($element['preview'])) {
    if ($element['fid']['#value'] != 0) {
        $output .= '<div class="image-preview">';
        hide($element['preview']);
        $output .= theme('image_style', array('style_name' => 'thumbnail', 'path' => $element['#file']->uri, 'getsize' => FALSE));
        $output .= drupal_render($element['remove_button']);
        $output .= '</div>';
    }

    $output .= '<div class="image-widget-data">';
//    if ($element['fid']['#value'] != 0) {
//        $element['filename']['#markup'] .= ' <span class="file-size">(' . format_size($element['#file']->filesize) . ')</span> ';
//    }

    $element['title']['#attributes']['placeholder'] = array('Добавьте описание изображения');
    $element['title']['#title'] = '';
    $element['title']['#size'] = 30;
    unset($element['title']['#description']);

    $output .= drupal_render_children($element);
    $output .= '</div>';
    $output .= '</div>';

    return $output;
}

/**
 * Implements hook_theme_registry_alter().
 */
function opie_theme_registry_alter(&$theme_registry) {
    $theme_path = path_to_theme();
    // For subthemes.
    $dl_theme_path = drupal_get_path('theme', 'opie');

    // темизация Checkboxes.
  if (isset($theme_registry['checkbox'])) {
    $theme_registry['checkbox']['type'] = 'theme';
    $theme_registry['checkbox']['theme path'] = $dl_theme_path;
    $theme_registry['checkbox']['template'] = $theme_path . '/templates/fields/field--type-checkbox';
    unset($theme_registry['checkbox']['function']);
  }

  // темизация Radios.
  if (isset($theme_registry['radio'])) {
    $theme_registry['radio']['type'] = 'theme';
    $theme_registry['radio']['theme path'] = $dl_theme_path;
    $theme_registry['radio']['template'] = $theme_path . '/templates/fields/field--type-radio';
    unset($theme_registry['radio']['function']);
  }
}

/**
 * Implements hook_theme_file().
 * добавлена обёртка с классом .file-input для темизации загрузки фото 
 */
function opie_file($variables) {
    $element = $variables['element'];
    $element['#attributes']['type'] = 'file';
    element_set_attributes($element, array('id', 'name', 'size'));
    _form_set_class($element, array('form-file'));

    return '<div class="file-input"><input' . drupal_attributes($element['#attributes']) . ' /></div>';
}

/**
 * Returns HTML for a username, potentially linked to the user's page.
 *
 * @see template_preprocess_username()
 * @see template_process_username()
 */
function opie_username($variables) {
    // автор
    $author_name = array();
    $author = person_get_user_array($variables['uid']);
    $author_name[] = $author['name'];
    $author_name[] = $author['surname'];
    $name = implode(' ', $author_name);
    if (trim($name) == '') $name = $variables['name'];

    $link_path = 'person/' . $variables['uid'] . '/summary';

    if (isset($variables['link_path'])) {
        // We have a link path, so we should generate a link using l().
        // Additional classes may be added as array elements like
        // $variables['link_options']['attributes']['class'][] = 'myclass';
        $output = l($name . $variables['extra'], $link_path, $variables['link_options']);
    }
    else {
        // Modules may have added important attributes so they must be included
        // in the output. Additional classes may be added as array elements like
        // $variables['attributes_array']['class'][] = 'myclass';
        $output = '<span' . drupal_attributes($variables['attributes_array']) . '>' . $name . $variables['extra'] . '</span>';
    }
    return $output;
}
