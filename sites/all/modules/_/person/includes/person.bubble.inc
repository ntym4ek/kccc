<?php

/**
 * ----------------------------------------- Bubbles, Уведомления ------------------------------------------------------
 * про примеру http://www.drupaler.by/lessons/kak-sozdat-sushchnost-v-drupal
 */

/**
 * sections (разделы сайта) для bubble
 */
define('BS_BEFORE_AFTER', 1);
define('BS_COMPANY',      2);
define('BS_STAFF',        3);
define('BS_BLOG',         4);
define('BS_REVIEWS',      5);
define('BS_ORDERS',       6);
define('BS_PERSONELL',    7);
define('BS_ADMIN_ORDERS', 8);
define('BS_CONTACT_US',   9);

/**
 * уровни важности bubble.
 */
define('BL_GREEN',  1);
define('BL_YELLOW', 2);
define('BL_RED',    3);

/**
 * зона действия bubble.
 */
define('BA_WEB',  1);   // отображать только на сайте
define('BA_APP',  2);   // отображать только в приложении
define('BA_BOTH', 3);   // и там и там

/**
 * структура поля data
 *
 * array(
 *   'web' => array(
 *      'link' => '',
 *      'text' => '',
 *   ),
 *   'app' => array(
 *      'link' => '',
 *      'text' => '',
 *   ),
 * );
 */


/**
 * Реализация hook_entity_info()
 */
function person_entity_info() {
  return array(
      'bubble' => array(                                      // Системное имя сущности
          'label' => t('Bubble note'),                 // Человеко-понятное имя сущности
          'entity class' => 'Entity',                         // Класс сущности
          'controller class' => 'EntityAPIController',        // Контроллер сущности
          'base table' => 'bubbles',                          // Таблица, в которой хранится информация об альбомах
          'entity keys' => array(
              'id' => 'bid',                                  // Название поля, в котором располагаются идентификаторы альбомов
          ),
          'admin ui' => array(
              'path' => 'admin/structure/bubbles'             // Путь, по которому будет доступна админка
          ),
          'access callback' => 'bubble_access',               // Имя функции, отвечающей за проверку прав доступа
          'module' => 'person',
      ),
  );
}


/**
 * список bubble в админке
 * на основе http://www.drupaler.by/lessons/kak-sozdat-sushchnost-v-drupal
 */
function person_admin_bubbles_list()
{
    // Формируем сортируемую шапку для таблицы.
    $header = array(
        'recipient' => t('Recipient'),
        'email' => t('Email'),
        'level' => array('data' => t('Level'), 'field' => 'b.level'),
        'section' => array('data' => t('Section'), 'field' => 'b.section'),
        'subject' => array('data' => t('Subject'), 'field' => 'b.subject'),
        'aux' => array('data' => t('Aux'), 'field' => 'b.aux'),
        'created' => array('data' => t('Created'), 'field' => 'b.created', 'sort' => 'desc'),
        'operations' => array('data' => t('Operations')),
    );

    $query = db_select('bubbles', 'b')->extend('PagerDefault')->extend('TableSort');
    $bubble_ids = $query->fields('b', array('bid'))
        ->limit(50)
        ->orderByHeader($header)
        ->execute()
        ->fetchCol();

    $bubbles = bubble_load_multiple($bubble_ids);

    // Подготавливаем список продуктов.
    $destination = drupal_get_destination();
    $rows = array();
    foreach ($bubbles as $bubble) {
        $user = person_get_user_array($bubble->uid);
        $rows[$bubble->bid] = array(
            'recipient' => $user['full_name'],
            'email' => $user['email'],
            'level' => $bubble->level,
            'section' => $bubble->section,
            'subject' => $bubble->subject,
            'aux' => $bubble->aux,
            'created' => format_date($bubble->created, 'date'),
        );

        // Формируем список доступных операций над текущим продуктом.
        $operations = array();
        $operations['delete'] = array(
            'title' => t('delete'),
            'href' => 'admin/structure/bubbles/manage/' . $bubble->bid . '/delete',
            'query' => $destination,
        );

        if (!empty($operations)) {
            // Выводим первую и единственную операцию.
            $link = reset($operations);
            $rows[$bubble->bid]['operations'] = array(
                'data' => array(
                    '#type' => 'link',
                    '#title' => $link['title'],
                    '#href' => $link['href'],
                    '#options' => array('query' => $link['query']),
                ),
            );
        }
    }

    $page = array();
    $page['bubbles'] = array(
        '#theme' => 'table',
        '#header' => $header,
        '#rows' => $rows,
        '#empty' => t('No bubbles available.'),
    );

    $page['pager'] = array('#markup' => theme('pager'));
    return $page;
}

