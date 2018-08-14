<?php

/**
 * Settings form.
 */
function commerce_fast_ajax_atc_settings_form($form, &$form_state) {
    $form['commerce_fast_ajax_atc_use_confirmation_dialog'] = array(
        '#type' => 'checkbox',
        '#title' => t('Use confirmation dialog'),
        '#default_value' => variable_get('commerce_fast_ajax_atc_use_confirmation_dialog'),
    );

    return system_settings_form($form);
}