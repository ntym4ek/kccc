<?php

/**
 * @file
 * Bootstrap sub-theme.
 *
 * Place your custom PHP code in this file.
 */

/**
 * Implements hook_theme().
 */
function ya_theme()
{
  return array(
    // карточка контакта
    'contact_card' => array(
      'variables' => array('contact' => null, 'collapse' => null, 'options' => null),
      'template' => 'contact-card',
      'path' => drupal_get_path('theme', 'ya') . '/templates/user',
    ),
    // карточка роутера
    'router_card' => array(
      'variables' => array('item' => null, 'options' => null),
      'template' => 'router-card',
      'path' => drupal_get_path('theme', 'ya') . '/templates/system',
    ),
  );
}


/**
 * Implements hook_pre_render().
 */
function ya_pre_render($element)
{
  // добавить для элементов обёртку div, чтобы можно было темизировать с помощью css
  if (in_array($element['#type'], ['select', 'textfield'])) {
    $element['#field_prefix'] = '<div class="form-input-wrapper">';
    $element['#field_suffix'] = '</div>';
  }

  return $element;
}