/**
 * Проверка прав доступа
 */
function bubble_access($op, $entity, $account = NULL, $entity_type = 'bubble') {
  return user_access('administer site configuration'); // сущность будет доступна только администратору сайта
}

/**
 * Возвращает bubble по условиям
 */
function bubble_load_multiple($bids = false, $conditions = array(), $reset = FALSE) {
  return entity_load('bubble', $bids, $conditions, $reset);
}


/**
 * возвращает bubble по заданным условиям
 */
function getBubble($conditions = array()) {
    global $user;

    if (!$user->uid && empty($conditions['uid'])) $conditions['uid'] = 0;

    return bubble_load_multiple(false, $conditions);
}

/**
 * возвращает разметку bubble по заданным условиям
 */
function getBubbleHTML($conditions = array(), $with_popup = false) {
  return ($bubbles = getBubble($conditions)) ? theme('bubble', array('bubbles' => $bubbles, 'with_popup' => $with_popup)) : '';
}

/**
 * добавляет bubble
 */
function addBubble($uids = array(), $section = 0, $level = BL_GREEN, $subject = '', $aux = '') {
  if (empty($uids) || empty($section)) return false;

  if (!is_array($uids)) $uids = array($uids);
  foreach ($uids as $uid) {
    $bubble = entity_create('bubble', array('uid' => $uid, 'section' => $section, 'level' => $level, 'subject' => $subject, 'aux' => $aux, 'created' => REQUEST_TIME));
    $bubble->save();
  }
}

/**
 * удаляет bubble
 * uid = 0 для удаления
 */
function delBubbles($conditions = array()) {
  $bids = array();
  $bubbles = getBubble($conditions);
  foreach($bubbles as $bubble) {
    $bids[$bubble->bid] = $bubble->bid;
  }
  entity_delete_multiple('bubble', $bids);
}


/**
 * Processes variables for bubble.tpl.php
 */
function template_preprocess_bubble(&$variables) {
  $bubbles = $variables['bubbles'];

  $variables['counter'] = 0; $level = 1;
  $variables['popup_content'] = '';
  foreach($bubbles as $bubble) {
    $variables['counter']++;
    $level = $level < $bubble->level ? $bubble->level : $level;
    // сформировать попап
    if ($variables['with_popup']) {
      $color = 'c-green';
      if ($bubble->level == 2) $color = 'c-orange';
      if ($bubble->level == 3) $color = 'c-red';
      $url = strpos($bubble->aux, 'url:') === 0 ? str_replace('url:', '', $bubble->aux) : '';
      $row = $url ? '<a href="/' . $url . '">' . $bubble->subject . '</a>' : $bubble->subject;
      $variables['popup_content'] .= '<li class="' . $color . '">' . $row . '</li>';
    }
  }

  if ($variables['popup_content']) $variables['popup_content'] = '<ul>' . $variables['popup_content'] . '</ul>';

  // Делаем поля доступными в качестве переменных для соответствующего языка.
//  field_attach_preprocess('product', $product, $variables['content'], $variables);

  $variables['popup_text'] = t('You have !counter notifications', array('!counter' => $variables['counter']));

  if ($level == BL_GREEN) {
    $variables['classes_array'][] = 'bubble-green';
  }
  elseif ($level == BL_YELLOW) {
    $variables['classes_array'][] = 'bubble-yellow';
  }
  elseif ($level == BL_RED) {
    $variables['classes_array'][] = 'bubble-red';
  }
}


/**
 * ----------------------------------------- хуки для добавления bubble -----------------------------------------------
 */

/**
 * добавление bubble при оформлении Заказа
 */
function person_commerce_checkout_complete($order) {
    $subject = 'Поступил новый заказ №' . $order->order_id;
    $url = 'url:'. 'admin/commerce/orders/' . $order->order_id . '/view';
    addBubble(_get_users_by_role(STAFF_SHOP), BS_ADMIN_ORDERS, BL_YELLOW, $subject, $url);
}

/**
 * добавление bubble при сохранении Замера в До и После
 */
