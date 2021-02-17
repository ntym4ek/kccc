<?php
/**
 * Created by PhpStorm.
 * User: Михаил
 * Date: 25.08.2016
 * Time: 11:58
 */

/**
 * функция, возвращающая контент страницы Компании
 */
function person_company($acid) {
  global $user;

  $output = false;

  // вернуть форму только, если свой аккаунт
  if ($user->uid == $acid) {
    $form = drupal_get_form('person_company_form', $acid);
    $output = render($form);
  }

  return $output;
}

/**
 * форма добавления/редактирования компании
 */
function person_company_form($form, &$form_state, $acid) {
  $user = user_load($acid);

  $form_state['acid'] = $acid;
  $form_state['user'] = $user;
  $form_state['section'] = 'company';

  // если нажата кнопка
  if (!empty($form_state['triggering_element'])) {
    // Удалить компанию
    if (strpos($form_state['triggering_element']['#name'], 'delete-company') !== false) {
      // удалить сотрудников
      // компанию
    }

    // Удалить вид: убрать из массива $form_state['activities']
    if (strpos($form_state['triggering_element']['#name'], 'del-act-') !== false) {
      $aid = str_replace('del-act-', '', $form_state['triggering_element']['#name']);
      if ($act = field_collection_item_load($aid)) {
        $act->delete();
      }
      unset($form_state['activities'][$aid]);
    }
  }

  // начальные значения
  $inn = $name = $opf = $slogan = $info = $web = $act = $ways = '';
  $l_address = empty($form_state['values']['l_address']) ? '' : $form_state['values']['l_address'];
  $form_state['photo'] = '';
  if ($company = profile2_load_by_user($user, 'company')) {
    $company_wrapper = entity_metadata_wrapper('profile2', $company);
    $form_state['pid'] = $company->pid;

    $inn = $company_wrapper->field_profile_company_inn->value();
    $opf = $company_wrapper->field_profile_company_type->value() ? $company_wrapper->field_profile_company_type->tid->value() : '';
    $name = $company_wrapper->field_profile_name->value();
    $slogan = $company_wrapper->field_profile_slogan->value();
    $info = $company_wrapper->field_profile_info->value() ? $company_wrapper->field_profile_info->value->raw() : '';
    $web = $company_wrapper->field_profile_website->value();

    // photo
    if ($company_wrapper->field_profile_photo->value()) {
      $file = $company_wrapper->field_profile_photo->file->value();
      $form_state['photo'] = $file->fid;
    }
    // адрес
    $l_address = $company_wrapper->field_profile_l_address->value();
    if ($l_address) $address = address_book_addr_to_string($l_address);

    // сотрудники $form_state['employees']
    $query = db_select('profile', 'p');
    $query->condition('p.type', 'staff');
    $query->innerJoin('field_data_field_profile_company2', 'fc', 'p.pid = fc.entity_id');
    $query->condition('fc.field_profile_company2_target_id', $company->pid);
    $query->fields('p', array('pid', 'uid'));
    $result_s = $query->execute()->fetchAll();

    if ($result_s) {
      foreach ($result_s as $item_s) {
        $user_s = user_load($item_s->uid);
        $staff_s = profile2_load_by_user($user_s, 'staff');
        $approved = $staff_s->field_profile_company_approved['und'][0]['value'];
        $fio = ext_user_get_user_name($user_s, 'short_sn');
        if ($approved != -1) {
          $form_state['employees'][$item_s->uid] = array(
            'uid' => $item_s->uid,
            'sid' => $staff_s->pid,
            'approved' => $approved,
            'name' => $fio,
            'post' => $staff_s->field_profile_post['und'][0]['value'],
          );
        }
      }
    }


    if (empty($form_state['triggering_element'])) {
      // при открытии формы загрузить массив Видов в form_state
      foreach ($company_wrapper->field_profile_activities as $act_wrapper) {
        $aid = $act_wrapper->item_id->value();
        $ways = array();
        foreach ($act_wrapper->field_pa_ways as $way_wrapper) {
          $ways[] = $way_wrapper->tid->value();
        }

        $form_state['activities'][$aid] = array(
          'act' => $act_wrapper->field_pa_activity->tid->value(),
          'ways' => $ways,
        );
      }
    }
  } else {
    drupal_set_message('Если Вы представитель компании или ИП и эта компания не внесена в наш реестр, заполните форму и получите доступ к дополнительными возможностями нашего сайта.', 'info');
  }

  $form['markup02'] = array(
    '#markup' => '<div class="profile-left">'
  );

  $form['photo'] = array(
    '#type' => 'managed_file',
    '#title' => 'Логотип',
    '#description' => 'Выберите файл с расширением jpg, jpeg, png',
    '#upload_location' => "public://temp/",
    '#upload_validators' => array(
      'file_validate_is_image' => array(),
      'file_validate_extensions' => array('png jpg jpeg'),
      'file_validate_image_resolution' => array('600x600', '100x100'),
    ),
    '#theme' => 'forms_ex_thumb_upload',
    '#default_value' => $form_state['photo'],
  );
  $form['markup03'] = array(
    '#markup' => '</div>',
  );

  $form['inn'] = array(
    '#type' => 'textfield',
    '#title' => 'ИНН',
    '#required' => true,
    '#default_value' => $inn,
    '#prefix' => '<div class="profile-right">',
  );
  // организационно-правовая форма
  $options_opf = get_options_by_taxonomy('company_types');
  $opf = $opf ? $opf : current($options_opf);
  $form['opf'] = array(
    '#type' => 'select',
    '#title' => 'Орг.-правовая форма',
    '#options' => $options_opf,
    '#required' => true,
    '#default_value' => $opf,
  );
  $form['name'] = array(
    '#type' => 'textfield',
    '#title' => 'Наименование',
    '#required' => true,
    '#default_value' => $name,
  );
  $form['slogan'] = array(
    '#type' => 'textfield',
    '#title' => 'Слоган',
    '#default_value' => $slogan,
  );
  $form['info'] = array(
    '#type' => 'textarea',
    '#title' => 'Описание',
    '#default_value' => $info,
  );
  $form['web'] = array(
    '#type' => 'textfield',
    '#title' => 'Веб-сайт',
    '#default_value' => $web,
  );

  $form['markup12'] = array(
    '#markup' => '<div class="profile-separator"></div>'
  );


  // виды деятельности
  // список видов
  $options_act = get_options_by_taxonomy('activities');
  $options_way = get_options_by_taxonomy('activity_ways');

  $act = current($options_act);
  $ways = current($options_way);

  // развернуть скрытую панель добавления, если значения заданы
  $pane_hidden = ($act || $ways) ? false : true;


  $form['markup01'] = array(
    '#markup' => '<div class="form-item form-item-activities"><label>Виды деятельности <span class="form-required" title="Обязательное поле">*</span></label>'
      . '<div class="activities">',
  );
  // список уже выбранных Видов с кнопкой удаления
  if (!empty($form_state['activities'])) {
    foreach ($form_state['activities'] as $aid => $item_a) {
      $name_a = trim(str_replace('--', '', $options_act[$item_a['act']]));
      $ways_arr = array();
      foreach ($item_a['ways'] as $item_w) {
        $ways_arr[] = drupal_strtolower($options_way[$item_w]);
      }

      $form['del-act-' . $aid] = array(
        '#type' => 'button',
        '#name' => 'del-act-' . $aid,
        '#value' => '×',
        '#ajax' => array(
          'callback' => 'person_company_activity_ajax_callback'
        ),
        '#prefix' => '<div class="activity">' . $name_a . '<span>' . implode(', ', $ways_arr) . '</span>',
        '#suffix' => '</div>',
        '#attributes' => array(
          'class' => array('close'),
        ),
      );
    }
  }

  // скрытая панель добавления Вида и направлений
  $form['markup06'] = array(
    '#markup' => '<div id="activity-add-control"><a>добавить</a></div></div></div><div id="activity-add-pane"' . ($pane_hidden ? ' class="hidden"': '') . '>',
  );

  $form['act'] = array(
    '#type' => 'hierarchical_select',
    '#title' => 'Вид',
    '#size' => 1,
    '#config' => array(
      'module' => 'hs_taxonomy',
      'params' => array(
        'vid' => taxonomy_vocabulary_machine_name_load('activities')->vid,
      ),
      'save_lineage'    => 0,
      'enforce_deepest' => 0,
      'resizable'       => 1,
      'level_labels' => array(
        'status' => 0,
        'labels' => array(
          0 => t('Main category'),
          1 => t('Subcategory'),
          2 => t('Third level category'),
        ),
      ),
      'dropbox' => array(
        'status'   => 0,
        'title'    => t('All selections'),
        'limit'    => 0,
        'reset_hs' => 1,
        'sort'     => 1,
      ),
      'editability' => array(
        'status'           => 0,
        'item_types'       => array(),
        'allowed_levels'   => array(
          0 => 0,
          1 => 0,
          2 => 1,
        ),
        'allow_new_levels' => 0,
        'max_levels'       => 3,
      ),
      'entity_count'    => array(
        'enabled' => 0,
        'require_entity' => 0,
        'settings' => array(
          'count_children' => 0,
          'entity_types' => array(),
        ),
      ),
      // These settings cannot be configured through the UI: they can only be
      // overridden through code.
      'animation_delay'    => 400,
      'special_items'      => array(),
      'render_flat_select' => 0,
    ),
    '#default_value' => $act,
  );
  $form['ways'] = array(
    '#type' => 'select',
    '#title' => 'Направления',
    '#options' => $options_way,
    '#default_value' => $ways,
    '#multiple' => true,
    '#chosen' => true,
  );
  $form['act_submit'] = array(
    '#type' => 'button',
    '#value' => 'добавить',
    '#name' => 'save-activity',
    '#ajax' => array(
      'callback' => 'person_company_activity_ajax_callback'
    ),
    '#prefix' => '<div class="activity-save">',
    '#suffix' => '</div></div>',
  );


  $form['markup05'] = array(
    '#markup' => '<div class="profile-separator"></div>'
  );

  chibs_include_modal(array('class' => 'custom-form'));
  $l_addr = 0;
  $form['markup08'] = array(
    '#markup' => '<div class="form-item form-item-l-address"><label>Юридический адрес <span class="form-required" title="Обязательное поле">*</span></label>',
  );
  $html = $address ? '<div class="address">' . $address['address'] . '<span class="close">×</span><input type="hidden" name="l_address" value="' . $l_address . '"></div>' : '';
  $link_l_addr = l('указать', 'adr-book/' . $l_addr . '/company_l/0/nojs', array('attributes' => array('class' => array('ctools-modal-style', 'ctools-use-modal'))));
  $form['l_addr'] = array(
    '#markup' => $html . '<div id="address-add-control" ' . ($html ? 'class="hidden"' : '') . '>' . $link_l_addr . '</div></div>',
  );

  $form['markup04'] = array(
    '#markup' => '<div class="profile-separator"></div>'
  );

  // струдники
  if (!empty($form_state['employees'])) {
    $form['markup09'] = array(
      '#markup' => '<div class="form-item form-item-employees"><label>Сотрудники</label><div class="employees">',
    );
    // список с кнопкой одобрения заявки и удаления
    foreach ($form_state['employees'] as $eid => $item_e) {
      $name = $item_e['name'];
      $post = drupal_strtolower($item_e['post']);
      $sid = $item_e['sid'];
      $approved = $item_e['approved'];

      // если заявка уже принята, вывести ссылку отмены, если нет - принятия
      $mark = '';
      if ($approved) $link_act01 = l('-', "person/$acid/company/staff/$sid/cancel/nojs", array('attributes' => array('title' => 'исключить из сотрудников', 'class' => array('action01 ctools-modal-style', 'ctools-use-modal'))));
      else {
        $mark = '<span class="mark yellow"></span>';
        $link_act01 = l('+', "person/$acid/company/staff/$sid/approve/nojs", array('attributes' => array('title' => 'включить в список сотрудников', 'class' => array('action01 ctools-modal-style', 'ctools-use-modal'))));
      }

      $link_act02 = l('×', "person/$acid/company/staff/$sid/delete/nojs", array('attributes' => array('title' => 'удалить из списка сотрудников', 'class' => array('action02 ctools-modal-style', 'ctools-use-modal'))));

      $link_person = '<a href="/person/' . $item_e['uid'] . '/summary">' . $name . '</a>';
      $form['employee' . $eid] = array(
        '#markup' => '<div class="employee" id="e' . $sid . '">' . $mark . $link_person . '<span>' . $post . '</span>'
          . $link_act01
          . $link_act02
          . '</div>'
      );
    }
    $form['markup10'] = array(
      '#markup' => '</div></div>',
    );
    $form['markup11'] = array(
      '#markup' => '<div class="profile-separator"></div>'
    );
  }

  $form['markup07'] = array(
    '#markup' => '<div class="profile-changes">для принятия всех изменений нажмите "Сохранить"</div>'
  );

  $form['c_submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save'),
    '#prefix' => '<div class="profile-save col-md-offset-4">',
    '#ajax' => array(
      'callback' => 'person_company_activity_ajax_callback'
    ),
  );

  if ($inn) {
    $form['markup13'] = array(
      '#markup' => '<a href="/person/' . $acid . '/company/delete/nojs" class="btn btn-link ctools-modal-style ctools-use-modal ml30">' . drupal_strtolower(t('Delete company')) . '</a></div>',
    );
  } else {
    $form['c_submit']['#suffix'] = '</div></div>';
  }

  return $form;
}

