<?php

/**
 * callback СЕЗОНА для представителей
 */
function before_after_season_callback($fid, $op = 'list', $sid = 0)
{
    $node_wrapper = entity_metadata_wrapper('node', $fid);

    // возвращаемый массив
    $output = array(
        'title1' => $node_wrapper->title->value(),
        'title2' => $node_wrapper->field_f_farm->value(),
    );
    $menu = array();

    switch ($op) {
        case 'list':
            // ссылки меню
            $menu[] = array('data' => l(t('Fields list'), "/fields/list"));
            $menu[] = array('data' => l(t('Add season'), "/field/$fid/season/add"));

            // сезоны текущего поля
            $query = db_select('field_data_field_f_sowing', 's');
            $query->condition('s.entity_id', $fid);
            $query->fields('s', array('field_f_sowing_value'));
            $result_sids = $query->execute()->fetchAll();

            if ($result_sids) {
                foreach ($result_sids as $s_item) {
                    $sid = $s_item->field_f_sowing_value;
                    $season_wrapper = entity_metadata_wrapper('field_collection_item', $sid);
                    $season = $season_wrapper->field_f_s_season->value();
                    $year = $season_wrapper->field_f_s_year->value();

                    $output['items'][$sid]['link']          = "/season/$sid/processing/list";
                    $output['items'][$sid]['image']         = "";
                    $output['items'][$sid]['title1']        = 'Сезон ' . $season . ' ' . $year;
                    $output['items'][$sid]['title2']        = $season_wrapper->field_f_s_culture->title->value() . ($season_wrapper->field_f_s_culture_note->value() ? ' (' . $season_wrapper->field_f_s_culture_note->value() . ')' : '');
                    $output['items'][$sid]['link_edit']     = "/field/$fid/season/edit/$sid";
                    $output['items'][$sid]['link_delete']   = "/field/$fid/season/del/$sid";
                }
            }
            $output['menu'] = theme('item_list', array('items' => $menu, 'attributes' => array('class' => array('nav', 'navbar-nav'))));
            $output = theme('before_after_user', array('content' => $output));
            break;
        case 'edit':
            drupal_set_title('Редактировать Сезон');
            $output = drupal_get_form('before_after_season_form', $fid, $sid);
            break;
        case 'add':
            drupal_set_title('Добавить новый Сезон');
            $output = drupal_get_form('before_after_season_form', $fid);
            break;
        case 'del':
            drupal_set_title('Удаление');
            $output = drupal_get_form('before_after_del_form', 'season', $fid, $sid);
            break;
    }

    // подключить стили
    drupal_add_css(drupal_get_path('module', 'before_after') . '/css/before_after.css');

    return $output;
}

/**
 * форма добавления/редактирования СЕЗОНА
 */
