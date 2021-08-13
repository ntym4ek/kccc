<?php

/**
 * Generates the message type editing form.
 */
function message_type_category_form($form, &$form_state, $message_type_category, $op) {
  if ($op == 'clone') {
    $message_type_category->description .= ' (cloned)';
    // Save the original message type into form state so that the submit
    // handler can clone its field instances.
    $form_state['original_message_type'] = menu_get_object('entity_object', 4);
  }

  $form['description'] = array(
    '#title' => t('Description'),
    '#type' => 'textfield',
    '#default_value' => $message_type_category->description,
    '#description' => t('The human-readable description of this message type.'),
    '#required' => TRUE,
    '#weight' => -5,
  );

  // Machine-readable type name.
  $form['category'] = array(
    '#type' => 'machine_name',
    '#default_value' => isset($message_type_category->category) ? $message_type_category->category : '',
    '#disabled' => $message_type_category->hasStatus(ENTITY_IN_CODE),
    '#machine_name' => array(
      'exists' => 'message_type_category_load',
      'source' => array('description'),
    ),
    '#description' => t('A unique machine-readable name for this message type category. It must only contain lowercase letters, numbers, and underscores.'),
    '#weight' => -5,
  );

  // Categories do not use the language field, so ignore it.
  // There's no known category fields yet, so no need to attach them.

  // Save Button.
  $form['actions'] = array('#type' => 'actions');
  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save message type category'),
    '#weight' => 40,
  );

  // Delete button.
  if (!$message_type_category->hasStatus(ENTITY_IN_CODE)) {
    $form['actions']['delete'] = array(
      '#type' => 'submit',
      '#value' => t('Delete message type category'),
      '#weight' => 45,
      '#limit_validation_errors' => array(),
      '#submit' => array('message_type_category_form_submit_delete')
    );
  }
  return $form;
}

/**
 * Form API submit callback for the type form.
 */
function message_type_category_form_submit(&$form, &$form_state) {
  $message_type_category = entity_ui_form_submit_build_entity($form, $form_state);
  $message_type_category->save();

  $bundle = $message_type_category->category;
  $instance = field_info_instance('message_type', MESSAGE_FIELD_MESSAGE_TEXT, $bundle);
  if (!$instance) {
    $instance = array(
      'field_name' => MESSAGE_FIELD_MESSAGE_TEXT,
      'bundle' => $bundle,
      'entity_type' => 'message_type',
      'label' => t('Message text'),
      'description' => t('This is the text of all messages of this type.'),
      'required' => TRUE,
      'settings' => array(
        'text_processing' => 1,
      ),
    );
    field_create_instance($instance);
    drupal_set_message(t('Created message text field.'));
  }

  $form_state['redirect'] = 'admin/structure/message-types';
}

/**
 * Form API submit callback for the delete button.
 */
function message_type_category_form_submit_delete(&$form, &$form_state) {
  $form_state['redirect'] = 'admin/structure/message-types/manage/' . $form_state['message_type']->name . '/delete';
}