/**
 * ajax callback, отрабатывающий события формы
 */
function person_company_activity_ajax_callback(&$form, &$form_state) {
  // завернуть контент и меню
  $message = theme('status_messages');
  $output = theme('person_wrap', array(
    'content' => array(
      'body' => drupal_render($form),
      'account' => ext_user_get_user_array($form_state['acid']),
      'menu' => _person_actions($form_state['acid'], $form_state['section']),
      'class' => $form_state['section'],
      'message' => $message ? $message : '',
    )
  ));
  $commands[] = ajax_command_replace('#profile', $output);

  // если ошибка на панели добавления Видов, открыть её
  if (!empty($form_state['error_acts'])) {
    $commands[] = ajax_command_css('#activity-add-control', array('display' => 'none'));
    $commands[] = ajax_command_css('#activity-add-pane', array('display' => 'block'));
  }

  return array('#type' => 'ajax', '#commands' => $commands);
}

function person_company_form_validate($form, &$form_state) {

  // если удаление Вида деятельности - не проводить вилидацию
  if (!empty($form_state['triggering_element']) && strpos($form_state['triggering_element']['#name'], 'del-act-') !== false) return true;

  $form_state['error_acts'] = 0;

  // проверить заполнение обоих полей Вида деятельности
  if (!empty($form_state['triggering_element']) && $form_state['triggering_element']['#name'] == 'save-activity') {
    if (empty($form_state['values']['act'])) {
      $form_state['error_acts'] = 1;
      form_set_error('act', 'Нужно выбрать название вида деятельности'); return false;
    }
    if (empty($form_state['values']['ways'])) { $form_state['error_acts'] = 1;
      form_set_error('ways', 'Нужно выбрать минимум одно направление деятельности'); return false;
    }
  }

  // при нажатии на кнопки сохранить введенный Вид в массив
  if (!empty($form_state['values']['act']) && !empty($form_state['values']['ways'])) {
    $id = 'n' . $form_state['values']['act'][0];
    $form_state['activities'][$id] = array(
      'act'   => $form_state['values']['act'][0],
      'ways'   => $form_state['values']['ways'],
    );
    unset($form_state['values']['act']); unset($form_state['input']['act']);
    unset($form_state['values']['ways']); unset($form_state['input']['ways']);
  }
  // не проверять остальные поля, если нажато Сохранить в панели Видов
  if (!empty($form_state['triggering_element']) && $form_state['triggering_element']['#name'] == 'save-activity') return true;

  // проверка ИНН
  // на корректность
  if (empty($form_state['values']['inn']) || drupal_strlen($form_state['values']['inn']) < 10) { form_set_error('inn', 'Нужно указать корректный ИНН компании'); return false; }
  // на уникальность
  $query = db_select('profile', 'p');
  $query->condition('p.type', 'company');
  if (!empty($form_state['pid'])) $query->condition('p.pid', $form_state['pid'], '!=');
  $query->innerJoin('field_data_field_profile_company_inn', 'fi', 'p.pid = fi.entity_id');
  $query->condition('fi.field_profile_company_inn_value', trim($form_state['values']['inn']));
  $query->fields('p', array('pid', 'uid'));
  $result_p = $query->execute()->fetchAll();

  if ($result_p) {
    $company_name = person_get_user_company_name($result_p->pid);
    $user_name = ext_user_get_user_name($result_p->uid, 'short_sn');
    form_set_error('inn', 'ИНН ' . $form_state['values']['inn'] . ' уже есть в нашей базе данных.<br />'
      . 'Компания ' . $company_name . ' внесена пользователем ' . $user_name . '.<br />'
      . 'Повторное добавление невозможно.');
    return false;
  }


  if (empty($form_state['values']['name'])) { form_set_error('name', 'Нужно указать Наименование компании'); return false; }
  if (empty($form_state['input']['l_address'])) {
    unset($form_state['values']['l_address']);
    form_set_error('', 'Нужно указать Юридический адрес компании'); return false;
  } else {
    $form_state['values']['l_address'] = $form_state['input']['l_address'];
  }

  if (empty($form_state['activities'])) {
    $form_state['error_acts'] = 1;
    form_set_error('act', 'Нужно задать Вид деятельности'); return false;
  }
}

