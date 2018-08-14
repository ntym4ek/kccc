<?php
/**
 * Created by PhpStorm.
 * User: Михаил
 * Date: 25.08.2016
 * Time: 11:58
 */


/**
 * функция, возвращающая контент страницы Сотрудник
 */
function person_staff($acid) {
  global $user;

  delBubbles(array('uid' => $user->uid, 'section' => BS_STAFF));

  $output = false;

  // вернуть форму только, если свой аккаунт
  if ($user->uid == $acid) {
      $form = drupal_get_form('person_staff_form', $acid);
      $output = render($form);
  }

  return $output;
}

/**
 * форма добавления/редактирования компании
 */
function person_staff_form($form, &$form_state, $acid) {
    $user = user_load($acid);

    $form_state['acid'] = $acid;
    $form_state['user'] = $user;
    $form_state['section'] = 'staff';

    // начальные значения
    $post = $wphone = $company_id = $approved = $request_sent = $message = $changes ='';
    $own_company_staff = false;
    $photo = '/' . drupal_get_path('module', 'person') . '/images/no_photo.png';
    if ($staff = profile2_load_by_user($user, 'staff')) {
        $staff_wrapper = entity_metadata_wrapper('profile2', $staff);

        $post    = $staff_wrapper->field_profile_post->value();
        $wphone  = $staff_wrapper->field_profile_wphone->value();
        if ($staff_wrapper->field_profile_company2->value()) {
            $company_id = $staff_wrapper->field_profile_company2->pid->value();
        }
        $approved = $staff_wrapper->field_profile_company_approved->value();
        $request_sent = !empty($company_id);

        $own_company_staff = ($own_company = profile2_load_by_user($user, 'company')) ? ($own_company->pid == $company_id) : false;

        // photo
        if ($company_id) {
            $company = profile2_load($company_id);
            $company_wrapper = entity_metadata_wrapper('profile2', $company);
            if ($company_wrapper->field_profile_photo->value()) {
                $file = $company_wrapper->field_profile_photo->file->value();
                $photo = image_style_url('profile_photo', $file->uri);
            }
        }
    }

    if (!$request_sent) {
        $changes =  'заполните все поля и нажмите "Отправить заявку"';
        $message = 'В данном разделе кабинета вы можете выбрать организацию, указать должность и телефон и отправить заявку администратору этой компании на включение вас в список сотрудников.';
    } else {
        if ($approved == -1) $message = 'Ваш запрос отклонён администратором компании.';
        if ($approved ==  0) $message = 'Ваш запрос отправлен и ожидает подтверждения.';
        if ($approved ==  1) {
            if ($own_company_staff) $message = 'Вы автоматически внесены в список сотрудников компании как её администратор.';
            else $message = 'Ваш запрос одобрен, вы внесены в список сотрудников компании.';
        }
    }


    $form['markup02'] = array(
        '#markup' =>  '<div class="profile-left">'
                    . '    <div class="photo">'
                    . '        <img src="' . $photo . '" />'
                    . '    </div>'
                    . '</div>'
                    . '<div class="profile-right">'
    );

    // список организаций
    $options_c = array(
        '0' => '- Выберите организацию -',
    );
    $query = db_select('profile', 'p');
    $query->condition('p.type', 'company');
    $query->fields('p', array('pid'));
    $result_p = $query->execute()->fetchAll();

    if ($result_p) {
        foreach($result_p as $item_p) {
            $profile_wrapper = entity_metadata_wrapper('profile2', $item_p->pid);

            $name = $profile_wrapper->field_profile_name->value();
            $opf = $profile_wrapper->field_profile_company_type->value() ? $profile_wrapper->field_profile_company_type->name->value() : '';
            $options_c[$item_p->pid] = $opf . ' ' . $name;
        }
    }

    $form['company'] = array(
        '#type' => 'select',
        '#title' => 'Организация',
        '#options' => $options_c,
        '#chosen' => true,
        '#required' => true,
        '#default_value' => $company_id,
        '#disabled' => $request_sent,
    );

    $form['post'] = array(
        '#type' => 'textfield',
        '#title' => 'Должность',
        '#required' => true,
        '#default_value' => $post,
        '#disabled' => $request_sent && !$approved,
    );
    $form['wphone'] = array(
        '#type' => 'textfield',
        '#title' => 'Рабочий телефон',
        '#required' => true,
        '#default_value' => $wphone,
        '#disabled' => $request_sent && !$approved,
    );

    $form['markup04'] = array(
        '#markup' => '<div class="profile-separator"></div>'
    );

    $form['markup07'] = array(
        '#markup' => '<div class="profile-changes">' . $changes . '</div></div>'
    );

    $text_s = $request_sent ? t('Cancel request') : t('Send request');
    if ($approved ==  1) $text_s = 'Сохранить изменения';
    $form['s_submit'] = array(
        '#type' => 'submit',
        '#value' => $text_s,
        '#prefix' => '<div class="profile-save">',
        '#suffix' => '</div>',
        '#ajax' => array(
            'callback' => 'person_staff_ajax_callback'
        ),
    );

    $form_state['approved'] = $approved;
    $form_state['request_sent'] = $request_sent;

    if ($message) drupal_set_message($message, 'info');

    return $form;
}

