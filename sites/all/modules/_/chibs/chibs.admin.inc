<?php

/**
 * @file
 * Admin UI.
 */

/**
 * Settings.
 */
function chibs_settings($form, &$form_state) {
    /** - Ограничение доступа к страницам --------------------------------------------------------------------------- */
    $form['authorized_access'] = array(
        '#type' => 'fieldset',
        '#title' => 'Доступ к страницам',
        '#description' => 'Запретить доступ анонимам к указанным страницам.',
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
    );
    $form['authorized_access']['aa_list'] = array(
        '#type' => 'textarea',
        '#title' => 'Список ссылок',
        '#description' => 'По одной ссылке на строку, без открывающего слеша, пример: info/contacts',
        '#default_value' => variable_get('aa_list', ''),
        '#required' => TRUE,
    );

    /** - Инструктаж по ТБ ------------------------------------------------------------------------------------------ */
    $form['labor_protection'] = array(
        '#type' => 'fieldset',
        '#title' => 'Инструктаж по ТБ',
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
    );
    $form['labor_protection']['lp_notification_email'] = array(
        '#type' => 'textfield',
        '#title' => 'Email для доставки уведомления',
        '#default_value' => variable_get('lp_notification_email', ''),
        '#required' => TRUE,
    );
    $form['labor_protection']['lp_step1_header'] = array(
        '#type' => 'textfield',
        '#title' => 'Шаг 1. Заголовок (англ.)',
        '#default_value' => variable_get('lp_step1_header', ''),
        '#required' => TRUE,
    );
    $form['labor_protection']['lp_step1_text'] = array(
        '#type' => 'textarea',
        '#title' => 'Шаг 1. Текст (англ.)',
        '#default_value' => variable_get('lp_step1_text', ''),
        '#required' => TRUE,
    );
    $form['labor_protection']['lp_step2_header'] = array(
        '#type' => 'textfield',
        '#title' => 'Шаг 2. Заголовок (англ.)',
        '#default_value' => variable_get('lp_step2_header', ''),
        '#required' => TRUE,
    );
    $form['labor_protection']['lp_step2_text'] = array(
        '#type' => 'textarea',
        '#title' => 'Шаг 2. Текст (англ.)',
        '#default_value' => variable_get('lp_step2_text', ''),
    );
    $form['labor_protection']['lp_step3_header'] = array(
        '#type' => 'textfield',
        '#title' => 'Шаг 3. Заголовок (англ.)',
        '#default_value' => variable_get('lp_step3_header', ''),
        '#required' => TRUE,
    );
    $form['labor_protection']['lp_step3_text'] = array(
        '#type' => 'textarea',
        '#title' => 'Шаг 3. Текст (англ.)',
        '#default_value' => variable_get('lp_step3_text', ''),
    );
    $form['labor_protection']['lp_success_header'] = array(
        '#type' => 'textfield',
        '#title' => 'Успех. Заголовок (англ.)',
        '#default_value' => variable_get('lp_success_header', ''),
        '#required' => TRUE,
    );
    $form['labor_protection']['lp_success_text'] = array(
        '#type' => 'textarea',
        '#title' => 'Успех. Текст (англ.)',
        '#default_value' => variable_get('lp_success_text', ''),
        '#required' => TRUE,
    );
    $form['labor_protection']['lp_fail_header'] = array(
        '#type' => 'textfield',
        '#title' => 'Провал. Заголовок (англ.)',
        '#default_value' => variable_get('lp_fail_header', ''),
        '#required' => TRUE,
    );
    $form['labor_protection']['lp_fail_text'] = array(
        '#type' => 'textarea',
        '#title' => 'Провал. Текст (англ.)',
        '#default_value' => variable_get('lp_fail_text', ''),
        '#required' => TRUE,
    );

    return system_settings_form($form);
}