function person_company_form_submit($form, &$form_state) {
  $user = $form_state['user'];

  if(!$company = profile2_load_by_user($user, 'company')) {
    $company = profile2_create(array('user' => $user->uid, 'type' => 'company'));
    $company->save();
  }
  $company_wrapper = entity_metadata_wrapper('profile2', $company);

  // задать значения полей
  $company_wrapper->field_profile_company_inn->set(trim($form_state['values']['inn']));
  $company_wrapper->field_profile_company_type->set($form_state['values']['opf']);
  $company_wrapper->field_profile_name->set($form_state['values']['name']);
  $company_wrapper->field_profile_slogan->set($form_state['values']['slogan']);
  $company_wrapper->field_profile_website->set($form_state['values']['web']);
  $company_wrapper->field_profile_info->value->set($form_state['values']['info']);
  $company_wrapper->field_profile_l_address->set($form_state['values']['l_address']);
  // логотип
  $old_photo = empty($form_state['photo']) ? 0 : $form_state['photo'];
  if ($old_photo != $form_state['values']['photo']) {
    // удалить старое изображение
    // сначала очистить поле, без этого картинка не меняется на новую
    $company_wrapper->field_profile_photo->set(null);
    $company_wrapper->save();
    // удалить сам файл
    if ($old_photo && $old_file = file_load($old_photo)) {
      file_usage_delete($old_file, 'person');
      file_delete($old_file);
    }
    // сохранить новый файл
    if ($form_state['values']['photo']) {
      $file = file_load($form_state['values']['photo']);
      $file->status = FILE_STATUS_PERMANENT;
      $filename = $file->timestamp . '.' . pathinfo($file->filename, PATHINFO_EXTENSION);
      $file->filename = $filename;
      $dir = "public://images/companies/$user->uid/";
      file_prepare_directory($dir, FILE_CREATE_DIRECTORY);
      $file = file_move($file, $dir . $filename, FILE_EXISTS_REPLACE);
      file_save($file);
      file_usage_add($file, 'person', 'profile2', 0);
      $form_state['photo'] = $file->fid;
      $company_wrapper->field_profile_photo->file->set($file);
    }
  }


  // задать виды деятельности
  // извлечь новый в массив $activities
  $activities = $form_state['activities'];
  foreach($company_wrapper->field_profile_activities as $key => $act_wrapper) {
    $aid = $act_wrapper->item_id->value();
    if (isset($activities[$aid])) unset($activities[$aid]);
  }
  // добавить в Компанию
  if ($activities) {
    foreach ($activities as $act_item) {
      $activity = entity_create('field_collection_item', array('field_name' => 'field_profile_activities'));
      $activity->setHostEntity('profile2', $company);

      $activity = entity_metadata_wrapper('field_collection_item', $activity);
      $activity->field_pa_activity->set($act_item['act']);
      $activity->field_pa_ways->set($act_item['ways']);

      $activity->save();
    }
  }

  $company_wrapper->save();

  // создать сотрудника-администратора
  if(!$staff = profile2_load_by_user($user, 'staff')) {
    $staff = profile2_create(array('user' => $user->uid, 'type' => 'staff'));
    $staff->save();
  }
  $staff_wrapper = entity_metadata_wrapper('profile2', $staff);
  if ((!$staff_wrapper->field_profile_company2->value() || $staff_wrapper->field_profile_company2->pid->value() != $company->pid)
    || $staff_wrapper->field_profile_company_approved->value() != 1) {
    $staff_wrapper->field_profile_company2->set($company);
    $staff_wrapper->field_profile_post->set('администратор');
    $staff_wrapper->field_profile_company_approved->set(1);
    $staff_wrapper->save();
    drupal_set_message('Вы автоматически внесены в список сотрудников компании как её администратор.');
  }

  $form_state['rebuild'] = true;
  drupal_set_message('Информация сохранена');
}

