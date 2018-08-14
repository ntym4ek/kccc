<?php

function services_shipping_services_resources() {
    $resources = array(
        'services_shipping_resources' => array(
            'actions' => array(
                'get_shipping_list' => array(
                    'help' => t('Get shipping list with rates.'),
                    'file' => array(
                        'type' => 'inc',
                        'module' => 'services_shipping',
                        'name' => 'services_shipping.resource',
                    ),
                    'callback' => 'services_shipping_get_list',
                    'args' => array(
                        array(
                            'name'         => 'order_id',
                            'type'         => 'int',
                            'description'  => t(' order id to pass.'),
                            'source'       => array('data' => 'order_id'),
                            'optional'     => FALSE,
                        ),
                    ),
                    'access callback' => '_drupalgap_resource_access',
                    'access callback file' => array(
                        'type' => 'inc',
                        'module' => 'drupalgap',
                        'name' => 'drupalgap.resource',
                    ),
                    'access arguments' => array('access user profiles'),
                    'access arguments append' => TRUE,
                ),
                'add_shipping_line_item' => array(
                    'help' => t('Adds shipping line_item to the order.'),
                    'file' => array(
                        'type' => 'inc',
                        'module' => 'services_shipping',
                        'name' => 'services_shipping.resource',
                    ),
                    'callback' => 'services_shipping_add_shipping_line_item',
                    'args' => array(
                        array(
                            'name'         => 'service_name',
                            'type'         => 'int',
                            'description'  => t(' service name to pass.'),
                            'source'       => array('data' => 'service_name'),
                            'optional'     => FALSE,
                        ),
                        array(
                            'name'         => 'order_id',
                            'type'         => 'int',
                            'description'  => t(' order id to pass.'),
                            'source'       => array('data' => 'order_id'),
                            'optional'     => FALSE,
                        ),
                    ),
                    'access callback' => '_drupalgap_resource_access',
                    'access callback file' => array(
                        'type' => 'inc',
                        'module' => 'drupalgap',
                        'name' => 'drupalgap.resource',
                    ),
                    'access arguments' => array('access user profiles'),
                    'access arguments append' => TRUE,
                ),
            ),
        ),
    );
    return $resources;
}