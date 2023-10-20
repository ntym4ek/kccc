<?php

function kccc_preprocess_page(&$vars)
{
  // -- Главная
  if (drupal_is_front_page()) {

    // не выводить заголовок
    drupal_set_title('');

    drupal_add_html_head([
      '#tag' => 'meta',
      '#attributes' => [
        'name' => 'description',
        'content' => $vars['site_slogan'],
      ],
    ], 'description');
  }

  // -- Форма поиска в шапке
  if ($_GET['q'] != 'poisk') {
//    $vars['search_form'] = ext_views_get_view_exposed_form('search');
    $vars['search_form'] = drupal_get_form('ext_form_search_form');
  }

  // -- Форма подписки в футере
  $vars['subscribe_form'] = drupal_get_form('ext_form_subscribe_form');
}


/**
 * Implements hook_theme().
 */
function kccc_theme()
{
  return [
    'card_product_v' => [
      'variables' => [],
      'template' => 'templates/card/card-product-v',
    ],
    'card_product_h' => [
      'variables' => [],
      'template' => 'templates/card/card-product-h',
    ],
    'card_contact' => [
      'variables' => [],
      'template' => 'templates/card/card-contact',
    ],
    'card_reglament' => [
      'variables' => [],
      'template' => 'templates/card/card-reglament',
    ],
  ];
}

/**
 * Implements theme_file_link().
 */
function kccc_file_link($vars)
{
  $file = $vars['file'];

  $file_extension = drupal_strtoupper(pathinfo($file->filename, PATHINFO_EXTENSION));
//  $file_description = !empty($file->description) ? $file->description : str_replace('.' .$file_extension, '', $file->filename);
  $icon_directory = $vars['icon_directory'];

  $url = file_create_url($file->uri);

  // Human-readable names, for use as text-alternatives to icons.
  $mime_name = array(
    'application/msword' => t('Microsoft Office document icon'),
    'application/vnd.ms-excel' => t('Office spreadsheet icon'),
    'application/vnd.ms-powerpoint' => t('Office presentation icon'),
    'application/pdf' => t('PDF icon'),
    'video/quicktime' => t('Movie icon'),
    'audio/mpeg' => t('Audio icon'),
    'audio/wav' => t('Audio icon'),
    'image/jpeg' => t('Image icon'),
    'image/png' => t('Image icon'),
    'image/gif' => t('Image icon'),
    'application/zip' => t('Package icon'),
    'text/html' => t('HTML icon'),
    'text/plain' => t('Plain text icon'),
    'application/octet-stream' => t('Binary Data'),
  );

  $mimetype = file_get_mimetype($file->uri);
  $icon = theme('file_icon', array(
    'file' => $file,
    'icon_directory' => $icon_directory,
    'alt' => !empty($mime_name[$mimetype]) ? $mime_name[$mimetype] : t('File'),
  ));

  // Set options as per anchor format described at
  // http://microformats.org/wiki/file-format-examples
  $options = array(
    'attributes' => array(
      'type' => $file->filemime . '; length=' . $file->filesize,
      'target' => '_blank',
    ),
  );

  // Use the description as the link text if available.
  if (empty($file->description)) {
    $link_text = $file->filename;
  }
  else {
    $link_text = $file->description;
    $options['attributes']['title'] = check_plain($file->filename);
  }

  $output  = '<div class="file">';
  $output .=  '<div class="file-img"><i class="icon icon-120"></i></div>';
  $output .=  '<div class="file-info">';
  $output .=      l($link_text, $url, $options);
  $output .=      '<span>' . $icon . ' ' . $file_extension . ' - ' . format_size($file->filesize) . '</span>';
  $output .=  '</div>';

  // добавить кнопку на скачивание
  if (!empty($file->display)) {
    $output .= '<div class="file-download"><a href="' . $url . '" class="btn btn-brand" download>Скачать</a></div>';
  }
  $output .= '</div>';

  return $output;
}

/**
 * Returns HTML for a webform managed file element.
 *
 * See #2495821 and #2497909. The core theme_file_managed_file creates a
 * wrapper around the element with the element's id, thereby creating 2 elements
 * with the same id.
 *
 * @param array $variables
 *   An associative array containing:
 *   - element: A render element representing the file.
 *
 * @return string
 *   The HTML.
 */
