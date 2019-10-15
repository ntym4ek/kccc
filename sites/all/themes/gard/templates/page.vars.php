<?php
/**
 * @file
 * Stub file for "page" theme hook [pre]process functions.
 */

/**
 * Pre-processes variables for the "page" theme hook.
 *
 * See template for list of available variables.
 *
 * @see page.tpl.php
 *
 * @ingroup theme_preprocess
 */
function gard_preprocess_page(&$vars)
{
    // некоторые пути, типа node/8650/registrations не загружают ноду
    if (empty($vars['node']) && arg(0) == 'node' && is_numeric(arg(1))) {
        $vars['node'] = node_load(arg(1));
        $path_alias_wo_lang = url('node/' . arg(1));
    }

    $lang = $GLOBALS['language']->language;

    $vars['search_input_placeholder'] = t('Enter your search query');
    $vars['logo'] = file_create_url('public://images/logo/logo.svg');
    $vars['site_name'] = t('Trading House Kirovo-Chepetsk Chemical Company'); //ООО ТД Кирово-Чепецкая Химическая Компания
    $vars['site_slogan'] = t('Production and selling of fertilizers, plant protection and fire-fighting products');//Производство и продажа удобрений, средств защиты растений и пожаротушения


    /** -------------------------------------------- Шаблон страницы ------------------------------------------------ */
        // шаблон для страниц без оформления
    if (isset($vars['node']) && $vars['node']->nid == 7247) {
        $vars['theme_hook_suggestions'][] = 'page__print';
    }


    /** -------------------------------------------- Шапка страницы ------------------------------------------------- */
        // описание страницы
    if ($menu_item = menu_get_item($_GET['q'])) {
        if ($menu_item['description']) drupal_set_subtitle($menu_item['description']);
    }

    // возможно заголовок уже задан (например, Представители задают его раньше)
    $title          = empty($vars['header']['title']) ? drupal_get_title() : $vars['header']['title'];
    $subtitle       = empty($vars['header']['subtitle']) ? drupal_set_subtitle() : $vars['header']['subtitle'];
    $category_title = empty($vars['header']['category_title']) ? '' : $vars['header']['category_title'];
    $image          = empty($vars['header']['image']) ? '' : $vars['header']['image'];
    $url            = isset($vars['header']['url']) ? $vars['header']['url'] : null;                              // адрес страницы для соцсетей
    $print          = isset($vars['header']['print']) ? $vars['header']['print'] : null;
    // true убирает заголовок страницы
    // используется в ЛК
    $title_off      = empty($vars['header']['title_off']) ? false : $vars['header']['title_off'];
    // true убирает обёртку, выводящую содержимое с отступом от края
    // необходимо для тёмного фона Views
    $wrapper_off    = empty($vars['wrapper_off']) ? false : $vars['wrapper_off'];

    $title = drupal_ucfirst($title);

    // определить путь, по которому будем искать изображение
    if (arg(0) == 'node' && is_numeric(arg(1))) {
        $path_alias_wo_lang = url('node/' . arg(1));
    }

        // если есть картинка PNG или JPG по пути, аналогичному URL страницы, взять её
    if (empty($path_alias_wo_lang))
        $path_alias_wo_lang = strpos(url($_GET['q']), '/en') === 0 ? drupal_substr(url($_GET['q']), 3) : url($_GET['q']);
    foreach(array('png', 'jpg') as $ext) {
        $hi_path = 'public://images/header_images/' . $_GET['q'] . '/header_image.' . $ext;
        if (file_exists($hi_path)) {
            $image = file_create_url($hi_path); break;
        }
        $hi_path = 'public://images/header_images' . $path_alias_wo_lang . '/header_image.' . $ext;
        if (file_exists($hi_path)) {
            $image = file_create_url($hi_path); break;
        }
        // посмотреть на уровнях выше
        $url_array = explode('/', $path_alias_wo_lang);
        while (array_pop($url_array)) {
            if (count($url_array) > 1) {
                $hi_path = 'public://images/header_images' . implode('/', $url_array) . '/header_image.' . $ext;
                if (file_exists($hi_path)) {
                    $image = file_create_url($hi_path);
                    break;
                }
            }
        }
    }

    /** -------------------------------------------- для таксономии - */
    if (arg(0) == 'taxonomy' && arg(1) == 'term' && $term = taxonomy_term_load(arg(2))) {
        $term_wrapper = entity_metadata_wrapper('taxonomy_term', $term);
        $subtitle = $term_wrapper->description->value();
        if (!empty($term->field_image_header)) {
            $image = $term_wrapper->field_image_header->file->url->value();
        }

        // определяем родительскую категорию для термина
        if ($parent_term = current(taxonomy_get_parents($term->tid))) {
            $category_title = '<a href="' . url('taxonomy/term/' . $parent_term->tid) . '">'. $parent_term->name . '</a>';
        }
    }


    /** -------------------------------------------- для прочих страниц -
     * на случай более длинных путей (фильтры добавляют аргументы) проверяем первые два аргумента
     * проверять раньше нод, чтобы можно было переписать
     */

    switch (arg(0) . (arg(1) ? '/' . arg(1) : '')) {
        case 'agenda':
            $subtitle = t('Exhibitions with Trade House participation');
            break;
        case 'blogs':
            $subtitle = t('Our representatives and staff posts');
            break;
        case 'reviews':
            $subtitle = t('Feedback from Trade House customers');
            break;
        case 'info/job':
            if (!arg(2)) {
                $category_title = t('Careers');
                $subtitle = '<p><b>' . t('ООО Trade House "Kirovo-Chepetsk Chemical Company"') . '</b> - ' . t('manufacturing and realizing company of plant protection and other agrochemical products') . '.</p>' .
                            '<p><b>' . t('Kirovo-Chepetsk factory «Agrohimikat»') . '</b> ' . t('produces herbicides, desiccants, insecticides, fungicides and disinfectants with international standards quality') . '.</p>' .
                            '<p>' . t('Our main partners are world\'s large companies') .'.</p>';
            }
            if (arg(2) == 'submissions') {
                $subtitle = 'Список резюме, отправленных через форму на странице вакансии';
                $category_title = '<a href="' . '/info/job' . '">' . t('Careers') . '</a>';
            }

            break;
        case 'handbook':
            $subtitle = t('List of handbooks available in Trade House');
            break;
        case 'handbook/protection-programs':
            $subtitle = t('Protection programs using products of Trade House');
            $category_title = l(t('Handbooks'), 'handbook');
            break;
        case 'handbook/cultures':
            $subtitle = t('Handbook of cultivated plants');
            $category_title = l(t('Handbooks'), 'handbook');
            break;
        case 'handbook/diseases':
            $subtitle = t('Handbook of plants diseases');
            $category_title = l(t('Handbooks'), 'handbook');
            break;
        case 'handbook/weeds':
            $subtitle = t('Handbook of weeds');
            $category_title = l(t('Handbooks'), 'handbook');
            break;
        case 'handbook/pests':
            $subtitle = t('Handbook of pests');
            $category_title = l(t('Handbooks'), 'handbook');
            break;
    }

    /** -------------------------------------------- для Нод - */
    if (isset($vars['node'])) {
        $node_wrapper = entity_metadata_wrapper('node', $vars['node']);

        // нужно ли показывать заголовок
        if (!empty($vars['node']->field_show_header['und'][0]['value'])) {
            $title_off = true;
        } else {
            // если на странице задан Top Image - поставить его
            if (!empty($vars['node']->field_image_header)) {
                $image = $node_wrapper->field_image_header->file->url->value();
            }

            // описание в зависимости от языка
            // но если для языка не задано, берем und или ru
            if (!$subtitle && isset($vars['node']->body)) {
                if (empty($vars['node']->body[$lang][0])) {
                    if (!empty($vars['node']->body['und'][0])) { $subtitle = $vars['node']->body['und'][0]['summary']; }
                    if (!empty($vars['node']->body['ru'][0])) { $subtitle = $vars['node']->body['ru'][0]['summary']; }
                } else {
                    $subtitle = isset($vars['node']->body[$lang]) ? $vars['node']->body[$lang][0]['summary'] : '';
                }
            }

            // определить заголовок Категории
            //для продукции
            if (in_array($vars['node']->type, array('product_agro', 'product_chem', 'product_fert', 'product_mix'))) {
                // если есть, брать название категории из которой попали в описание, иначе первую из списка
                $cat = empty($_GET['cat']) ? $node_wrapper->field_pd_category[0]->tid->value() : $_GET['cat'];
                foreach ($node_wrapper->field_pd_category->getIterator() as $term_wrapper) {
                    if ($term_wrapper->tid->value() == $cat) {
                        $category_title = '<a href="' . url('taxonomy/term/' . $cat) . '">' . $term_wrapper->name->value() . '</a>';
                    }
                }
            }

            // для новостей
            if ($vars['node']->type == 'news') {
                if ($node_wrapper->language('und')->field_news_category->value()) {
                    $tid = $node_wrapper->language('und')->field_news_category->tid->value();
                    $category_title = '<a href="' . url('taxonomy/term/' . $tid) . '">' . $node_wrapper->language('und')->field_news_category->name->value() . '</a>';
                }
            }
            // для Блогов
            if ($vars['node']->type == 'blog') { $category_title = '<a href="' . '/blogs' . '">' . t('Blogs') . '</a>'; }
            // для Афиша
            if ($vars['node']->type == 'agenda') { $category_title = '<a href="' . '/agenda' . '">' . t('Agenda') . '</a>'; }
            // для Отзывов
            if ($vars['node']->type == 'review') { $category_title = '<a href="' . '/reviews' . '">' . t('Reviews') . '</a>'; }
            // для Программ защиты
            if ($vars['node']->type == 'protection_program') {
                $category_title = '<a href="' . '/handbook/protection-programs' . '">' . t('Protection programs') . '</a>';
                $wrapper_off = true;
            }
            // для Вакансии
            if ($vars['node']->type == 'vacancy') {
                $category_title = '<a href="' . '/info/job' . '">' . t('Careers') . '</a>';
                if (empty($subtitle)) $subtitle = t('Vacancy');
            }
            // для Культур
            if ($vars['node']->type == 'main_cultures') { $category_title = '<a href="' . '/handbook/cultures' . '">' . t('Cultivated plants') . '</a>'; }
            // для Вредителей
            if ($vars['node']->type == 'pest') { $category_title = '<a href="' . '/handbook/pests' . '">' . t('Pests') . '</a>'; }
            // для Сорняков
            if ($vars['node']->type == 'weed') { $category_title = '<a href="' . '/handbook/weeds' . '">' . t('Weeds') . '</a>'; }
            // для Болезней
            if ($vars['node']->type == 'disease') { $category_title = '<a href="' . '/handbook/diseases' . '">' . t('Deseases of plants') . '</a>'; }

            if (!isset($print)) $url = url('node/' . $vars['node']->nid, array('absolute' => true));
            if (!isset($print)) $print = true;
        }
    }

    // если нет изображения и собственного заголовка Категории, присвоить Заголовок страницы
    if (empty($category_title) && empty($image)) {
        $category_title = $title;
    }

    $vars['header'] = array(
        'image' => $image,
        'category_title' => $category_title,
        'title' => $title,
        'title_suffix' => isset($title_suffix) ? $title_suffix : '',
        'title_off' => $title_off,
        'subtitle' => $subtitle,
        'print' => $print,
        'url' => $url,
    );


    /** -------------------------------------------- Контент  ------------------------------------------------------- */
    /** -------------------------------------------- Представления (Views) - */
//    if (isset($vars['page']['content']['system_main']['view']) || isset($vars['page']['#contextual_links']['views_ui'])) {
    if (isset($vars['page']['content']['system_main']['view'])
        || (isset($vars['page']['content']['system_main']['main']) && strpos($vars['page']['content']['system_main']['main']['#markup'], 'class="view') !== false && arg(0) !== 'person')) {
        $wrapper_off = true;
    }

    if (!isset($vars['wrapper_off'])) $vars['wrapper_off'] = $wrapper_off;

    /** -------------------------------------------- Меню  ---------------------------------------------------------- */
    // Primary desktop nav.
    $vars['primary_nav_d'] = FALSE;
    $menu = menu_tree_all_data('menu-main-d');
    $vars['primary_nav_d'] = menu_tree_output($menu);

    // Primary nav.
    $vars['primary_nav'] = FALSE;
    if ($vars['main_menu']) {
        $menu = menu_tree_all_data(variable_get('menu_main_links_source', 'main-menu'));
        $vars['primary_nav'] = menu_tree_output($menu);

        // Provide default theme wrapper function.
        $vars['primary_nav']['#theme_wrappers'] = array('menu_tree__primary');
    }

    // Secondary nav.
    $vars['secondary_nav'] = FALSE;
    if ($vars['secondary_menu']) {
        // Build links.
        $menu = menu_tree_all_data(variable_get('menu_secondary_links_source', 'user-menu'));
        $vars['secondary_nav'] = menu_tree_output($menu);
        // убрать ссылку User account для анонима
        //if (!$GLOBALS['user']->uid) unset($vars['secondary_nav'][3]);
        // Provide default theme wrapper function.
        $vars['secondary_nav']['#theme_wrappers'] = array('menu_tree__user_menu');
    }

    // Navigation nav.
    $vars['navigation_nav'] = FALSE;
    if ($menu = menu_tree_all_data(variable_get('menu_main_links_source', 'navigation'))) {
        $vars['navigation_nav'] = menu_tree_output($menu);
        $vars['navigation_nav']['#theme_wrappers'] = array('menu_tree__navigation');
    }

    // меню в подвале
//    $menu = menu_tree_all_data('menu-footer');
//    $vars['footer_nav'] = menu_tree_output($menu);
    $vars['footer_nav'] = menu_tree('menu-footer');

    // языковое меню
    $lang = ($vars['language']->language == 'ru') ? '' : $vars['language']->language;
    $url = drupal_get_path_alias(current_path());
    $url = ($url == 'home') ? '' : $url;
    $url = $lang ? $url : 'en/' . $url;
    $text = '<i class="fas fa-globe"></i> ' . ($lang ? 'Русская версия' : 'English version');
    $vars['language_link'] = '<a href="/' . $url . '">' . $text . '</a>';


    /** - Главная страница ----------------------------------------------------------------- */
    // не выводить ноду
    // todo тратится время на выборку ноды, вообще убрать запрос для главной
    if ($vars['is_front']) {
        unset($vars['page']['content']['system_main']);


        // подготовка блоков
        // todo можно сделать добавление классов в зависимости от количества блоков в ряду
        // todo чтобы выводить произвольное сочетание, заданное в админке, а не фиксированые 1-2-3-1
        // todo пока проблема с тем, как сюда передать из админки необходимую раскладку
//        if (!empty($vars['page']['highlighted']['bootstrap_slider_bootstrap_slider'])) {
//            $vars['slider'] = $vars['page']['highlighted']['bootstrap_slider_bootstrap_slider'];
//        }
        $vars['row_1']['block_1'] = $vars['page']['content']['views_sliders-block_1'];

        $vars['row_2']['block_1'] = $vars['page']['content']['block_18'];
        $vars['row_2']['block_2'] = $vars['page']['content']['block_19'];

        $vars['row_3']['block_1'] = $vars['page']['content']['block_20'];
        $vars['row_3']['block_2'] = $vars['page']['content']['views_interesting_facts-block'];
//        $vars['row_3']['block_2'] = $vars['page']['content']['block_21'];
        $vars['row_3']['block_3'] = $vars['page']['content']['views_sliders-block'];

        $vars['row_4']['block_1'] = $vars['page']['content']['block_23'];
    }

}

/**
 * Processes variables for the "page" theme hook.
 *
 * See template for list of available variables.
 *
 * @see page.tpl.php
 *
 * @ingroup theme_process
 */
function gard_process_page(&$vars) {

}