/**
 * функция возвращающая обязательные поля для модуля Address Book
 * @return array
 */
function company_l_address_book_info() {
  return array('email', 'phone', 'region', 'city', 'street', 'house');
}
/**
 * функция, вызываемая при ajax submit в модуле Address Book
 * вставка выбранного адреса в форму
 */
function company_l_address_book_ajax_submit($form_state) {
  $commands = array();

  // сформировать адрес и фио в строку
  $addr_id = $form_state['values']['radios'];
  $address = address_book_addr_to_string($addr_id);

  // вывести в форму
  if ($form_state['executed']) {
    // убираем все сообщения с формы оформления заказа
    $commands[] = ajax_command_invoke('.messages', 'replaceWith', array(''));

    $html = '<div class="address">' . $address['address'] . '<span class="close">×</span><input type="hidden" name="l_address" value="' . $addr_id . '"></div>';
    $commands[] = ajax_command_before('#address-add-control', $html);
    $commands[] = ajax_command_css('#address-add-control', array('display' => 'none'));
    $commands[] = ajax_command_invoke('#edit-customer-profile-shipping-field-ship-address-und-0-value', 'val', array($form_state['values']['radios']));
  }
  return $commands;
}

/**
 * callback вызова окна подтверждения операции с сотрудником
 */
