<?php

/**
 * @file
 * ext_message.views_default.inc
 */

/**
 * Implements hook_views_default_views().
 */
function ext_message_views_default_views()
{
    $export = array();

    $view = new view();
    $view->name = 'user_subscribes';
    $view->description = '';
    $view->tag = 'message';
    $view->base_table = 'taxonomy_term_data';
    $view->human_name = 'Пользователь | Подписки';
    $view->core = 7;
    $view->api_version = '3.0';
    $view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

    /* Display: Master */
    $handler = $view->new_display('default', 'Master', 'default');
    $handler->display->display_options['title'] = 'Notifications subscriptions';
    $handler->display->display_options['css_class'] = 'view-table';
    $handler->display->display_options['use_more_always'] = FALSE;
    $handler->display->display_options['use_more_text'] = 'ещё';
    $handler->display->display_options['access']['type'] = 'perm';
    $handler->display->display_options['cache']['type'] = 'none';
    $handler->display->display_options['query']['type'] = 'views_query';
    $handler->display->display_options['exposed_form']['type'] = 'basic';
    $handler->display->display_options['exposed_form']['options']['submit_button'] = 'Применить';
    $handler->display->display_options['exposed_form']['options']['reset_button_label'] = 'Сбросить';
    $handler->display->display_options['exposed_form']['options']['exposed_sorts_label'] = 'Сортировать по';
    $handler->display->display_options['exposed_form']['options']['sort_asc_label'] = 'По возрастанию';
    $handler->display->display_options['exposed_form']['options']['sort_desc_label'] = 'По убыванию';
    $handler->display->display_options['pager']['type'] = 'none';
    $handler->display->display_options['style_plugin'] = 'views_bootstrap_table_plugin_style';
    $handler->display->display_options['style_options']['default_row_class'] = FALSE;
    $handler->display->display_options['style_options']['row_class_special'] = FALSE;
    $handler->display->display_options['style_options']['columns'] = array(
        'name' => 'name',
        'ops' => 'ops',
    );
    $handler->display->display_options['style_options']['class'] = '';
    $handler->display->display_options['style_options']['default'] = '-1';
    $handler->display->display_options['style_options']['info'] = array(
        'name' => array(
            'sortable' => 0,
            'default_sort_order' => 'asc',
            'align' => '',
            'separator' => '',
            'empty_column' => 0,
        ),
        'ops' => array(
            'align' => 'views-align-center',
            'separator' => '',
            'empty_column' => 0,
        ),
    );
    $handler->display->display_options['style_options']['override'] = FALSE;
    $handler->display->display_options['style_options']['bootstrap_styles'] = array(
        'striped' => 'striped',
        'bordered' => 'bordered',
        'hover' => 0,
        'condensed' => 0,
    );
    $handler->display->display_options['style_options']['responsive'] = 0;
    /* Связь: Flags: subscribe_subscribes */
    $handler->display->display_options['relationships']['flag_content_rel']['id'] = 'flag_content_rel';
    $handler->display->display_options['relationships']['flag_content_rel']['table'] = 'taxonomy_term_data';
    $handler->display->display_options['relationships']['flag_content_rel']['field'] = 'flag_content_rel';
    $handler->display->display_options['relationships']['flag_content_rel']['required'] = 0;
    $handler->display->display_options['relationships']['flag_content_rel']['flag'] = 'subscribe_subscribes';
    /* Поле: Термин таксономии: Описание термина */
    $handler->display->display_options['fields']['description']['id'] = 'description';
    $handler->display->display_options['fields']['description']['table'] = 'taxonomy_term_data';
    $handler->display->display_options['fields']['description']['field'] = 'description';
    $handler->display->display_options['fields']['description']['label'] = '';
    $handler->display->display_options['fields']['description']['exclude'] = TRUE;
    $handler->display->display_options['fields']['description']['element_label_colon'] = FALSE;
    /* Поле: Термин таксономии: Имя */
    $handler->display->display_options['fields']['name']['id'] = 'name';
    $handler->display->display_options['fields']['name']['table'] = 'taxonomy_term_data';
    $handler->display->display_options['fields']['name']['field'] = 'name';
    $handler->display->display_options['fields']['name']['label'] = 'Subscription';
    $handler->display->display_options['fields']['name']['alter']['alter_text'] = TRUE;
    $handler->display->display_options['fields']['name']['alter']['text'] = '[name]<br />
<span class="color: #aaa; font-size: 13px;">[description]</span>';
    $handler->display->display_options['fields']['name']['alter']['word_boundary'] = FALSE;
    $handler->display->display_options['fields']['name']['alter']['ellipsis'] = FALSE;
    $handler->display->display_options['fields']['name']['element_label_colon'] = FALSE;
    /* Поле: Flags: Flag link */
    $handler->display->display_options['fields']['ops']['id'] = 'ops';
    $handler->display->display_options['fields']['ops']['table'] = 'flagging';
    $handler->display->display_options['fields']['ops']['field'] = 'ops';
    $handler->display->display_options['fields']['ops']['relationship'] = 'flag_content_rel';
    $handler->display->display_options['fields']['ops']['label'] = 'Action';
    $handler->display->display_options['fields']['ops']['element_label_colon'] = FALSE;
    /* Критерий фильтра: Словарь таксономии: Машинное имя */
    $handler->display->display_options['filters']['machine_name']['id'] = 'machine_name';
    $handler->display->display_options['filters']['machine_name']['table'] = 'taxonomy_vocabulary';
    $handler->display->display_options['filters']['machine_name']['field'] = 'machine_name';
    $handler->display->display_options['filters']['machine_name']['value'] = array(
        'subscribes' => 'subscribes',
    );

    /* Display: Page */
    $handler = $view->new_display('page', 'Page', 'page');
    $handler->display->display_options['path'] = 'user/%/subscriptions';
    $handler->display->display_options['menu']['type'] = 'tab';
    $handler->display->display_options['menu']['title'] = 'Subscriptions';
    $handler->display->display_options['menu']['weight'] = '0';
    $handler->display->display_options['menu']['context'] = 0;
    $handler->display->display_options['menu']['context_only_inline'] = 0;


    $export['user_subscribes'] = $view;

    return $export;
}
