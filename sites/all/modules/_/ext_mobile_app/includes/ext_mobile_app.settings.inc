<?php

/**
 * @file
 * Code required only for the update status settings form.
 */

/**
 * Form constructor for the update settings form.
 *
 * @see update_settings_validate()
 * @see update_settings_submit()
 * @ingroup forms
 */
function ext_mobile_app_settings($form)
{
  $form['ema_actual_product'] = array(
    '#type' => 'fieldset',
    '#title' => 'Продуктовые баннеры',
    '#description' => 'Заголовок, вводный текст и список препаратов для размещения в слайдере на главной странице мобильного приложения',
  );
  $form['ema_actual_product']['ema_ap_title'] = array(
    '#type' => 'textfield',
    '#title' => t('Banner title'),
    '#default_value' => variable_get('ema_ap_title', ''),
  );
  $form['ema_actual_product']['ema_ap_intro'] = array(
    '#type' => 'textarea',
    '#title' => t('Intro text'),
    '#default_value' => variable_get('ema_ap_intro', ''),
    '#rows' => 2,
  );
  $entity_items = entity_load('node', false, ['type' => ['product_agro', 'product_fert']]);
  $entity_options = array();
  foreach ($entity_items as $foreign_entity_item) {
    $entity_options[$foreign_entity_item->nid] = $foreign_entity_item->title;
  }
  $form['ema_actual_product']['ema_ap_list'] = array(
    '#type' => 'select',
    '#title' => t('Products list'),
    '#options' => $entity_options,
    '#default_value' => variable_get('ema_ap_list', []),
    '#multiple' => true,
    '#chosen' => true,
  );

  $form = system_settings_form($form);
  $form['#validate'][] = 'ext_mobile_app_settings_validate';

  // We need to call our own submit callback first, not the one from
  // system_settings_form(), so that we can process and save the emails.
  unset($form['#submit']);

  return $form;
}

/**
 * Form validation handler for update_settings().
 */
function ext_mobile_app_settings_validate($form, &$form_state)
{

}

/**
 * Form submission handler for update_settings().
 */
function ext_mobile_app_settings_submit($form, $form_state)
{
  // own processing

  // default processing
  system_settings_form_submit($form, $form_state);
}