function person_company_staff_confirm_callback($acid, $sid, $op, $mode = NULL) {
  if ($mode == 'ajax') {
    ctools_include('modal');
    $form_state = array('ajax' => TRUE, 'title' => t('Employee'));
    $form_state['build_info']['args'] = array('acid' => $acid, 'sid' => $sid, 'op' => $op);
    $output = ctools_modal_form_wrapper('person_company_staff_confirm_form', $form_state);
    if (!empty($form_state['ajax_commands'])) $output = $form_state['ajax_commands'];
    print ajax_render($output);
    drupal_exit();
  }
  else return drupal_get_form('person_company_staff_confirm_form', $acid, $sid, $op);
}
/**
 * форма подтверждения операции с Сотрудником
 */
function person_company_staff_confirm_form($form, &$form_state, $acid, $sid, $op) {
  $form_state['acid'] = $acid;
  $form_state['sid'] = $sid;
  $form_state['op'] = $op;

  $form = array(
    '#attributes' => array('class' => array('custom-form'))
  );
  $text = 'Операция не задана.';
  switch ($op) {
    case 'approve':
      $text = 'Одобрить заявку пользователя на приём в список сотрудников?';
      break;
    case 'cancel':
      $text = 'Данная операция отменяет одобрение заявки пользователя, но не исключает его из списка.<br />Подтверждаете операцию?';
      break;
    case 'delete':
      $text = 'Данная операция удаляет пользователя из списка сотрудников.<br />Подтверждаете операцию?';
      break;
  }

  $form['fs1'] = array('#type' => 'fieldset');
  $form['fs1']['markup01'] = array(
    '#markup' => $text,
  );

  $form['fs2'] = array('#type' => 'container', '#attributes' => array('class' => array('form-actions')));
  $form['fs2']['st_cancel'] = array(
    '#type' => 'button',
    '#value' => t('Cancel'),
    '#ajax' => array(
      'callback' => 'person_company_staff_confirm_ajax_submit'
    ),
    '#attributes' => array('class' => array('button-link'))
  );
  $form['fs2']['st_submit'] = array(
    '#type' => 'submit',
    '#value' => t('Ok'),
    '#ajax' => array(
      'callback' => 'person_company_staff_confirm_ajax_submit'
    ),
  );

  return $form;
}


