<?php
/**
 * @file
 * The primary PHP file for this theme.
 */

/**
 * Implements hook_theme_registry_alter().
 */
function cbtheme_theme_registry_alter(&$theme_registry)
{
  // вставляем свою preprocess функцию после основной
  $preprocess_functions = $theme_registry['page']['preprocess functions'];
  $key = array_search('template_preprocess_page', $preprocess_functions);
  if ($key !== FALSE) {
    array_splice($preprocess_functions, ++$key, 0, ['cbtheme_preprocess_page']);
    $preprocess_functions = array_unique($preprocess_functions);
    $theme_registry['page']['preprocess functions'] = $preprocess_functions;
  }
}

/**
 * Implements hook_preprocess_html().
 */
function cbtheme_preprocess_html(&$vars)
{
  // -- С какой стороны мобильное меню
    // по умолчанию слева
  $vars['classes_array'][] = 'nav-mobile-' . (theme_get_setting('nav-mobile-position') ?? 'left');
}

/**
 * Implements hook_preprocess_page().
 */
function cbtheme_preprocess_page(&$vars)
{
  $vars['is_mobile'] = (stripos($_SERVER["HTTP_USER_AGENT"], "mobile")!== false);

  // можно отключить шапку
  $vars['is_header_off'] = false;

  // можно отключить заголовок
  $vars['is_title_off'] = false;

  // выводить ли заголовок в виде широкого баннера
  $vars['is_title_as_banner'] = false;
  // пример вывода в виде баннера
//  if (!empty($vars["node"]->field_image_banner)) {
//    $vars['title_background'] = file_create_url($vars["node"]->field_image_banner['und'][0]['uri']);
//  } else {
    //  $img_url = 'public://images/categories/plant1.jpg';
    //  $img_url = $is_mobile ? image_style_url('banner_mobile', $img_url) : file_create_url($img_url);
    //  $vars['title_background'] = $img_url;
//  }

//  $vars['title_prefix'] = [
//    '#markup' => '<div class="page-title-logo"><img src="/sites/default/files/images/logo/logo_kccc_black.png" alt="' . $GLOBALS['base_url'] .'"></div>'
//  ];
//  $vars['title_suffix'] = [
//    '#markup' => '<div class="page-title-description"><h3>Мы работаем,<br>чтобы обеспечить мир современной,<br>экологически безопасной и&nbsp;эффективной химией</h3></div>'
//  ];

  // сменить шаблон страницы на пустой
  //  if (arg(0) == 'card') {
  //    $vars['theme_hook_suggestions'][] = 'page__empty';
  //  }

  // вывод баннера, если в папке page-banners есть изображение с именем, аналогичным пути
  $img_uri = '';
  $paths = [$_GET['q']];
  $paths[] = drupal_get_path_alias($_GET['q']);
  foreach($paths as $path) {
    foreach(['jpg', 'png'] as $ext) {
      $uri = 'public://images/page-banners/' . arg(0, $path) . '.' . $ext;
      if (file_exists($uri)) {
        $img_uri = $uri;
        break;
      }
    }
  }

  if ($img_uri) {
    $vars['is_title_as_banner'] = true;
    $img_url = $vars['is_mobile'] ? image_style_url('banner_mobile', $img_uri) : file_create_url($img_uri);
    $vars['title_background'] = $img_url;
  }

  // вывод многоуровневого меню
  if (isset($vars['main_menu'])) {
    if (module_exists('i18n_menu')) {
      $main_menu = i18n_menu_translated_tree('main-menu');
    } else {
      $main_menu = menu_tree('main-menu');
    }
    $main_menu['#attributes']['class'] = ['main-menu'];
    $vars['primary_nav'] = render($main_menu);
  }
  else {
    $vars['primary_nav'] = FALSE;
  }

  if (isset($vars['secondary_menu'])) {
    $main_menu =  module_exists('i18n_menu') ? i18n_menu_translated_tree('user-menu') : menu_tree('user-menu');
    $main_menu['#attributes']['class'] = ['secondary-menu'];
    $vars['secondary_nav'] = render($main_menu);
  }
  else {
    $vars['secondary_nav'] = FALSE;
  }

}


/**
 * Implements hook_theme().
 */
function cbtheme_theme()
{
  return [
    'button_icon' => array(             // кнопка с возможностью добавить иконку
      'render element' => 'element',
    ),
  ];
}


/**
 * Pre-processes variables for the "region" theme hook.
 */
function cbtheme_preprocess_region(array &$vars)
{
  $region = $vars['region'];

  // Content region.
  if (in_array($region, ['header', 'highlighted', 'sidebar_first', 'content', 'sidebar_second'])) {
    $vars['theme_hook_suggestions'][] = 'region__no_wrapper';
  }
}

/**
 * Returns HTML for a region.
 */
function cbtheme_region__no_wrapper(&$vars)
{
  return $vars['elements']['#children'];
}

/**
 * Pre-processes variables for the "block" theme hook.
 */
