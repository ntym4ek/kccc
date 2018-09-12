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
                        array(
                            'name' => 'fid',
                            'optional' => TRUE,
                            'source' => array('path' => 1),
                            'type' => 'int',
                            'description' => 'id категории препаратов',
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

function _reglaments_resource_retrieve($pid, $fid = NULL)
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
                $query = db_select('field_data_field_pd_r_cultures', 'fc')
                    ->fields('fc', array('entity_id'))
                    ->condition('fc.bundle', 'field_pd_reglaments')
                    ->condition('fc.field_pd_r_cultures_target_id', $culture_id)
                    ->distinct()
                    ->execute();
                $reglaments_ids = array_merge($reglaments_ids, $query->fetchCol());
            }

            if ($reglaments_ids) {
                // массив этапов роста
                // выстроить по порядку и поменять местами значения с ключами
                $stages_arr = array_flip(_get_stages_ordered($program_wr->field_pp_cultures[0]->field_growth_stages_set->field_mc_periods->value()));

                foreach($reglaments_ids as $reglament_id) {
                    $reglament_wr = entity_metadata_wrapper('field_collection_item', $reglament_id);

                    // список категорий
                    $weight = $reglament_wr->field_pd_r_function->weight->value();
                    $tid = $reglament_wr->field_pd_r_function->tid->value();
                    if (!isset($result['categories'][$weight])) {
                        $result['categories'][$weight] = array(
                            'tid' => $tid,
                            'weight' => $weight,
                            'name' => $reglament_wr->field_pd_r_function->name->value(),
                            'bkg' => $reglament_wr->field_pd_r_function->field_image_mobile->file->url->value(),
                            'icon' => $reglament_wr->field_pd_r_function->field_icon->file->url->value(),
                        );
                    }

                    // просматриваем регламенты
                    $preparation_id = $reglament_wr->value()->hostEntity()->nid;
                    $preparation_wr = entity_metadata_wrapper('node', $preparation_id);
                    $prep_info = get_product_agro_title($preparation_id);

                    // уникальный ключ этапа Программы защиты - сочетание id препарата и id функции
                    // по этому ключу будет располагаться массив, элементами которого могут быть:
                    // 1. один регламент
                    // 2. один регламент склеенный из нескольких (пересечение этапов применения)
                    // 3. несколько отдельных регламентов (этапы применения не пересекаются)
                    // ключом элемента будет порядковый номер этапа роста (установит порядок регламентов)
                    $stage_key = $preparation_id . '-' . $tid;

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
                            'title' => empty($prep_info['formulation']) ? $prep_info['title'] : $prep_info['title'] . ', ' . $prep_info['formulation'],
                            'ingredients' => $prep_info['ingredients'],
                            'rate' => array(
                                'from' => (float)$reglament_wr->field_pd_r_prep_rate[0]->from->value(),
                                'to' => (float)$reglament_wr->field_pd_r_prep_rate[0]->to->value(),
                                'unit' => $prep_info['unit_cons'],
                            ),
                            'photo' => image_style_url('thumbnail', $preparation_wr->field_product[0]->field_p_images[0]->value()['uri']),
                            'icon' => $preparation_wr->field_icon->file->url->value(),
                        ),
//                        'description' => $reglament_wr->field_pd_r_processing->value(),
                    );

                    // проверить на наличие регламентов на этом этапе ПЗ
                    // и склеить старый и новый регламенты при наличии пересечения.
                    // проверить все регламенты на этапе, в теории возможно неоднократное склеивание
                    if (!empty($reglaments[$tid]['stages'][$stage_key])) {
                        foreach($reglaments[$tid]['stages'][$stage_key] as $key =>$regl_old) {
                            if (_reglaments_merge_crossing_periods($stages_arr,$regl_new, $regl_old)) {
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


    return $result;
}

/**
 * вернуть массив этапов роста культуры в последовательном порядке
 */
function _get_stages_ordered($stages)
{
    $result = [];
    foreach($stages as $key=>$stage) {
        $result[$key] = $stage->tid;
    }

    return $result;
}

/**
 * функция возвращает true, если к $regl1 присоединен $regl2
 * и false в случае отсутствия изменений
 *
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
        if ($regl2['preparation']['rates']['from'] < $regl1['preparation']['rates']['from']) $regl1['preparation']['rates']['from'] = $regl2['preparation']['rates']['from'];
        if ($regl2['preparation']['rates']['to'] > $regl1['preparation']['rates']['to']) $regl1['preparation']['rates']['to'] = $regl2['preparation']['rates']['to'];

        return true;
    }

    return false;
}