function before_after_season_form($form, &$form_state, $fid, $sid = 0)
{
    $form_state['fid'] = $fid;

    // если нажата кнопка Удалить ВО, удалить соответствующий элемент формы
    if (!empty($form_state['triggering_element']) && strpos($form_state['triggering_element']['#name'], 'del-ho-') !== false) {
        $id = str_replace('del-ho-', '', $form_state['triggering_element']['#name']);
        unset($form_state['values']['hobject-' . $id]); unset($form_state['values']['dominant-' . $id]); unset($form_state['values']['phase-' . $id]); unset($form_state['values']['del-' . $id]);
        unset($form_state['input']['hobject-' . $id]); unset($form_state['input']['dominant-' . $id]); unset($form_state['input']['phase-' . $id]); unset($form_state['input']['del-' . $id]);
    }

    // список всех Культур, для которых задан Набор этапов развития
    // только для те периоды, для которых есть термины (т.к. есть удалённые и прописанные в сезонах)
    $query = db_select('node', 'n');
    $query->condition('n.type', 'main_cultures');
    $query->innerJoin('field_data_field_growth_stages_set', 'fp', 'n.nid = fp.entity_id');
    $query->condition('fp.field_growth_stages_set_tid', 0, '!=');
    $query->fields('n', array('nid', 'title'));
    $query->distinct()->orderBy('title', 'asc');
    $result_c = $query->execute()->fetchAll();

    $options_c = array(
        '_none' => 'Не выбрано',
        8842 => 'Пар',
        8843 => 'Полупар',
    );
    foreach($result_c as $c_item) {
        $options_c[$c_item->nid] = $c_item->title;
    }

    // список всех ВО
    $query = db_select('node', 'n');
    $query->condition('n.type', array('disease', 'weed', 'pest'), 'IN');
    $query->fields('n', array('nid', 'title'));
    $result_ho = $query->execute()->fetchAll();

    $options_ho = array();
    foreach($result_ho as $ho_item) {
        $options_ho[$ho_item->nid] = $ho_item->title;
    }

    // ИНИЦИАЛИЗАЦИЯ ПЕРЕМЕННЫХ
    // задать переменные для заполненных элементов формы
    $culture    = $culture_note = '';
    $previous_culture = empty($form_state['values']['previous_culture'])  ? '' : $form_state['values']['previous_culture'];
    $sub_culture = empty($form_state['values']['subculture'])  ? '' : $form_state['values']['subculture'];
    $area       = empty($form_state['values']['area'])        ? '' : $form_state['values']['area'];
//    $seeding    = empty($form_state['values']['seeding'])     ? '' : $form_state['values']['seeding'];
    $season     = empty($form_state['values']['season'])      ? 'Весна' : $form_state['values']['season'];
    $year       = empty($form_state['values']['year'])        ? date('Y') : $form_state['values']['year'];

        // введенные вредные объекты
    if (!empty($form_state['values'])) {
        foreach ($form_state['values'] as $key => $value) {
            if (strpos($key, 'hobject-') !== false) {
                $id = str_replace('hobject-', '', $key);

                $hobjects[$id] = array(
                    'nid' => $form_state['values']['hobject-' . $id],
                    'phase' => $form_state['values']['phase-' . $id],
                    'dominant' => $form_state['values']['dominant-' . $id],
                );
            }
        }
    }
    // если редактирование, то задать переменные из редактируемого объекта
    if ($sid && empty($form_state['triggering_element'])) {
        $form_state['sid'] = $sid;
        $season_wrapper = entity_metadata_wrapper('field_collection_item', $sid);
        $culture            = $season_wrapper->field_f_s_culture->nid->value();
        $culture_note       = $season_wrapper->field_f_s_culture_note->value();
        $previous_culture   = $season_wrapper->field_f_s_previous_culture->value() ? $season_wrapper->field_f_s_previous_culture->nid->value() : 0;
        $sub_culture        = $season_wrapper->field_f_s_sub_culture->value() ? $season_wrapper->field_f_s_sub_culture->value() : '';
        $area               = $season_wrapper->field_f_s_area->value();
        $season             = $season_wrapper->field_f_s_season->value();
        $year               = $season_wrapper->field_f_s_year->value();

        // при открытии формы загрузить массив ВО в form_state
        $hobjects = array();
        if ($season_wrapper->field_f_s_hobjects->count()) {
            foreach ($season_wrapper->field_f_s_hobjects->getIterator() as $ho_wrapper) {
              if ($ho_wrapper->value() && $ho_wrapper->field_f_s_h_hobject->value()) {
                $ho_nid = $ho_wrapper->field_f_s_h_hobject->nid->value();

                $hobjects[$ho_wrapper->item_id->value()] = array(
                    'nid' => $ho_nid,
                    'dominant' => $ho_wrapper->field_f_s_h_dominant->value() ? $ho_wrapper->field_f_s_h_dominant->value() : 0,
                    'phase' => $ho_wrapper->field_f_s_h_phase->value(),
                );
              }
            }
        }
    }

  $options_s = array(
      'Весна' => 'Весна',
      'Осень' => 'Осень',
  );
  $options_y = array();
  for($i = date('Y'); $i <= 2030; $i++) {
    $options_y[$i] = $i;
  }

  // СОЗДАНИЕ ФОРМЫ
  $form = array(
      '#attributes' => array('class' => array('season-form')),
  );

  $form['fs0'] = array('#type' => 'fieldset', '#title' => 'Название сезона');
  $form['fs0']['season'] = array(
      '#type' => 'select',
      '#options' => $options_s,
      '#default_value' => $season,
      '#prefix' => '<div class="row"><div class="col-md-6">',
      '#suffix' => '</div>',
  );
  $form['fs0']['year'] = array(
      '#type' => 'select',
      '#options' => $options_y,
      '#default_value' => $year,
      '#prefix' => '<div class="col-md-6">',
      '#suffix' => '</div></div>',
  );


    // культура и субкультура
    $form['fs1'] = array('#type' => 'fieldset', '#title' => 'Укажите культуры<i>, засеваемые в этом сезоне</i>');
    $form['fs1']['culture'] = array(
        '#type' => 'select',
        '#title' => 'Основная',
        '#options' => $options_c,
        '#chosen' => true,
        '#default_value' => $culture,
        '#prefix' => '<div class="row"><div class="col-md-6">',
    );
  $form['fs1']['culture_note'] = array(
    '#type' => 'textfield',
    '#title' => 'Комментарий к основной культуре',
    '#default_value' => $culture_note,
    '#description' => 'Уточнения, напр.: мягкие сорта, на зерно, маточники, семенные посевы',
    '#prefix' => '',
    '#suffix' => '</div>',
  );
    $form['fs1']['previous_culture'] = array(
        '#type' => 'select',
        '#title' => 'Предшественник',
        '#options' => $options_c,
        '#chosen' => true,
        '#default_value' => $previous_culture,
        '#prefix' => '<div class="col-md-6">',
    );
    $form['fs1']['subculture'] = array(
        '#type' => 'textfield',
        '#title' => 'Подсев',
        '#default_value' => $sub_culture,
        '#suffix' => '</div></div>',
    );

    // площадь
    $form['fs2'] = array('#type' => 'fieldset', '#title' => 'Задайте параметры посева<i></i>');
    $form['fs2']['area'] = array(
        '#type' => 'textfield',
        '#title' => 'Площадь (га)',
        '#default_value' => $area,
        '#prefix' => '<div class="row"><div class="col-md-6">',
        '#suffix' => '</div></div>',
    );

//    // ввод нормы высева - пока не сохраняется
//    $form['fs2']['seeding'] = array(
//        '#type' => 'textfield',
//        '#title' => 'Норма высева (кг/га)',
//        '#default_value' => $seeding,
//        '#prefix' => '<div class="col-md-6">',
//        '#suffix' => '</div></div>',
//    );

    // вредные объекты
    $form['fs3'] = array('#type' => 'fieldset', '#title' => 'Добавьте вредные объекты<i>, с указанием фазы развития. </i>');
        // вывести уже добавленные
    if (!empty($hobjects)) {
        $form['fs3']['header'] = array(
            '#markup' => '<div class="add-title row">'
                . '<div class="col-sm-5">Вредный объект</div>'
                . '<div class="col-sm-4">Фаза развития</div>'
                . '<div class="col-sm-2"></div>'
                . '<div class="col-sm-1"></div>'
                . '</div>',
            '#weight' => -1,
        );

        $counter = -1;
        foreach ($hobjects as $key => $value) {
            $id = $key;
            $counter = ($id > $counter) ? $id : $counter;

            $form['fs3']['cnt-' . $id] = array(
                '#prefix' => '<div class="add-ho-item row">',
                '#suffix' => '</div>',
                '#weight' => $id,
            );
            $form['fs3']['cnt-' . $id]['hobject-' . $id] = array(
                '#type' => 'select',
                '#options' => $options_ho,
                '#chosen' => true,
                '#size' => 5,
                '#default_value' => $value['nid'],
                '#wrapper_attributes' => array('class' => array('col-sm-5')),
            );
            $form['fs3']['cnt-' . $id]['phase-' . $id] = array(
                '#type' => 'textfield',
                '#attributes' => array('placeholder' => array('фаза развития')),
                '#default_value' => $value['phase'],
                '#wrapper_attributes' => array('class' => array('col-sm-4')),
            );
            $form['fs3']['cnt-' . $id]['dominant-' . $id] = array(
                '#type' => 'checkbox',
                '#title' => 'Доминант',
                '#default_value' => $value['dominant'],
                '#wrapper_attributes' => array('class' => array('col-sm-2')),
            );
            $form['fs3']['cnt-' . $id]['del-' . $id] = array(
                '#type' => 'button',
                '#name' => 'del-ho-' . $id,
                '#value' => '×',
                '#ajax' => array('callback' => 'before_after_season_form_ajax_handler'),
                '#attributes' => array('class' => array('button-icon')),
                '#wrapper_attributes' => array('class' => array('col-sm-1')),
            );
        }
    }

    // пустое поле для нового, если нажата кнопка Добавления ВО
    if (!empty($form_state['triggering_element']) && strpos($form_state['triggering_element']['#name'], 'add-ho') !== false) {

        $counter++;
        $form['fs3']['cnt-' . $counter] = array(
            '#prefix' => '<div class="add-ho-item row">',
            '#suffix' => '</div>',
            '#weight' => $counter,
        );
        $form['fs3']['cnt-' . $counter]['hobject-' . $counter] = array(
            '#type' => 'select',
            '#options' => $options_ho,
            '#chosen' => true,
            '#size' => 5,
            '#wrapper_attributes' => array('class' => array('col-sm-5')),
        );
        $form['fs3']['cnt-' . $counter]['phase-' . $counter] = array(
            '#type' => 'textfield',
            '#attributes' => array('placeholder' => array('фаза развития')),
            '#wrapper_attributes' => array('class' => array('col-sm-4')),
        );
        $form['fs3']['cnt-' . $counter]['dominant-' . $counter] = array(
            '#type' => 'checkbox',
            '#title' => 'Доминант',
            '#wrapper_attributes' => array('class' => array('col-sm-2')),
        );
        $form['fs3']['cnt-' . $counter]['del-' . $counter] = array(
            '#type' => 'button',
            '#name' => 'del-ho-' . $counter,
            '#value' => '×',
            '#ajax' => array('callback' => 'before_after_season_form_ajax_handler'),
            '#attributes' => array('class' => array('button-icon')),
            '#wrapper_attributes' => array('class' => array('col-sm-1')),
        );

    }

    // кнопка добавления ВО
    $form['fs3']['add-ho'] = array(
        '#type' => 'button',
        '#name' => 'add-ho',
        '#value' => 'добавить объект',
        '#prefix' => '<div class="add-ho-button">',
        '#suffix' => '</div>',
        '#ajax' => array('callback' => 'before_after_season_form_ajax_handler'),
        '#attributes' => array('class' => array('button-action')),
        '#weight' => 100000,
    );

    $form['fs4'] = array('#type' => 'container', '#attributes' => array('class' => array('form-actions')));
    $form['fs4']['submit'] = array(
        '#type' => 'submit',
        '#value' => empty($sid) ? 'Добавить' : 'Сохранить',
        '#attributes' => array('class' => array('pull-right')),
    );
    $form['fs4']['cancel'] = array(
        '#markup' => '<a href="/field/' . $fid . '/season/list" class="btn btn-link pull-right">Отменить</a>',
        '#attributes' => array('class' => array('btn-link', 'pull-right')),
    );

    return $form;
}