function person_company_staff_confirm_form_submit($form, &$form_state) {
  global $user;
  $staff_wrapper = entity_metadata_wrapper('profile2', $form_state['sid']);

  switch ($form_state['op']) {
    case 'approve':
      $staff_wrapper->field_profile_company_approved->set(1);

      // добавить роль сотрудника (только для Торгового Дома)
      $company = profile2_load_by_user($user, 'company');
      if ($company->pid == KCCC_COMPANY_PID) {
        $staff_account = user_load($staff_wrapper->user->uid->value());
        $staff_account->roles[STAFF] = 'Сотрудник';
        user_save($staff_account);
      }

      // отправить письмо
      $mail = $staff_wrapper->user->mail->value();
      drupal_mail('system', 'mail', $mail, language_default(), array(
        'context' => array(
          'subject' => 'Ваш запрос одобрен',
          'message' => 'Вы подали заявку для включения в список сотрудников компании на сайте <a href="http://kccc.ru">http://kccc.ru</a>.<br />'
            . 'Ваш запрос был одобрен.',
        ),
      ));
      break;
    case 'cancel':
      $staff_wrapper->field_profile_company_approved->set(0);
      // убрать роль сотрудника (только для Торгового Дома)
      $staff_account = user_load($staff_wrapper->user->uid->value());
      unset($staff_account->roles[STAFF]);
      user_save($staff_account);

      break;
    case 'delete':
      $staff_wrapper->field_profile_company_approved->set(-1);
      break;
  }

  $staff_wrapper->save();
}
function person_company_staff_confirm_ajax_submit($form, $form_state) {
  ctools_include('ajax'); ctools_include('modal');

  if ($form_state['triggering_element']['#type'] == 'submit') {
    switch ($form_state['op']) {
      case 'approve':
        $commands[] = ajax_command_replace('#e' . $form_state['sid'] . ' .mark', '');
        $link_act = l('-', "person/{$form_state['acid']}/company/staff/{$form_state['sid']}/cancel/nojs", array('attributes' => array('title' => 'исключить из сотрудников', 'class' => array('action01 ctools-modal-style', 'ctools-use-modal'))));

        $commands[] = ajax_command_replace('#e' . $form_state['sid'] . ' .action01', '<div>' . $link_act . '</div');
        break;
      case 'cancel':
        $commands[] = ajax_command_prepend('#e' . $form_state['sid'], '<span class="mark yellow"></span>');
        $link_act = l('+', "person/{$form_state['acid']}/company/staff/{$form_state['sid']}/approve/nojs", array('attributes' => array('title' => 'включить в список сотрудников', 'class' => array('action01 ctools-modal-style', 'ctools-use-modal'))));

        $commands[] = ajax_command_replace('#e' . $form_state['sid'] . ' .action01', '<div>' . $link_act . '</div');
        break;
      case 'delete':
        $commands[] = ajax_command_replace('#e' . $form_state['sid'], '');
        break;
    }
  }

  $commands[] = ctools_modal_command_dismiss();

  return array('#type' => 'ajax', '#commands' => $commands);
}

