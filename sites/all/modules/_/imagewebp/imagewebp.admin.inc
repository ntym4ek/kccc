<?php

/**
 * @file
 * ImageWebP admin functions.
 */


function imagewebp_admin_form() {
  $form['imagewebp_quality'] = array(
    '#type' => 'textfield',
    '#title' => t('ImageWebP quality'),
    '#field_suffix' => '%',
    '#required' => TRUE,
    '#description' => t('Determines the quality of the generated .webp images'),
    '#default_value' => variable_get('imagewebp_quality', 80),
    '#attributes' => array(
      'size' => 4,
    ),
  );
  $form['#submit'][] = 'imagewebp_admin_form_submit';

  return system_settings_form($form);
}

function imagewebp_admin_form_validate($form, &$form_state) {
  if (!is_numeric($form_state['values']['imagewebp_quality'])) {
    form_set_error('imagewebp_quality', t('Quality must be numeric.'));
  }
  else {
    if ($form_state['values']['imagewebp_quality'] <= 0 || $form_state['values']['imagewebp_quality'] > 100) {
      form_set_error('imagewebp_quality', t('Quality percentage must be between 0 and 100.'));
    }
  }
}

function imagewebp_admin_form_submit($form, &$form_state) {
  drupal_set_message(t('ImageWebP quality has been set to !percent%', array('!percent' => $form_state['values']['imagewebp_quality'])));
}

function imagewebp_setup_batch($form, &$form_state) {
  $fields = field_info_field_map();
  $image_fields = array_filter($fields, function($key) {
    return ($key['type'] == 'image');
  });
  $image_bundles = array();
  if (!empty($image_fields)) {
    foreach ($image_fields as $image_field => $data) {
      if (!empty($data['bundles'])) {
        foreach ($data['bundles'] as $type => $instances) {
          foreach ($instances as $instance) {
            $info = field_info_instance($type, $image_field, $instance);
            if (!empty($info)) {
              foreach ($info['display'] as $display) {
                if (array_key_exists('webp_support', $display['settings']) && $display['settings']['webp_support']) {
                  $image_bundles[$image_field][$instance] = $info['label'];
                }
              }
            }
          }
        }
      }
    }
  }
  if (!empty($image_bundles)) {
    foreach ($image_bundles as $field => $bundles) {
      foreach ($bundles as $bundle => $label) {
        $suffix = t('(in %bundle content type)', array('%bundle' => $bundle));
        $field_options[$field . '|' . $bundle] = $label . " " . $suffix;
      }
    }
    $form['imagewebp_image_fields'] = array(
      '#type' => 'checkboxes',
      '#title' => t("Which fields should be checked for generating WebP images?"),
      '#options' => $field_options,
      '#required' => TRUE,
    );
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Start process'),
    );
  }
  else {
    $form['no_image_field'] = array(
      '#type' => 'markup',
      '#markup' => t("There isn't any configured image field which uses Webp images."),
    );
  }

  return $form;
}

function imagewebp_setup_batch_submit($form, &$form_state) {
  $chosen_fields = array_filter($form_state['values']['imagewebp_image_fields'], function ($val, $key) {
    return $val !== 0;
  }, ARRAY_FILTER_USE_BOTH);

  $operations = array();
  foreach ($chosen_fields as $field) {
    $fields = explode('|', $field);
    $operations[] = array('imagewebp_generate_webp_images_batch_process', array($fields[0], $fields[1]));
  }

  $batch = array(
    'title' => 'Generate WebP images',
    'operations' => $operations,
    'finished' => 'imagewebp_generate_webp_images_batch_finished',
  );
  batch_set($batch);
}

function imagewebp_generate_webp_images_batch_process($field_name, $bundle, &$context) {
  if (!isset($context['sandbox']['progress'])) {
    $context['sandbox']['progress'] = 0;
    $context['sandbox']['max'] = _imagewebp_get_images($field_name, $bundle, TRUE)->fetchField();
  }
  if (!isset($context['results'])) {
    $context['results']['created'] = 0;
    $context['results']['failed'] = 0;
    $context['results']['skipped'] = 0;
    $context['results']['processed'] = 0;
  }
  $limit = 10;
  $images = _imagewebp_get_images($field_name, $bundle, FALSE, $limit, $context['sandbox']['progress'])->fetchCol();
  if (!empty($images)) {
    foreach ($images as $fid) {
      $file = file_load($fid);
      $context['message'] = t('Now processing %file', array('%file' => $file->filename));
      $webp_file_create = createwebpimage($file);
      if ($webp_file_create === TRUE) {
        $context['results']['created']++;
      }
      elseif ($webp_file_create === FALSE) {
        $context['results']['failed']++;
      }
      else {
        $context['results']['skipped']++;
      }
      $context['sandbox']['progress']++;
      $context['results']['processed']++;
    }
  }
  if ($context['sandbox']['progress'] < $context['sandbox']['max'] && !empty($images)) {
    $context['finished'] = $context['sandbox']['progress'] / $context['sandbox']['max'];
  }
  else {
    $context['finished'] = 1;
  }
}

function _imagewebp_get_images($field_name, $bundle, $only_count = FALSE, $limit = 0, $offset = 0) {
  $query = db_select('field_data_' . $field_name, 'fd');
  $column_name = $field_name . '_fid';
  $query->join('file_managed', 'file', 'file.fid = fd.' . $column_name);
  if ($only_count) {
    $query->addExpression('count(*)', 'summa');
  }
  else {
    $query->fields('file', array('fid'));
  }
  $query->condition('bundle', $bundle);
  $query->orderBy('file.fid');
  if ($limit > 0) {
    $query->range($offset, $limit);
  }
  return $query->execute();
}

function imagewebp_generate_webp_images_batch_finished($success, $results, $operations) {
  if ($success) {
    $report = t('Generating result: Processed %max records, %created image(s) created, %skipped image(s) skipped, %failed image(s) failed');
    $args = array(
      '%max' => $results['processed'],
      '%created' => isset($results['created']) ? $results['created'] : 0,
      '%skipped' => isset($results['skipped']) ? $results['skipped'] : 0,
      '%failed' => isset($results['failed']) ? $results['failed'] : 0,
    );
    drupal_set_message(t($report, $args));
  }
  else {
    drupal_set_message(t('Finished with an error.'), 'error');
  }
}
