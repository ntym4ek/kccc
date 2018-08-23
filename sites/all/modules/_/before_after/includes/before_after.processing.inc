<?php

/**
 * callback ОБРАБОТОК и ЗАМЕРОВ для представителей
 */
function before_after_processing_callback($sid = 0, $type = 'processing', $op = 'list', $pid = 0)
{
    $season_wrapper = entity_metadata_wrapper('field_collection_item', $sid);
    $season = $season_wrapper->value();
    $fid = $season->hostEntityId();

    $output = '';
    if ($type == 'processing') {
        $field_wrapper = entity_metadata_wrapper('node', $fid);

        $year_time = $season_wrapper->field_f_s_season->value();
        $year = $season_wrapper->field_f_s_year->value();

        $output = array(
            'title1' => $field_wrapper->title->value(),
            'title2' => $field_wrapper->field_f_farm->value(),
            'title3' => 'Сезон ' . $year_time . ' ' . $year,
            'title4' => $season_wrapper->field_f_s_culture->title->value(),
        );
        $menu = array();

        switch ($op) {
            case 'list':
                // ссылки меню
                $menu[] = array('data' => l(t('Fields list'), "/fields/list"));
                $menu[] = array('data' => l(t('Seasons list'), "/field/$fid/season/list"));

                // обработки текущего поля
                $query = db_select('field_data_field_f_s_processings', 'p');
                $query->condition('p.entity_id', $sid);
                $query->fields('p', array('field_f_s_processings_value'));
                $result_p = $query->execute()->fetchAll();

                $procs = array();
                if ($result_p) {
                    foreach ($result_p as $key_p => $p_item) {
                        $pid = $p_item->field_f_s_processings_value;
                        $processing_wrapper = entity_metadata_wrapper('field_collection_item', $pid);
                        $date = $processing_wrapper->field_f_s_p_date->value();
                        $procs[$date . 'p' . $key_p]['proc'] = "p";
                        $procs[$date . 'p' . $key_p]['id'] = $pid;
                        $procs[$date . 'p' . $key_p]['link'] = "/season/$sid/processing/edit/$pid";
                        $procs[$date . 'p' . $key_p]['image'] = "";
                        $procs[$date . 'p' . $key_p]['title1'] = date('d.m.Y', $date) . '&nbsp;&nbsp;Обработка';
                        $procs[$date . 'p' . $key_p]['title2'] = $processing_wrapper->field_f_s_p_preparation->title->value();
                        $procs[$date . 'p' . $key_p]['link_edit'] = "/season/$sid/processing/edit/$pid";
                        $procs[$date . 'p' . $key_p]['link_delete'] = "/season/$sid/processing/del/$pid";
                    }
                }

                // замеры текущего поля
                $query = db_select('field_data_field_f_s_measurements', 'm');
                $query->condition('m.entity_id', $sid);
                $query->fields('m', array('field_f_s_measurements_value'));
                $result_m = $query->execute()->fetchAll();

                if ($result_m) {
                    foreach ($result_m as $key_m => $m_item) {
                        $mid = $m_item->field_f_s_measurements_value;
                        $measurement_wrapper = entity_metadata_wrapper('field_collection_item', $mid);
                        $date = $measurement_wrapper->field_f_s_m_date->value();
                        $procs[$date . 'm' . $key_m]['proc'] = "m";
                        $procs[$date . 'm' . $key_m]['id'] = $mid;
                        $procs[$date . 'm' . $key_m]['link'] = "/season/$sid/measurement/edit/$mid";
                        $procs[$date . 'm' . $key_m]['image'] = "";
                        $procs[$date . 'm' . $key_m]['title1'] = date('d.m.Y', $date) . '&nbsp;&nbsp;Замер';
                        $procs[$date . 'm' . $key_m]['link_edit'] = "/season/$sid/measurement/edit/$mid";
                        $procs[$date . 'm' . $key_m]['link_delete'] = "/season/$sid/measurement/del/$mid";
                        if ($measurement_wrapper->field_f_s_m_phase_mc->value())
                            $procs[$date . 'm' . $key_m]['title2'] = $measurement_wrapper->field_f_s_m_phase_mc->name->value();
                    }
                }

                $menu[] = array('data' => l(t('Add measurement'), "/season/$sid/measurement/add"));
                $menu[] = array(
                    'data' => l(t('Add processing'), "/season/$sid/processing/add"),
                    'class' => array($result_m  ? '' : 'disabled'),
                );

                if ($procs) {
                    ksort($procs);
                    $output['items'] = $procs;
                    $menu[] = array(
                        'data' => l(t('View result'), "/before-after/$sid", ['attributes' => ['target' => '_blank']])
                    );
                }
                $output['menu'] = theme('item_list', array('items' => $menu, 'attributes' => array('class' => array('nav', 'navbar-nav'))));
                $output = theme('before_after_user', array('content' => $output));
                break;
            case 'edit':
                drupal_set_title('Редактировать Обработку');
                $output = drupal_get_form('before_after_processing_form', $sid, $pid);
                break;
            case 'add':
                drupal_set_title('Добавить новую Обработку');
                $output = drupal_get_form('before_after_processing_form', $sid);
                break;
            case 'del':
                drupal_set_title('Удаление');
                $output = drupal_get_form('before_after_del_form', 'processing', $sid, $pid);
                break;
        }
    }

    if ($type == 'measurement') {
        switch ($op) {
            case 'edit':
                drupal_set_title('Редактировать Замер');
                $output = drupal_get_form('before_after_measurement_form', $sid, $pid);
                break;
            case 'add':
                drupal_set_title('Добавить новый Замер');
                $output = drupal_get_form('before_after_measurement_form', $sid);
                break;
            case 'del':
                drupal_set_title('Удаление');
                $output = drupal_get_form('before_after_del_form', 'measurement', $sid, $pid);
                break;
        }
    }

    // подключить стили
    drupal_add_css(drupal_get_path('module', 'before_after') . '/css/before_after.css');

    return $output;
}

