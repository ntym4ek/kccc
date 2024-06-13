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
function ext_mobile_app_settings_form($form, $form_state)
{
  $form['update'] = array(
    '#type' => 'fieldset',
    '#title' => 'Обновление приложения',
  );
  $form['update']['ext_mobile_update_version'] = array(
    '#type' => 'textfield',
    '#title' => 'Версия приложения',
    '#description' => 'Актуальная версия для функции обновления в приложении',
    '#default_value' => variable_get('ext_mobile_update_version', 'not set'),
  );
  $form['update']['ext_mobile_update_message'] = array(
    '#type' => 'textfield',
    '#title' => 'Сообщение',
    '#description' => 'Сообщение для всплывающего окна о наличии обновления (по умолчанию: Доступна для скачивания новая версия приложения.)',
    '#default_value' => variable_get('ext_mobile_update_message', ''),
  );
  $form['update']['ext_mobile_update_link_android'] = array(
    '#type' => 'textfield',
    '#title' => 'Ссылка на скачивание для Андроид',
    '#default_value' => variable_get('ext_mobile_update_link_android', ''),
  );
  $form['update']['ext_mobile_update_link_ios'] = array(
    '#type' => 'textfield',
    '#title' => 'Ссылка на скачивание для iOS',
    '#default_value' => variable_get('ext_mobile_update_link_ios', ''),
  );

  return system_settings_form($form);
}

/**
 * Form constructor for the update settings form.
 *
 * @see update_settings_validate()
 * @see update_settings_submit()
 * @ingroup forms
 */
function ext_mobile_app_news_settings_form($form, $form_state)
{
  $front_banners = variable_get('ema_front_banners', []);
  if (isset($form_state["triggering_element"]) && strpos($form_state["triggering_element"]["#name"], 'delete-') != -1) {
    $bann_fid = str_replace('delete-','', $form_state["triggering_element"]["#name"]);
    if ($file = file_load($bann_fid)) {
      file_usage_delete($file, 'ext_mobile_app');
      file_delete($file);
    }
    unset($front_banners[$bann_fid]);
    variable_set('ema_front_banners', $front_banners);
  }

  $form['ema_news'] = array(
    '#type' => 'fieldset',
    '#title' => 'Новостные баннеры',
    '#description' => '<p>Изображение размером xx на xx и ссылка для баннера</p>',
  );
  $form['ema_news']['table_i'] = [
    '#type' => 'container',
    '#prefix' => '<table><tr><th style="width: 200px;">Баннер</th><th>Ссылка</th><th>Полноэкранный баннер</th><th style="width: 100px;"></th></tr>',
    '#suffix' => '</table>',
  ];
  if ($front_banners) {
    foreach($front_banners as $key => $banner) {
      $bann_fid = $banner['bann_fid'];
      $file = file_load($bann_fid);
      $form['ema_news']['table_i']['preview-' . $bann_fid] = array(
        '#markup' => '<td><img src="' . image_style_url('thumbnail', $file->uri) . '" /></td>',
        '#prefix' => '<tr>'
      );
      $form['ema_news']['table_i']['url-' . $bann_fid] = array(
        '#markup' => '<td>' . $banner['type'] . ($banner['url']? ':'.$banner['url']:'') . '</td>',
      );
      if ($bann_full_fid = $banner['bann_full_fid']) {
        $file = file_load($bann_full_fid);
        $form['ema_news']['table_i']['preview-' . $bann_full_fid] = array(
          '#markup' => '<td><img src="' . image_style_url('thumbnail', $file->uri) . '" /></td>',
          '#prefix' => ''
        );
      } else {
        $form['ema_news']['table_i']['preview-full'] = [
          '#markup' => '<td></td>',
        ];
      }
      $form['ema_news']['table_i']['delete-' . $bann_fid] = array(
        '#type' => 'button',
        '#value' => 'удалить',
        '#name' => 'delete-' . $bann_fid,
        '#limit_validation_errors' => [],
        '#prefix' => '<td>',
        '#suffix' => '</td></tr>',
      );
    }
  } else {
    $form['ema_news']['table_i']['empty'] = [
      '#markup' => '<tr><td>Нет добавленных баннеров</td><td></td><td></td></tr>',
    ];
  }

  $form['ema_news']['table_a'] = [
    '#type' => 'container',
    '#prefix' => '<table><tr><th style="width: 30%;">Добавить баннер</th><th></th><th style="width: 30%;"></th><th style="width: 10%;"></th></tr>',
    '#suffix' => '</table>',
  ];
  $form['ema_news']['table_a']['image-wr'] = [
    '#type' => 'container',
    '#prefix' => '<tr><td>',
    '#suffix' => '</td>',
  ];
  $form['ema_news']['table_a']['image-wr']['image-add'] = array(
    '#title' => 'Изображение',
    '#type' => 'managed_file',
    '#description' => 'Выберите файл с расширением jpg, jpeg, png',
    '#upload_location' => "public://images/app/front/",
    '#upload_validators' => array(
      'file_validate_is_image' => array(),
      'file_validate_extensions' => array('jpg jpeg png'),
      'file_validate_image_resolution' => array('1200x1200', '400x400'),
    ),
  );
  $form['ema_news']['table_a']['url-add'] = array(
    '#title' => 'Адрес ссылки',
    '#type' => 'textfield',
    '#description' => 'Возможные варианты: 1. external:ссылка - ссылка на внешний ресурс; ' .
                                          '2. internal:ссылка - ссылка на страницу в приложении; ' .
                                          '3. banner - открывает полноэкранное изображение, добавленное в поле рядом;' .
                                          '4. empty - клик по баннеру не предполагает никаких действий',
    '#default_value' => '',
    '#prefix' => '<td>',
    '#suffix' => '</td>',
  );
  $form['ema_news']['table_a']['image-full-wr'] = [
    '#type' => 'container',
    '#prefix' => '<td>',
    '#suffix' => '</td>',
  ];
  $form['ema_news']['table_a']['image-full-wr']['image-full-add'] = array(
    '#title' => 'Полноэкранное изображение',
    '#type' => 'managed_file',
    '#description' => 'Выберите файл с расширением png',
    '#upload_location' => "public://images/app/front/",
    '#upload_validators' => array(
      'file_validate_is_image' => array(),
      'file_validate_extensions' => array('png'),
      'file_validate_image_resolution' => array('1200x1200', '400x400'),
    ),
  );
  $form['ema_news']['table_a']['add'] = array(
    '#type' => 'submit',
    '#value' => 'добавить',
    '#name' => 'add',
    '#prefix' => '<td>',
    '#suffix' => '</td></tr>',
  );


  $form = system_settings_form($form);
  $form['#validate'][] = 'ext_mobile_app_news_settings_form_validate';

  // We need to call our own submit callback first, not the one from
  // system_settings_form(), so that we can process and save the emails.
  unset($form['#submit']);

  return $form;
}

