<?php
/**
 * @file
 * Generate configuration form and save settings.
 */

/**
 * Configuration form for slider slides.
 */
function bootstrap_slider_slide_configuration_form ($form, &$form_state) { 

	$form['mtm_settings'] = array(
    '#type' => 'fieldset',
    '#title' => t('Bootstrap Slider Settings'),
    '#collapsible' => FALSE,
    '#collapsed' => FALSE,
	);

  $slides = bootstrap_slider_get_slides();

  $form['mtm_settings']['images'] = array(
    '#type' => 'vertical_tabs',
    '#title' => t('Slider images'),
    '#weight' => 2,
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
    '#tree' => TRUE,
  );

  $i = 0;
  foreach ($slides as $image_data) {
  $form['mtm_settings']['images'][$i] = array(
    '#type' => 'fieldset',
    '#title' => t('Image !number: !title', array('!number' => $i + 1, '!title' => $image_data['image_title'])),
    '#weight' => $i,
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
    '#tree' => TRUE,
    // Add image config form to $form
    'image' => _bootstrap_slider_slide_form($image_data),
  );

  $i++;
  }

  $form['mtm_settings']['image_upload'] = array(
    '#type' => 'file',
    '#title' => t('Upload a new Image'),
    '#weight' => $i,
  );

  $form['#submit'][]   = 'bootstrap_slider_settings_submit';

  return system_settings_form($form);

}

/**
 * Save settings data.
 */
function bootstrap_slider_settings_submit($form, &$form_state) {
    $settings = array();

    // Update image field
    foreach ($form_state['input']['images'] as $image) {
        if (is_array($image)) {
            $image = $image['image'];

            if ($image['image_delete']) {
            // Delete slide file
            file_unmanaged_delete($image['image_path']);
            // Delete slide thumbnail file
            file_unmanaged_delete($image['image_thumb']);
            } else {
            // Update image
            $settings[] = $image;
            }
        }
    }
  
    // Check for a new uploaded file, and use that if available.
    if ($file = file_save_upload('image_upload')) {
        $file->status = FILE_STATUS_PERMANENT;
        if ($image = _bootstrap_slider_save_image($file)) {
        // Put new image into settings
        $settings[] = $image;
        }
    }

  // Save settings
  bootstrap_slider_set_slides($settings);
}

/**
 * Check if folder is available or create it.
 *
 * @param <string> $dir
 *    Folder to check
 */
function _bootstrap_slider_check_dir($dir) {
    // Normalize directory name
    $dir = file_stream_wrapper_uri_normalize($dir);

    // Create directory (if not exist)
    file_prepare_directory($dir,  FILE_CREATE_DIRECTORY);
}

/**
 * Save file uploaded by user and generate setting to save.
 *
 * @param <file> $file
 *    File uploaded from user
 *
 * @param <string> $slide_folder
 *    Folder where save image
 *
 * @param <string> $slide_thumb_folder
 *    Folder where save image thumbnail
 *
 * @return <array>
 *    Array with file data.
 *    FALSE on error.
 */
function _bootstrap_slider_save_image($file, $slide_folder = 'public://bootstrap-slider/', $slide_thumb_folder = 'public://bootstrap-slider/thumb/') {
    // Check directory and create it (if not exist)
    _bootstrap_slider_check_dir($slide_folder);
    _bootstrap_slider_check_dir($slide_thumb_folder);

    $parts = pathinfo($file->filename);
    $destination = $slide_folder . $parts['basename'];
    $setting = array();

    $file->status = FILE_STATUS_PERMANENT;
  
    // Copy temporary image into slide folder
    if ($img = file_copy($file, $destination, FILE_EXISTS_REPLACE)) {
        // Generate image thumb
        $image = image_load($destination);
        $small_img = image_scale($image, 300, 100);
        $image->source = $slide_thumb_folder . $parts['basename'];
        image_save($image);

        // Set image info
        $setting['image_path'] = $destination;
        $setting['image_thumb'] = $image->source;
        $setting['image_title'] = '';
        $setting['image_description'] = '';
        $setting['image_url'] = '';
        $setting['image_weight'] = 0;
        $setting['image_published'] = FALSE;

        return $setting;
    }
  
    return FALSE;
}

/**
 * Generate form to manage slide informations
 *
 * @param <array> $image_data
 *    Array with image data
 *
 * @return <array>
 *    Form to manage image informations
 */
function _bootstrap_slider_slide_form($image_data) {
    $img_form = array();

    // Image preview
    $img_form['image_preview'] = array(
        '#markup' => theme('image', array('path' => $image_data['image_thumb'])),
    );

    // Image path
    $img_form['image_path'] = array(
        '#type' => 'hidden',
        '#value' => $image_data['image_path'],
    );

    // Thumbnail path
    $img_form['image_thumb'] = array(
        '#type' => 'hidden',
        '#value' => $image_data['image_thumb'],
    );

    // Image title
    $img_form['image_title'] = array(
        '#type' => 'textfield',
        '#title' => t('Title'),
        '#default_value' => $image_data['image_title'],
    );

    // Image description
    $img_form['image_description'] = array(
        '#type' => 'textarea',
        '#title' => t('Description'),
        '#default_value' => $image_data['image_description'],
    );

    // Link url
    $img_form['image_url'] = array(
        '#type' => 'textfield',
        '#title' => t('Url'),
        '#default_value' => $image_data['image_url'],
    );

    // Image weight
    $img_form['image_weight'] = array(
        '#type' => 'weight',
        '#title' => t('Weight'),
        '#default_value' => $image_data['image_weight'],
    );

    // Image is published
    $img_form['image_published'] = array(
        '#type' => 'checkbox',
        '#title' => t('Published'),
        '#default_value' => $image_data['image_published'],
    );

    // Delete image
    $img_form['image_delete'] = array(
        '#type' => 'checkbox',
        '#title' => t('Delete image.'),
        '#default_value' => FALSE,
    );

    return $img_form;
}
