<?php

/**
 * callback ОБРАБОТОК и ЗАМЕРОВ для представителей
 */
function before_after_processing_callback($season_id = 0, $type = 'processing', $op = 'list', $pid = 0)
{
  $season_wr = entity_metadata_wrapper('field_collection_item', $season_id);
  $season_info = before_after_get_season_info($season_id);
  if ($season = $season_wr->value()) {

    $fid = $season->hostEntityId();

    $output = '';
    if ($type == 'processing') {
      $field_wrapper = entity_metadata_wrapper('node', $fid);

      $year_time = $season_wr->field_f_s_season->value();
      $year = $season_wr->field_f_s_year->value();

      $output = array(
        'title1' => $field_wrapper->label(),
        'title2' => $field_wrapper->field_f_farm->value(),
        'title3' => 'Сезон ' . $year_time . ' ' . $year,
        'title4' => $season_wr->field_f_s_culture->label() . ($season_wr->field_f_s_culture_note->value() ? ' (' . $season_wr->field_f_s_culture_note->value() . ')' : ''),
      );
      $menu = [];

      switch ($op) {
        case 'list':
          // ссылки меню
          $menu[] = array('data' => l(t('Fields list'), '/user/' . $GLOBALS['user']->uid .'/before-after'));
          $menu[] = array('data' => l(t('Add measurement'), "/season/$season_id/measurement/add"));
          $menu[] = array(
            'data' => l(t('Add processing'), "/season/$season_id/processing/add"),
            'class' => array($season_info['records'] ? '' : 'disabled'),
          );

          $items = [];
          foreach($season_info['records'] as $record) {
            if ($record['type'] == 'm') {
              $items[] = [
                'title1' => date('d.m.Y', $record['date']) . '&nbsp;&nbsp;Контроль',
                'title2' => $record['ho_phase'],
                'link' => "/season/$season_id/measurement/edit/" . $record['id'],
                'link_edit' => "/season/$season_id/measurement/edit/" . $record['id'],
                'link_del' => "/season/$season_id/measurement/del/" . $record['id'],
              ];
            } else {
              $items[] = [
                'title1' => date('d.m.Y', $record['date']) . '&nbsp;&nbsp;Обработка',
                'title2' => implode(', ', $record['preparations']),
                'link' => "/season/$season_id/processing/edit/" . $record['id'],
                'link_edit' => "/season/$season_id/processing/edit/" . $record['id'],
                'link_del' => "/season/$season_id/processing/del/" . $record['id'],
              ];
            }
          }

          foreach ($items as $key => $item) {
            $output['items'][$key] = theme('pvp_teaser', ['item' => $item]);
          }

          if (!$season_info['publication']['published']) {
            $reasons = '';
            foreach ($season_info['publication']['reasons'] as $reason) {
              $reasons .= '<li>' . $reason . '</li>';
            }
            $reasons = '<ul>' . $reasons . '</ul>';
            $output['message'] = '<div class="alert alert-block alert-danger ">Для публикации сезона не хватает следующих записей:' . $reasons . '</div>';
          } else {
            $menu[] = array(
              'data' => l(t('View result'), "/before-after/$season_id", ['attributes' => ['target' => '_blank']])
            );
          }

          $output['menu'] = theme('item_list', array('items' => $menu, 'attributes' => array('class' => array('nav', 'navbar-nav'))));
          $output = theme('before_after_user', array('content' => $output));
          break;
        case 'edit':
          drupal_set_title('Редактировать Обработку');
          $output = drupal_get_form('before_after_processing_form', $season_id, $pid);
          break;
        case 'add':
          drupal_set_title('Добавить новую Обработку');
          $output = drupal_get_form('before_after_processing_form', $season_id);
          break;
        case 'del':
          drupal_set_title('Удаление');
          $output = drupal_get_form('before_after_del_form', 'processing', $season_id, $pid);
          break;
      }
    }

    if ($type == 'measurement') {
      switch ($op) {
        case 'edit':
          drupal_set_title('Редактировать Замер');
          $output = drupal_get_form('before_after_measurement_form', $season_id, $pid);
          break;
        case 'add':
          drupal_set_title('Добавить новый Замер');
          $output = drupal_get_form('before_after_measurement_form', $season_id);
          break;
        case 'del':
          drupal_set_title('Удаление');
          $output = drupal_get_form('before_after_del_form', 'measurement', $season_id, $pid);
          break;
      }
    }
  } else {
    $output = 'Сезон не найден';
  }
  // подключить стили
  drupal_add_css(drupal_get_path('module', 'before_after') . '/css/before_after.css');

  return $output;
}