function kccc_webform_managed_file($variables)
{
  $element = $variables['element'];

  $attributes = array();

  // For webform use, do not add the id to the wrapper.
  // @code
  // if (isset($element['#id'])) {
  //   $attributes['id'] = $element['#id'];
  // }
  // @endcode
  if (!empty($element['#attributes']['class'])) {
    $attributes['class'] = (array) $element['#attributes']['class'];
  }
  $attributes['class'][] = 'form-managed-file';

  // This wrapper is required to apply JS behaviors and CSS styling.
  $output = '';
  $output .= '<div' . drupal_attributes($attributes) . '>';

  $output .= '<div class="file-widget-data">';
  if ($element['fid']['#value'] != 0) {
    $element['filename']['#markup'] = '<div class="form-group">' . $element['filename']['#markup'] . ' <span class="file-size badge">' . format_size($element['#file']->filesize) . '</span></div>';
  }
  else {
    $element['upload']['#prefix'] = '<div class="input-group"><div class="input-group-btn"><div class="btn btn-brand btn-file">+';
    $element['upload']['#suffix'] = '</div></div>';
    $element['upload_button']['#prefix'] = '<input class="form-text" type="text" readonly=""><div class="input-group-btn">';
    $element['upload_button']['#suffix'] = '</div></div>';
  }
  drupal_add_js('
    (function($) {
      Drupal.behaviors.bootstrapImages = {
        attach: function (context) {
              $(".btn-file :file", context).once().on("change", function() {
                var txt  = "";
                var input = $(this);
                var numFiles = input.get(0).files ? input.get(0).files.length : 1;
                if (numFiles > 1) {
                  txt = numFiles + " выбрано";
                } else {
                  txt = input.val().replace(/.*?([^\\\]+)$/, "$1");
                }
                input.closest(".input-group").children("input[type=text]:lt(1)").val(txt);
              });
        }
      };
    })(jQuery)
  ', 'inline');

  //  txt = input.val();

  $output .=    drupal_render_children($element);
  $output .= '</div>';


  $output .= '</div>';
  return $output;
}

function kccc_image_widget($variables)
{
  $element = $variables['element'];
  $output = '';
  $output .= '<div class="image-widget form-managed-file clearfix">';

  if (isset($element['preview'])) {
    $output .= '<div class="image-preview">';
    $output .= drupal_render($element['preview']);
    $output .= '</div>';
  }

  $output .= '<div class="image-widget-data">';
  if ($element['fid']['#value'] != 0) {
    $element['filename']['#markup'] = '<div class="form-group">' . $element['filename']['#markup'] . ' <span class="file-size badge">' . format_size($element['#file']->filesize) . '</span></div>';
  }
  else {
    $element['upload']['#prefix'] = '<div class="input-group"><span class="input-group-btn"><span class="btn btn-brand btn-file">+';
    $element['upload']['#suffix'] = '</span></span>';
    $element['upload_button']['#prefix'] = '<input class="form-text" type="text" readonly=""><span class="input-group-btn">';
    $element['upload_button']['#suffix'] = '</span></div>';
  }
  drupal_add_js('
    (function($) {
      Drupal.behaviors.bootstrapImages = {
        attach: function (context) {
              $(".btn-file :file", context).once().on("change", function() {
                var txt  = "";
                var input = $(this);
                var numFiles = input.get(0).files ? input.get(0).files.length : 1;
                if (numFiles > 1) {
                  txt = numFiles + " выбрано";
                } else {
                  txt = input.val().replace(/.*?([^\\\]+)$/, "$1");
                }
                input.closest(".input-group").children("input[type=text]:lt(1)").val(txt);
              });
        }
      };
    })(jQuery)
  ', 'inline');

  $output .= drupal_render_children($element);
  $output .= '</div>';
  $output .= '</div>';

  return $output;
}

/**
 * Process variables for menu-block-wrapper.tpl.php.
 *
 * @see menu-block-wrapper.tpl.php
 */
function kccc_preprocess_menu_block_wrapper(&$vars)
{
  // для контекстного меню под баннером добавить класс,
  // выключающий срабатывание бокового меню на свайп
  if ($vars["config"]["menu_name"] = 'menu-context') {
    $vars['classes_array'][] = 'main-menu-disabled';
  }
}

function kccc_preprocess_mimemail_message(&$vars)
{
  // переменные для шаблона письма
  $vars['logo']   = $GLOBALS['base_url'] . '/sites/all/themes/kccc/images/logo/logo_mail.png';
  $vars['site_name'] = '';
}

/**
 * Implements hook_menu_breadcrumb_alter()
 */
function kccc_menu_breadcrumb_alter(&$active_trail, $item)
{
  // Если Path Breadcrumbs для страницы не задан,
  // то рядом с иконкой добавляется слово "Главная", убрать.
  // Хук вызывается только на проблемных страницах.
  if (module_exists('path_breadcrumbs')) {
    $active_trail[0]["title"] = '';
  }
}
