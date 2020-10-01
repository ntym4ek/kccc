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
 * @param array $variables
 *   An associative array of variables, passed by reference.
 *
 * @see page.tpl.php
 *
 * @ingroup theme_preprocess
 */
function ya_preprocess_page(array &$vars)
{
  $vars['logo'] = file_create_url('public://images/logo/logo.svg');
  $vars['logo_bl'] = file_create_url('public://images/logo/logo_bl.svg');

  // Заменить стандартную запись из Bootstrap page.vars.php на названия слассов
  if (!empty($vars['page']['sidebar_first']) && !empty($vars['page']['sidebar_second'])) {
    $vars['content_column_class'] = 'col-sm-6';
  }
  elseif (!empty($vars['page']['sidebar_first']) || !empty($vars['page']['sidebar_second'])) {
    $vars['content_column_class'] = 'col-sm-8';
  }
  else {
    $vars['content_column_class'] = 'col-sm-12';
  }

  /** -------------------------------------------- Меню */
  // Primary desktop nav.
  $menu = menu_tree_all_data('menu-main-d');
  $vars['primary_nav_d'] = menu_tree_output($menu);

  // top desktop nav.
  $menu = menu_tree_all_data('menu-top');
  $vars['top_nav'] = menu_tree_output($menu);

  // Secondary nav.
  if ($vars['secondary_menu']) {
    $menu = menu_tree_all_data(variable_get('menu_secondary_links_source', 'user-menu'));
    $vars['secondary_nav'] = menu_tree_output($menu);
    $vars['secondary_nav']['#theme_wrappers'] = array('menu_tree__user_menu');
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
  // true убирает заголовок страницы (используется в ЛК и в нодах)
  $title_off      = empty($vars['header']['title_off']) ? false : $vars['header']['title_off'];

  $title = drupal_ucfirst($title);

  /** -------------------------------------------- Изображение в шапке - */
  // определить путь, по которому будем искать изображение
  if (arg(0) == 'node' && is_numeric(arg(1))) {
    $path_alias_wo_lang = preg_replace('/^\/en/', '', url($_GET['q']));
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


  if (strpos($_GET['q'], 'node') === 0) {
    $vars['title_off'] = true;
  }

  /** -------------------------------------------- Категория, Заголовок, Подзаголовок - */
  /**  для таксономии - */
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


  /**  для прочих страниц -
   * на случай более длинных путей (фильтры добавляют аргументы) проверяем первые два аргумента
   * проверять раньше нод, чтобы можно было переписать
   */

  switch (arg(0) . (arg(1) ? '/' . arg(1) : '')) {
    case 'agenda':
      $title_off = true;
//      $subtitle = t('Exhibitions with Trade House participation');
      break;
    case 'blogs':
      $title_off = true;
//      $subtitle = t('Our representatives and staff posts');
      break;
    case 'reviews':
      $subtitle = t('Feedback from Trade House customers');
      break;
    case 'info/job':
      if (!arg(2)) {
        $category_title = t('Careers');
        $title_off = true;
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
      if (!empty(arg(2))) {
        $subtitle = t('Cultivated plants starting with letter ') . arg(2);
      }
      $category_title = l(t('Handbooks'), 'handbook');
      break;
    case 'handbook/diseases':
      $subtitle = t('Handbook of plants diseases');
      $category_title = l(t('Handbooks'), 'handbook');
      break;
    case 'handbook/weeds':
      $subtitle = t('Handbook of weeds');
      if (!empty(arg(2))) {
        $subtitle = t('Weeds starting with letter ')  . arg(2);
      }
      $category_title = l(t('Handbooks'), 'handbook');
      break;
    case 'handbook/pests':
      $subtitle = t('Handbook of pests');
      $category_title = l(t('Handbooks'), 'handbook');
      break;
  }

  if (strpos(url($_GET['q']), '/news') === 0) {
    $title_off = true;
//    $category_title = t('News');
  }
//  if (strpos(url($_GET['q']), '/info/job') === 0) {
//    $category_title = t('Careers');
//  }

  /** -------------------------------------- Натройки ноды */
  // отключить заголовок на странице
  if (strpos($_GET['q'], 'node') === 0) {
    $title_off = true;
  }



  $vars['header'] = array(
    'image' => $image,
    'category_title' => $category_title,
    'title' => $title,
    'title_off' => $title_off,
    'subtitle' => $subtitle,
    'print' => $print,
    'url' => $url,
  );
}

/**
 * Processes variables for the "page" theme hook.
 *
 * See template for list of available variables.
 *
 * @param array $variables
 *   An associative array of variables, passed by reference.
 *
 * @see page.tpl.php
 *
 * @ingroup theme_process
 */
function ya_process_page(array &$vars) {

}
