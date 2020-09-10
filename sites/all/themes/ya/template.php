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
      'path' => drupal_get_path('theme', 'gard') . '/templates/user',
    ),
  );
}
