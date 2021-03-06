<?php

/**
 * Implements hook_views_data()
 *
 * привязка своей таблицы ко Views
 * https://habr.com/ru/sandbox/74002/
 */
function ext_message_got_views_data()
{
    // Определяем новую группу во Views
    $data['message_got']['table']['group'] = t('Message Got');

    // мы ссылаемся на Сообщения, поэтому сообщаем для отношений
    // что нам надо прилепить в случае их использования
    $data['message_got']['table']['join'] = array(
        // message - значит наши значения будут доступны только при выборке Сообщений
        'message' => array(
            'left_field' => 'mid',
            'field' => 'mid',
        ),
    );

    // timestamp - столбец таблицы, по которому будет выборка
    $data['message_got']['timestamp'] = array(
        'title' => t('Message viewed timestamp'),
        'help' => t('Viewed by user timestamp'),
        // свой обработчик, чтобы выбрать только для текущего пользователя
        // https://knackforge.com/blog/selvam/drupal-7-exposing-pseudo-field-view-custom-table
        'field' => array(
            'handler' => 'ext_message_got_handler_timestamp',
            'click sortable' => FALSE,
        ),
        // свой обработчик, чтобы можно было задать аргумент
        // для вывода всех, прочтенных и непрочтенных по отдельности
        'argument' => array(
            'handler' => 'ext_message_got_views_handler_argument_timestamp',
        ),
    );

    return $data;
}

function ext_message_got_views_handlers()
{
    return array(
        'info' => array(
            'path' => drupal_get_path('module', 'ext_message_got') . '/views',
        ),
        'handlers' => array(
            'ext_message_got_handler_timestamp' => array(
                'parent' => 'views_handler_field_date',
            ),
            'ext_message_got_views_handler_argument_timestamp' => array(
                'parent' => 'views_handler_argument',
            ),
        ),

    );
}