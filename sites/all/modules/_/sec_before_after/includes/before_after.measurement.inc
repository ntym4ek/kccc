<?php


function before_after_measurement_form($form, &$form_state, $sid, $mid = '')
{
    if (!$season = field_collection_item_load($sid)) return;

    if (is_numeric($mid)) {
        $measurement_wrapper = entity_metadata_wrapper('field_collection_item', $mid);
        if (!$measurement_wrapper->value()) $mid = '';
    }

    // определить, первый ли это Замер
    $is_first_measure = false;
    $query = db_select('field_data_field_f_s_measurements', 'm');
    $query->condition('m.entity_id', $sid);
    $query->innerJoin('field_data_field_f_s_m_date', 'd', 'd.entity_id = m.field_f_s_measurements_value');
    $query->fields('m', array('field_f_s_measurements_value'));
    $query->fields('d', array('field_f_s_m_date_value'));
    $query->orderBy('field_f_s_m_date_value');
    $result_m = $query->execute()->fetchAll();
    if (!count($result_m) || $result_m[0]->field_f_s_measurements_value == $mid) {
        $is_first_measure = true;
    }
    $form_state['is_first_measure'] = $is_first_measure;


    $fid = $season->hostEntityId();

    $form_state['fid'] = $fid;
    $form_state['sid'] = $sid;

    // список фаз культуры
    $season_wrapper = entity_metadata_wrapper('field_collection_item', $season);
    $culture_wrapper = $season_wrapper->field_f_s_culture;
    $options_p = [];
    // если Пары, то не заполнять фазы
    if (!in_array($culture_wrapper->nid->value(), [8842, 8843])) {
        $periods = $culture_wrapper->field_growth_stages_set->field_mc_periods->value();
        if ($periods) {
            foreach ($periods as $period) {
                $options_p[$period->tid] = $period->name;
            }
        }
    }

    // если нажата кнопка Удалить ВО, удалить ВО, картинку и убрать из массива $form_state['hobjects']
    if (!empty($form_state['triggering_element']) && strpos($form_state['triggering_element']['#name'], 'del-ho-') !== false) {
        $fsid = str_replace('del-ho-', '', $form_state['triggering_element']['#name']);
        if ($hobject = field_collection_item_load($fsid)) {
            $ho_fid = $hobject->field_f_s_m_c_foto['und'][0]['value'];
            $hobject->delete();
            if ($old_file = file_load($ho_fid)) {
                file_usage_delete($old_file, 'before_after');
                file_delete($old_file);
            }
        }
        unset($form_state['hobjects'][$fsid]);
    }

    // список ВО, заданных в сезоне
    if ($season_wrapper->field_f_s_hobjects->count()) {
        foreach ($season_wrapper->field_f_s_hobjects->getIterator() as $ho_wrapper) {
            if ($ho_wrapper->value() && $ho_wrapper->field_f_s_h_hobject->value()) {
                $options_ho[$ho_wrapper->field_f_s_h_hobject->nid->value()] = $ho_wrapper->field_f_s_h_hobject->title->value();
            }
        }
    }
    // список ВО, которые останутся для вновь добавляемого ВО
    $options_ho_free = $options_ho;

    // установить недостающие наименования по nid
    if (!empty($form_state['hobjects'])) {
        foreach ($form_state['hobjects'] as $key => $hobject) {
            if (empty($hobject['name'])) {
                $form_state['hobjects'][$key]['name'] = $options_ho[$hobject['nid']];
            }
        }
    }


    // список Состояние культуры
    $options_c = [];
    // если Пары, то не заполнять Состояние
    if (!in_array($culture_wrapper->nid->value(), [8842, 8843])) {
        $options_c = array(
            'Хорошее' => 'Хорошее',
            'Среднее' => 'Среднее',
            'Плохое' => 'Плохое',
        );
    }

    $date = $condition = $phase = $comment = '';
    $photo_f = $photo_c = 0;

    // если редактирование и ещё не нажата кнопка, то задать переменные по умолчанию
    if ($mid && empty($form_state['triggering_element'])) {
        $form_state['mid'] = $mid;

        $date = date('d.m.Y', $measurement_wrapper->field_f_s_m_date->value());
        $condition = $measurement_wrapper->field_f_s_m_condition_mc->value();
        if ($measurement_wrapper->field_f_s_m_phase_mc->value()) {
            $phase = $measurement_wrapper->field_f_s_m_phase_mc->tid->value();
        }
        if ($measurement_wrapper->field_f_s_m_foto_field->value()) {
            $photo_f = $measurement_wrapper->field_f_s_m_foto_field->value();
            $form_state['photo_f'] = $photo_f;
        }
        if ($measurement_wrapper->field_f_s_m_foto_culture->value()) {
            $photo_c = $measurement_wrapper->field_f_s_m_foto_culture->value();
            $form_state['photo_c'] = $photo_c;
        }
        $comment = $measurement_wrapper->field_f_s_m_comment->value();

        // при открытии формы загрузить массив ВО в form_state
        if ($measurement_wrapper->field_f_s_m_condition_ho->count()) {
            foreach ($measurement_wrapper->field_f_s_m_condition_ho->getIterator() as $ho_wrapper) {
                if ($ho_wrapper->value() && $ho_wrapper->field_f_s_m_c_hobject->value()) {
                    $ho_nid = $ho_wrapper->field_f_s_m_c_hobject->nid->value();

                    $form_state['hobjects'][$ho_wrapper->item_id->value()] = array(
                        'nid' => $ho_nid,
                        'name' => $ho_wrapper->field_f_s_m_c_hobject->title->value(),
                        'percent' => $ho_wrapper->field_f_s_m_c_percent->value() ? $ho_wrapper->field_f_s_m_c_percent->value() : '0',
                        'photo' => $ho_wrapper->field_f_s_m_c_foto->value() ? $ho_wrapper->field_f_s_m_c_foto->value() : 0,
                    );
                    // исключаем Сорняк из списка доступных для добавления
                    unset($options_ho_free[$ho_nid]);
                }
            }
        }
    }

    // если нажата Добавить объект - добавить пустой ВО в массив
    if (!empty($options_ho_free) && !empty($form_state['triggering_element']) && $form_state['triggering_element']['#name'] == 'add-ho') {
        $key = 'n' . rand(1, 1000);
        $form_state['hobjects'][$key] = array(
            'nid' => 0,
            'name' => '',
            'percent' => '',
            'photo' => 0,
        );
    }

    // СОЗДАНИЕ ФОРМЫ
    $form = array(
        '#attributes' => array('class' => array('measurement-form'))
    );

    // обработка
    $form['fs1'] = array('#type' => 'fieldset', '#title' => 'Укажите дату и параметры замера');
    $form['fs1']['date'] = array(
        '#type' => 'textfield',
        '#title' => t('Date'),
        '#default_value' => $date,
        '#attributes' => array('class' => array('datepicker')),
        '#prefix' => '<div class="row">',
        '#wrapper_attributes' => array('class' => array('col-sm-4')),
    );
    if ($options_c) {
        $form['fs1']['condition'] = array(
            '#type' => 'select',
            '#title' => t('Condition'),
            '#options' => $options_c,
            '#chosen' => true,
            '#size' => '5',  // при наличии size в поле по умолчанию выводится надпись "Выберите значение"
            '#default_value' => $condition,
            '#wrapper_attributes' => array('class' => array('col-sm-4')),
        );
    }
    if ($options_p) {
        $form['fs1']['phase'] = array(
            '#type' => 'select',
            '#title' => 'Фаза развития культуры',
            '#options' => $options_p,
            '#chosen' => true,
            '#size' => '5',  // при наличии size в поле по умолчанию выводится надпись "Выберите значение"
            '#default_value' => $phase,
            '#wrapper_attributes' => array('class' => array('col-sm-4')),
        );
    }
    $form['fs1']['comment'] = array(
        '#type' => 'textarea',
        '#title' => 'Комментарий',
        '#default_value' => $comment,
        '#rows' => '3',
        '#wrapper_attributes' => array('class' => array('col-sm-12')),
        '#suffix' => '</div>',
    );

    $form['fs2'] = array('#type' => 'fieldset', '#title' => 'Добавьте фото<i>, поля в целом и экземпляра культуры вблизи</i>');
    $form['fs2']['file1'] = array(
        '#type' => 'container',
        '#attributes' => array('class' => array('col-md-6')),
        '#prefix' => '<div class="row">',
    );
    $form['fs2']['file1']['photo_f'] = array(
        '#type' => 'managed_file',
        '#title' => 'Фото поля',
        '#description' => 'Выберите файл с расширением jpg, jpeg, png',
        '#upload_location' => "public://images/before_after/field_$fid/",
        '#upload_validators' => array(
            'file_validate_is_image' => array(),
            'file_validate_extensions' => array('png jpg jpeg'),
            'file_validate_image_resolution' => array('2560x2048', '400x400'),
        ),
        '#default_value' => $photo_f,
        '#element_validate' => array('file_managed_file_validate', '_unset_status_message'),   // подавление статусных сообщений после стандартного валидатора file_managed_file_validate
    );
    // если Пары, то не заполнять Фото культуры
    if (!in_array($culture_wrapper->nid->value(), [8842, 8843])) {
        $form['fs2']['file2'] = array(
            '#type' => 'container',
            '#attributes' => array('class' => array('col-md-6')),
            '#suffix' => '</div>',
        );
        $form['fs2']['file2']['photo_c'] = array(
            '#type' => 'managed_file',
            '#title' => 'Фото культуры',
            '#description' => 'Выберите файл с расширением jpg, jpeg, png',
            '#upload_location' => "public://images/before_after/field_$fid/",
            '#upload_validators' => array(
                'file_validate_is_image' => array(),
                'file_validate_extensions' => array('png jpg jpeg'),
                'file_validate_image_resolution' => array('2560x2048', '400x400'),
            ),
            '#default_value' => $photo_c,
            '#element_validate' => array('file_managed_file_validate', '_unset_status_message'),   // подавление статусных сообщений после стандартного валидатора file_managed_file_validate
        );
    }

    // состояние вредных объектов
    $form['fs3'] = array('#type' => 'fieldset', '#title' => 'Задайте состояние вредных объектов');
    // вывести уже добавленные
    if (!empty($form_state['hobjects'])) {
        $form['fs3']['header'] = array(
            '#markup' => '<div class="add-title row">'
                . '<div class="col-sm-5">Вредный объект</div>'
                . ($is_first_measure ? '' : '<div class="col-sm-2">% гибели</div>')
                . ($is_first_measure ? '<div class="col-sm-6">Фото</div>' : '<div class="col-sm-4">Фото</div>')
                . '<div class="col-sm-1"></div>'
                . '</div>',
        );

        $counter = -1;
        foreach ($form_state['hobjects'] as $fsid => $hobject) {
            $counter = ($fsid > $counter) ? $fsid : $counter;

            if ($hobject['nid']) {
                $form['fs3']['ho_name-' . $fsid] = array(
                    '#type' => 'textfield',
                    '#default_value' => $hobject['name'],
                    '#disabled' => true,
                    '#prefix' => '<div class="add-ho-item row">',
                    '#wrapper_attributes' => array('class' => array('col-sm-5')),
                );
                $form['fs3']['hobject-' . $fsid] = array(
                    '#type' => 'hidden',
                    '#value' => $hobject['nid'],
                );
            } else {
                $form['fs3']['hobject-' . $fsid] = array(
                    '#type' => 'select',
                    '#options' => $options_ho,
                    '#chosen' => true,
                    '#prefix' => '<div class="add-ho-item row">',
                    '#wrapper_attributes' => array('class' => array('col-sm-5')),
                );
            }
            if (!$is_first_measure) {
                $form['fs3']['percent-' . $fsid] = array(
                    '#type' => 'textfield',
                    '#default_value' => $hobject['percent'],
                    '#disabled' => $hobject['percent'] ? true : false,
                    '#wrapper_attributes' => array('class' => array('col-sm-2')),
                );
            }

            // фото
            if ($hobject['nid']) {
                $photo = $hobject['photo'] ? file_load($hobject['photo']) : '';
                $photo_txt = $photo ? ('<a href="' . file_create_url($photo->uri) . '" class="fancybox">Просмотреть</a>') : 'Нет фото';

                $form['fs3']['image_ho_' . $fsid] = array(
                    '#markup' => '<div class="' . ($is_first_measure ? 'col-sm-6' : 'col-sm-4') . ' form-item form-type-file"><p class="form-control-static">' . $photo_txt . '</p></div>'
                );
                $form['fs3']['photo_ho-' . $fsid] = array(
                    '#type' => 'hidden',
                    '#value' => $hobject['photo'],
                );
            } else {
                $form['fs3']['file'] = array(
                    '#type' => 'container',
                    '#attributes' => $is_first_measure ? ['class' => array('col-sm-6')] : ['class' => array('col-sm-4')],
                );
                $form['fs3']['file']['photo_ho-' . $fsid] = array(
                    '#type' => 'managed_file',
                    '#description' => 'Выберите файл с расширением jpg, jpeg, png',
                    '#upload_location' => "public://images/before_after/field_$fid/",
                    '#upload_validators' => array(
                        'file_validate_is_image' => array(),
                        'file_validate_extensions' => array('png jpg jpeg'),
                        'file_validate_image_resolution' => array('2560x2048', '400x400'),
                    ),
                    '#element_validate' => array('file_managed_file_validate', '_unset_status_message'),   // подавление статусных сообщений после стандартного валидатора file_managed_file_validate
                );
            }

            $form['fs3']['del-' . $fsid] = array(
                '#type' => 'button',
                '#name' => 'del-ho-' . $fsid,
                '#value' => '×',
                '#ajax' => array('callback' => 'before_after_measurement_form_ajax_handler'),
                '#suffix' => '</div>',
                '#attributes' => array('class' => array('button-icon')),
                '#wrapper_attributes' => array('class' => array('col-sm-1')),
                '#limit_validation_errors' => array(),
            );

            // удаляем из выбора уже добавленные ВО
            unset($options_ho[$hobject['nid']]);
        }
    }

    $form['fs3']['add-ho'] = array(
        '#type' => 'button',
        '#name' => 'add-ho',
        '#value' => 'добавить объект',
        '#prefix' => '<div class="add-ho-button">',
        '#suffix' => '</div>',
        '#ajax' => array('callback' => 'before_after_measurement_form_ajax_handler'),
        '#attributes' => array('class' => array('button-action')),
        '#limit_validation_errors' => array(),
    );

    $form['fs4'] = ['#type' => 'container', '#attributes' => ['class' => array('form-actions')]];
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

function before_after_measurement_form_validate(&$form, &$form_state)
{
  if (strpos($form_state['triggering_element']['#name'], 'del-ho-') !== 0) {

    if (empty($form_state['values']['date'])) { form_set_error('date', 'Задайте дату замера.'); return false; }
    if (isset($form_state['values']['condition'])
      && !$form_state['values']['condition']) {
      form_set_error('condition', 'Задайте состояние культуры.');
      return false;
    }
    if (isset($form_state['values']['phase'])
      && !$form_state['values']['phase']) {
      form_set_error('phase', 'Задайте фазу развития культуры.');
      return false;
    }
    if (empty($form_state['values']['comment'])) { form_set_error('comment', 'Напишите короткий комментарий к замеру.'); return false; }
    if (empty($form_state['values']['photo_f'])) { form_set_error('photo_f', 'Задайте фото поля.'); return false; }
    if (isset($form_state['values']['photo_c'])
      && !$form_state['values']['photo_c']) {
      form_set_error('photo_c', 'Задайте фото культуры.');
      return false;
    }

    // при нажатии на кнопки сохранить введенные ВО в массив
    $form_state['hobjects'] = array();
    foreach ($form_state['values'] as $key => $value) {
      if (strpos($key, 'hobject-') !== false) {
        $id = str_replace('hobject-', '', $key);

        if (!$form_state['values']['hobject-' . $id]) { form_set_error('hobject-' . $id, 'Нужно задать наименование ВО.'); }
        if (isset($form_state['values']['percent-' . $id])
          && ($form_state['values']['percent-' . $id] == '' || intval($form_state['values']['percent-' . $id]) > 100)) {
          form_set_error('percent-' . $id, 'Нужно задать % гибели ВО.');
        }

        if ($value) {
          $query = db_select('node', 'n');
          $query->condition('n.nid', $value);
          $query->fields('n', array('title'));
          $title = $query->execute()->fetchField();

          $form_state['hobjects'][$id] = array(
            'nid'       => $value,
            'name'       => $title,
            'percent'  => isset($form_state['values']['percent-' . $id]) ? $form_state['values']['percent-' . $id] : '',
            'photo'     => $form_state['values']['photo_ho-' . $id],
          );
        }
        unset($form_state['values']['hobject-' . $id]); unset($form_state['values']['percent-' . $id]); unset($form_state['values']['photo_ho-' . $id]);
        unset($form_state['input']['hobject-' . $id]); unset($form_state['input']['percent-' . $id]); unset($form_state['input']['photo_ho-' . $id]);
      }
    }
  }
}

function before_after_measurement_form_submit(&$form, &$form_state)
{
    $fid = $form_state['fid'];

    // создать или загрузить Замер
    if (empty($form_state['mid'])) {
        $measurement = entity_create('field_collection_item', array('field_name' => 'field_f_s_measurements'));
        $measurement_wrapper = entity_metadata_wrapper('field_collection_item', $measurement);
        $season = field_collection_item_load($form_state['sid']);
        $measurement->setHostEntity('field_collection_item', $season);
        $op = 'create';
    } else {
        $measurement_wrapper = entity_metadata_wrapper('field_collection_item', $form_state['mid']);
        $measurement = $measurement_wrapper->value();
        $op = 'update';
    }

    $date_elements = explode(".", $form_state['values']['date']);
    $date = mktime(0, 0, 0, $date_elements[1], $date_elements[0], $date_elements[2]);

    $measurement_wrapper->field_f_s_m_date->set($date);
    if (isset($form_state['values']['condition'])) $measurement_wrapper->field_f_s_m_condition_mc->set($form_state['values']['condition']);
    if (!empty($form_state['values']['condition'])) $measurement_wrapper->field_f_s_m_phase_mc->set($form_state['values']['phase']);
    $measurement_wrapper->field_f_s_m_comment->set(trim($form_state['values']['comment']));

    // работа с изображением
    $old_photo_f = empty($form_state['photo_f']) ? 0 : $form_state['photo_f'];
    if ($old_photo_f != $form_state['values']['photo_f']) {
        // удалить старое изображение
            // сначала очистить поле, без этого картинка не меняется на новую
        $measurement_wrapper->field_f_s_m_foto_field->set(null);
        $measurement_wrapper->save();
            // удалить сам файл
        if ($old_photo_f && $old_file = file_load($form_state['photo_f'])) {
            file_usage_delete($old_file, 'before_after');
            file_delete($old_file);
        }
        // сохранить новый файл
        if ($form_state['values']['photo_f']) {
            $file = file_load($form_state['values']['photo_f']);
            $file->status = FILE_STATUS_PERMANENT;
            $filename = 'field_' . $file->timestamp . '.' . pathinfo($file->filename, PATHINFO_EXTENSION);
            $file->filename = $filename;
            $file = file_move($file, "public://images/before_after/field_$fid/" . $filename, FILE_EXISTS_REPLACE);
            file_save($file);
            file_usage_add($file, 'before_after', 'field_collection_item', 0);
            $form_state['photo_f'] = $file->fid;
            $measurement_wrapper->field_f_s_m_foto_field->set($form_state['photo_f']);
        }
    }
    $old_photo_c = empty($form_state['photo_c']) ? 0 : $form_state['photo_c'];
    if (isset($form_state['values']['photo_c']) && $old_photo_c != $form_state['values']['photo_c']) {
        // удалить старое изображение
            // сначала очистить поле, без этого картинка не меняется на новую
        $measurement_wrapper->field_f_s_m_foto_culture->set(null);
        $measurement_wrapper->save();
            // удалить сам файл
        if ($old_photo_c && $old_file = file_load($form_state['photo_c'])) {
            file_usage_delete($old_file, 'before_after');
            file_delete($old_file);
        }
        // сохранить новый файл
        if ($form_state['values']['photo_c']) {
            $file = file_load($form_state['values']['photo_c']);
            $file->status = FILE_STATUS_PERMANENT;
            $filename = 'culture_' . $file->timestamp . '.' . pathinfo($file->filename, PATHINFO_EXTENSION);
            $file->filename = $filename;
            $file = file_move($file, "public://images/before_after/field_$fid/" . $filename, FILE_EXISTS_REPLACE);
            file_save($file);
            file_usage_add($file, 'before_after', 'field_collection_item', 0);
            $form_state['photo_c'] = $file->fid;
            $measurement_wrapper->field_f_s_m_foto_culture->set($form_state['photo_c']);
        }
    }

    // задать  ВО
    foreach($measurement_wrapper->field_f_s_m_condition_ho as $key => $ho_wrapper) {
        $cid = $ho_wrapper->item_id->value();
        if (isset($form_state['hobjects'][$cid])) unset($form_state['hobjects'][$cid]);
    }

    // добавить новые
    if (!empty($form_state['hobjects'])) {
        foreach ($form_state['hobjects'] as $ho_item) {
            $hobject = entity_create('field_collection_item', array('field_name' => 'field_f_s_m_condition_ho'));
            $hobject->setHostEntity('field_collection_item', $measurement);

            $hobject_wrapper = entity_metadata_wrapper('field_collection_item', $hobject);
            $hobject_wrapper->field_f_s_m_c_hobject->set($ho_item['nid']);
            $hobject_wrapper->field_f_s_m_c_percent->set($ho_item['percent']);

            if ($ho_item['photo']) {
                $file = file_load($ho_item['photo']);
                $file->status = FILE_STATUS_PERMANENT;
                $filename = 'hobject_' . $file->timestamp . '.' . pathinfo($file->filename, PATHINFO_EXTENSION);
                $file->filename = $filename;
                $file = file_move($file, "public://images/before_after/field_$fid/" . $filename, FILE_EXISTS_REPLACE);
                file_save($file);
                file_usage_add($file, 'before_after', 'before_after', 0);
                $hobject_wrapper->field_f_s_m_c_foto->set($ho_item['photo']);
            }

            $hobject_wrapper->save();
        }
    }

    $measurement_wrapper->save();
    $form_state['mid'] = $measurement_wrapper->item_id->value();

    // обработать события на сохранение Замера
    // использовалось в Bubble, todo создание Уведомления
    $data = array('op' => $op, 'sid' => $measurement->hostEntityId());
    drupal_alter('before_after_measurement_save', $data);

    $form_state['redirect'] = 'season/' . $form_state['sid'] . '/processing/list';
}

function before_after_measurement_form_ajax_handler(&$form, &$form_state) {
    $commands[] = ajax_command_remove('#messages-wrap');
    $commands[] = ajax_command_before('.measurement-form', '<div id="messages-wrap">' . theme('status_messages') . '</div>');
    $commands[] = ajax_command_replace('.measurement-form', drupal_render($form));

    return array('#type' => 'ajax', '#commands' => $commands);
}