/**
 * возвращает название компании
 * @param $pid - id профиля
 * @return string
 */
function person_get_user_company_name($pid){
  $company_name = '';
  if (!empty($pid) && $company = profile2_load($pid)) {
    $company_wrapper = entity_metadata_wrapper('profile2', $company);
    $opf  = $company_wrapper->field_profile_company_type->value() ? $company_wrapper->field_profile_company_type->name->value() : '';
    $name = $company_wrapper->field_profile_name->value();
    $company_name = $opf . ' ' . $name;
  }

  return trim($company_name);
}

/**
 * возвращает информацию о компании
 * @param $user
 * @return array
 */
function person_get_user_company_array($user){
  if (is_numeric($user)) $account = user_load($user);
  else $account = $user;

  $name = $opf = '';
  $photo_url = '/sites/default/files/default_images/no_photo.png';
  if (!empty($account->uid) && $company = profile2_load_by_user($account, 'company')) {
    $company_wrapper = entity_metadata_wrapper('profile2', $company);
    $opf  = $company_wrapper->field_profile_company_type->value() ? $company_wrapper->field_profile_company_type->name->value() : '';
    $name = $company_wrapper->field_profile_name->value();
    if ($company_wrapper->field_profile_photo->value()) {
      $file = $company_wrapper->field_profile_photo->file->value();
      $photo_url = image_style_url('user_photo', $file->uri);
    }
  }

  return array(
    'name' => $name,
    'opf' => $opf,
    'photo' => $photo_url,
  );
}