function cbtheme_preprocess_block(array &$vars)
{
  // Use a bare template for the page's main content.
  if ($vars['block_html_id'] == 'block-system-main') {
    $vars['theme_hook_suggestions'][] = 'block__no_wrapper';
  }
  $vars['title_attributes_array']['class'][] = 'block-title';
}

/**
 * Returns HTML for a block.
 */
function cbtheme_block__no_wrapper($vars)
{
  return $vars['elements']['#children'];
}

/**
 * Pre-processes variables for the "image" theme hook.
 */
function cbtheme_preprocess_image(&$vars)
{
  // удалить атрибуты размера для изображений
  unset($vars['width']);
  unset($vars['height']);
}

/**
 * Implements theme_menu_tree().
 * выводим многоуровневое меню с дополнительными классами
 */
function cbtheme_menu_tree($vars)
{
  $attributes = $vars['#tree']['#attributes'] ?? ['class' => []];

  // определить глубину уровня
  $depth = 0;
  foreach ($vars["#tree"] as $index => $item) {
    if (is_numeric($index)) {
      $depth = $item["#original_link"]["depth"] ?? $depth;
    }
  }
  $attributes['class'][] = $depth > 1 ? 'sub-menu' : 'menu';
  $attributes['class'][] = 'level-' . $depth;

  return '<ul' . drupal_attributes($attributes) . '>' . $vars['tree'] . '</ul>';
}

/**
 * Implements hook_preprocess_node().
 */
function cbtheme_preprocess_node(&$vars)
{
  // возможность создавать шаблоны
  $node_type_suggestion_key = array_search('node__' . $vars['type'], $vars['theme_hook_suggestions']);
  if ($node_type_suggestion_key !== FALSE) {
    $node_view_mode_suggestion = 'node__' . $vars['type'] . '__' . $vars['view_mode'];
    array_splice($vars['theme_hook_suggestions'], $node_type_suggestion_key + 1, 0, array($node_view_mode_suggestion));
    $node_view_mode_suggestion = 'node__' . $vars['view_mode'];
    array_splice($vars['theme_hook_suggestions'], $node_type_suggestion_key, 0, array($node_view_mode_suggestion));
  }
  if ($vars['view_mode'] == 'full') {
    $vars["classes_array"][] = 'node-full';
  }
}


/**
 * Implements hook_preprocess_button().
 */
function cbtheme_preprocess_button(&$vars)
{
  // добавим Кнопкам класс для темизации и цвета
  if (!isset($vars["element"]["#attributes"])) $vars["element"]["#attributes"] = [];
  if (!isset($vars["element"]["#attributes"]['class'])) $vars["element"]["#attributes"]['class'] = [];

  // добавляем только, если класс не был добавлен ранее (например в кастомных формах)
  if (!in_array('btn', $vars["element"]['#attributes']['class'])) {
    $vars["element"]['#attributes']['class'][] = 'btn';
    if (isset($vars["element"]["#id"])) {
      if (strpos($vars["element"]["#id"], 'file') !== FALSE || strpos($vars["element"]["#id"], 'image') !== FALSE || strpos($vars["element"]["#id"], 'upload') !== FALSE) {
        $vars["element"]['#attributes']['class'][] = 'btn-small';
      }
      if (strpos($vars["element"]["#id"], 'submit') !== FALSE) {
        $vars["element"]['#attributes']['class'][] = 'btn-brand';
      }
      elseif (strpos($vars["element"]["#id"], 'delete') !== FALSE || strpos($vars["element"]["#id"], 'remove') !== FALSE) {
        $vars["element"]['#attributes']['class'][] = 'btn-danger';
      } else {
        $vars["element"]['#attributes']['class'][] = 'btn-default';
      }
    }
  }
}

function cbtheme_preprocess_mimemail_message(&$vars)
{
  // переменные для шаблона письма
  $vars['logo']   = $GLOBALS['base_url'] . '/sites/all/themes/cbtheme/images/logo/logo_mail.png';
  $site_name  = (theme_get_setting('toggle_name') ? filter_xss_admin(variable_get('site_name', 'Drupal')) : '');
  $vars['site_name'] = $site_name;
  // подпись на языке письма
  $vars['sign']   = empty($vars['message']['params']['context']['sign']) ? t('Postal robot') . ' ' . t($site_name) : $vars['message']['params']['context']['sign'];
  // notice - текст сообщения о том, что письмо сформировано автоматически
  $vars['notice'] = !isset($vars['message']['params']['context']['auto']) ? t('This message was generated automatically and does not require a response') : $vars['message'] ['params']['context']['auto'];
}


/**
 * Button theme function.
 * @see theme_button().
 */
function cbtheme_button_icon($vars)
{
  $element = $vars['element'];
  $element['#attributes']['type'] = 'submit';
  element_set_attributes($element, array('id', 'name', 'value'));

  $element['#attributes']['class'][] = 'form-' . $element['#button_type'];
  if (!empty($element['#attributes']['disabled'])) {
    $element['#attributes']['class'][] = 'form-button-disabled';
  }

  if (empty($element['#text'])) {
    $element['#text'] = $element['#value'];
  }

  return '<button' . drupal_attributes($element['#attributes']) . '>' . $element['#text'] . '</button>';
}