function person_before_after_measurement_save_alter($data) {
    $subject = ($data['op'] == 'create') ? 'Новый замер' : 'Обновлён замер';
    $url = 'url:'. 'before-after/' . $data['sid'];
    addBubble(_get_users_by_role(STAFF_EDITOR), BS_BEFORE_AFTER, BL_YELLOW, $subject, $url);
}


/**
 * hook_page_alter
 */
function person_page_alter($vars) {
    global $user;

    // todo удаление bubbles при удалении сущности к которой он привязан
    $path_alias = drupal_get_path_alias($_GET['q']);

    // сбросить bubbles для открываемой страницы блогов
    if (strpos($path_alias, 'blogs/') === 0) delBubbles(array('aux' => 'url:' . $path_alias));

    // сбросить bubbles для открываемого заказа
    if (strpos($path_alias, 'admin/commerce/orders') === 0) delBubbles(array('aux' => 'url:' . $path_alias));

    // сбросить bubbles для открываемой записи До и После
    if (strpos($path_alias, 'before-after/') === 0) delBubbles(array('aux' => 'url:' . $path_alias));

    // сбросить bubbles для открываемой страницы
    switch ($_GET['q']) {
        case 'support/vacancies/submissions':
            delBubbles(array('uid' => $user->uid, 'section' => BS_PERSONELL));
            break;
        case 'support/contact_us/submissions':
            delBubbles(array('uid' => $user->uid, 'section' => BS_CONTACT_US));
            break;
    }
}


/**
 * обработчик события submit для разных форм
 * добавить bubble при сабмите
 */
function person_bubble_add_form_submit($form, $form_state) {
    $url = '';

    // составить url, если возможно
    switch ($form_state['bubble']['section']) {
        case BS_BLOG:
            $url = $form_state['nid'] ? 'url:'. drupal_get_path_alias('node/' . $form_state['nid']) : '';
            break;
        case BS_PERSONELL:
            $url = 'url:support/vacancies/submissions';
            break;
        case BS_CONTACT_US:
            $url = 'url:support/contact_us/submissions';
            break;
    }

    addBubble(
      $form_state['bubble']['uid'],
      $form_state['bubble']['section'] ? $form_state['bubble']['section'] : 0,
      $form_state['bubble']['level'] ? $form_state['bubble']['level'] : BL_GREEN,
      $form_state['bubble']['subject'] ? $form_state['bubble']['subject'] : '',
        $url
  );
}

/**
 * добавление обработчика на сабмит формы в Вакансиях
 *  - добавить bubble при сабмите
 */
function person_form_contact_vacancy_entityform_edit_form_alter(&$form, &$form_state) {
  // собрать данные для bubble
  $form_state['bubble'] = array(
      'uid' => _get_users_by_role(STAFF_PERSONNEL),
      'section' => BS_PERSONELL,
      'level' => 2,
      'subject' => 'Пользователь заполнил форму в разделе Вакансий'
  );
  // добавить обработчик сабмита
  $form['actions']['submit']['#submit'][] = 'person_bubble_add_form_submit';
}

/**
 * добавление обработчика на сабмит формы Обратная связь
 *  - добавить bubble при сабмите
 */
function person_form_contact_us_entityform_edit_form_alter(&$form, &$form_state) {
  // собрать данные для bubble
  $form_state['bubble'] = array(
      'uid' => _get_users_by_role(STAFF_EDITOR),
      'section' => BS_CONTACT_US,
      'level' => 2,
      'subject' => 'Пользователь заполнил форму обратной связи'
  );
  // добавить обработчик сабмита
  $form['actions']['submit']['#submit'][] = 'person_bubble_add_form_submit';
}

/**
 * добавление обработчика на сабмит формы в Блогах
 *  - добавить bubble при сабмите
 */
function person_form_blog_node_form_alter(&$form, &$form_state) {
  global $user;

  // если запись создана или отредактирована не редактором, создать bubble
  $editors = _get_users_by_role(STAFF_EDITOR);
  if (!in_array($user->uid, $editors)) {
      // собрать данные для bubble
      $form_state['bubble'] = array(
          'uid' => $editors,
          'section' => BS_BLOG,
          'level' => 1,
          'subject' => 'Пользователь создал или отредактировал запись Блога'
      );
      // добавить обработчик сабмита
      $form['actions']['submit']['#submit'][] = 'person_bubble_add_form_submit';
  }
}
