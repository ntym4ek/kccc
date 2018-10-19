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
                    'callback' => '_reglaments_send_request',
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

function _reglaments_send_request($program)
{
    // отправка письма менеджеру и представителю
    $message = 'Добрый день!<br /><br />';
    $message .= 'В мобильном приложении в разделе Калькулятор сделан новый запрос.<br />';
    $message .= '<br />';
    $message .= '<b>Данные пользователя</b><br />';
    $message .= empty($program['name'])     ? '' : 'Имя: ' . $program['name'] . '<br />';
    $message .= empty($program['phone'])    ? '' : 'Телефон: ' . $program['phone'] . '<br />';
    $message .= empty($program['region'])   ? '' : 'Регион: ' . $program['region'] . '<br />';
    $message .= '<br />';

    $message .= '<b>Поле</b><br />';
    $message .= 'Культура: ' . $program['culture'] . '<br />';
    $message .= 'Фаза: ' . (empty($program['phase'])  ? 'не указана' : $program['phase'] . '<br />');
    $message .= empty($program['area'])     ? '' : 'Площадь посева: ' . $program['area'] . ' га' . '<br />';
    $message .= empty($program['seeding'])     ? '' : 'Норма высева: ' . $program['seeding'] . ' кг/га' . '<br />';
    $message .= '<br />';

    $message .= '<b>Препараты</b><br />';
    foreach($program['preparations'] as $nid => $preparation) {
        if ($preparation['status'] == 'on') {
            $mix = count($preparation['items']) > 1;
            if ($mix) $message .= '<i>Баковая смесь</i><br />';
            foreach ($preparation['items'] as $pid => $item) {
                $message .= $item['title'] . '<br />';
                $message .= '<span style="color: #999;">' . $item['rate'] . ' ' . $item['units'] . ', ' . $item['rate'] * $program['area'] . ' ' . $item['unit'] . ' на сумму ' . number_format($item['amount'] * $program['area'], 0, ',', ' ') . ' руб.</span><br />';
            }
            if (!empty($preparation['hobjects'])) $message .= '<span style="color: #999;">Вредные объекты: ' . $preparation['hobjects'] . '</span><br />';
            $message .= '<br />';
        }
    }
    $message .= '<br />';
    $message .= 'Итого: ' . number_format($program['total'] * $program['area'], 0, ',', ' ') . ' руб.<br /><br />';

    if ($program['hobjects']) {
        $message .= 'Для следующих вредных объектов решения не подобраны:<br />';
        foreach($program['hobjects'] as $hobjects) {
            $message .= '&nbsp;&nbsp;- ' . $hobjects . ';<br />';
        }
    }

    $email = 'marina.shirokova@kccc.ru' . (valid_email_address($program['email']) ? ', ' . $program['email'] : '');
    drupal_mail('system', 'mail', $email, language_default(), array(
        'context' => array(
            'subject' => 'Новый запрос в мобильном приложении',
            'message' => $message,
        ),
    ));
}


/**
 * составляем программу защиты для заданной культуры или списка культур
 * в зависимости от заданных параметров:
 * $parameters['program_id'] или $parameters['culture_id'] - список культур из программы или явно заданная культура
 * $parameters['phase_id'] - фильтр по фазе культуры
 * $parameters['weeds_arr'], $parameters['pests_arr'], $parameters['diseases_arr'] - фильтр по вредным объектам
 * $parameters['dessicants'] - добавить десиканты
 * $parameters['fertilizers'] - добавить удобрения
 *
 * @return array
 *
 */
