<?php

/**
 * Pre-processes variables for the "node" theme hook.
 *
 * See template for list of available variables.
 *
 * @see node.tpl.php
 *
 * @ingroup theme_preprocess
 */
function ya_preprocess_user_profile(&$vars)
{
  global $user;

  // имя
  $vars['name'] = ext_user_get_user_name($user->uid);
  if (strpos($vars['name'], 'User') === 0) {
    $link = l(t('add name'), '/user/' . $user->uid . '/edit');
    $vars['name'] = $vars['name'] . ' <span>(' . $link . ')</span>';
  }

  $is_own_account = ($user->uid == $user->uid) || $user->uid == 1;

  // фото
  $vars['photo'] = '/sites/default/files/default_images/no_photo.png';
  $vars['add_photo_link'] = l(t('add photo'), '/person/' . $user->uid . '/main/edit');

  $profile2 = profile2_load_by_user($user->uid);
  if (!empty($profile2['main']->pid)) {
    $main_wrapper = entity_metadata_wrapper('profile2', $profile2['main']->pid);
    $main_wrapper->language($user->language);

    // добавить фото
    if ($main_wrapper->field_profile_photo->value()) {
      $file = $main_wrapper->field_profile_photo->file->value();
      $vars['photo'] = image_style_url('profile_photo', $file->uri);
      $vars['add_photo_link'] = '';
    }
  }

  // статус
  $vars['is_online'] = ($user->uid == $user->uid) || (time() - $user->access) / 60 < 5;

  // роли
  $vars['role'] = 'Пользователь';
  if (!empty($profile2['staff'])) {
    $staff_wrapper = entity_metadata_wrapper('profile2', $profile2['staff']->pid);
    if ($staff_wrapper->field_profile_company2->value()) {
      $company_type = $staff_wrapper->field_profile_company2->field_profile_company_type->name->value();
      $company_name = $staff_wrapper->field_profile_company2->field_profile_name->value();
      $vars['role'] .= '<br>Cотрудник ' . $company_type . ' ' . $company_name;
    }
  }

  // счётчики
  // блоги
  $query = db_select('node');
  $query->addExpression('COUNT(*)');
  $query->condition('uid', $user->uid);
  $query->condition('type', 'blog');
  $result = $query->execute()->fetchField();
  $vars['counts'][] = array(
    'title' => t('Blog'),
    'amount' => $result,
    'link' => '/blogs/user/' . $user->uid,
  );
  // отзывы
  $query = db_select('node');
  $query->addExpression('COUNT(*)');
  $query->condition('uid', $user->uid);
  $query->condition('type', 'review');
  $result = $query->execute()->fetchField();
  $vars['counts'][] = array(
    'title' => t('Reviews'),
    'amount' => $result,
    'link' => '/reviews/user/' . $user->uid,
  );

  // До и После
  if (user_access('access before_after edit')) {
    $query = db_select('field_data_field_f_sowing', 's');
    $query->addExpression('COUNT(*)');
    $query->innerJoin('node', 'n', 'n.nid = s.entity_id');
    $query->condition('n.uid', $user->uid);
    $result = $query->execute()->fetchField();

    $vars['counts'][] = array(
      'title' => t('Before & After'),
      'amount' => $result,
      'link' => '/before-after',
    );
  }

  if ($is_own_account) {
    // заказы
    $vars['counts'][] = array(
      'title' => t('Orders'),
      'amount' => ext_user_orders_count($user->uid),
      'link' => "/user/$user->uid/orders",
    );
  }
}
