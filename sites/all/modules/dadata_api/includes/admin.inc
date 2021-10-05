<?php

/**
 * @file
 * Admin pages of the DaData API module.
 */

/**
 * Provides a form to manage settings.
 *
 * @param array $form
 *   The form structure.
 *
 * @return array
 *   The form structure.
 */
function dadata_api_settings_form(array $form) {
  $form['dadata_api_key'] = array(
    '#type' => 'textfield',
    '#title' => t('API key'),
    '#default_value' => variable_get('dadata_api_key'),
    '#size' => 40,
    '#attributes' => array(
      'autocomplete' => 'off',
    ),
  );
  $form['dadata_api_secret'] = array(
    '#type' => 'textfield',
    '#title' => t('Secret'),
    '#description' => t('Secret key for cleaner API.'),
    '#default_value' => variable_get('dadata_api_secret'),
    '#size' => 40,
    '#attributes' => array(
      'autocomplete' => 'off',
    ),
  );
  $form['dadata_api_timeout'] = array(
    '#type' => 'textfield',
    '#title' => t('Timeout'),
    '#description' => t('Float representing the maximum number of seconds the API request may take.'),
    '#default_value' => variable_get('dadata_api_timeout', 30),
    '#element_validate' => array('dadata_api_element_validate_float_positive'),
    '#size' => 5,
    '#required' => TRUE,
  );
  return system_settings_form($form);
}

/**
 * Form element validation handler for float elements that must be positive.
 *
 * @param array $element
 *   The element structure.
 */
function dadata_api_element_validate_float_positive(array $element) {
  $value = $element['#value'];
  if ($value !== '' && (!is_numeric($value) || floatval($value) != $value || $value <= 0)) {
    form_error($element, t('%name must be a positive float.', array('%name' => $element['#title'])));
  }
}