/**
 * ajax callback, отрабатывающий события формы
 */
function person_staff_ajax_callback(&$form, &$form_state) {
    // завернуть контент и меню
    $message = theme('status_messages');
    $output = theme('person_wrap', array(
        'content' => array(
            'body' => drupal_render($form),
            'account' => person_get_user_array($form_state['acid']),
            'menu' => _person_actions($form_state['acid'], $form_state['section']),
            'class' => $form_state['section'],
            'message' => $message ? $message : '',
        )
    ));
    $commands[] = ajax_command_replace('#profile', $output);

    return array('#type' => 'ajax', '#commands' => $commands);
}

function person_staff_form_validate($form, &$form_state) {
    if (empty($form_state['values']['company'])) { form_set_error('post', 'Нужно указать организацию'); return false; }
    if (empty($form_state['values']['post'])) { form_set_error('post', 'Нужно указать должность'); return false; }
    if (empty($form_state['values']['wphone'])) { form_set_error('wphone', 'Нужно указать рабочий телефон'); return false; }
}

function person_staff_form_submit($form, &$form_state) {
    $user = $form_state['user'];

    if ($form_state['approved'] == 1) {
        drupal_set_message('Информация сохранена');
    } elseif ($form_state['request_sent']) {
        $form_state['values']['company'] = null;
        $form_state['values']['post'] = '';
        $form_state['values']['wphone'] = '';
        drupal_set_message('Запрос отменен');
    } else {
      drupal_set_message('Запрос отправлен');

      $company = profile2_load($form_state['values']['company']);
      if ($admin = user_load($company->uid)) {
        drupal_mail('system', 'mail', $admin->mail, language_default(), array(
            'context' => array(
                'subject' => 'Запрос на добавление сотрудника в вашу компанию',
                'message' => 'Пользователь подал заявку для включения его в список сотрудников добавленной вами компании.<br />'
                    . 'Для одобрения заявки перейдите в раздел Моя компания в Личном кабинете на сайте <a href="http://kccc.ru">http://kccc.ru</a>.',
            ),
        ));

        // отправить bubble админу компании
        addBubble($company->uid, 'company', BL_RED, 'Запрос на добавление сотрудника в вашу компанию');
      }
    }

    if (!$staff = profile2_load_by_user($user, 'staff')) $staff = profile2_create(array('user' => $user->uid, 'type' => 'staff'));
    $staff_wrapper = entity_metadata_wrapper('profile2', $staff);

    // удалить запись, если заявка отклонена
    if ($form_state['approved'] == -1) $staff_wrapper->delete();
    else {
        if (!$company = profile2_load($form_state['values']['company'])) $company = null;
        // задать значения полей
        $staff_wrapper->field_profile_post->set($form_state['values']['post']);
        $staff_wrapper->field_profile_wphone->set($form_state['values']['wphone']);
        $staff_wrapper->field_profile_company2->set($company);
        $staff_wrapper->field_profile_company_approved->set((integer)$form_state['approved']);
        $staff_wrapper->save();
    }

    $form_state['rebuild'] = true;
}



/**
 * выбрать пользователей с заданной ролью
 * @param $rids - id роли или array ролей
 * @return array - id пользователей
 */
function _get_users_by_role($rids) {
  $uids = array();

  if (!is_array($rids)) $rids = array($rids);

  $query = db_select('users', 'u');
  $query->fields('u', array('uid', 'status'));
  $query->innerJoin('users_roles', 'ur', 'ur.uid = u.uid');
  $query->condition('u.status', 1);
  $query->condition('ur.rid', $rids, 'IN');
  $result = $query->execute()->fetchAll();

  foreach($result as $item) {
    $uids[] = $item->uid;
  }

  return $uids;
}