function _reglaments_get_protection_system2($parameters)
{
    $result = [];
    // в зависимости от наличия fid выдать список категорий ПЗ или список препаратов заданной категории

    $cultures_ids = [];
    if (!empty($parameters['program_id'])) {
        $program_wr = entity_metadata_wrapper('node', $parameters['program_id']);

        if (isset($program_wr->field_pp_cultures)) {
            // массив id культур, для которых берем регламенты
            foreach ($program_wr->field_pp_cultures->raw() as $culture) {
                $cultures_ids[] = $culture;
            }
        }
    } else {
        if (!empty($parameters['culture_id'])) $cultures_ids[] = $parameters['culture_id'];
    }

    // составляем список регламентов для заданных культур
    if ($cultures_ids) {
        $reglaments = [];

        $culture_wr = entity_metadata_wrapper('node', $cultures_ids[0]);

        $phase_name = '';
        if (!empty($parameters['phase_id'])) {
            $phase_wr = entity_metadata_wrapper('taxonomy_term', $parameters['phase_id']);
            $phase_name = $phase_wr->name->value();
        }

        // если ВО не заданы, показать всю программу
        $show_full_program = empty($parameters['weeds_arr']) && empty($parameters['pests_arr']) && empty($parameters['diseases_arr']);

        // описание программы и ссылка на pdf
        $result['header'] = array(
            'title'     => empty($parameters['program_id']) ? $culture_wr->title->value() : $culture_wr->field_growth_stages_set->name->value(),
            'description' => empty($parameters['program_id']) ? 'Комплексная программа защиты и увеличения урожайности культуры' : trim(strip_tags($program_wr->body->value->value())),
            'phase'     => $phase_name,
            'pdf'       => empty($parameters['program_id']) ? '' : $program_wr->field_file_attachments[0]->file->url->value(),
            'area'      => empty($parameters['area']) ? '' : $parameters['area'],
            'seeding'   => empty($parameters['seeding']) ? '' : $parameters['seeding'],
        );

        // массив этапов роста
        // выстроить по порядку и поменять местами значения с ключами
        $stages_arr = array_flip(_get_stages_ordered($culture_wr->field_growth_stages_set->field_mc_periods->value()));

        // массив ВО
        $hobjects_all = [
            AGRO_CATEGORY_HERBICIDES_TID => [],
            AGRO_CATEGORY_INSECTICIDES_TID => [],
            AGRO_CATEGORY_FUNGICIDES_TID => [],
            AGRO_CATEGORY_DISINFECTANTS_TID => [],
        ];
        if (!empty($parameters['weeds_arr'])) {
            $query = db_select('node', 'n');
            $query->condition('n.nid', $parameters['weeds_arr'], 'IN');
            $query->fields('n', array('nid', 'title'));
            $hobjects_all[AGRO_CATEGORY_HERBICIDES_TID] = $query->execute()->fetchAllKeyed();

            $herb_term = entity_metadata_wrapper('taxonomy_term', AGRO_CATEGORY_HERBICIDES_TID);
            $result['categories'][$herb_term->weight->value()] = array(
                'tid' => $herb_term->tid->value(),
                'weight' => $herb_term->weight->value(),
                'name' => $herb_term->name->value(),
                'bkg' => $herb_term->field_image_mobile->file->url->value(),
                'icon' => $herb_term->field_icon->file->url->value(),
            );
        } else $parameters['weeds_arr'] = [];
        if (!empty($parameters['pests_arr'])) {
            $query = db_select('node', 'n');
            $query->condition('n.nid', $parameters['pests_arr'], 'IN');
            $query->fields('n', array('nid', 'title'));
            $hobjects_all[AGRO_CATEGORY_INSECTICIDES_TID] = $query->execute()->fetchAllKeyed();

            $insect_term = entity_metadata_wrapper('taxonomy_term', AGRO_CATEGORY_INSECTICIDES_TID);
            $result['categories'][$insect_term->weight->value()] = array(
                'tid' => $insect_term->tid->value(),
                'weight' => $insect_term->weight->value(),
                'name' => $insect_term->name->value(),
                'bkg' => $insect_term->field_image_mobile->file->url->value(),
                'icon' => $insect_term->field_icon->file->url->value(),
            );

        } else $parameters['pests_arr'] = [];
        if (!empty($parameters['diseases_arr'])) {
            $query = db_select('node', 'n');
            $query->condition('n.nid', $parameters['diseases_arr'], 'IN');
            $query->fields('n', array('nid', 'title'));
            $hobjects_all[AGRO_CATEGORY_FUNGICIDES_TID] = $hobjects_all[AGRO_CATEGORY_DISINFECTANTS_TID] = $query->execute()->fetchAllKeyed();

            $fung_term = entity_metadata_wrapper('taxonomy_term', AGRO_CATEGORY_FUNGICIDES_TID);
            $result['categories'][$fung_term->weight->value()] = array(
                'tid' => $fung_term->tid->value(),
                'weight' => $fung_term->weight->value(),
                'name' => $fung_term->name->value(),
                'bkg' => $fung_term->field_image_mobile->file->url->value(),
                'icon' => $fung_term->field_icon->file->url->value(),
            );
        } else $parameters['diseases_arr'] = [];

        // массив из которого будем вычёркивать ВО для которых есть решения
        $hobjects_missing = $hobjects_all;

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

//        $time[] = microtime(true);
        if ($reglaments_ids) {
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
                    // если заданы ВО, проверить регламенты на соответствие
                    $hobjects = array();

                    $show_category = true;
                    $show_reglament = true;

                    // проверить на соответствие заданной фазе
                    if (!empty($parameters['phase_id'])) {
                        $phase_order = $stages_arr[$parameters['phase_id']];
                        $start_order = $stages_arr[$reglament_wr->field_pd_rn_period_start->tid->value()];
                        $end_order = $stages_arr[$reglament_wr->field_pd_rn_period_end->tid->value()];
                        if ($phase_order < $start_order || $phase_order > $end_order) $show_reglament = false;
                    }

                    switch($reglament_wr->field_pd_r_function->tid->value()) {
                        case AGRO_CATEGORY_HERBICIDES_TID:
                            if (!$show_reglament || !$show_full_program && empty($parameters['weeds_arr'])) $show_category = false;
                            $hobjects = _check_reglament_fits_hobjects($reglament_id, $parameters['weeds_arr'], AGRO_CATEGORY_HERBICIDES_TID);
                            if (!$show_full_program && !$hobjects) $show_reglament = false;
                            if ($show_reglament) foreach($hobjects as $hoid) unset($hobjects_missing[AGRO_CATEGORY_HERBICIDES_TID][$hoid]);

                            break;
                        case AGRO_CATEGORY_INSECTICIDES_TID:
                            if (!$show_reglament || !$show_full_program && empty($parameters['pests_arr'])) $show_category = false;
                            $hobjects = _check_reglament_fits_hobjects($reglament_id, $parameters['pests_arr'], AGRO_CATEGORY_INSECTICIDES_TID);
                            if (!$show_full_program && !$hobjects) $show_reglament = false;
                            if ($show_reglament) foreach($hobjects as $hoid) unset($hobjects_missing[AGRO_CATEGORY_INSECTICIDES_TID][$hoid]);

                            break;
                        case AGRO_CATEGORY_FUNGICIDES_TID:
                        case AGRO_CATEGORY_DISINFECTANTS_TID:
                            if (!$show_reglament || !$show_full_program && empty($parameters['diseases_arr'])) $show_category = false;
                            $hobjects = _check_reglament_fits_hobjects($reglament_id, $parameters['diseases_arr'], AGRO_CATEGORY_FUNGICIDES_TID);
                            if (!$show_full_program && !$hobjects) $show_reglament = false;
                            if ($show_reglament) foreach($hobjects as $hoid) unset($hobjects_missing[AGRO_CATEGORY_FUNGICIDES_TID][$hoid]);

                            break;
                        case AGRO_CATEGORY_DESICCANTS_TID:
                            // не выводить только если явно отключен
                            // если параметр отсутствует - это системы защиты - выводить
                            if (isset($parameters['desiccants']) && !$parameters['desiccants']) $show_reglament = false;
                            // $show_reglament мог быть задан ранее
                            $show_category = $show_reglament;

                            break;
                        case AGRO_CATEGORY_FERT_TID:
                            // не выводить только если явно отключен
                            // если параметр отсутствует - это системы защиты - выводить
                            if (isset($parameters['fertilizers']) && !$parameters['fertilizers']) $show_reglament = false;
                            // $show_reglament мог быть задан ранее
                            $show_category = $show_reglament;

                            break;
                    }

                    // список категорий
                    // категория может быть показана, даже если нет регламентов
                    // чтобы вывести сообщение, что для заданного ВО у нас пока нет препаратов
                    if ($show_category && !isset($result['categories'][$weight])) {
                        $result['categories'][$weight] = array(
                            'tid' => $tid,
                            'weight' => $weight,
                            'name' => $reglament_wr->field_pd_r_function->name->value(),
                            'bkg' => $reglament_wr->field_pd_r_function->field_image_mobile->file->url->value(),
                            'icon' => $reglament_wr->field_pd_r_function->field_icon->file->url->value(),
                        );
                    }

                    if (!$show_reglament) continue;

                    // заполняем запись нового регламента
                    // инфо о препарате/смеси
                    $title_arr = $ingredients_arr = $rates_arr = $photo_arr = $units_arr = $preparations = [];
                    $index = 0;
                    foreach($prep_info['items'] as $prep_item) {
                        $title_arr[] = $prep_item['title'] . ($prep_item['form_short'] ? ', ' . $prep_item['form_short'] : '');
                        $ingredients_arr = array_merge($ingredients_arr, $prep_item['ingredients']);
                        $units_arr[] = $prep_item['unit_short'] . '/' . $prep_item['unit_field'][$tid];
                        $photo_arr[] = image_style_url('thumbnail', $prep_item['photo_url']);

                        $preparations['items'][$index . '-' . $prep_item['id']] = array(
                            'id' => $prep_item['id'],
                            'title' => $prep_item['title'] . ($prep_item['form_short'] ? ', ' . $prep_item['form_short'] : ''),
                            'photo' => image_style_url('thumbnail', $prep_item['photo_url']),
                            'units' => $prep_item['unit_short'] . '/' . $prep_item['unit_field'][$tid],
                            'unit'  => $prep_item['unit_short'],
                            'unit_field'  => $prep_item['unit_field'],
                            'price' => $prep_item['price'],
                            'order' => $prep_item['order'],
                        );
                        $index++;
                    }
                    $preparations['id'] = $preparation_id;
                    $preparations['title'] = implode(' + ', $title_arr);
                    $preparations['type']  = $prep_info['type'];
                    $preparations['icon'] = $prep_info['icon'];
                    $preparations['ingredients'] = implode(' + ', $ingredients_arr);

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
                        'preparations' => $preparations,
                        'hobjects' => $hobjects,
//                        'description' => $reglament_wr->field_pd_r_processing->value(),
                    );

                    // так как норма расхода связана с препаратом в БС порядковым номером
                    // а get_product_info возвращает БС в определенном порядке (не по порядковому номеру)
                    // связать нормы с препаратом по полю order
                    foreach($reglament_wr->field_pd_r_prep_rate as $key_r => $rate_wr) {
                        $index = 0;
                        foreach($prep_info['items'] as $prep_item) {
                            if ($prep_item['order'] == $key_r) {
                                $regl_new['preparations']['items'][$index . '-' . $prep_item['id']]['rate'] = array(
                                    'from'  => (float)$rate_wr->from->value(),
                                    'to'    => (float)$rate_wr->to->value(),
                                    'unit'  => $units_arr[$index],
                                );
                            }
                            $index++;
                        }
                    }

                    // для препаратов кроме смесей
                    // проверить на наличие регламентов на этом этапе ПЗ
                    // и склеить старый и новый регламенты при наличии пересечения.
                    // проверить все регламенты на этапе, в теории возможно неоднократное склеивание
                    if (!empty($reglaments[$tid]['stages'][$stage_key])) {
                        foreach ($reglaments[$tid]['stages'][$stage_key] as $key => $regl_old) {
                            if (_reglaments_merge_crossing_periods2($stages_arr, $regl_new, $regl_old)) {
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

    if (!empty($result['categories'])) {

        // записать в результаты неизлечимые ВО
        foreach ($result['categories'] as $weight => $value) {
            if ($value['tid'] == AGRO_CATEGORY_HERBICIDES_TID && !empty($hobjects_missing[AGRO_CATEGORY_HERBICIDES_TID]))       $result['categories'][$weight]['hobjects'] = implode(', ', $hobjects_missing[AGRO_CATEGORY_HERBICIDES_TID]);
            if ($value['tid'] == AGRO_CATEGORY_INSECTICIDES_TID && !empty($hobjects_missing[AGRO_CATEGORY_INSECTICIDES_TID]))   $result['categories'][$weight]['hobjects'] = implode(', ', $hobjects_missing[AGRO_CATEGORY_INSECTICIDES_TID]);
            if ($value['tid'] == AGRO_CATEGORY_FUNGICIDES_TID && !empty($hobjects_missing[AGRO_CATEGORY_FUNGICIDES_TID]))       $result['categories'][$weight]['hobjects'] = implode(', ', $hobjects_missing[AGRO_CATEGORY_FUNGICIDES_TID]);
            if ($value['tid'] == AGRO_CATEGORY_DISINFECTANTS_TID && !empty($hobjects_missing[AGRO_CATEGORY_FUNGICIDES_TID]))    $result['categories'][$weight]['hobjects'] = implode(', ', $hobjects_missing[AGRO_CATEGORY_FUNGICIDES_TID]);

            // todo о наличии hobjects_missing нужно рапортовать по email
        }

        // формируем итоговый массив
        // массив поделен на категории
        // в категориях этапы содержат элементы с ключами:
        // stages[номер начального этапа роста][длительность этапа роста]
        foreach ($result['categories'] as $weight => $value) {
            if (!empty($reglaments[$value['tid']]['stages'])) {
                foreach ($reglaments[$value['tid']]['stages'] as $key_s => $stage) {
                    $hobjects_names_arr = [];
                    foreach ($stage as $start => $regl) {
                        // преобразовать массив ВО в строку
                        if ($regl['hobjects']) {
                            foreach ($regl['hobjects'] as $ho)
                                $hobjects_names_arr[] = $hobjects_all[$value['tid']][$ho];
                            $regl['hobjects'] = implode(', ', array_unique($hobjects_names_arr));
                        }
                        $result['categories'][$weight]['stages'][$start][$stage[$start]['period']['duration']][$key_s] = $regl;
                    }
                }
                if ($result['categories'][$weight]['stages']) ksort($result['categories'][$weight]['stages']);
            }
        }

    }
//    $time[] = (microtime(true) - $time[0]) * 1000;
    return $result;
}

/**
 * вернуть массив этапов роста культуры в последовательном порядке
 */
function _check_reglament_fits_hobjects($reglament_id, $hobject_ids, $type)
{
    $reglament_wr = entity_metadata_wrapper('field_collection_item', $reglament_id);

    // проверка видов
    $hobjects = array_intersect($reglament_wr->field_pd_a_hobjects->raw(), $hobject_ids);

    switch ($type) {
        case AGRO_CATEGORY_HERBICIDES_TID:
            // получить список родительских категорий регламента
            $weeds_reglament_groups_arr = [];
            foreach($reglament_wr->field_pd_a_weeds_groups as $key_g => $weeds_group_wr) {
                foreach($weeds_group_wr->field_pd_a_wg_class as $weeds_class_wr) {
                    $weeds_reglament_groups_arr[$key_g]['tree'][] = $weeds_class_wr->tid->value();
                }
                foreach($weeds_group_wr->field_pd_a_wg_life_time as $weeds_life_time_wr) {
                    $weeds_reglament_groups_arr[$key_g]['time'][] = $weeds_life_time_wr->tid->value();
                }
            }
            // проверка объекта на вхождение в полученный массив
            foreach($hobject_ids as $hobject_id) {
                $weed_wr = entity_metadata_wrapper('node', $hobject_id);
                foreach($weeds_reglament_groups_arr as $weeds_reglament_group) {
                    // проверить лайфтайм
                    if (isset($weeds_reglament_group['time']) && in_array($weed_wr->field_weed_life_time->tid->value(), $weeds_reglament_group['time']) || !isset($weeds_reglament_group['time'])) {
                        $weed_parent_groups = taxonomy_get_parents_all($weed_wr->field_weed_kind->tid->value());
                        // проверить иерархию
                        foreach ($weed_parent_groups as $weed_parent_group) {
                            if (in_array($weed_parent_group->tid, $weeds_reglament_group['tree'])) $hobjects[] = $hobject_id;
                        }
                    }
                }
            }

            break;

            case AGRO_CATEGORY_INSECTICIDES_TID:
                // получить список родительских категорий регламента
                $pests_reglament_groups_arr = [];
                foreach($reglament_wr->field_pd_a_pests_groups as $pests_group_wr) {
                    $pests_reglament_groups_arr[] = $pests_group_wr->tid->value();
                }
                // проверка категорий
                foreach($hobject_ids as $hobject_id) {
                    $pest_wr = entity_metadata_wrapper('node', $hobject_id);
                    $pest_parent_groups = taxonomy_get_parents_all($pest_wr->field_pest_classificator->tid->value());
                    foreach($pest_parent_groups as $pest_parent_group) {
                        if (in_array($pest_parent_group->tid, $pests_reglament_groups_arr)) $hobjects[] = $hobject_id;
                    }
                }

            break;
    }

    $hobjects = array_unique($hobjects);

    return $hobjects;
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
function _reglaments_merge_crossing_periods2($stages_arr, &$regl1, $regl2)
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
        foreach ($regl1['preparations']['items'] as $key_prep => $preparation) {
            if ($regl2['preparations']['items'][$key_prep]['rate']['from'] < $regl1['preparations']['items'][$key_prep]['rate']['from']) $regl1['preparations']['items'][$key_prep]['rate']['from'] = $regl2['preparations']['items'][$key_prep]['rate']['from'];
            if ($regl2['preparations']['items'][$key_prep]['rate']['to'] > $regl1['preparations']['items'][$key_prep]['rate']['to']) $regl1['preparations']['items'][$key_prep]['rate']['to'] = $regl2['preparations']['items'][$key_prep]['rate']['to'];
        }

        $regl1['hobjects'] = array_merge($regl1['hobjects'], $regl2['hobjects']);

        return true;
    }

    return false;
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