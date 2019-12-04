<?php

/**
 * @file
 * ext_message.views_default.inc
 */

/**
 * Implements hook_views_default_views().
 */
function ext_message_got_views_default_views()
{
    $export = array();

    $view = new view();
    $view->name = 'user_notifications';
    $view->description = '';
    $view->tag = 'messages';
    $view->base_table = 'message';
    $view->human_name = 'Пользователь | Уведомления';
    $view->core = 7;
    $view->api_version = '3.0';
    $view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

    /* Display: Master */
    $handler = $view->new_display('default', 'Master', 'default');
    $handler->display->display_options['title'] = 'Пользователь | Уведомления';
    $handler->display->display_options['use_more_always'] = FALSE;
    $handler->display->display_options['use_more_text'] = 'ещё';
    $handler->display->display_options['access']['type'] = 'none';
    $handler->display->display_options['cache']['type'] = 'none';
    $handler->display->display_options['query']['type'] = 'views_query';
    $handler->display->display_options['exposed_form']['type'] = 'basic';
    $handler->display->display_options['exposed_form']['options']['submit_button'] = 'Применить';
    $handler->display->display_options['exposed_form']['options']['reset_button_label'] = 'Сбросить';
    $handler->display->display_options['exposed_form']['options']['exposed_sorts_label'] = 'Сортировать по';
    $handler->display->display_options['exposed_form']['options']['sort_asc_label'] = 'По возрастанию';
    $handler->display->display_options['exposed_form']['options']['sort_desc_label'] = 'По убыванию';
    $handler->display->display_options['pager']['type'] = 'full';
    $handler->display->display_options['pager']['options']['items_per_page'] = '10';
    $handler->display->display_options['pager']['options']['expose']['items_per_page_label'] = 'Элементов на страницу';
    $handler->display->display_options['pager']['options']['expose']['items_per_page_options_all_label'] = '- Все -';
    $handler->display->display_options['pager']['options']['expose']['offset_label'] = 'Пропустить';
    $handler->display->display_options['pager']['options']['tags']['first'] = '« первая';
    $handler->display->display_options['pager']['options']['tags']['previous'] = '‹ предыдущая';
    $handler->display->display_options['pager']['options']['tags']['next'] = 'следующая ›';
    $handler->display->display_options['pager']['options']['tags']['last'] = 'последняя »';
    $handler->display->display_options['style_plugin'] = 'default';
    $handler->display->display_options['style_options']['default_row_class'] = FALSE;
    $handler->display->display_options['style_options']['row_class_special'] = FALSE;
    $handler->display->display_options['style_options']['uses_fields'] = TRUE;
    $handler->display->display_options['row_plugin'] = 'entity';
    $handler->display->display_options['row_options']['view_mode'] = 'notification';
    /* Поведение при отсутствии результатов: Глобальный: Текстовое поле */
    $handler->display->display_options['empty']['area']['id'] = 'area';
    $handler->display->display_options['empty']['area']['table'] = 'views';
    $handler->display->display_options['empty']['area']['field'] = 'area';
    $handler->display->display_options['empty']['area']['label'] = 'Сообщения отсутствуют';
    $handler->display->display_options['empty']['area']['empty'] = TRUE;
    $handler->display->display_options['empty']['area']['content'] = 'Сообщения отсутствуют';
    $handler->display->display_options['empty']['area']['format'] = 'full_html';
    /* Связь: Сообщение: Type */
    $handler->display->display_options['relationships']['type']['id'] = 'type';
    $handler->display->display_options['relationships']['type']['table'] = 'message';
    $handler->display->display_options['relationships']['type']['field'] = 'type';
    $handler->display->display_options['relationships']['type']['required'] = TRUE;
    /* Поле: Сообщение: Render message (Get text) */
    $handler->display->display_options['fields']['message_render']['id'] = 'message_render';
    $handler->display->display_options['fields']['message_render']['table'] = 'message';
    $handler->display->display_options['fields']['message_render']['field'] = 'message_render';
    $handler->display->display_options['fields']['message_render']['label'] = 'Message';
    $handler->display->display_options['fields']['message_render']['element_label_colon'] = FALSE;
    $handler->display->display_options['fields']['message_render']['partials'] = 1;
    $handler->display->display_options['fields']['message_render']['partials_delta'] = '1';
    /* Поле: Сообщение: Timestamp */
    $handler->display->display_options['fields']['timestamp']['id'] = 'timestamp';
    $handler->display->display_options['fields']['timestamp']['table'] = 'message';
    $handler->display->display_options['fields']['timestamp']['field'] = 'timestamp';
    $handler->display->display_options['fields']['timestamp']['label'] = 'Отправлено';
    $handler->display->display_options['fields']['timestamp']['date_format'] = 'date_time';
    $handler->display->display_options['fields']['timestamp']['second_date_format'] = 'long';
    /* Поле: Message Got: Message viewed timestamp */
    $handler->display->display_options['fields']['timestamp_2']['id'] = 'timestamp_2';
    $handler->display->display_options['fields']['timestamp_2']['table'] = 'message_got';
    $handler->display->display_options['fields']['timestamp_2']['field'] = 'timestamp';
    $handler->display->display_options['fields']['timestamp_2']['label'] = 'Viewed';
    $handler->display->display_options['fields']['timestamp_2']['date_format'] = 'date_time';
    $handler->display->display_options['fields']['timestamp_2']['second_date_format'] = 'long';
    /* Критерий сортировки: Сообщение: Timestamp */
    $handler->display->display_options['sorts']['timestamp']['id'] = 'timestamp';
    $handler->display->display_options['sorts']['timestamp']['table'] = 'message';
    $handler->display->display_options['sorts']['timestamp']['field'] = 'timestamp';
    $handler->display->display_options['sorts']['timestamp']['order'] = 'DESC';
    /* Контекстный фильтр: Тип сообщения: Внутренний числовой ID тип сообщения */
    $handler->display->display_options['arguments']['id']['id'] = 'id';
    $handler->display->display_options['arguments']['id']['table'] = 'message_type';
    $handler->display->display_options['arguments']['id']['field'] = 'id';
    $handler->display->display_options['arguments']['id']['relationship'] = 'type';
    $handler->display->display_options['arguments']['id']['default_action'] = 'empty';
    $handler->display->display_options['arguments']['id']['exception']['title'] = 'Все';
    $handler->display->display_options['arguments']['id']['default_argument_type'] = 'fixed';
    $handler->display->display_options['arguments']['id']['summary']['number_of_records'] = '0';
    $handler->display->display_options['arguments']['id']['summary']['format'] = 'default_summary';
    $handler->display->display_options['arguments']['id']['summary_options']['items_per_page'] = '25';
    $handler->display->display_options['arguments']['id']['break_phrase'] = TRUE;
    /* Контекстный фильтр: Message Got: Message viewed timestamp */
    $handler->display->display_options['arguments']['timestamp']['id'] = 'timestamp';
    $handler->display->display_options['arguments']['timestamp']['table'] = 'message_got';
    $handler->display->display_options['arguments']['timestamp']['field'] = 'timestamp';
    $handler->display->display_options['arguments']['timestamp']['exception']['title'] = 'Все';
    $handler->display->display_options['arguments']['timestamp']['default_argument_type'] = 'fixed';
    $handler->display->display_options['arguments']['timestamp']['summary']['number_of_records'] = '0';
    $handler->display->display_options['arguments']['timestamp']['summary']['format'] = 'default_summary';
    $handler->display->display_options['arguments']['timestamp']['summary_options']['items_per_page'] = '25';

    /* Display: Page */
    $handler = $view->new_display('page', 'Page', 'page');
    $handler->display->display_options['path'] = 'user/%/notifications';


    $export['user_notifications'] = $view;

    return $export;
}
