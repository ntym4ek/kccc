<?php

/**
 * @file
 * Views support of ImageWebP.
 */

/**
 * Implements hook_views_data_alter().
 */
function imagewebp_views_data_alter(&$data) {
    foreach (field_info_fields() as $field) {
    if ($field['type'] == 'image' && $field['module'] == 'image') {
      if (array_key_exists('field_data_' . $field['field_name'], $data)) {
        $data['field_data_' . $field['field_name']][$field['field_name']]['field']['handler'] = 'imagewebp_views_handler_image_field';
      }
    }
  }
}