/**
 * форма добавления/редактирования ОБРАБОТКИ
 */
function before_after_processing_form($form, &$form_state, $sid, $pid = '')
{
  if (!$season = field_collection_item_load($sid)) return;

  // если нажата кнопка Удалить препарат, убрать из массива $form_state['preparations']
  if (!empty($form_state['triggering_element']) && strpos($form_state['triggering_element']['#name'], 'del-prep-') !== false) {
    $fsid = str_replace('del-prep-', '', $form_state['triggering_element']['#name']);
    unset($form_state['preparations'][$fsid]);
  }

  $fid = $season->hostEntityId();

  $form_state['fid'] = $fid;
  $form_state['sid'] = $sid;

  // список всех Препаратов
  $query = db_select('node', 'n');
  $query->condition('n.type', ['product_agro', 'product_fert'], 'IN');
  $query->condition('n.status', 1);
  $query->leftJoin('field_data_title_field', 'tf', 'n.nid = tf.entity_id');
  $query->condition('tf.language', $GLOBALS['language']->language);
  $query->fields('n', array('nid', 'title'));
  $query->orderby('tf.title_field_value', 'ASC');
  $result_p = $query->execute()->fetchAll();

  $options_p = [];
  foreach($result_p as $p_item) $options_p[$p_item->nid] = $p_item->title;

  // список Кислотность
  $query = db_select('taxonomy_term_data', 't');
  $query->condition('t.vid', '26');
  $query->fields('t', array('tid', 'name'));
  $result_a = $query->execute()->fetchAll();

  $options_a = array();
  foreach($result_a as $a_item) $options_a[$a_item->tid] = $a_item->name;

  // список Осадки
  $options_pr = array(
      'Без осадков'   => 'Без осадков',
      'Туман'         => 'Туман',
      'Дождь'         => 'Дождь',
      'Роса'          => 'Роса',
  );

  // список Время суток
  $options_t = array(
      '06:00-09:00'    => '06:00-09:00',
      '09:00-11:00'    => '09:00-11:00',
      '10:00-12:00'    => '10:00-12:00',
      'после 12:00'    => 'после 12:00',
  );


  // если редактирование, то задать переменные по умолчанию
  $date = $humidity = $t_day = $t_night = $wind = $mechanism = '';
  $time = $precipitation = $acidity = '';
  $photo = 0;
  if ($pid) {
    $processing_wr = entity_metadata_wrapper('field_collection_item', $pid);
    if (empty($form_state['pid'])) {
      $form_state['pid'] = $pid;
      $date = date('d.m.Y', $processing_wr->field_f_s_p_date->value());
      $time = $processing_wr->field_f_s_p_time->value();
      $precipitation = $processing_wr->field_f_s_p_precipitation->value();
      $acidity = $processing_wr->field_f_s_p_acidity->tid->value();
      $humidity = $processing_wr->field_f_s_p_humidity->value();
      $wind = $processing_wr->field_f_s_p_wind_speed->value();
      $t_day = $processing_wr->field_f_s_p_temp_day->value();
      $t_night = $processing_wr->field_f_s_p_temp_night->value();
      $mechanism = $processing_wr->field_f_s_p_mechanism->value();
      if ($processing_wr->field_f_s_p_photo->value()) {
        $form_state['photo'] = $processing_wr->field_f_s_p_photo->value();
        $photo = $form_state['photo'];
      }

      foreach($processing_wr->field_f_s_p_preparations->getIterator() as $prep_wr) {
        if ($prep_wr->value()) {
          $form_state['preparations'][$prep_wr->field_f_s_p_p_preparation->getIdentifier()] = array(
            'rate' => $prep_wr->field_f_s_p_p_rate->value(),
            'cons' => $prep_wr->field_f_s_p_p_consumption->value(),
          );
        }
      }

      // для обратной совместимости при редактировании добавить препараты из старых записей
      $preparation_wr = $processing_wr->field_f_s_p_preparation;
      if ($preparation_wr->value()) {
        $form_state['preparations'][$preparation_wr->getIdentifier()] = array(
          'rate' => '',
          'cons' => $processing_wr->field_f_s_p_consumption->value(),
        );
      }
      $preparation2_wr = $processing_wr->field_f_s_p_preparation2;
      if ($preparation2_wr->value()) {
        $form_state['preparations'][$preparation2_wr->getIdentifier()] = array(
          'rate' => '',
          'cons' => $processing_wr->field_f_s_p_consumption2->value(),
        );
      }
    }
  }

  // если нажата Добавить объект - добавить пустой Препарат в массив
  if (empty($form_state['preparations']) || (!empty($form_state['triggering_element']) && $form_state['triggering_element']['#name'] == 'add-prep')) {
    $form_state['preparations']['new'] = array(
      'rate' => '',
      'cons' => '',
    );
  }

  // СОЗДАНИЕ ФОРМЫ
  $form = array(
    '#attributes' => array('class' => array('processing-form')),
  );

  //
  $form['fs1'] = array('#type' => 'fieldset', '#title' => 'Препараты');

  // вывести уже добавленные
  if (!empty($form_state['preparations'])) {
    $form['fs1']['header'] = array(
      '#markup' => '<div class="add-title row">'
        . '<div class="col-sm-5">Препарат</div>'
        . '<div class="col-sm-3">Норма расхода, л(г)/га</div>'
        . '<div class="col-sm-3">Расход раб. жидкости, л/га</div>'
        . '<div class="col-sm-1"></div>'
        . '</div>',
      '#weight' => -1,
    );

    foreach ($form_state['preparations'] as $key => $value) {
      $form['fs1']['preparation-' . $key] = array(
        '#prefix' => '<div class="add-prep-item row">',
        '#suffix' => '</div>',
      );
      $form['fs1']['preparation-' . $key]['prep-' . $key] = array(
        '#type' => 'select',
        '#options' => $options_p,
        '#chosen' => true,
        '#size' => 5,
        '#default_value' => $key,
        '#wrapper_attributes' => array('class' => array('col-sm-5')),
      );
      $form['fs1']['preparation-' . $key]['rate-' . $key] = array(
        '#type' => 'textfield',
        '#attributes' => array('placeholder' => array('норма расхода')),
        '#default_value' => $value['rate'],
        '#wrapper_attributes' => array('class' => array('col-sm-3')),
      );
      $form['fs1']['preparation-' . $key]['cons-' . $key] = array(
        '#type' => 'textfield',
        '#attributes' => array('placeholder' => array('расход рабочей жидкости')),
        '#default_value' => $value['cons'],
        '#wrapper_attributes' => array('class' => array('col-sm-3')),
      );
      $form['fs1']['preparation-' . $key]['del-' . $key] = array(
        '#type' => 'button',
        '#name' => 'del-prep-' . $key,
        '#value' => '×',
        '#ajax' => array('callback' => 'before_after_processing_form_ajax_handler'),
        '#attributes' => array('class' => array('button-icon')),
        '#wrapper_attributes' => array('class' => array('col-sm-1')),
        '#limit_validation_errors' => [],
      );
    }
  }


  // кнопка добавления ВО
  $form['fs1']['add-prep'] = array(
    '#type' => 'button',
    '#name' => 'add-prep',
    '#value' => 'добавить препарат',
    '#prefix' => '<div class="add-prep-button">',
    '#suffix' => '</div>',
    '#ajax' => array('callback' => 'before_after_processing_form_ajax_handler'),
    '#attributes' => array('class' => array('button-action', 'btn-xs')),
    '#limit_validation_errors' => array(['fs1']),
  );

  //
  $form['fs5'] = array('#type' => 'fieldset', '#title' => 'Дата и средства обработки');

  $form['fs5']['date'] = array(
      '#type' => 'textfield',
      '#title' => t('Date'),
      '#default_value' => $date,
      '#attributes' => array('class' => array('datepicker')),
    '#prefix' => '<div class="row">',
      '#wrapper_attributes' => array('class' => array('col-sm-3')),
  );
  $form['fs5']['time'] = array(
      '#type' => 'select',
      '#title' => t('Time'),
      '#options' => $options_t,
      '#chosen' => true,
      '#size' => '5',  // при наличии size в поле по умолчанию выводится надпись "Выберите значение"
      '#default_value' => $time,
      '#wrapper_attributes' => array('class' => array('col-sm-3')),
  );
  $form['fs5']['mechanism'] = array(
      '#type' => 'textfield',
      '#title' => 'Механизм для внесения',
      '#default_value' => $mechanism,
      '#wrapper_attributes' => array('class' => array('col-sm-6')),
      '#suffix' => '</div>',
  );


  // параметры обработки
  $form['fs2'] = array('#type' => 'fieldset', '#title' => 'Параметры обработки');
  $form['fs2']['acidity'] = array(
      '#type' => 'select',
      '#title' => 'Кислотность',
      '#options' => $options_a,
      '#chosen' => true,
      '#size' => '5',  // при наличии size в поле по умолчанию выводится надпись "Выберите значение"
      '#default_value' => $acidity,
      '#prefix' => '<div class="row">',
      '#wrapper_attributes' => array('class' => array('col-sm-6 col-md-4')),
  );
  $form['fs2']['precipitation'] = array(
      '#type' => 'select',
      '#title' => 'Осадки',
      '#options' => $options_pr,
      '#chosen' => true,
      '#size' => '5',  // при наличии size в поле по умолчанию выводится надпись "Выберите значение"
      '#default_value' => $precipitation,
      '#wrapper_attributes' => array('class' => array('col-sm-6 col-md-4')),
  );
  $form['fs2']['humidity'] = array(
      '#type' => 'textfield',
      '#title' => 'Влажность, %',
      '#size' => '13',
      '#default_value' => $humidity,
      '#wrapper_attributes' => array('class' => array('col-sm-6 col-md-4')),
  );
  $form['fs2']['wind'] = array(
      '#type' => 'textfield',
      '#title' => 'S ветра, м/с',
      '#size' => '15',
      '#default_value' => $wind,
      '#wrapper_attributes' => array('class' => array('col-sm-6 col-md-4')),
  );
  $form['fs2']['t_day'] = array(
      '#type' => 'textfield',
      '#title' => 't днём, C',
      '#size' => '15',
      '#default_value' => $t_day,
      '#wrapper_attributes' => array('class' => array('col-sm-6 col-md-4')),
  );
  $form['fs2']['t_night'] = array(
      '#type' => 'textfield',
      '#title' => 't ночью, C',
      '#size' => '15',
      '#default_value' => $t_night,
      '#wrapper_attributes' => array('class' => array('col-sm-6 col-md-4')),
      '#suffix' => '</div>',
  );

  // фото
  $form['fs3'] = array('#type' => 'fieldset', '#title' => 'Фото<i> процесса обработки</i>');
  $form['fs3']['photo'] = array(
      '#type' => 'managed_file',
      '#description' => 'Выберите файл с расширением jpg, jpegg',
      '#upload_location' => "public://images/before_after/field_$fid/",
      '#upload_validators' => array(
          'file_validate_is_image' => array(),
          'file_validate_extensions' => array('jpg jpeg'),
          'file_validate_image_resolution' => array('1200x1200', '400x400'),
      ),
      '#default_value' => $photo,
      '#element_validate' => array('file_managed_file_validate', '_unset_status_message'),   // подавление статусных сообщений после стандартного валидатора file_managed_file_validate
  );

  $form['fs4'] = array('#type' => 'container', '#attributes' => array('class' => array('form-actions')));
  $form['fs4']['submit'] = array(
      '#type' => 'submit',
      '#value' => empty($sid) ? 'Добавить' : 'Сохранить',
      '#attributes' => array('class' => array('pull-right')),
  );
  $form['fs4']['cancel'] = array(
      '#markup' => '<a href="/season/' . $sid . '/processing/list" class="btn btn-link pull-right">Отменить</a>',
      '#attributes' => array('class' => array('btn-link', 'pull-right')),
  );

  $form['#attached']['library'][] = array('system', 'ui.datepicker');
  $form['#attached']['js'][] = drupal_get_path('module', 'before_after') . '/js/datepicker.js';

  return $form;
}

