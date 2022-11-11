<?php

function before_after_services_resources()
{
  $resources = array(
    'atfield' => array(
      'operations' => array(
        'retrieve' => array(
          'help' => 'Retrieve a record',
          'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'before_after.services'),
          'callback' => '_before_after_resource_retrieve',
          'args' => array(
            array(
              'name' => 'sid',
              'optional' => FALSE,
              'source' => array('path' => 0),
              'type' => 'int',
              'description' => 'The sid of the season to retrieve',
            ),
          ),
          'access callback' => '_drupalgap_resource_access',
          'access callback file' => array(
            'type' => 'inc',
            'module' => 'drupalgap',
            'name' => 'drupalgap.resource',
          ),
          'access arguments' => array('access content'),
          'access arguments append' => TRUE,
        ),
      ),
    ),
  );

  return $resources;
}

function _before_after_resource_retrieve($sid)
{
  return before_after_get_atfield($sid);
}
