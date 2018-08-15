<?php

/**
 * страница списка Сезонов
 */
function before_after_list_page()
{
    $list = '';

    if ($atf_array = before_after_get_atfield_list(isset($_GET['region'])?$_GET['region']:null, isset($_GET['culture'])?$_GET['culture']:null)) {
        foreach ($atf_array as $atf_item) {
            $list .= theme('before_after_item', array('content' => $atf_item));
        }
    }

    drupal_add_css(drupal_get_path('module', 'before_after') . '/css/before_after.css');

    $filters_html = '';
    if ($filters_form = drupal_get_form('before_after_exposed_filters_form')) {
        ctools_add_js('auto-submit');
        $filters_html = drupal_render($filters_form);
    }

    $html = '<div class="view view-before-after view-list">' . $filters_html;
    // отправляем список
    if ($list) {
        $html .= '<div class="view-content">'
                  . '<div id="views-bootstrap-grid-1" class="views-bootstrap-grid-plugin-style col-sm-offset-1 col-sm-10 col-md-offset-2 col-md-8">'
                    . '<div class="row">'
                        . $list
                    . '</div></div></div></div>';
    }
    // или текст
    else {
        $html .= '<div class="view-empty">'
                . '<div class="col-sm-offset-1 col-sm-10 col-md-offset-2 col-md-8">Записей не найдено</div>'
               . '</div>';
    }

    return $html;
}

/**
 * возвращает список сезонов с возможностью фильтрации по культуре и региону
 *
 * выходные данные отсортированы по дате последнего внесенного замера или обработки
 * и зависят от вида последней внесенной записи
 */