function before_after_processing_form_validate(&$form, &$form_state)
{
  $flag_e = false;
  foreach ($form_state['values'] as $key => $value) {
    if (strpos($key, 'prep-') !== false) {
      $fsid = str_replace('prep-', '', $key);
      if (is_numeric($form_state['values']['prep-' . $fsid])) {
        if (empty($form_state['values']['rate-' . $fsid])) {
          form_set_error('rate-' . $fsid, 'Задайте норму расхода');
          $flag_e = true;
        }
        if (empty($form_state['values']['cons-' . $fsid])) {
          form_set_error('cons-' . $fsid, 'Задайте расход рабочей жидкости');
          $flag_e = true;
        }

        if (!$flag_e) {
          $form_state['preparations'][$form_state['values']['prep-' . $fsid]] = [
            'rate' => $form_state['values']['rate-' . $fsid],
            'cons' => $form_state['values']['cons-' . $fsid],
          ];
        }
      }
    }
  }
  if ($flag_e) return false;

  if (empty($form_state['values']['date'])) { form_set_error('date', 'Задайте дату обработки.'); return false; }
  if (empty($form_state['values']['time'])) { form_set_error('time', 'Задайте время обработки.'); return false; }
  if (empty($form_state['values']['mechanism'])) { form_set_error('mechanism', 'Задайте механизм обработки.'); return false; }
  if (empty($form_state['values']['acidity'])) { form_set_error('acidity', 'Задайте кислотность почвы.'); return false; }
  if (empty($form_state['values']['precipitation'])) { form_set_error('precipitation', 'Задайте осадки.'); return false;  }
  if (empty($form_state['values']['humidity'] = preg_replace('![^\d\.,]*!', '', $form_state['values']['humidity']))) { form_set_error('humidity', 'Задайте влажность почвы.'); return false; }
  if (empty($form_state['values']['wind'] = preg_replace('![^\d\.,]*!', '', $form_state['values']['wind']))) { form_set_error('wind', 'Задайте скорость ветра.'); return false; }
  if (empty($form_state['values']['t_day'] = preg_replace('![^\d\.,]*!', '', $form_state['values']['t_day']))) { form_set_error('t_day', 'Задайте температуру.'); return false; }
  if (empty($form_state['values']['t_night'] = preg_replace('![^\d\.,]*!', '', $form_state['values']['t_night']))) { form_set_error('t_night', 'Задайте температуру.'); return false; }
  if (empty($form_state['values']['photo'])) { form_set_error('photo', 'Задайте фото процесса обработки.'); return false; }
}