/**
 * callback вызова окна подтверждения удаления компании
 */
function person_delete_confirm_callback($acid, $section, $mode = NULL) {
  if ($mode == 'ajax') {
    ctools_include('modal');
    $form_state = array('ajax' => TRUE, 'title' => t('Delete company'));
    $form_state['build_info']['args'] = array('acid' => $acid, 'section' => $section);
    $output = ctools_modal_form_wrapper('person_delete_confirm_form', $form_state);
    if (!empty($form_state['ajax_commands'])) $output = $form_state['ajax_commands'];
    print ajax_render($output);
    drupal_exit();
  }
  else return drupal_get_form('person_delete_confirm_form', $acid, $section);
}
/**
 * форма подтверждения операции с Сотрудником
 */
function person_delete_confirm_form($form, $form_state, $acid, $section) {
  $form_state['acid'] = $acid;
  $form_state['section'] = $section;

  $form['c'] = array(
    '#type' => 'container',
    '#attributes' => array(
      'class' => array('modal-body'),
    ),
  );
  $text = 'Операция не задана.';
  switch ($section) {
    case 'company':
      $text = 'Удалить компанию и отменить все заявки поданные сотрудниками?';
      break;
  }
  $form['c']['markup01'] = array(
    '#markup' => $text,
  );

  $form['st_cancel'] = array(
    '#type' => 'button',
    '#value' => t('Cancel'),
    '#prefix' => '<div class="modal-controls">',
    '#attributes' => array(
      'class' => array('button-link'),
    ),
    '#ajax' => array(
      'callback' => 'person_delete_confirm_ajax_submit'
    ),
  );
  $form['st_submit'] = array(
    '#type' => 'submit',
    '#value' => t('Ok'),
    '#suffix' => '</div>',
    '#ajax' => array(
      'callback' => 'person_delete_confirm_ajax_submit'
    ),
  );

  return $form;
}
function person_delete_confirm_form_submit($form, $form_state) {
  global $user;

  switch ($form_state['section']) {
    case 'company':
      $company = profile2_load_by_user($user, 'company');
      // отменить все заявки сотрудников
      $query = db_select('profile', 'p');
      $query->condition('p.type', 'staff');
      $query->innerJoin('field_data_field_profile_company2', 'fc', 'p.pid = fc.entity_id');
      $query->condition('fc.field_profile_company2_target_id', $company->pid);
      $query->fields('p', array('pid'));
      $result = $query->execute()->fetchAll();

      if ($result) {
        foreach ($result as $item) {
          $staff = profile2_load($item->pid);
          $staff->delete();
        }
      }
      $company->delete();

      drupal_set_message('Информация о компании удалена из нашего реестра.');
      break;
  }
}
function person_delete_confirm_ajax_submit($form, $form_state) {
  ctools_include('ajax'); ctools_include('modal');

  switch ($form_state['section']) {
    case 'company':
      $message = theme('status_messages');
      $company_form = drupal_get_form('person_company_form', $form_state['acid']);
      $output = theme('person_wrap', array(
        'content' => array(
          'body' => drupal_render($company_form),
          'account' => ext_user_get_user_array($form_state['acid']),
          'menu' => _person_actions($form_state['acid'], $form_state['section']),
          'class' => $form_state['section'],
          'message' => $message ? $message : '',
        )
      ));
      $commands[] = ajax_command_replace('#profile', $output);
      break;
  }

  $commands[] = ctools_modal_command_dismiss();

  return array('#type' => 'ajax', '#commands' => $commands);
}