function before_after_season_form_validate(&$form, &$form_state)
{
    // если нажат submit
    if (!empty($form_state['submitted'])) {

        // заполняем массив ВО
        $form_state['hobjects'] = array();
        foreach ($form_state['values'] as $key => $value) {
            if (strpos($key, 'hobject-') !== false) {
                $id = str_replace('hobject-', '', $key);

                if ($value) {
                    // заполняем массив с ключами id Сорняков, чтобы проще было сохранять в submit
                    $form_state['hobjects'][$value] = array(
                        'nid'       => $value,
                        'dominant'  => empty($form_state['values']['dominant-' . $id]) ? 0 : 1,
                        'phase'     => $form_state['values']['phase-' . $id],
                    );
                }
                unset($form_state['values']['hobject-' . $id]); unset($form_state['values']['dominant-' . $id]); unset($form_state['values']['phase-' . $id]);
                unset($form_state['input']['hobject-' . $id]); unset($form_state['input']['dominant-' . $id]); unset($form_state['input']['phase-' . $id]);
            }
        }
        // проверка наличия ВО
        if (empty($form_state['hobjects'])) form_set_error('hobject-0', 'Задайте хотя бы один Вредный объект');

        // проверка наличия Культуры
        if (empty($form_state['values']['culture'])
            || $form_state['values']['culture'] == '_none') {
            form_set_error('culture', 'Задайте основную культуру.');
        }
    }
}

