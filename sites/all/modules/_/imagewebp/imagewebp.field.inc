<?php
/**
 * @file
 * Implement webpsupport for image field.
 */

/**
 * Implements hook_field_formatter_info_alter().
 */
function imagewebp_field_formatter_info_alter(array &$info) {
  // Add a setting to a formatter.
  $info['image']['settings'] += array(
    'webp_support' => FALSE,
  );
}

/**
 * Implements hook_field_formatter_settings_form().
 */
function imagewebp_field_formatter_settings_form_alter(&$settings_form, $context) {
  if ($context['instance']['display'][$context['view_mode']]['type'] == 'image') {
    $display = $context['instance']['display'][$context['view_mode']];
    $images = _imagewebp_get_affected_images($context['form']['#bundle'], $context['field']['field_name']);
    $settings = $display['settings'];
    $settings_form['webp_support'] = array(
      '#type' => 'checkbox',
      '#title' => t('Webp support'),
      '#default_value' => $settings['webp_support'],
    );
    $settings_form['webp_create'] = array(
      '#type' => 'checkbox',
      '#title' => t('Create webp images for all images of this field'),
      '#default_value' => 0,
      '#description' => t('If checked it will create %num webp images when form will be submitted. It can cause issues if you have many images.', array('%num' => $images)),
    );
  }
}

/**
 * Implements hook_field_formatter_settings_summary_alter().
 */
function imagewebp_field_formatter_settings_summary_alter(&$summary, $context) {
  // Append a message to the summary when image has
  // webpsupport set to TRUE for the current view mode.
  if ($context['instance']['display'][$context['view_mode']]['type'] == 'image') {

    $display = $context['instance']['display'][$context['view_mode']];
    $settings = $display['settings'];
    if (!empty($summary)) {
      $summary .= '<br />';
    }

    if (isset($settings['webp_support']) && $settings['webp_support']) {
      $summary .= t('Webp support enabled.');
    }
    if (isset($settings['webp_create']) && $settings['webp_create']) {
      $summary .= ' ' . t('Webp images will be created on submit.');
    }
  }
}
