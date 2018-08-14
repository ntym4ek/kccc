<?php
/**
 * Created by PhpStorm.
 * User: Михаил
 * Date: 25.08.2016
 * Time: 11:58
 */

// константы действий пользователя, за которые начисляются Баллы
const PREPARATION_REVIEW = 1;
const ORDER_USE = 10;

/**
 * функция, возвращающая контент страницы Компании
 */
function person_main($acid) {
    global $user;

    $output = false;
    
    // вернуть форму только, если свой аккаунт 
    if ($user->uid == $acid) {
        $form = drupal_get_form('person_main_form', $acid);
        $output = render($form);
    }

    return $output;
}

/**
 * форма добавления/редактирования компании
 */
function person_main_form($form, &$form_state, $acid) {
    $user = user_load($acid);

    $form_state['acid'] = $acid;
    $form_state['user'] = $user;
    $form_state['section'] = 'main';
    
    if (isset($form_state['triggering_element']['#name']) && $form_state['triggering_element']['#name'] == 'revalidate') {
        $user->password = t('If required, you may reset your password from: !url', array('!url' => url('user/password', array('absolute' => TRUE))));
        _user_mail_notify('register_no_approval_required', $user);
        drupal_set_message(t('A validation e-mail has been sent to your e-mail address. You will need to follow the instructions in that message in order to gain full access to the site.'));
    }

    // начальные значения
    $surname = $name = $name2 = $phone = '';
    $form_state['photo'] = '';
    $email = $user->mail;
    if ($main = profile2_load_by_user($user, 'main')) {
        $main_wrapper = entity_metadata_wrapper('profile2', $main);

        // photo
        if ($main_wrapper->field_profile_photo->value()) {
            $file = $main_wrapper->field_profile_photo->file->value();
            $form_state['photo'] = $file->fid;
        }

        if (empty($form_state['triggering_element'])) {
            $surname    = $main_wrapper->field_profile_surname->value();
            $name       = $main_wrapper->field_profile_name->value();
            $name2      = $main_wrapper->field_profile_name2->value();
            $phone      = $main_wrapper->field_profile_phone->value();
        }
    } else {
        drupal_set_message('Заполните краткий профиль, чтобы мы могли с Вами познакомиться :)', 'info');
    }


    $form = array(
        '#attributes' => array('class' => array('form-horizontal')),
    );

    $form['markup02'] = array(
        '#markup' => '<div class="row"><div class="profile-left">'
    );

    $form['photo'] = array(
        '#type' => 'managed_file',
        '#description' => t('Upload photo'),
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

    $form['surname'] = array(
        '#type' => 'textfield',
        '#title' => 'Фамилия',
        '#required' => true,
        '#default_value' => $surname,
        '#prefix' => '<div class="profile-right">',
    );
    $form['name'] = array(
        '#type' => 'textfield',
        '#title' => 'Имя',
        '#required' => true,
        '#default_value' => $name,
    );
    $form['name2'] = array(
        '#type' => 'textfield',
        '#title' => 'Отчество',
        '#default_value' => $name2,
    );
    $form['phone'] = array(
        '#type' => 'textfield',
        '#title' => 'Телефон',
        '#default_value' => $phone,
    );
    $form['markup04'] = array(
        '#markup' => '<div class="profile-separator"></div>'
    );

    // заполнить поля для сотрудников
    if (isset($user->roles[STAFF])) {
        $office = '';
        if ($staff = profile2_load_by_user($user, 'staff')) {
            $staff_wrapper = entity_metadata_wrapper('profile2', $staff);

            $office = $staff_wrapper->field_profile_post->value();
        }

        $form['office'] = array(
            '#type' => 'textarea',
            '#title' => 'Должность в Торговом Доме',
            '#required' => true,
            '#rows' => 2,
            '#default_value' => $office,
            '#suffix' => '<div class="profile-separator"></div>',
        );
    }

    $form['markup09'] = array(
        '#type' =>  'item',
        '#title' =>  t('Email'),
        '#markup' =>  '<p class="form-control-static">' . $email . '</p>',
    );

    $form['markup01'] = array(
        '#markup' => '<div class="profile-separator"></div>'
    );

    // панель смены пароля
    $form['old_pass'] = array(
        '#type' => 'textfield',
        '#title' => 'Старый',
        '#value' => '',
        '#prefix' => '<div class="form-item form-item-password form-group"><label class="control-label">' . t('Password') . '</label>'
            . '<p class="form-control-static pass-text"><a class="pass-show btn-link">' . t('change') . '</a></p>'
            . '<div class="password-pane">',
    );
    $form['new_pass'] = array(
        '#type' => 'textfield',
        '#title' => 'Новый',
        '#value' => '',
        '#attributes' => array('placeholder' => 'Не менее 4х символов'),
        '#suffix' => '</div></div>',
    );

    $form['markup05'] = array(
        '#markup' => '<div class="profile-separator"></div>'
    );


    // ссылка на повторный email, если не активирован  http://dev.kccc.ru/toboggan/revalidate/4028
    if (isset($user->roles[STAFF_UNCONFIRMED])) {
        $form['revalidate'] = array(
            '#type' => 'button',
            '#name' => 'revalidate',
            '#value' => t('re-send validation e-mail'),
            '#prefix' => '<div class="form-item form-item-activation form-group">'
                       . '    <label class="control-label">' . t('Activation') . '</label>',
            '#suffix' => '</div><div class="profile-separator"></div>',
            '#ajax' => array(
                'callback' => 'person_main_ajax_callback'
            ),
            '#limit_validation_errors' => array(),
        );
    }

    $form['markup07'] = array(
        '#markup' => '<div class="profile-changes">для принятия всех изменений нажмите "Сохранить"</div>'
    );

    $form['m_submit'] = array(
        '#type' => 'submit',
        '#value' => t('Save'),
        '#prefix' => '<div class="profile-save">',
        '#suffix' => '</div></div></div>',
        '#ajax' => array(
            'callback' => 'person_main_ajax_callback'
        ),
    );

    return $form;
}

/**
 * ajax callback, отрабатывающий события формы
 */
function person_main_ajax_callback(&$form, &$form_state) {
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

    // если ошибка на панели Смены пароля, открыть её
    if (!empty($form_state['pane_error'])) {
        $commands[] = ajax_command_css('.pass-link', array('display' => 'none'));
        $commands[] = ajax_command_html('.pass-text', t('fill fields below'));
        $commands[] = ajax_command_css('.password-pane', array('display' => 'block'));
    }

    return array('#type' => 'ajax', '#commands' => $commands);
}

function person_main_form_validate($form, &$form_state) {
    $user = $form_state['user'];

    if (isset($form_state['triggering_element']['#name']) && $form_state['triggering_element']['#name'] == 'revalidate') return true;

    $form_state['pane_error'] = 0;
    $old_pass = $form_state['input']['old_pass'];
    $form_state['new_pass'] = trim($form_state['input']['new_pass']);
    unset($form_state['input']['old_pass']); unset($form_state['values']['old_pass']);
    unset($form_state['input']['new_pass']); unset($form_state['values']['new_pass']);

    if (empty($form_state['values']['surname'])) { form_set_error('surname', 'Нужно указать фамилию'); return false; }
    if (empty($form_state['values']['name'])) { form_set_error('name', 'Нужно указать имя'); return false; }

    // проверка старого пароля (только при указании нового)
    if (!empty($form_state['new_pass']) || $old_pass) {
        require_once DRUPAL_ROOT . '/' . variable_get('password_inc', 'includes/password.inc');
        if (!$old_pass) {
            $form_state['pane_error'] = 1;
            form_set_error('old_pass', 'Для смены пароля на новый укажите старый пароль'); return false;
        }
        if (!user_check_password($old_pass, $user)) {
            $form_state['pane_error'] = 1;
            form_set_error('old_pass', 'Неверно указан старый пароль'); return false;
        }
        if (drupal_strlen($form_state['new_pass']) < 4) {
            $form_state['pane_error'] = 1;
            form_set_error('new_pass', 'Длина нового пароля должна быть не менее 4 символов'); return false;
        }
    }
}

function person_main_form_submit($form, &$form_state) {
    $user = $form_state['user'];

    if(!$main = profile2_load_by_user($user, 'main')) $main = profile2_create(array('user' => $user->uid, 'type' => 'main'));

    $main_wrapper = entity_metadata_wrapper('profile2', $main);

    // задать значения полей
    $main_wrapper->field_profile_surname->set($form_state['values']['surname']);
    $main_wrapper->field_profile_name->set($form_state['values']['name']);
    $main_wrapper->field_profile_name2->set($form_state['values']['name2']);
    $main_wrapper->field_profile_phone->set($form_state['values']['phone']);
    // логотип
    $old_photo = empty($form_state['photo']) ? 0 : $form_state['photo'];
    if ($old_photo != $form_state['values']['photo']) {
        // удалить старое изображение
        // сначала очистить поле, без этого картинка не меняется на новую
        $main_wrapper->field_profile_photo->set(null);
        $main_wrapper->save();
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
            $dir = "public://images/users/$user->uid/";
            file_prepare_directory($dir, FILE_CREATE_DIRECTORY);
            $file = file_move($file, $dir . $filename, FILE_EXISTS_REPLACE);
            file_save($file);
            file_usage_add($file, 'person', 'profile2', 0);
            $form_state['photo'] = $file->fid;
            $main_wrapper->field_profile_photo->file->set($file);
        }
    }

    $main_wrapper->save();

    // заполнить поля для сотрудников
    if (isset($user->roles[STAFF])) {
        if (!$staff = profile2_load_by_user($user, 'staff')) $staff = profile2_create(array('user' => $user->uid, 'type' => 'staff'));
        $staff_wrapper = entity_metadata_wrapper('profile2', $staff);

        $company = profile2_load(KCCC_COMPANY_PID);
        $staff_wrapper->field_profile_post->set($form_state['values']['office']);
        $staff_wrapper->field_profile_company2->set($company);
        $staff_wrapper->field_profile_company_approved->set(1);
        $staff_wrapper->save();
    }

    // смена пароля
    if (!empty($form_state['new_pass'])) {
        $account = user_load($user->uid);
        $account->pass = $form_state['new_pass'];
        user_save((object)array('uid' => $account->uid), (array)$account);
        // формируем новый токен для формы, чтобы избежать сообщения "The form has become outdated. Copy any unsaved work in the form below and then reload this page."
        $form_state['input']['form_token'] = drupal_get_token($form['#token']);
    }

    $form_state['rebuild'] = true;
    drupal_set_message('Информация сохранена');
}
