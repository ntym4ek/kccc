<?php

function reglaments_services_resources()
{
    $resources = array(
        'protection-program' => array(
            'operations' => array(
                'retrieve' => array(
                    'help' => 'Retrieve a protection program',
                    'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'before_after.services.inc'),
                    'callback' => '_reglaments_resource_retrieve',
                    'args' => array(
                        array(
                            'name' => 'pid',
                            'optional' => FALSE,
                            'source' => array('path' => 0),
                            'type' => 'int',
                            'description' => 'id программы защиты',
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
        'reglaments' => array(
            'actions' => array(
                'get_protection_system' => array(
                    'help' => 'Retrieve a protection program',
                    'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'reglaments.services.inc'),
                    'callback' => '_reglaments_get_protection_system2',
                    'args' => array(
                        array(
                            'name' => 'parameters',
                            'optional' => FALSE,
                            'source' => array('data' => 'parameters'),
                            'type' => 'array',
                            'description' => 'Массив параметров программы защиты',
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
                'send_request' => array(
                    'help' => 'Send a calculated request to manager',
                    'file' => array('type' => 'inc', 'module' => 'services', 'name' => 'reglaments.services.inc'),
                    'callback' => 'reglaments_send_request',
                    'args' => array(
                        array(
                            'name' => 'parameters',
                            'optional' => FALSE,
                            'source' => array('data' => 'program'),
                            'type' => 'array',
                            'description' => 'Расчитанная программа',
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












// устаревшая версия для приложений версии <=2.0.7
function _reglaments_resource_retrieve($pid)
{
    $result = [];
    // в зависимости от наличия fid выдать список категорий ПЗ или список препаратов заданной категории

    $cultures_ids = [];
    $program_wr = entity_metadata_wrapper('node', $pid);

    // описание программы и ссылка на pdf
    $result['header'] = array(
        'title' => $program_wr->field_pp_cultures[0]->field_growth_stages_set->name->value(),
        'description' => trim(strip_tags($program_wr->body->value->value())),
        'pdf' => $program_wr->field_file_attachments[0]->file->url->value(),
    );

    if (isset($program_wr->field_pp_cultures)) {
        // массив id культур, для которых берем регламенты
        foreach($program_wr->field_pp_cultures->raw() as $culture) {
            $cultures_ids[] = $culture;
        }

        // список регламентов для заданных культур
        if ($cultures_ids) {
            $reglaments = [];

            // массив id регламентов
            $reglaments_ids = [];
            foreach ($cultures_ids as $culture_id) {
                $query = db_select('field_data_field_pd_r_cultures', 'fc');
                $query->fields('fc', array('entity_id'));
                $query->condition('fc.bundle', 'field_pd_reglaments');
                $query->condition('fc.field_pd_r_cultures_target_id', $culture_id);
                $query->leftJoin('field_data_field_pd_reglaments', 'fr', 'fr.field_pd_reglaments_value = fc.entity_id');
                $query->fields('fr', array('entity_id'));
                foreach($query->execute()->fetchAllKeyed() as $rid => $nid) {
                    $reglaments_ids[$rid] = $nid;
                }
            }

//            $time[] = microtime(true);
            if ($reglaments_ids) {
                // массив этапов роста
                // выстроить по порядку и поменять местами значения с ключами
                $stages_arr = array_flip(_get_stages_ordered($program_wr->field_pp_cultures[0]->field_growth_stages_set->field_mc_periods->value()));

                foreach($reglaments_ids as $reglament_id => $preparation_id) {
                    $reglament_wr = entity_metadata_wrapper('field_collection_item', $reglament_id);

                    $weight = $reglament_wr->field_pd_r_function->weight->value();
                    $tid = $reglament_wr->field_pd_r_function->tid->value();
                    // просматриваем регламенты
                    $prep_info = get_product_info($preparation_id);


                    // уникальный ключ этапа Программы защиты - сочетание id препарата и id функции
                    // по этому ключу будет располагаться массив, элементами которого могут быть:
                    // 1. один регламент
                    // 2. один регламент склеенный из нескольких (пересечение этапов применения)
                    // 3. несколько отдельных регламентов (этапы применения не пересекаются)
                    // ключом элемента будет порядковый номер этапа роста (установит порядок регламентов)
                    $stage_key = $preparation_id . '-' . $tid;

                    if ($prep_info['status'] && $reglament_wr->field_pd_rn_period_start->value() && $reglament_wr->field_pd_rn_period_end->value()) {
                        // список категорий
                        if (!isset($result['categories'][$weight])) {
                            $result['categories'][$weight] = array(
                                'tid' => $tid,
                                'weight' => $weight,
                                'name' => $reglament_wr->field_pd_r_function->name->value(),
                                'bkg' => $reglament_wr->field_pd_r_function->field_image_mobile->file->url->value(),
                                'icon' => $reglament_wr->field_pd_r_function->field_icon->file->url->value(),
                            );
                        }

                        // заполняем запись нового регламента
                        // инфо о препарате/смеси
                        $title_arr = $ingredients_arr = $rates_arr = $photo_arr = $units_arr = [];
                        foreach($prep_info['items'] as $prep_item) {
                            $title_arr[] = $prep_item['title'] . ($prep_item['form_short'] ? ', ' . $prep_item['form_short'] : '');
                            $ingredients_arr = array_merge($ingredients_arr, $prep_item['ingredients']);
                            $units_arr[] = $prep_item['unit_short'] . '/' . $prep_item['unit_field'][$tid];
                            $photo_arr[] = image_style_url('thumbnail', $prep_item['photo_url']);
                        }

                        $regl_new = array(
                            'period' => array(
                                'start' => array(
                                    'tid' => $reglament_wr->field_pd_rn_period_start->tid->value(),
                                    'name' => drupal_strtolower($reglament_wr->field_pd_rn_period_start->name->value()),
                                ),
                                'end' => array(
                                    'tid' => $reglament_wr->field_pd_rn_period_end->tid->value(),
                                    'name' => drupal_strtolower($reglament_wr->field_pd_rn_period_end->name->value()),
                                ),
                            ),
                            'preparation' => array(
                                'id' => $preparation_id,
                                'title' => implode(' + ', $title_arr),
                                'type'  => $prep_info['type'],
                                'ingredients' => implode(' + ', $ingredients_arr),
                                'photo' => $photo_arr,
                                'icon' => $prep_info['icon'],
                            ),
//                        'description' => $reglament_wr->field_pd_r_processing->value(),
                        );

                        // так как норма расхода связана с препаратом в БС порядковым номером
                        // а get_product_info возвращает БС в определенном порядке (не по порядковому номеру)
                        // связать нормы с препаратом по полю order
                        foreach($reglament_wr->field_pd_r_prep_rate as $key_r => $rate_wr) {
                            $key = 0;
                            foreach($prep_info as $prep_item) {
                                if ($prep_item['order'] == $key_r) {
                                    $regl_new['preparation']['rates'][$key] = array(
                                        'from'  => (float)$rate_wr->from->value(),
                                        'to'    => (float)$rate_wr->to->value(),
                                        'unit'  => $units_arr[$key_r],
                                    );
                                }
                                $key++;
                            }
                        }

                        // для препаратов кроме смесей
                        // проверить на наличие регламентов на этом этапе ПЗ
                        // и склеить старый и новый регламенты при наличии пересечения.
                        // проверить все регламенты на этапе, в теории возможно неоднократное склеивание
                        if (!empty($reglaments[$tid]['stages'][$stage_key])) {
                            foreach ($reglaments[$tid]['stages'][$stage_key] as $key => $regl_old) {
                                if (_reglaments_merge_crossing_periods($stages_arr, $regl_new, $regl_old)) {
                                    unset($reglaments[$tid]['stages'][$stage_key][$key]);
                                }
                            }
                        }

                        // вычислить длительность периода регламента
                        $regl_new['period']['duration'] = $stages_arr[$regl_new['period']['end']['tid']] - $stages_arr[$regl_new['period']['start']['tid']] + 1;

                        // ключ регламента в массиве этапа ПЗ
                        $regl_key = $stages_arr[$regl_new['period']['start']['tid']];

                        // записываем регламент в этап ПЗ
                        $reglaments[$tid]['stages'][$stage_key][$regl_key] = $regl_new;
                    }
                }
            }
        }
    }

    // формируем итоговый массив
    // массив поделен на категории
    // в категориях этапы содержат элементы с ключами:
    // stages[номер начального этапа роста][длительность этапа роста]
    foreach($result['categories'] as $weight => $value) {
        foreach($reglaments[$value['tid']]['stages'] as $key_s => $stage) {
            foreach($stage as $start => $regl) {
                $result['categories'][$weight]['stages'][$start][$stage[$start]['period']['duration']][$key_s] = $regl;
            }
        }
        ksort($result['categories'][$weight]['stages']);
    }

//    $time[] = (microtime(true) - $time[0]) * 1000;
    return $result;
}


/**
 * функция возвращает true, если к $regl1 присоединен $regl2
 * и false в случае отсутствия изменений
 *
 * устаревшая версия для приложений версии <=2.0.7
 */
function _reglaments_merge_crossing_periods($stages_arr, &$regl1, $regl2)
{
    $r1_start_num   = $stages_arr[$regl1['period']['start']['tid']];
    $r1_end_num     = $stages_arr[$regl1['period']['end']['tid']];
    $r2_start_num   = $stages_arr[$regl2['period']['start']['tid']];
    $r2_end_num     = $stages_arr[$regl2['period']['end']['tid']];

    // если второй регламент пересекается с первым по периоду применения
    // или расположен сразу за или перед ним - склеить
    if ($r1_end_num + 1 >= $r2_start_num && $r1_start_num - 1 <= $r2_end_num) {
        if ($r2_end_num > $r1_end_num) $regl1['period']['end']['tid'] = $regl2['period']['end']['tid'];
        if ($r2_start_num < $r1_start_num) $regl1['period']['start']['tid'] = $regl2['period']['start']['tid'];
        foreach($regl1['preparation']['rates'] as $key => $item) {
            if ($regl2['preparation']['rates'][$key]['from'] < $regl1['preparation']['rates'][$key]['from']) $regl1['preparation']['rates'][$key]['from'] = $regl2['preparation']['rates'][$key]['from'];
            if ($regl2['preparation']['rates'][$key]['to'] > $regl1['preparation']['rates'][$key]['to']) $regl1['preparation']['rates'][$key]['to'] = $regl2['preparation']['rates'][$key]['to'];
        }

        return true;
    }

    return false;
}