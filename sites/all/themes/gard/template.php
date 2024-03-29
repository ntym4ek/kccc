<?php
/**
 * @file
 * The primary PHP file for this theme.
 */

/**
 * Implements hook_pre_render().
 */
function gard_pre_render($element)
{
    // добавить для form select обёртку div, чтобы можно было темизировать с помощью css
    if (in_array($element['#type'], array('select', 'textfield'))) {
        $element['#field_prefix'] = '<div class="form-input-wrapper">';
        $element['#field_suffix'] = '</div>';
    }

    return $element;
}

/**
 * Implements hook_theme().
 */
function gard_theme()
{
    return array(
        // карточка контакта
        'contact_card' => array(
            'variables' => array('contact' => null, 'collapse' => null, 'options' => null),
            'template' => 'contact-card',
            'path' => drupal_get_path('theme', 'gard') . '/templates/system',
        ),
        // карточка роутера
        'router_card' => array(
            'variables' => array('item' => null, 'options' => null),
            'template' => 'router-card',
            'path' => drupal_get_path('theme', 'gard') . '/templates/system',
        ),
        'tabular_form' => array(
            'render element' => 'form',
        ),
    );
}


/**
 * Реализация функции темизации tabular_form
 */
function theme_tabular_form($vars)
{
    $form = $vars['form'];
    $rows = array();

    foreach (element_children($form['data']) as $key) {
        foreach (element_children($form['data'][$key]) as $name) {
            $rows[$key][] = drupal_render($form['data'][$key][$name]);
        }
    }

    return theme('table', array(
        'header' => $form['header']['#value'],
        'rows' => $rows,
    ));
}


/**
 * Implements hook_theme_registry_alter().
 */
function gard_theme_registry_alter(&$theme_registry) {
  $theme_path = path_to_theme();

  // Checkboxes.
  if (isset($theme_registry['checkbox'])) {
    $theme_registry['checkbox']['type'] = 'theme';
    $theme_registry['checkbox']['theme path'] = $theme_path;
    $theme_registry['checkbox']['template'] = $theme_path. '/templates/field/field--type-checkbox';
    unset($theme_registry['checkbox']['function']);
  }

  // Radios.
//  if (isset($theme_registry['radio'])) {
//    $theme_registry['radio']['type'] = 'theme';
//    $theme_registry['radio']['theme path'] = $theme_path;
//    $theme_registry['radio']['template'] = $theme_path . '/templates/field/field--type-radio';
//    unset($theme_registry['radio']['function']);
//  }
}

