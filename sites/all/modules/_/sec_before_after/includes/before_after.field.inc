<?php

/**
 * callback ПОЛЯ для представителей
 */
function before_after_field_callback($op = 'list', $fid = 0)
{
    // возвращаемый массив
    $output = [];

    switch ($op) {
        case 'list':

          $output['items']['add'] = theme('pvp_field_teaser', ['field' => []]);

          // поля текущего пользователя
          $query = db_select('node', 'n');
          // для админа вывести все поля
          if (!(user_has_role(ROLE_ADMIN) || user_has_role(ROLE_STAFF_EDITOR))) {
            $query->condition('n.uid', $GLOBALS['user']->uid);
          }
          $query->condition('n.type', 'field');
          $query->fields('n', array('nid'));
          $query->orderBy('n.nid', 'DESC');
          if ($fields = $query->execute()->fetchAll()) {
            foreach ($fields as $field) {
              $field_info = before_after_get_field_info($field->nid);
              $output['items'][$field->nid] = theme('pvp_field_teaser', ['field' => $field_info]);
            }
          }

          $output['menu'] = '';
          $output = theme('before_after_user', array('content' => $output));

          drupal_set_title('Список полей');
          break;
        case 'edit':
            drupal_set_title('Редактировать Поле');
            $output = drupal_get_form('before_after_field_form', $fid);
            break;
        case 'add':
            drupal_set_title('Добавить новое Поле');
            $output = drupal_get_form('before_after_field_form');
            break;
        case 'del':
            drupal_set_title('Удаление');
            $output = drupal_get_form('before_after_del_form', 'field', 0, $fid);
            break;
    }

    // подключить стили
    drupal_add_css(drupal_get_path('module', 'before_after') . '/css/before_after.css');

    return $output;
}

function before_after_field_form($form, &$form_state, $fid = '') {
    if ($fid) $form_state['fid'] = $fid;

    $name = isset($form_state['values']['name']) ? $form_state['values']['name'] : '';
    $farm = isset($form_state['values']['farm']) ? $form_state['values']['farm'] : '';
    $reg_id = empty($form_state['values']['region']) ? '' : $form_state['values']['region'];

    // задать значения при редактировании
    if ($fid) {
        $form_state['fid'] = $fid;
        $field_wrapper = entity_metadata_wrapper('node', $fid);
        $name = $field_wrapper->label();
        $farm = html_entity_decode($field_wrapper->field_f_farm->value());
        $reg_id = $field_wrapper->field_f_region->getIdentifier();
    }

    $form = array(
      'intro' => [
        '#markup' => '<p>"Поле" обозначает конкретный участок земли, на котором в сезон засевается культура.<br>' .
          'Не добавляйте в систему новое Поле, если в следующем сезоне засеваете этот же участок. Просто добавьте новый сезон в уже существующее Поле.</p>'
      ]
    );

    $form['fs1'] = array('#type' => 'fieldset', '#title' => 'Название поля<i></i>');
    $form['fs1']['name'] = array(
        '#type' => 'textfield',
        '#default_value' => $name,
        '#description' => 'Например: <b>Опытное поле №1, Поле №2, Первое поле питомника, КФХВ-003, СО 59га, Аэродром</b> и т.д.<br>' .
          'Это название видно только вам в Личном кабинете.<br>' .
          'НЕ используйте название культуры или хозяйства.',
    );

    // регионы
    $regions = taxonomy_get_tree(29);
    $options_r = array();
    foreach($regions as $region) {
        if (!$reg_id) $reg_id = $region->tid;
        $options_r[$region->tid] = $region->name;
    }
    $form['fs2'] = array('#type' => 'fieldset', '#title' => 'Регион<i>, где расположено поле</i>');
    $form['fs2']['region'] = array(
        '#type' => 'select',
        '#options' => $options_r,
        '#chosen' => true,
        '#default_value' => $reg_id,
    );

    $form['fs3'] = array('#type' => 'fieldset', '#title' => 'Хозяйство<i>, засевающее поле</i>');
    $form['fs3']['farm'] = array(
        '#type' => 'textfield',
        '#default_value' => $farm,
    );

    $form['fs4'] = array('#type' => 'container', '#attributes' => array('class' => array('form-actions')));
    $form['fs4']['submit'] = array(
        '#type' => 'submit',
        '#value' => empty($fid) ? 'Добавить' : 'Сохранить',
        '#attributes' => array('class' => array('pull-right')),
    );
    $form['fs4']['cancel'] = array(
        '#markup' => '<a href="/user/' . $GLOBALS['user']->uid .'/before-after" class="btn btn-link pull-right">Отменить</a>',
        '#attributes' => array('class' => array('btn-link', 'pull-right')),
    );


    return $form;
}

function before_after_field_form_validate(&$form, &$form_state) {
    if (!empty($form_state['submitted'])) {
        if (empty($form_state['values']['name'])) form_set_error('name', 'Задайте наименование поля.');
        if (empty($form_state['values']['farm'])) form_set_error('farm', 'Задайте наименование хозяйства.');
    }
}

function before_after_field_form_submit(&$form, &$form_state) {
    // добавить или загрузить Поле
    if (empty($form_state['fid'])) {
        $node = new stdClass();
        $node->type = 'field';
        node_object_prepare($node);
        $node->language = LANGUAGE_NONE;
    } else {
        $node = node_load($form_state['fid']);
    }

    $node->title                                        = $form_state['values']['name'];
    $node->field_f_farm[LANGUAGE_NONE][0]['value']      = $form_state['values']['farm'];
    $node->field_f_region[LANGUAGE_NONE][0]['tid']      = $form_state['values']['region'];
    node_save($node);

    $form_state['redirect'] = '/user/' . $GLOBALS['user']->uid . '/before-after';
}

function before_after_get_field_info($field_id)
{
  $result = [];

  $field_wr = entity_metadata_wrapper('node', $field_id);
  if ($field_wr->value()) {
    $seasons = [];
    foreach($field_wr->field_f_sowing->getIterator() as $season_wr) {
      $seasons[] = before_after_get_season_info($season_wr->getIdentifier());
    }

    $result = [
      'id' => $field_wr->getIdentifier(),
      'label' => $field_wr->label(),
      'farm' => $field_wr->field_f_farm->value(),
      'region' => [
        'id' => $field_wr->field_f_region->getIdentifier(),
        'label' => $field_wr->field_f_region->label(),
      ],
      'seasons' => $seasons,
      'author'  => ext_user_get_user_info($field_wr->author->getIdentifier())
    ];
  }

  return $result;
}