function before_after_get_atfield_list($region = null, $culture = null)
{
    $atf_array = [];

    // список всех сезонов
    $query = db_select('field_data_field_f_sowing', 's');
    if (!empty($region) && $region != 'All') {
        $query->innerJoin('node', 'n', 'n.nid = s.entity_id');
        $query->innerJoin('field_data_field_f_region', 'fr', 'fr.entity_id = n.nid');
        $query->condition('fr.field_f_region_tid', $region);
    }
    if (!empty($culture) && $culture != 'All') {
        $query->innerJoin('field_data_field_f_s_culture', 'fc', 'fc.entity_id = s.field_f_sowing_value');
        $query->condition('fc.field_f_s_culture_target_id', $culture);
    }
    $query->fields('s', array('field_f_sowing_value'));
    $query->orderBy('s.field_f_sowing_value', 'DESC');
    $result_s = $query->execute()->fetchAll();

    if ($result_s) {
        foreach ($result_s as $item_s) {
            $output = array();

            $sid = $item_s->field_f_sowing_value;
            $season_wrapper = entity_metadata_wrapper('field_collection_item', $sid);
            $season = $season_wrapper->value();

            $fid = $season->hostEntityId();
            $field_wrapper = entity_metadata_wrapper('node', $fid);
            $region = $field_wrapper->field_f_region->name->value();

            $farm = $field_wrapper->field_f_farm->value();
            $culture = $season_wrapper->field_f_s_culture->title->value();

            $output['region'] = $region;

            // процессы сезона
            // обработки
            $query = db_select('field_data_field_f_s_processings', 'p');
            $query->condition('p.entity_id', $sid);
            $query->innerJoin('field_data_field_f_s_p_date', 'd', 'd.entity_id = p.field_f_s_processings_value');
            $query->fields('p', array('field_f_s_processings_value'));
            $query->fields('d', array('field_f_s_p_date_value'));
            $result_p = $query->execute()->fetchAll();

            // замеры
            $query = db_select('field_data_field_f_s_measurements', 'm');
            $query->condition('m.entity_id', $sid);
            $query->innerJoin('field_data_field_f_s_m_date', 'd', 'd.entity_id = m.field_f_s_measurements_value');
            $query->fields('m', array('field_f_s_measurements_value'));
            $query->fields('d', array('field_f_s_m_date_value'));
            $result_m = $query->execute()->fetchAll();

            // собрать все процессы сезона
            $processes = array();
            $count_processings = 0;
            foreach($result_p as  $key_p => $item_p) {
                $date = $item_p->field_f_s_p_date_value;
                $processes[$date . 'p' . $key_p]['type'] = 'processing';
                $processes[$date . 'p' . $key_p]['id'] = $item_p->field_f_s_processings_value;
                $processes[$date . 'p' . $key_p]['date'] = $item_p->field_f_s_p_date_value;
                $count_processings++;
            }
            $count_measurements = 0;
            foreach($result_m as  $key_m => $item_m) {
                $date = $item_m->field_f_s_m_date_value;
                $processes[$date . 'm' . $key_m]['type'] = 'measurement';
                $processes[$date . 'm' . $key_m]['id'] = $item_m->field_f_s_measurements_value;
                $processes[$date . 'm' . $key_m]['date'] = $item_m->field_f_s_m_date_value;
                $count_measurements++;
            }


            if ($processes) {
                // отсортировать по возрастанию даты и заменить ключи на цифровые
                ksort($processes);
                $processes = array_values($processes);
                $count_processes = count($processes);

                $process = $processes[$count_processes - 1];

                // составить список препаратов из обработок
                $preps = $preps_w_links = array();
                foreach ($processes as $item_p) {
                    if ($item_p['type'] == 'processing') {
                        $processing_wrapper = entity_metadata_wrapper('field_collection_item', $item_p['id']);
                        $preparation = $processing_wrapper->field_f_s_p_preparation->title->value();
                        $prep_form = $processing_wrapper->field_f_s_p_preparation->field_pd_formulation->field_tax_short_name->value();
                        $nid = $processing_wrapper->field_f_s_p_preparation->nid->value();
                        $preps[$nid] = $preparation . ', ' . $prep_form;
                        $preps_w_links[] = '<a href="' . drupal_get_path_alias('node/' . $processing_wrapper->field_f_s_p_preparation->nid->value()) . '">' . $preparation . ', ' . $prep_form . '</a>';
                    }
                }
                $output['preps_title'] = 'Обработка препаратами: ';
                $output['preps_w_links'] = 'нет данных';
                if ($preps) {
                    $output['preps'] = $preps;
                    $output['preps_w_links'] = implode('; ', $preps_w_links);
                }

                // заголовки
                $output['culture'] = $culture;
                $output['link'] = "/before-after/$sid";
                $output['sid'] = $sid;
                $output['farm_title'] = 'Хозяйство: ';
                $output['farm'] = $farm;
                $output['date'] = date('d.m.Y', $process['date']);

                $output['footer'] = '';
                $output['type'] = $process['type'];
                switch ($process['type']) {
                    // если последним процессом является ОБРАБОТКА
                    case 'processing':
                        $output['culture'] = $season_wrapper->field_f_s_culture->title->value();

                        $processing_wrapper = entity_metadata_wrapper('field_collection_item', $process['id']);
                        $category_tid = $processing_wrapper->field_f_s_p_preparation->field_pd_category[0]->tid->value();
                        $category = 'препаратом';
                        if ($category_tid == AGRO_CATEGORY_HERBICIDES_TID) $category = 'гербицидом';
                        if ($category_tid == AGRO_CATEGORY_FUNGICIDES_TID) $category = 'фунгицидом';
                        if ($category_tid == AGRO_CATEGORY_INCECTICIDES_TID) $category = 'инсектицидом';

                        // фото поля
                        $output['image_thumb'] = $output['image_full'] = '';
                        $image_fid = $processing_wrapper->field_f_s_p_photo->value();
                        if ($image_fid && $image = file_load($image_fid)) {
                            $output['image_thumb'] = image_style_url('news_teaser', $image->uri);
                            $output['image_full'] = file_create_url($image->uri);
                        }

                        // содержимое
                        $output['date'] = date('d.m.Y', $processing_wrapper->field_f_s_p_date->value());
                        $output['time'] = $processing_wrapper->field_f_s_p_time->value();
                        $output['mechanism'] = $processing_wrapper->field_f_s_p_mechanism->value();
                        $output['consumption'] = $processing_wrapper->field_f_s_p_consumption->value();
                        $output['precipitation'] = drupal_strtolower($processing_wrapper->field_f_s_p_precipitation->value());
                        $output['wind'] = $processing_wrapper->field_f_s_p_wind_speed->value();
                        $output['t_day'] = $processing_wrapper->field_f_s_p_temp_day->value();
                        $output['t_night'] = $processing_wrapper->field_f_s_p_temp_night->value();
                        $output['acidity'] = drupal_strtolower($processing_wrapper->field_f_s_p_acidity->name->value());
                        $output['humidity'] = $processing_wrapper->field_f_s_p_humidity->value();

                        // препарат
                        $preparation_wr = $processing_wrapper->field_f_s_p_preparation;
                        $title_arr = get_product_agro_title($preparation_wr->nid->value());
                        $output['preparation'] = $title_arr['title'] . ', ' . $title_arr['formulation'];
                        $output['ingredients'] = $title_arr['ingredients'];
                        $output['prep_link'] = drupal_get_path_alias('node/' . $preparation_wr->nid->value());;

                        $image_prep = $processing_wrapper->field_f_s_p_preparation->field_product[0]->field_p_images[0]->value();
                        $output['image_prep_thumb'] = empty($image_prep['fid']) ? '' : image_style_url('medium', $image_prep['uri']);
                        $output['image_prep_full'] = empty($image_prep['fid']) ? '' : file_create_url($image_prep['uri']);

                        // препарат2
                        $output['preparation2'] = '';
                        if ($processing_wrapper->field_f_s_p_preparation2->value()) {
                            $preparation_wr = $processing_wrapper->field_f_s_p_preparation2;
                            $title_arr = get_product_agro_title($preparation_wr->nid->value());
                            $output['preparation2'] = $title_arr['title'] . ', ' . $title_arr['formulation'];
                            $output['ingredients2'] = $title_arr['ingredients'];
                            $output['prep_link2'] = drupal_get_path_alias('node/' . $preparation_wr->nid->value());;

                            $image_prep = $processing_wrapper->field_f_s_p_preparation2->field_product[0]->field_p_images[0]->value();
                            $output['image_prep_thumb2'] = empty($image_prep['fid']) ? '' : image_style_url('medium', $image_prep['uri']);
                            $output['image_prep_full2'] = empty($image_prep['fid']) ? '' : file_create_url($image_prep['uri']);
                        }

                        $output['footer'] = 'Проведена обработка ' . $category;

                        $atf_array[] = $output;

                        break;

                    // если последним процессом является ЗАМЕР
                    case 'measurement':
                        // является ли ЗАМЕР первым
                        $first = $count_measurements == 1 ? true : false;

                        $measurement_wrapper = entity_metadata_wrapper('field_collection_item', $process['id']);
//                        $phase = $measurement_wrapper->field_f_s_m_phase_mc->name->value();

                        // фото поля
                        $image_fid = $measurement_wrapper->field_f_s_m_foto_field->value();
                        $image = file_load($image_fid);
                        $output['image_alt']    = $image ? '' : '';
                        $output['image_thumb']  = $image ? image_style_url('news_teaser', $image->uri) : '';
                        $output['image_full']   = $image ? file_create_url($image->uri) : '';

                        // содержимое
                        $output['condition_title'] = 'Состояние культуры: ';
                        $output['condition'] = $measurement_wrapper->field_f_s_m_condition_mc->value();


                        // все вредные объекты сезона
                        $arr_ho_all = array();
                        foreach ($season_wrapper->field_f_s_hobjects as $item_ho) {

                            $name = $item_ho->field_f_s_h_hobject->value() ? $item_ho->field_f_s_h_hobject->title->value() : 'Не найден';
                            $dom = $item_ho->field_f_s_h_dominant->value();
                            $arr_ho_all[] = $dom ? $name . ' (доминант)' : $name;
                        }

                        // состояние вредных объектов
                        $arr_ho = array();
                        foreach ($measurement_wrapper->field_f_s_m_condition_ho as $item_ho) {
                            if ($item_ho->field_f_s_m_c_hobject->value()) {
                                $hobject = $item_ho->field_f_s_m_c_hobject->title->value();
                                $percent = $item_ho->field_f_s_m_c_percent->value();
                                if (!$first) $percent = $percent ? ' (-' . $percent . '%)' : '';
                                $arr_ho[] = $hobject . $percent;
                            }
                        }

                        if ($first) {
                            $output['hobjects_title'] = 'Вредные объекты: ';
                            $output['hobjects'] = implode(', ', $arr_ho_all);
                            if (empty($output['hobjects'])) $output['hobjects'] = 'не заданы';
                        } else {
                            $output['hobjects_title'] = 'Состояние вредных объектов: ';
                            $output['hobjects'] = implode(', ', $arr_ho);
                            if (empty($output['hobjects'])) $output['hobjects'] = 'не указано';
                        }

                        $output['comment_title'] = 'Комментарий представителя: ';
                        $output['comment'] = $measurement_wrapper->field_f_s_m_comment->value();

                        // количество дней после предыдущего процесса
                        if ($count_processes == 1) {
                            $footer = 'Cостояние культуры перед обработкой';
                        } else {
                            $diff = _seconds2times($processes[$count_processes - 1]['date'] - $processes[$count_processes - 2]['date']);
                            $footer = $diff['3'] . ' дней после';

                            if ($processes[$count_processes - 2]['type'] == 'processing') $footer .= ' обработки';
                            else                                                            $footer .= ' предыдущего замера';
                        }
                        $output['footer'] = $footer;
                        //                        if ($count_measurements) $output['h3'] .= '<span>' . format_plural($count_measurements, '1 measurement', '@count measurements') . '</span>';
                        //                        if ($count_processings) $output['h3'] .= '<span>' . format_plural($count_processings, '1 processing', '@count processings') . '</span>';

                        $atf_array[] = $output;

                        break;
                }
            }
        }
    }

    return $atf_array;
}