function before_after_season_form_submit(&$form, &$form_state)
{
    // создать или загрузить Сезон
    if (empty($form_state['sid'])) {
        $season = entity_create('field_collection_item', array('field_name' => 'field_f_sowing'));
        $season_wrapper = entity_metadata_wrapper('field_collection_item', $season);
        $node = node_load($form_state['fid']);
        $season->setHostEntity('node', $node);
    } else {
        $season_wrapper = entity_metadata_wrapper('field_collection_item', $form_state['sid']);
    }

    // задать Культуру, Субкультуру и площадь
    $season_wrapper->field_f_s_culture->set($form_state['values']['culture']);
    $season_wrapper->field_f_s_culture_note->set($form_state['values']['culture_note']);
    $season_wrapper->field_f_s_sub_culture->set($form_state['values']['sub_culture']);
    if (!empty($form_state['values']['previous_culture'])) {
        if ($form_state['values']['previous_culture'] == '_none') $season_wrapper->field_f_s_previous_culture->set(null);
        else $season_wrapper->field_f_s_previous_culture->set($form_state['values']['previous_culture']);
    }
    $season_wrapper->field_f_s_area->set(intval($form_state['values']['area']));
    $season_wrapper->field_f_s_season->set($form_state['values']['season']);
    $season_wrapper->field_f_s_year->set($form_state['values']['year']);
    $season_wrapper->save();
    $form_state['sid'] = $season_wrapper->item_id->value();

    // отредактировать имеющиеся ВО и удалить отсутствующие
    if ($season_wrapper->field_f_s_hobjects->count()) {
        foreach ($season_wrapper->field_f_s_hobjects->getIterator() as $delta => $ho_wrapper) {
            if ($ho_wrapper->value() && $ho_wrapper->field_f_s_h_hobject->value()) {

                $hoid = $ho_wrapper->field_f_s_h_hobject->nid->value();
                if (isset($form_state['hobjects'][$hoid])) {
                    $ho_wrapper->field_f_s_h_dominant->set($form_state['hobjects'][$hoid]['dominant']);
                    $ho_wrapper->field_f_s_h_phase->set($form_state['hobjects'][$hoid]['phase']);
                    $ho_wrapper->save();
                    // убрать из $form_state['hobjects']
                    unset($form_state['hobjects'][$hoid]);
                } else {
                    // $season_wrapper должна быть уже сохранена, так как fieldCollection->delete(); сама сохраняет HostEntity
                    // если сохранить $season_wrapper после удаления FieldCollection, то в field_f_s_hobjects сохранится ссылка на удалённый FC
                    $ho_wrapper->delete();
                }
            }
        }
    }

    // после $ho_wrapper->delete(); нужно заново загрузить $season, иначе ->setHostEntity дальше сохранит старую версию с уже удалённым индексом Вредного объекта
    $season_wrapper = entity_metadata_wrapper('field_collection_item', $form_state['sid']);
    $season = $season_wrapper->value();

    // добавить оставшиеся в массиве $form_state['hobjects'] новые ВО
    foreach($form_state['hobjects'] as $hoid => $ho_item) {
        $hobject = entity_create('field_collection_item', array('field_name' => 'field_f_s_hobjects'));
        $hobject->setHostEntity('field_collection_item', $season);

        $hobject_wrapper = entity_metadata_wrapper('field_collection_item', $hobject);
        $hobject_wrapper->field_f_s_h_hobject->set($ho_item['nid']);
        $hobject_wrapper->field_f_s_h_dominant->set($ho_item['dominant']);
        $hobject_wrapper->field_f_s_h_phase->set($ho_item['phase']);
        $hobject_wrapper->save();
    }

    $form_state['redirect'] = 'field/' . $form_state['fid'] . '/season/list';
}

function before_after_season_form_ajax_handler(&$form, &$form_state) {
    $commands[] = ajax_command_remove('#messages-wrap');
    $commands[] = ajax_command_before('.season-form', '<div id="messages-wrap">' . theme('status_messages') . '</div>');
    $commands[] = ajax_command_replace('.season-form', drupal_render($form));

    return array('#type' => 'ajax', '#commands' => $commands);
}
