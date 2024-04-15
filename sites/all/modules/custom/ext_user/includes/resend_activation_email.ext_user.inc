<?php

function ext_user_resend_activation_email_form($form, &$form_state)
{
  // обёртка для темизации формы
  $form['#prefix'] =  '<div class="user-auth-form-wrapper">';
  $form['#prefix'] .=   '<h1>' . t('Account activation', [], ['context' => 'account']) . '</h1>';
  $form['#prefix'] .=   '<div class="user-links"><a href="' . url('user/login') . '">' . t('Log in') . '</a> ' . t('or') . ' <a href="' . url('user/password') . '">' . t('Password recovery') . '</a></div>';
  $form['#suffix'] = '</div>';

  $form['email'] = [
    '#type' => 'textfield',
    '#title' => t('E-Mail'),
    '#default_value' => $_GET['email'] ?? '',
    '#size' => 60,
    '#required' => TRUE,
  ];

  $form['actions'] = ['#type' => 'actions'];
  $form['actions']['submit'] = [
    '#type' => 'submit',
    '#value' => t('Resend activation e-mail'),
  ];

  return $form;
}
/**
 * Validation callback
 */
function ext_user_resend_activation_email_form_validate($form, &$form_state)
{
  $account = user_load_by_mail($form_state['values']['email']);

  if(!$account) {
    form_error($form['email'], t('Invalid user e-mail'));
  }
}
/**
 * Submit callback for the 'Re-send welcome message'.
 */
function ext_user_resend_activation_email_form_submit($form, &$form_state)
{
  global $language;

  $destination = array();

  if (isset($_GET['destination'])) {
    $destination = drupal_get_destination();
    unset($_GET['destination']);
  }

  $account = user_load_by_mail($form_state['values']['email']);
  $user_register = variable_get('user_register', 2);

  switch ($user_register) {
    case 0:
      $op = 'register_admin_created';
      break;
    case 1:
      $op = 'register_no_approval_required';
      break;
    case 2:
      $op = 'register_pending_approval';
      break;
  }

  $mail = _user_mail_notify($op, $account, $language);
  if (!empty($mail)) {
    watchdog('user', 'Welcome message has been re-sent to %name at %email.', array('%name' => isset($account->realname)? $account->realname : $account->name, '%email' => $account->mail));
    drupal_set_message(t('Welcome message has been re-sent to %name at %email', array('%name' => isset($account->realname)? $account->realname : $account->name, '%email' => $account->mail)));
  } else {
    watchdog('user', 'There was an error re-sending welcome message to %name at %email', array('%name' => isset($account->realname)? $account->realname : $account->name, '%email' => $account->mail));
    drupal_set_message(t('There was an error re-sending welcome message to %name at %email', array('%name' => isset($account->realname)? $account->realname : $account->name, '%email' => $account->mail)), 'error');
  }
  $form_state['redirect'] = $destination;
}

