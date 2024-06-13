<?php

function ext_mobile_app_services_resources()
{
  $resources = array(
    'solution' => array(
      'actions' => array(

        'get_solution' => array(
          'help' => 'Retrieve a solution program',
          'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'ext_mobile_app.services'),
          'callback' => '_ext_mobile_app_get_solution',
          'args' => array(
            array(
              'name' => 'params',
              'optional' => FALSE,
              'source' => array('data' => 'params'),
              'type' => 'array',
              'description' => 'Массив параметров программы защиты',
            ),
          ),
          'access callback' => '_drupalgap_resource_access',
          'access callback file' => array('type' => 'inc', 'module' => 'drupalgap', 'name' => 'drupalgap.resource'),
          'access arguments' => array('access content'),
          'access arguments append' => TRUE,
        ),

        'send_request' => array(
          'help' => 'Send a calculated request to manager',
          'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'ext_mobile_app.services'),
          'callback' => '_ext_mobile_app_send_request',
          'args' => array(
            array(
              'name' => 'params',
              'optional' => FALSE,
              'source' => array('data' => 'params'),
              'type' => 'array',
              'description' => 'Данные пользователя и параметры запроса',
            ),
          ),
          'access callback' => '_drupalgap_resource_access',
          'access callback file' => array('type' => 'inc', 'module' => 'drupalgap', 'name' => 'drupalgap.resource'),
          'access arguments' => array('access content'),
          'access arguments append' => TRUE,
        ),

      ),
    ),
  );

  return $resources;
}


/**
 * вернуть рассчитанную программу
 */
function _ext_mobile_app_get_solution($params)
{
  $program = _reglaments_get_protection_system($params);
  $directions = sec_sellers_get_regions('directions');

  return ['program' => $program, 'directions' => $directions];
}

/**
 * отправить запрос с параметрами Программы
 */
function _ext_mobile_app_send_request($params)
{
  agrocalc_send_mails($params);

  return true;
}

