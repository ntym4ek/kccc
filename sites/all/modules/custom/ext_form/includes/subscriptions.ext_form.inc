<?php

/**
 * --- Форма подписки на рассылку ----------------------------------------------
 */
function ext_form_subscribe_form($form, $form_state)
{
  $form['#prefix'] = '<div id="subscribe-form-wr">';
  $form['#suffix'] = '</div>';

  // если email был подписан успешно, вывести сообщение
  if (isset($form_state["triggering_element"])) {
    $form['message']['#markup'] = '<p class="success-message">' . t('E-mail %email was successfully subscribed!', ['%email' => $form_state['values']['email']]) . '</p>';
  }
  // или форму подписки
  else {
    $form['email'] = [
      '#type' => 'textfield',
      '#attributes' => ['placeholder' => 'E-mail'],
      '#prefix' => '<div class="form-item input-group">',
      '#weight' => -2,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => '<i class="icon icon-083"></i>',
      '#attributes' => ['class' => ['btn', 'btn-brand']],
      '#theme_wrappers' => ['button_icon'],
      '#suffix' => '</div>',
      '#weight' => -1,
      '#ajax' => [
        'callback' => 'ext_form_subscribe_form_ajax',
        'wrapper' => 'subscribe-form-wr',
      ],
    ];

    $form['notice'] = [
      '#markup' => '<div class="form-item-fz152-agreement">' . t('Submitting this form you agree with <a href="@url" target="_blank" rel="nofollow">personal data processing policy</a>.', ['@url' => url('privacy-policy')]) . '</div>',
      '#weight' => 1,
    ];
  }

  return $form;
}
function ext_form_subscribe_form_ajax($form, $form_state)
{
  return $form;
}
function ext_form_subscribe_form_validate($form, &$form_state)
{
  if (!valid_email_address($form_state['values']['email'])) {
    form_set_error($form_state['values']['email'], t('The entered e-mail address "@email" does not appear valid.', array('@email' => $form_state['values']['email'])));
  }
}
function ext_form_subscribe_form_submit($form, &$form_state)
{
  // проверить наличие Аккаунта,
  // создать Аккаунт и профиль Main, если отсутствует
  $query = db_select('users', 'u')
    ->fields('u', ['uid'])
    ->condition('u.mail', $form_state['values']['email'])
    ->execute();
  if ($result = $query->fetch()) {
    $account = user_load($result->uid);
    $profile_main = profile2_load_by_user($account, 'main');
  } else {
    $new_user = [
      'name' => user_password(), // будет заменено в hook_insert
      'pass' => user_password(),
      'mail' => $form_state['values']['email'],
      'init' => $form_state['values']['email'],
      'status' => 0,
    ];
    $account = user_save(NULL, $new_user);
    $profile_main = profile2_create([
      'type' => 'main',
      'uid' => $account->uid,
    ]);
  }

  if ($profile_main) {
    $profile_main->field_user_subscription_news['und'][0]['value'] = 1;
    profile2_save($profile_main);
  }

  $form_state['rebuild'] = true;
}

/**
 * --- Отписка от рассылки -----------------------------------------------------
 */
function ext_form_unsubscribe_callback()
{
  $mail = empty($_GET['email']) ? '' : $_GET['email'];
  if ($mail && $user = user_load_by_mail($mail)) {
    $form = drupal_get_form('ext_form_unsubscribe_form', $user->uid);
    return drupal_render($form);
  } else {
    drupal_goto('<front>');
  }
}

/**
 * форма Отписки от рассылки
 */
function ext_form_unsubscribe_form($form, $form_state, $uid)
{
  $form['#prefix'] = '<div id="unsubscribe-form-wr" class="user-auth-form-wrapper">';
  $form['#prefix'] .=   '<h1>' . t('Mailing list') . '</h1>';
  $form['#suffix'] = '</div>';

  // если email был подписан успешно, вывести сообщение
  if (isset($form_state["triggering_element"])) {
    $form['message']['#markup'] = '<p class="success-message">' . t('You have unsubscribed from the mailing list.') . '</p>';
  }
  // или форму подписки
  else {
    $form['uid'] = ['#type' => 'hidden', '#value' => $uid];
    $form['message'] = ['#markup' => '<p >' . t('You are going to unsubscribe from our mailing list.') . '</p>'];
    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Unsubscribe'),
      '#attributes' => ['class' => ['btn-brand', 'btn-wide']],
      '#ajax' => [
        'callback' => 'ext_form_unsubscribe_form_ajax',
        'wrapper' => 'unsubscribe-form-wr',
      ],
    ];
  }

  return $form;
}
function ext_form_unsubscribe_form_ajax($form, $form_state)
{
  return $form;
}
function ext_form_unsubscribe_form_submit($form, &$form_state)
{
  $account = user_load($form_state['values']['uid']);
  if ($profile_main = profile2_load_by_user($account, 'main')) {
    $profile_main->field_user_subscription_news['und'][0]['value'] = 0;
    profile2_save($profile_main);
  }

  $form_state['rebuild'] = true;
}