function before_after_processing_form_submit(&$form, &$form_state)
{
  $fid = $form_state['fid'];

  // создать или загрузить Обработку
  if (empty($form_state['pid'])) {
    $processing = entity_create('field_collection_item', array('field_name' => 'field_f_s_processings'));
    $processing_wr = entity_metadata_wrapper('field_collection_item', $processing);
    $season = field_collection_item_load($form_state['sid']);
    $processing->setHostEntity('field_collection_item', $season);
  } else {
    $processing_wr = entity_metadata_wrapper('field_collection_item', $form_state['pid']);
    $processing = $processing_wr->value();
  }

  $date_elements = explode(".", $form_state['values']['date']);
  $date = mktime(0, 0, 0, $date_elements[1], $date_elements[0], $date_elements[2]);

  $processing_wr->field_f_s_p_date->set($date);
  $processing_wr->field_f_s_p_time->set($form_state['values']['time']);
  $processing_wr->field_f_s_p_mechanism->set($form_state['values']['mechanism']);

  // работа с изображением
  $old_file_fid = empty($form_state['photo']) ? 0 : $form_state['photo'];
  if ($old_file_fid != $form_state['values']['photo']) {
    // удалить старое изображение
    // сначала очистить поле, без этого картинка не меняется на новую
    $processing_wr->field_f_s_p_photo->set(null);
    $processing_wr->save();
    // удалить сам файл
    if ($old_file_fid && $old_file = file_load($form_state['photo'])) {
      file_usage_delete($old_file, 'before_after');
      file_delete($old_file);
    }
    // сохранить новый файл
    if ($form_state['values']['photo']) {
      $file = file_load($form_state['values']['photo']);
      $file->status = FILE_STATUS_PERMANENT;
      $filename = 'processing_' . $file->timestamp . '.' . pathinfo($file->filename, PATHINFO_EXTENSION);
      $file->filename = $filename;
      $file = file_move($file, "public://images/before_after/field_$fid/" . $filename, FILE_EXISTS_REPLACE);
      file_save($file);
      file_usage_add($file, 'before_after', 'before_after', 0);
      $form_state['photo'] = $file->fid;
      $processing_wr->field_f_s_p_photo->set($form_state['photo']);
    }
  }

  // препараты
  foreach($processing_wr->field_f_s_p_preparations->getIterator() as $prep_wr) {
    if ($prep_wr->value()) {
      $prep_wr->delete();
    }
  }
  $processing_wr->field_f_s_p_preparations->set(null);

  foreach($form_state['preparations'] as $key => $prep) {
    if (is_numeric($key)) {
      $preparation = entity_create('field_collection_item', array('field_name' => 'field_f_s_p_preparations'));
      $preparation->setHostEntity('field_collection_item', $processing);

      $preparation_wr = entity_metadata_wrapper('field_collection_item', $preparation);
      $preparation_wr->field_f_s_p_p_preparation = $key;
      $preparation_wr->field_f_s_p_p_rate = $prep['rate'];
      $preparation_wr->field_f_s_p_p_consumption = $prep['cons'];
      $preparation_wr->save();
    }
  }
  // обнуляем старые поля Препаратов
  $processing_wr->field_f_s_p_preparation->set(null);
  $processing_wr->field_f_s_p_consumption->set(null);
  $processing_wr->field_f_s_p_preparation2->set(null);
  $processing_wr->field_f_s_p_consumption2->set(null);

  $processing_wr->field_f_s_p_acidity->set($form_state['values']['acidity']);
  $processing_wr->field_f_s_p_precipitation->set($form_state['values']['precipitation']);
  $processing_wr->field_f_s_p_humidity->set($form_state['values']['humidity']);
  $processing_wr->field_f_s_p_wind_speed->set($form_state['values']['wind']);
  $processing_wr->field_f_s_p_temp_day->set($form_state['values']['t_day']);
  $processing_wr->field_f_s_p_temp_night->set($form_state['values']['t_night']);

  $processing_wr->save();

  $form_state['pid'] = $processing_wr->getIdentifier();

  $form_state['redirect'] = 'season/' . $form_state['sid'] . '/processing/list';
}

function before_after_processing_form_ajax_handler($form, &$form_state)
{
    $commands[] = ajax_command_remove('#messages-wrap');
    $commands[] = ajax_command_before('.processing-form', '<div id="messages-wrap">' . theme('status_messages') . '</div>');
    $commands[] = ajax_command_replace('.processing-form', drupal_render($form));

    return array('#type' => 'ajax', '#commands' => $commands);
}