/**
 * форма добавления/редактирования ОБРАБОТКИ
 */
function before_after_processing_form($form, &$form_state, $sid, $pid = '') {
    if (!$season = field_collection_item_load($sid)) return;
    $fid = $season->hostEntityId();

    $form_state['fid'] = $fid;
    $form_state['sid'] = $sid;

    // список всех Препаратов
    $query = db_select('node', 'n');
    $query->condition('n.type', 'product_agro');
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
    $consumption = $consumption2 = $date = $humidity = $t_day = $t_night = $wind = $mechanism = '';
    $preparation = $preparation2 = $time = $precipitation = $acidity = '';
    $photo = 0;
    if ($pid && empty($form_state['triggering_element'])) {
        $form_state['pid'] = $pid;
        $processing_wrapper = entity_metadata_wrapper('field_collection_item', $pid);
        $preparation = $processing_wrapper->field_f_s_p_preparation->nid->value();
        if (isset($processing_wrapper->field_f_s_p_preparation2)
            && $processing_wrapper->field_f_s_p_preparation2->value()) $preparation2 = $processing_wrapper->field_f_s_p_preparation2->nid->value();
        $consumption = $processing_wrapper->field_f_s_p_consumption->value();
        if (isset($processing_wrapper->field_f_s_p_consumption2)
            && $processing_wrapper->field_f_s_p_consumption2->value()) $consumption2 = $processing_wrapper->field_f_s_p_consumption2->value();
        $date = date('d.m.Y', $processing_wrapper->field_f_s_p_date->value());
        $time = $processing_wrapper->field_f_s_p_time->value();
        $precipitation = $processing_wrapper->field_f_s_p_precipitation->value();
        $acidity = $processing_wrapper->field_f_s_p_acidity->tid->value();
        $humidity = $processing_wrapper->field_f_s_p_humidity->value();
        $wind = $processing_wrapper->field_f_s_p_wind_speed->value();
        $t_day = $processing_wrapper->field_f_s_p_temp_day->value();
        $t_night = $processing_wrapper->field_f_s_p_temp_night->value();
        $mechanism = $processing_wrapper->field_f_s_p_mechanism->value();
        if ($processing_wrapper->field_f_s_p_photo->value()) {
            $form_state['photo'] = $processing_wrapper->field_f_s_p_photo->value();
            $photo = $form_state['photo'];
        }
    }

    // СОЗДАНИЕ ФОРМЫ
    $form = array(
        '#attributes' => array('class' => array('processing-form')),
    );

    // средства обработки
    $form['fs1'] = array('#type' => 'fieldset', '#title' => 'Дата и средства обработки');
    $form['fs1']['preparation'] = array(
        '#type' => 'select',
        '#title' => t('Preparation'),
        '#options' => $options_p,
        '#chosen' => true,
        '#size' => '5',  // при наличии size в поле по умолчанию выводится надпись "Выберите значение"
        '#default_value' => $preparation,
        '#prefix' => '<div class="row">',
        '#wrapper_attributes' => array('class' => array('col-sm-8')),
    );
    $form['fs1']['consumption'] = array(
        '#type' => 'textfield',
        '#title' => 'Расход раб.жидкости, л/га',
        '#default_value' => $consumption,
        '#wrapper_attributes' => array('class' => array('col-sm-4')),
    );
    $form['fs1']['preparation2'] = array(
        '#type' => 'select',
        '#title' => 'Дополнительный препарат (для баковой смеси)',
        '#options' => $options_p,
        '#chosen' => true,
        '#size' => '5',  // при наличии size в поле по умолчанию выводится надпись "Выберите значение"
        '#default_value' => $preparation2,
        '#wrapper_attributes' => array('class' => array('col-sm-8')),
    );
    $form['fs1']['consumption2'] = array(
        '#type' => 'textfield',
        '#title' => 'Расход доп. раб. жидкости, л/га',
        '#default_value' => $consumption2,
        '#wrapper_attributes' => array('class' => array('col-sm-4')),
    );
    $form['fs1']['date'] = array(
        '#type' => 'textfield',
        '#title' => t('Date'),
        '#default_value' => $date,
        '#attributes' => array('class' => array('datepicker')),
        '#wrapper_attributes' => array('class' => array('col-sm-3')),
    );
    $form['fs1']['time'] = array(
        '#type' => 'select',
        '#title' => t('Time'),
        '#options' => $options_t,
        '#chosen' => true,
        '#size' => '5',  // при наличии size в поле по умолчанию выводится надпись "Выберите значение"
        '#default_value' => $time,
        '#wrapper_attributes' => array('class' => array('col-sm-3')),
    );
    $form['fs1']['mechanism'] = array(
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
        '#description' => 'Выберите файл с расширением jpg, jpeg, png',
        '#upload_location' => "public://images/before_after/field_$fid/",
        '#upload_validators' => array(
            'file_validate_is_image' => array(),
            'file_validate_extensions' => array('png jpg jpeg'),
            'file_validate_image_resolution' => array('1280x1024', '400x400'),
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

function before_after_processing_form_validate(&$form, &$form_state) {
    if ($form_state['triggering_element']['#name'] != 'delete') {
        if (empty($form_state['values']['preparation'])) { form_set_error('preparation', 'Задайте препарат.'); return false;  }
        if (empty($form_state['values']['consumption'] = preg_replace('![^\d\.,]*!', '', $form_state['values']['consumption']))) { form_set_error('consumption', 'Задайте норму расхода рабочей жидкости.'); return false; }
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
}

function before_after_processing_form_submit(&$form, &$form_state) {
    $fid = $form_state['fid'];

    // создать или загрузить Обработку
    if (empty($form_state['pid'])) {
        $processing = entity_create('field_collection_item', array('field_name' => 'field_f_s_processings'));
        $processing_wrapper = entity_metadata_wrapper('field_collection_item', $processing);
        $season = field_collection_item_load($form_state['sid']);
        $processing->setHostEntity('field_collection_item', $season);
    } else {
        $processing_wrapper = entity_metadata_wrapper('field_collection_item', $form_state['pid']);
        $processing = $processing_wrapper->value();
    }

    if ($form_state['triggering_element']['#name'] == 'delete') {
        $processing->delete();
        // удалить картинку
        if ($old_file = file_load($form_state['photo'])) {
            file_usage_delete($old_file, 'before_after');
            file_delete($old_file);
        }
    } else {
        $date_elements = explode(".", $form_state['values']['date']);
        $date = mktime(0, 0, 0, $date_elements[1], $date_elements[0], $date_elements[2]);

        $processing_wrapper->field_f_s_p_date->set($date);
        $processing_wrapper->field_f_s_p_time->set($form_state['values']['time']);
        $processing_wrapper->field_f_s_p_mechanism->set($form_state['values']['mechanism']);

        // работа с изображением
        $old_file_fid = empty($form_state['photo']) ? 0 : $form_state['photo'];
        if ($old_file_fid != $form_state['values']['photo']) {
            // удалить старое изображение
                // сначала очистить поле, без этого картинка не меняется на новую
            $processing_wrapper->field_f_s_p_photo->set(null);
            $processing_wrapper->save();
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
                $processing_wrapper->field_f_s_p_photo->set($form_state['photo']);
            }
        }

        $processing_wrapper->field_f_s_p_preparation->set($form_state['values']['preparation']);
        $processing_wrapper->field_f_s_p_consumption->set($form_state['values']['consumption']);

        if ($form_state['values']['preparation2']) {
            $processing_wrapper->field_f_s_p_preparation2->set($form_state['values']['preparation2']);
            $processing_wrapper->field_f_s_p_consumption2->set($form_state['values']['consumption2']);
        }

        $processing_wrapper->field_f_s_p_acidity->set($form_state['values']['acidity']);
        $processing_wrapper->field_f_s_p_precipitation->set($form_state['values']['precipitation']);

        $processing_wrapper->field_f_s_p_humidity->set($form_state['values']['humidity']);
        $processing_wrapper->field_f_s_p_wind_speed->set($form_state['values']['wind']);

        $processing_wrapper->field_f_s_p_temp_day->set($form_state['values']['t_day']);
        $processing_wrapper->field_f_s_p_temp_night->set($form_state['values']['t_night']);

        $processing_wrapper->save();

        $form_state['pid'] = $processing_wrapper->item_id->value();
    }

    $form_state['redirect'] = 'season/' . $form_state['sid'] . '/processing/list';
}

function before_after_processing_form_ajax_handler($form, &$form_state) {
    $commands[] = ajax_command_remove('#messages-wrap');
    $commands[] = ajax_command_before('.processing-form', '<div id="messages-wrap">' . theme('status_messages') . '</div>');
    $commands[] = ajax_command_replace('.processing-form', drupal_render($form));

    return array('#type' => 'ajax', '#commands' => $commands);
}

