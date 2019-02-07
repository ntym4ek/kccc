<?php

function ext_registration_services_resources()
{
    $resources = array(
        'registration' => array(
            'operations' => array(
                'create' => array(
                    'help' => 'Create a registration',
                    'file' => array('type' => 'inc', 'module' => 'ext_registration', 'name' => 'includes/ext_registration.resource'),
                    'callback' => '_registration_create',
                    'args' => array(
                        array(
                            'name' => 'parameters',
                            'optional' => FALSE,
                            'source' => array('data' => 'registration'),
                            'type' => 'array',
                            'description' => 'Массив с данными регистрации',
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