function before_after_exposed_filters_form($form, &$form_state)
{
    $form = array(
        '#attributes' => array('class' => array('ctools-auto-submit-full-form')),
        '#prefix' => '<div class="view-filters">',
        '#suffix' => '</div>',
    );

    $form['c'] = array(
        '#type' => 'container',
        '#attributes' => array('class' => array('views-exposed-form')), // <div class="views-exposed-form"><div class="views-exposed-widgets clearfix">
    );
    $form['c']['c1'] = array(
        '#type' => 'container',
        '#attributes' => array('class' => array('views-exposed-widgets', 'clearfix')), // <div class="views-exposed-form"><div class="">
    );

    // опция Регион
    $region_options = array();
    $query = db_select('taxonomy_term_data', 't');
    $query->condition('t.vid', 29); // словарь 'regions'
    $query->distinct();
    $query->innerJoin('field_data_field_f_region', 'fr', 't.tid = fr.field_f_region_tid');
    $query->innerJoin('node', 'n', 'n.nid = fr.entity_id');

    // если уже выбрана культура, добавить её в условия
    $culture_selected = '';
    if (isset($_POST['culture']) && $_POST['culture'] != 'All') {
        $culture_selected = $_POST['culture'];
    }
    elseif (isset($_GET['culture']) && $_GET['culture'] != 'All') {
        $culture_selected = $_GET['culture'];
    }
    if ($culture_selected) {
        $query->innerJoin('field_data_field_f_sowing', 'fs', 'fs.entity_id = n.nid');
        $query->innerJoin('field_data_field_f_s_culture', 'fc', 'fc.entity_id = fs.field_f_sowing_value');
        $query->condition('fc.field_f_s_culture_target_id', $culture_selected);
    }
    $query->condition('n.type', 'field');
    $query->fields('t', array('tid', 'name'));
    if ($regions = $query->execute()->fetchAll()) {
        foreach ($regions as $region) {
            $region_options[$region->tid] = $region->name;
        }
        asort($region_options);
    }
    $region_options = array('All' => '- Любой -') + $region_options;
    $form['c']['c1']['region'] = array(
        '#type' => 'select',
        '#title' => 'Уточнить по региону',
        '#options' => $region_options,
        '#prefix' => '<div class="col-sm-6">',
        '#suffix' => '</div>',
        '#default_value' => empty($_GET['region']) ? 'All' : $_GET['region'],
    );


    $culture_options = [];
    $query = db_select('node', 'n');
    $query->condition('n.type', 'main_cultures');
    $query->distinct();
    $query->innerJoin('field_data_field_f_s_culture', 'fc', 'fc.field_f_s_culture_target_id = n.nid');

    // если уже выбран регион, добавить его в условия
    $region_selected = '';
    if (isset($_POST['region']) && $_POST['region'] != 'All') {
        $region_selected = $_POST['region'];
    }
    elseif (isset($_GET['region']) && $_GET['region'] != 'All') {
        $region_selected = $_GET['region'];
    }
    if ($region_selected) {
        $query->innerJoin('field_data_field_f_sowing', 'fs', 'fs.field_f_sowing_value = fc.entity_id');
        $query->innerJoin('node', 'nf', 'nf.nid = fs.entity_id');
        $query->innerJoin('field_data_field_f_region', 'fr', 'fr.entity_id = nf.nid');
        $query->condition('fr.field_f_region_tid', $region_selected);
    }
    $query->fields('n', array('nid', 'title'));
    if ($cultures = $query->execute()->fetchAll()) {
        foreach ($cultures as $culture) {
            $culture_options[$culture->nid] = $culture->title;
        }
        asort($culture_options);
    }
    $culture_options = array('All' => '- Любая -') + $culture_options;

    $form['c']['c1']['culture'] = array(
        '#type' => 'select',
        '#title' => 'Уточнить по культуре',
        '#options' => $culture_options,
        '#prefix' => '<div class="col-sm-6">',
        '#suffix' => '</div>',
        '#default_value' => empty($_GET['culture']) ? 'All' : $_GET['culture'],
    );

    $form['c']['c1']['submit'] = array(
        '#type' => 'submit',
        '#value' => t('Submit'),
        '#attributes' => array('class' => array('ctools-use-ajax', 'ctools-auto-submit-click', 'js-hide')),
        '#prefix' => '<div class="col-sm-12">',
        '#suffix' => '</div>',
    );

    return $form;
}

