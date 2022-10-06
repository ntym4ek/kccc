<?php

function ext_mobile_app_services_resources()
{
  $resources = array(
    'mobile_app_resources' => array(
      'actions' => array(

        'get_news_for_slider' => array(
          'help' => t('Gets news list for front slider'),
          'file' => array(
            'type' => 'inc',
            'module' => 'ext_mobile_app',
            'name' => 'ext_mobile_app.resource',
          ),
          'callback' => 'ext_mobile_app_get_news_for_slider',
          'args' => array(),
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