/**
 * Form validation handler for update_settings().
 */
function ext_mobile_app_news_settings_form_validate($form, &$form_state)
{
  if (empty($form_state['values']['image-add'])) {
    form_set_error('image-add', 'Изображение баннера обязательно.');
  }
  if (empty($form_state['values']['url-add'])) {
    form_set_error('image-add', 'Не указан тип ссылки.');
  } elseif (!in_array(explode(':', $form_state['values']['url-add'])[0], ['external', 'internal', 'banner', 'empty'])) {
    form_set_error('image-add', 'Не указан тип ссылки.');
  } elseif (explode(':', $form_state['values']['url-add'])[0] == 'banner' && empty($form_state['values']['image-full-add'])) {
    form_set_error('image-add', 'Полноэкранное зображение баннера для ссылки типа banner обязательно.');
  }
}

/**
 * Form submission handler for update_settings().
 */
function ext_mobile_app_news_settings_form_submit($form, $form_state)
{
  // own processing
  $banners = variable_get('ema_front_banners', []);
  if ($file = file_load($form_state['values']['image-add'])) {
    $file->status = FILE_STATUS_PERMANENT;
    $filename = 'banner_' . $file->timestamp . '.' . pathinfo($file->filename, PATHINFO_EXTENSION);
    $file->filename = $filename;
    if ($file = file_move($file, "public://images/app/front/" . $filename, FILE_EXISTS_REPLACE)) {
      file_save($file);
      file_usage_add($file, 'ext_mobile_app', 'banner', 0);
      $banners[$form_state['values']['image-add']] = [
        'fid' => $form_state['values']['image-add'],
        'url' => $form_state['values']['url-add']
      ];
    }
  }

  if (!empty($form_state['values']['image-full-add'])) {
    if ($file = file_load($form_state['values']['image-full-add'])) {
      $file->status = FILE_STATUS_PERMANENT;
      $filename = 'banner_full_' . $file->timestamp . '.' . pathinfo($file->filename, PATHINFO_EXTENSION);
      $file->filename = $filename;
      if ($file = file_move($file, "public://images/app/front/" . $filename, FILE_EXISTS_REPLACE)) {
        file_save($file);
        file_usage_add($file, 'ext_mobile_app', 'banner', 0);
      }
    }
  }
  $banners[$form_state['values']['image-add']] = [
    'bann_fid' => $form_state['values']['image-add'],
    'type' => trim(explode(':', $form_state['values']['url-add'])[0]),
    'url' => trim(explode(':', $form_state['values']['url-add'])[1] ?: ''),
    'bann_full_fid' => $form_state['values']['image-full-add'] ?: '',
  ];
  variable_set('ema_front_banners', $banners);

  // default processing
  system_settings_form_submit($form, $form_state);
}



function ext_mobile_app_actual_settings_form($form, $form_state)
{
  // настройки Актуального препарата
  $form['ema_actual_product'] = array(
    '#type' => 'fieldset',
    '#title' => 'Баннеры препаратов',
    '#description' => 'Заголовок, вводный текст и список препаратов для размещения в слайдере на главной странице мобильного приложения',
  );
  $form['ema_actual_product']['ema_ap_title'] = array(
    '#type' => 'textfield',
    '#title' => 'Заголовок раздела',
    '#default_value' => variable_get('ema_ap_title', ''),
  );
  $form['ema_actual_product']['ema_ap_intro'] = array(
    '#type' => 'textarea',
    '#title' => 'Промо текст',
    '#default_value' => variable_get('ema_ap_intro', ''),
    '#rows' => 2,
  );
  $entity_items = entity_load('node', false, ['type' => ['preparation']]);
  $entity_options = array();
  foreach ($entity_items as $foreign_entity_item) {
    $entity_options[$foreign_entity_item->nid] = $foreign_entity_item->title;
  }
  $form['ema_actual_product']['ema_ap_list'] = array(
    '#type' => 'select',
    '#title' => 'Список препаратов',
    '#options' => $entity_options,
    '#default_value' => variable_get('ema_ap_list', []),
    '#multiple' => true,
    '#chosen' => true,
  );

  $form = system_settings_form($form);

  // We need to call our own submit callback first, not the one from
  // system_settings_form(), so that we can process and save the emails.
  unset($form['#submit']);

  return $form;
}

function ext_mobile_app_actual_settings_form_submit($form, $form_state)
{
  system_settings_form_submit($form, $form_state);
}