function before_after_exposed_filters_form_submit($form, &$form_state)
{
    $form_state['redirect'] = array(
        'before-after',
        array(
            'query' => array(
                'region' => empty($form_state['values']['region']) ? 'All' : $form_state['values']['region'],
                'culture' => empty($form_state['values']['culture']) ? 'All' : $form_state['values']['culture'],
            ),
        ),
    );
}

/**
 * страница СЕЗОНА
 */
function before_after_page($sid)
{
    $season = before_after_get_atfield($sid);

    drupal_set_title($season['culture']);
    drupal_set_subtitle($season['farm'] . ', ' . $season['region']);

    drupal_add_css(drupal_get_path('module', 'before_after') . '/css/before_after.css');
    drupal_add_js(drupal_get_path('module', 'before_after') . '/js/before_after.js');

    return theme('before_after_page', array('content' => $season));
}

/**
 * возвращает массив с информацией о Сезоне
 * @param $sid
 * @return mixed
 */
function before_after_get_atfield($sid)
{
    $season_wrapper = entity_metadata_wrapper('field_collection_item', $sid);
    $season = $season_wrapper->value();

    $fid = $season->hostEntityId();
    $field_wrapper = entity_metadata_wrapper('node', $fid);
    $field = $field_wrapper->value();

    $uid = $field->uid;
    $user_wrapper = entity_metadata_wrapper('user', $uid);
    $user = $user_wrapper->value();

    $output['farm']     = $field_wrapper->field_f_farm->value();
    $output['culture']  = $season_wrapper->field_f_s_culture->title->value();
    $output['region']   = $field_wrapper->field_f_region->name->value();
    $output['author']   = person_get_user_array($user);

    // user
    $img = 'default_images/ru-default-thickbox_default.jpg';

    if ($profile_main = profile2_load_by_user($user, 'main')) {
        if (isset($profile_main->field_profile_photo['und'][0]['uri'])) $img = $profile_main->field_profile_photo['und'][0]['uri'];
    }
    $output['author_img'] = image_style_url('user_photo', $img);

    // если утвержденный сотрудник вывести регалии
    if ($staff = profile2_load_by_user($user, 'staff')) {
        $staff_wrapper = entity_metadata_wrapper('profile2', $staff);

        if ($staff_wrapper->field_profile_company_approved->value() == 1) {
            $output['author_company'] = person_get_user_company_name($staff_wrapper->field_profile_company2->pid->value());
            $output['author_post'] = $staff_wrapper->field_profile_post->value();
        }
    }


    // процессы сезона
        // обработки
    $query = db_select('field_data_field_f_s_processings', 'p');
    $query->condition('p.entity_id', $sid);
    $query->innerJoin('field_data_field_f_s_p_date', 'd', 'd.entity_id = p.field_f_s_processings_value');
    $query->fields('p', array('field_f_s_processings_value'));
    $query->fields('d', array('field_f_s_p_date_value'));
    $result_p = $query->execute()->fetchAll();

        // замеры
    $query = db_select('field_data_field_f_s_measurements', 'm');
    $query->condition('m.entity_id', $sid);
    $query->innerJoin('field_data_field_f_s_m_date', 'd', 'd.entity_id = m.field_f_s_measurements_value');
    $query->fields('m', array('field_f_s_measurements_value'));
    $query->fields('d', array('field_f_s_m_date_value'));
    $result_m = $query->execute()->fetchAll();

        // собрать все процессы сезона
    $processes = array();
    $date_first_processing = '';
    $count_processings = 0;
    foreach($result_p as $key_p => $item_p) {
        $date = $item_p->field_f_s_p_date_value;
        if (!$date_first_processing) $date_first_processing = $date;
        $processes[$date . 'p' . $key_p]['type'] = 'processing';
        $processes[$date . 'p' . $key_p]['id'] = $item_p->field_f_s_processings_value;
        $processes[$date . 'p' . $key_p]['date'] = $item_p->field_f_s_p_date_value;
        $count_processings++;
    }
    $count_measurements = 0;
    foreach($result_m as $key_m => $item_m) {
        $date = $item_m->field_f_s_m_date_value;
        if (!$date_first_processing) $date_first_processing = $date;
        $processes[$date . 'm' . $key_m]['type'] = 'measurement';
        $processes[$date . 'm' . $key_m]['id'] = $item_m->field_f_s_measurements_value;
        $processes[$date . 'm' . $key_m]['date'] = $item_m->field_f_s_m_date_value;
        $count_measurements++;
    }

        // отсортировать по возрастанию даты и заменить ключи на цифровые
    if ($processes) {
        ksort($processes);
        $processes = array_values($processes);
        $count_processes = count($processes);

        // список вредных объектов сезона
        $output['weeds_d'] = $output['weeds_o'] = $output['pests'] = $output['fungis'] = 0;
        $arr_hobjects = array();
        foreach ($season_wrapper->field_f_s_hobjects as $item_ho) {
            if ($item_ho->field_f_s_h_hobject->value()) {
                $nid = $item_ho->field_f_s_h_hobject->nid->value();
                $arr_hobjects[$nid] = array(
                    'name' => $item_ho->field_f_s_h_hobject->title->value(),
                    'link' => drupal_get_path_alias('node/' . $item_ho->field_f_s_h_hobject->nid->value()),
                    'phase' => $item_ho->field_f_s_h_phase->value(),
                    'dominant' => $item_ho->field_f_s_h_dominant->value() ? $item_ho->field_f_s_h_dominant->value() : 0,
                    'type' => $item_ho->field_f_s_h_hobject->type->value(),
                    'percent' => '-',
                    'photo' => '',
                );

                // посчитать количество каждого вида вредителей
                if ($arr_hobjects[$nid]['type'] == 'weed') {
                    if ($arr_hobjects[$nid]['dominant']) $output['weeds_d']++;
                    else $output['weeds_o']++;
                }
                if ($arr_hobjects[$nid]['type'] == 'pest') $output['pests']++;
                if ($arr_hobjects[$nid]['type'] == 'disease') $output['fungis']++;
            }
        }

        $output['date_last'] = $processes[$count_processes - 1]['date'];


        $measurements = $processings = array();
        $key_m = $key_p = 0;
        foreach ($processes as $process) {
            if ($process['type'] == 'measurement') {
                $measurement_wrapper = entity_metadata_wrapper('field_collection_item', $process['id']);
                $image_f_fid = $measurement_wrapper->field_f_s_m_foto_field->value();
                $image_f = file_load($image_f_fid);
                $measurements[$key_m]['image_field_thumb'] = $image_f ? image_style_url('news_teaser', $image_f->uri) : '';
                $measurements[$key_m]['image_field_full'] = $image_f ? file_create_url($image_f->uri) : '';
                $image_c_fid = $measurement_wrapper->field_f_s_m_foto_culture->value();
                $image_c = file_load($image_c_fid);
                $measurements[$key_m]['image_culture_thumb'] = $image_c ? image_style_url('news_teaser', $image_c->uri) : '';
                $measurements[$key_m]['image_culture_full'] = $image_c ? file_create_url($image_c->uri) : '';
                $measurements[$key_m]['condition'] = drupal_strtolower($measurement_wrapper->field_f_s_m_condition_mc->value());
                $measurements[$key_m]['phase'] = drupal_strtolower($measurement_wrapper->field_f_s_m_phase_mc->name->value());
                $measurements[$key_m]['date'] = date('d.m.Y', $measurement_wrapper->field_f_s_m_date->value());
                $comment = mb_ucfirst(trim($measurement_wrapper->field_f_s_m_comment->value()));
                $comment_short = _cropStr($comment, 150, '... <span class="s1">развернуть</span>');
                $measurements[$key_m]['comment'] = $comment;
                if ($comment != $comment_short) {
                    $measurements[$key_m]['comment_short'] = $comment_short;
                    $measurements[$key_m]['comment'] .= ' <span class="s1">свернуть</span>';
                }

                if ($date_first_processing && $key_m) {
                    $diff = _seconds2times($measurement_wrapper->field_f_s_m_date->value() - $date_first_processing);
                    $measurements[$key_m]['days_after'] = $diff['3'] . ' дней';
                }

                // состояние вредных объектов
                $arr_ho = $arr_hobjects;
                foreach ($measurement_wrapper->field_f_s_m_condition_ho as $item_ho) {
                    if ($item_ho->field_f_s_m_c_hobject->value()) {
                        $nid = $item_ho->field_f_s_m_c_hobject->nid->value();
                        $photo_fid = $item_ho->field_f_s_m_c_foto->value();
                        if (isset($arr_ho[$nid])) {
                            $arr_ho[$nid]['photo'] = '';
                            if ($photo_fid && $photo = file_load($photo_fid)) {
                                $arr_ho[$nid]['photo'] = $photo ? file_create_url($photo->uri) : '';
                            }

                            $arr_ho[$nid]['percent'] = $item_ho->field_f_s_m_c_percent->value() ? $item_ho->field_f_s_m_c_percent->value() : '-';
                        }
                    }
                }

                $measurements[$key_m]['hobjects'] = $arr_ho;

                $key_m++;
            } else {
                $processing_wrapper = entity_metadata_wrapper('field_collection_item', $process['id']);
                $processings[$key_p]['date'] = date('d.m.Y', $processing_wrapper->field_f_s_p_date->value());
                $processings[$key_p]['time'] = $processing_wrapper->field_f_s_p_time->value();
                $processings[$key_p]['mechanism'] = $processing_wrapper->field_f_s_p_mechanism->value();
                $processings[$key_p]['consumption'] = $processing_wrapper->field_f_s_p_consumption->value();
                if ($processing_wrapper->field_f_s_p_preparation2->value()) {
                    $processings[$key_p]['consumption2'] = $processing_wrapper->field_f_s_p_consumption2->value();
                }
                $processings[$key_p]['precipitation'] = drupal_strtolower($processing_wrapper->field_f_s_p_precipitation->value());
                $processings[$key_p]['wind'] = $processing_wrapper->field_f_s_p_wind_speed->value();
                $processings[$key_p]['t_day'] = $processing_wrapper->field_f_s_p_temp_day->value();
                $processings[$key_p]['t_night'] = $processing_wrapper->field_f_s_p_temp_night->value();
                $processings[$key_p]['acidity'] = drupal_strtolower($processing_wrapper->field_f_s_p_acidity->name->value());
                $processings[$key_p]['humidity'] = $processing_wrapper->field_f_s_p_humidity->value();

                $image_fid = $processing_wrapper->field_f_s_p_photo->value();
                $image = file_load($image_fid);
                $processings[$key_p]['image_thumb'] = $image ? image_style_url('news_teaser', $image->uri) : '';
                $processings[$key_p]['image_full'] = $image ? file_create_url($image->uri) : '';

                // препарат
                $preparation_wr = $processing_wrapper->field_f_s_p_preparation;
                $title_arr = get_product_agro_title($preparation_wr->nid->value());
                $processings[$key_p]['preparation'] = $title_arr['title'] . ', ' . $title_arr['formulation'];
                $processings[$key_p]['ingredients'] = $title_arr['ingredients'];
                $processings[$key_p]['prep_link'] = drupal_get_path_alias('node/' . $preparation_wr->nid->value());

                $image_prep = $processing_wrapper->field_f_s_p_preparation->field_product[0]->field_p_images[0]->value();
                $processings[$key_p]['image_prep_thumb'] = empty($image_prep['fid']) ? '' : image_style_url('medium', $image_prep['uri']);
                $processings[$key_p]['image_prep_full'] = empty($image_prep['fid']) ? '' : file_create_url($image_prep['uri']);


                // препарат2
                $processings[$key_p]['preparation2'] = '';
                if ($processing_wrapper->field_f_s_p_preparation2->value()) {
                    $preparation_wr = $processing_wrapper->field_f_s_p_preparation2;
                    $title_arr = get_product_agro_title($preparation_wr->nid->value());
                    $processings[$key_p]['preparation2'] = $title_arr['title'] . ', ' . $title_arr['formulation'];
                    $processings[$key_p]['ingredients2'] = $title_arr['ingredients'];
                    $processings[$key_p]['prep_link2'] = drupal_get_path_alias('node/' . $preparation_wr->nid->value());

                    $image_prep = $processing_wrapper->field_f_s_p_preparation2->field_product[0]->field_p_images[0]->value();
                    $processings[$key_p]['image_prep_thumb2'] = empty($image_prep['fid']) ? '' : image_style_url('medium', $image_prep['uri']);
                    $processings[$key_p]['image_prep_full2'] = empty($image_prep['fid']) ? '' : file_create_url($image_prep['uri']);
                }

                $key_p++;
            }
        }

        $output['measurements'] = $measurements;
        $output['processings'] = $processings;
    }

    return $output;
}