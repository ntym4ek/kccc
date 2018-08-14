<?php

function services_mobile_app_services_resources() {
    $resources = array(
        'services_mobile_app_resources' => array(
            'actions' => array(
                'get_shipping_list' => array(
                    'help' => t('Get shipping list with rates.'),
                    'file' => array(
                        'type' => 'inc',
                        'module' => 'services_mobile_app',
                        'name' => 'services_mobile_app.resource',
                    ),
                    'callback' => 'services_mobile_app_get_list',
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
                        'module' => 'services_mobile_app',
                        'name' => 'services_mobile_app.resource',
                    ),
                    'callback' => 'services_mobile_app_add_shipping_line_item',
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
                'set_payment_status' => array(
                    'help' => t('Set payment status for selected order'),
                    'file' => array(
                        'type' => 'inc',
                        'module' => 'services_mobile_app',
                        'name' => 'services_mobile_app.resource',
                    ),
                    'callback' => 'services_mobile_app_set_payment_status',
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
                        array(
                            'name'         => 'status',
                            'type'         => 'int',
                            'description'  => t(' status to pass.'),
                            'source'       => array('data' => 'status'),
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
                'create_ym_transaction' => array(
                    'help' => t('Create YMoney transaction'),
                    'file' => array(
                        'type' => 'inc',
                        'module' => 'services_mobile_app',
                        'name' => 'services_mobile_app.resource',
                    ),
                    'callback' => 'services_mobile_app_create_ym_transaction',
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
                'clear_cart' => array(
                    'help' => t('Clear user cart'),
                    'file' => array(
                        'type' => 'inc',
                        'module' => 'services_mobile_app',
                        'name' => 'services_mobile_app.resource',
                    ),
                    'callback' => 'services_mobile_app_clear_cart',
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