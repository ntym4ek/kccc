<?php

/**
 * страница списка Сезонов
 */
function before_after_list_page()
{
    $list = '';

    if ($atf_array = before_after_get_atfield_list(isset($_GET['region'])?$_GET['region']:null, isset($_GET['culture'])?$_GET['culture']:null)) {
      $index = 0;
      foreach ($atf_array as $atf_item) {
            $list .= theme('before_after_item', array('content' => $atf_item));
            if (++$index % 2 == 0) $list .= '<div class="clearfix visible-sm"></div>';
            if ($index % 3 == 0) $list .= '<div class="clearfix visible-md visible-lg"></div>';
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
        $query->condition('fc.bundle', 'field_f_sowing');
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

            $farm = $field_wrapper->field_f_farm->value();

            $output['region'] = $field_wrapper->field_f_region->label();

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
                      $processing_wr = entity_metadata_wrapper('field_collection_item', $item_p['id']);
                      //
                      foreach ($processing_wr->field_f_s_p_preparations->getIterator() as $prep_wr) {
                        $nid = $prep_wr->field_f_s_p_p_preparation->getIdentifier();
                        $name = $prep_wr->field_f_s_p_p_preparation->label();
                        $prep_form = '';
                        if ($prep_wr->field_f_s_p_p_preparation->getBundle() != 'product_fert') {
                          $prep_form = $prep_wr->field_f_s_p_p_preparation->field_pd_formulation->field_tax_short_name->value();
                        }
                        $preps_w_links[] = '<a href="' . url('node/' . $nid) . '">' . $name . ', ' . $prep_form . '</a>';
                      }

                      // старый вариант
                      if ($processing_wr->field_f_s_p_preparation->value()) {
                        $name = $processing_wr->field_f_s_p_preparation->label();
                        $prep_form = '';
                        if ($processing_wr->field_f_s_p_preparation->getBundle() != 'product_fert') {
                          $prep_form = $processing_wr->field_f_s_p_preparation->field_pd_formulation->field_tax_short_name->value();
                        }
                        $nid = $processing_wr->field_f_s_p_preparation->getIdentifier();
                        $preps[$nid] = $name . ($prep_form ? ', ' . $prep_form : '');
                        $preps_w_links[] = '<a href="' . url('node/' . $nid) . '">' . $name . ', ' . $prep_form . '</a>';
                      }
                    }
                }
                $output['preps_title'] = 'Обработка препаратами: ';
                $output['preps_w_links'] = 'нет данных';
                if ($preps) {
                    $output['preps'] = $preps;
                    $output['preps_w_links'] = implode('; ', $preps_w_links);
                }

                // заголовки
                $output['culture'] = $season_wrapper->field_f_s_culture->title->value() . ($season_wrapper->field_f_s_culture_note->value() ? ' (' . $season_wrapper->field_f_s_culture_note->value() . ')' : '');
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
                        $output['culture'] = $season_wrapper->field_f_s_culture->title->value() . ($season_wrapper->field_f_s_culture_note->value() ? ' (' . $season_wrapper->field_f_s_culture_note->value() . ')' : '');

                        $processing_wr = entity_metadata_wrapper('field_collection_item', $process['id']);
                        $category = 'препаратом';
                        if ($processing_wr->field_f_s_p_preparation->value()) {
                          $category_tid = $processing_wr->field_f_s_p_preparation->field_pd_category[0]->tid->value();
                          if ($category_tid == AGRO_CATEGORY_HERBICIDES_TID) $category = 'гербицидом';
                          if ($category_tid == AGRO_CATEGORY_FUNGICIDES_TID) $category = 'фунгицидом';
                          if ($category_tid == AGRO_CATEGORY_INSECTICIDES_TID) $category = 'инсектицидом';
                        }

                        // фото поля
                        $output['image_thumb'] = $output['image_full'] = '';
                        $image_fid = $processing_wr->field_f_s_p_photo->value();
                        if ($image_fid && $image = file_load($image_fid)) {
                            $output['image_thumb']  = image_style_url('news_teaser', $image->uri);
                            $output['image_full']   = image_style_url('1200_1200', $image->uri);
                        }

                        // содержимое
                        $output['date'] = date('d.m.Y', $processing_wr->field_f_s_p_date->value());
                        $output['time'] = $processing_wr->field_f_s_p_time->value();
                        $output['mechanism'] = $processing_wr->field_f_s_p_mechanism->value();
                        $output['consumption'] = $processing_wr->field_f_s_p_consumption->value();
                        $output['precipitation'] = drupal_strtolower($processing_wr->field_f_s_p_precipitation->value());
                        $output['wind'] = $processing_wr->field_f_s_p_wind_speed->value();
                        $output['t_day'] = $processing_wr->field_f_s_p_temp_day->value();
                        $output['t_night'] = $processing_wr->field_f_s_p_temp_night->value();
                        $output['acidity'] = drupal_strtolower($processing_wr->field_f_s_p_acidity->name->value());
                        $output['humidity'] = $processing_wr->field_f_s_p_humidity->value();

                      // препараты
                      $preparations = [];
                      $titles = [];
                      foreach ($processing_wr->field_f_s_p_preparations->getIterator() as $prep_wr) {
                        $product_info = get_product_info($prep_wr->field_f_s_p_p_preparation->value());
                        $prep_info = $product_info['items'][$prep_wr->field_f_s_p_p_preparation->getIdentifier()];

                        $title = $prep_info['title'] . ($prep_info['form_short'] ? ', ' . $prep_info['form_short'] : '');
                        $preparations[] = [
                          'title' => $title,
                          'ingredients' => implode(' + ', $prep_info['ingredients']),
                          'url' => $prep_info['url'],
                          'image_thumb' => image_style_url('medium', $prep_info['photo_uri']),
                          'image_full' => image_style_url('1200_1200', $prep_info['photo_uri']),
                        ];
                        $titles[] = $title;
                      }

                      // для обратной совместимости со старыми полями препаратов
                      // препарат
                      if ($processing_wr->field_f_s_p_preparation->value()) {
                        $prep_info = array_shift(get_product_info($processing_wr->field_f_s_p_preparation->value())['items']);
                        $title = $prep_info['title'] . ($prep_info['form_short'] ? ', ' . $prep_info['form_short'] : '');
                        $preparations[] = [
                          'title' => $title,
                          'ingredients' => implode(' + ', $prep_info['ingredients']),
                          'url' => $prep_info['url'],
                          'image_thumb' => image_style_url('medium', $prep_info['photo_uri']),
                          'image_full' => image_style_url('1200_1200', $prep_info['photo_uri']),
                        ];
                        $titles[] = $title;
                      }
                      // препарат2
                      if ($processing_wr->field_f_s_p_preparation2->value()) {
                        $prep_info2 = array_shift(get_product_info($processing_wr->field_f_s_p_preparation2->value())['items']);
                        $title2 = $prep_info2['title'] . ($prep_info2['form_short'] ? ', ' . $prep_info2['form_short'] : '');
                        $preparations[] = [
                          'title' => $title2,
                          'ingredients' => implode(' + ', $prep_info2['ingredients']),
                          'url' => $prep_info2['url'],
                          'nid' => $prep_info2['id'],
                          'image_thumb' => image_style_url('medium', $prep_info2['photo_uri']),
                          'image_full' => image_style_url('1200_1200', $prep_info2['photo_uri']),
                        ];
                        $titles[] = $title2;
                      }

                      $output['preparations'] = $preparations;
                      $output['title'] = implode('<br/>', $titles);


//                      $preparation = array_shift(get_product_info($processing_wr->field_f_s_p_preparation->value())['items']);
//                        $output['preparation'] = $preparation['title'] . ', ' . $preparation['form_short'];
//                        $output['ingredients'] = implode(' + ', $preparation['ingredients']);
//                        $output['prep_link'] = drupal_get_path_alias('node/' . $preparation['id']);
//
//                        $image_prep = $processing_wr->field_f_s_p_preparation->field_product[0]->field_p_images[0]->value();
//                        $output['image_prep_thumb'] = empty($image_prep['fid']) ? '' : image_style_url('medium', $image_prep['uri']);
//                        $output['image_prep_full'] = empty($image_prep['fid']) ? '' : file_create_url($image_prep['uri']);
//
//                        // препарат2
//                        $output['preparation2'] = '';
//                        if ($processing_wr->field_f_s_p_preparation2->value()) {
//                            $preparation = array_shift(get_product_info($processing_wr->field_f_s_p_preparation2->value())['items']);
//                            $output['preparation2'] = $preparation['title'] . ', ' . $preparation['form_short'];
//                            $output['ingredients2'] = implode(' + ', $preparation['ingredients']);
//                            $output['prep_link2'] = drupal_get_path_alias('node/' . $preparation['id']);
//
//                            $image_prep = $processing_wr->field_f_s_p_preparation2->field_product[0]->field_p_images[0]->value();
//                            $output['image_prep_thumb2'] = empty($image_prep['fid']) ? '' : image_style_url('medium', $image_prep['uri']);
//                            $output['image_prep_full2'] = empty($image_prep['fid']) ? '' : file_create_url($image_prep['uri']);
//                        }

                        $output['footer'] = 'Проведена обработка ' . $category;

                        $atf_array[$process['date'] . $sid] = $output;

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
                        $output['image_alt']    = 'Контрольное фото поля - ООО ТД Кирово-Чепеццкая Химическая Компания';
                        $output['image_thumb']  = $image ? image_style_url('news_teaser', $image->uri) : '';
                        $output['image_full']   = $image ? image_style_url('1200_1200', $image->uri) : '';

                        // содержимое
                        $output['condition_title'] = '';
                        $output['condition'] = '';
                        if ($measurement_wrapper->field_f_s_m_condition_mc->value()) {
                            $output['condition_title'] = 'Состояние культуры: ';
                            $output['condition'] = $measurement_wrapper->field_f_s_m_condition_mc->value();
                        }


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
                            $footer = 'Культура перед обработкой';
                        } else {
                            $diff = _seconds2times($processes[$count_processes - 1]['date'] - $processes[$count_processes - 2]['date']);
//                            $footer = $diff['3'] . ' дней после';
                            $footer = format_plural($diff['3'], '@count day', '@count days') .  ' ' . t('after', [], ['context' => 'B&A']);

                            if ($processes[$count_processes - 2]['type'] == 'processing') $footer .= ' ' . t('processing', [], ['context' => 'B&A']);
                            else                                                            $footer .= ' ' . t('last control', [], ['context' => 'B&A']);
                        }
                        $output['footer'] = $footer;
                        //                        if ($count_measurements) $output['h3'] .= '<span>' . format_plural($count_measurements, '1 measurement', '@count measurements') . '</span>';
                        //                        if ($count_processings) $output['h3'] .= '<span>' . format_plural($count_processings, '1 processing', '@count processings') . '</span>';

                        $atf_array[$process['date'] . $sid] = $output;

                        break;
                }
            }
        }
    }

    // сортировка по дате процесса
    krsort($atf_array);

    return $atf_array;
}


function _before_after_filters($rid = null, $cid = null)
{
    $rid = (int)$rid;
    $cid = (int)$cid;

    // опция Регион
    $query = db_select('taxonomy_term_data', 't');
    $query->condition('t.vid', 29); // словарь 'regions'
    $query->distinct();
    $query->innerJoin('field_data_field_f_region', 'fr', 't.tid = fr.field_f_region_tid');
    $query->innerJoin('node', 'n', 'n.nid = fr.entity_id');

    // если уже выбрана культура, добавить её в условия
    if ($cid) {
        $query->innerJoin('field_data_field_f_sowing', 'fs', 'fs.entity_id = n.nid');
        $query->innerJoin('field_data_field_f_s_culture', 'fc', 'fc.entity_id = fs.field_f_sowing_value');
        $query->condition('fc.field_f_s_culture_target_id', $cid);
    }
    $query->condition('n.type', 'field');
    $query->fields('t', array('tid', 'name'));
    $region_options = $query->execute()->fetchAllKeyed();
    asort($region_options);

    $query = db_select('node', 'n');
    $query->condition('n.type', 'main_cultures');
    $query->distinct();
    $query->innerJoin('field_data_field_f_s_culture', 'fc', 'fc.field_f_s_culture_target_id = n.nid');
    $query->condition('fc.bundle', 'field_f_sowing');
    $query->orderBy('n.title', 'ASC');
    $query->fields('n', array('nid', 'title'));

    // если уже выбран регион, добавить его в условия
    if ($rid) {
        $query->innerJoin('field_data_field_f_sowing', 'fs', 'fs.field_f_sowing_value = fc.entity_id');
        $query->innerJoin('node', 'nf', 'nf.nid = fs.entity_id');
        $query->innerJoin('field_data_field_f_region', 'fr', 'fr.entity_id = nf.nid');
        $query->condition('fr.field_f_region_tid', $rid);
    }
    $culture_options = $query->execute()->fetchAllKeyed();
    asort($culture_options);

    return [
        'regions' => $region_options,
        'cultures' => $culture_options,
    ];
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

    $culture_selected = null;
    if (isset($_POST['culture']) && $_POST['culture'] != 'All') {
        $culture_selected = $_POST['culture'];
    }
    elseif (isset($_GET['culture']) && $_GET['culture'] != 'All') {
        $culture_selected = $_GET['culture'];
    }
    $region_selected = null;
    if (isset($_POST['region']) && $_POST['region'] != 'All') {
        $region_selected = $_POST['region'];
    }
    elseif (isset($_GET['region']) && $_GET['region'] != 'All') {
        $region_selected = $_GET['region'];
    }
    $options = _before_after_filters($region_selected, $culture_selected);

    $region_options = array('All' => '- ' . t('Any') . ' -') + $options['regions'];
    $form['c']['c1']['region'] = array(
        '#type' => 'select',
        '#title' => t('Region'),
        '#options' => $region_options,
        '#prefix' => '<div class="col-sm-6">',
        '#suffix' => '</div>',
        '#default_value' => empty($_GET['region']) ? 'All' : $_GET['region'],
    );

    $culture_options = array('All' => '- ' . t('Any') . ' -') + $options['cultures'];
    $form['c']['c1']['culture'] = array(
        '#type' => 'select',
        '#title' => t('Culture'),
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
function before_after_page($season_id)
{
  if ($season = before_after_get_atfield($season_id)) {
    $title = htmlspecialchars_decode('Защита культуры ' . $season['culture'] . ($season['culture_note'] ? ' (' . $season['culture_note'] . ')' : '') . '. ' . $season['farm']);
    drupal_set_title($title);
    drupal_set_subtitle(before_after_page_description($season_id));

    drupal_add_css(drupal_get_path('module', 'before_after') . '/css/before_after.css');
    drupal_add_js(drupal_get_path('module', 'before_after') . '/js/before_after.js');

    return theme('before_after_page', array('content' => $season));
  } else {
    drupal_not_found();
  }
}

/**
 * Генерирует описание страницы сезона для ПвП
 * @param $sid - season
 * @return string
 */
function before_after_page_description($season_id)
{
  $output = '';
  $season = before_after_get_atfield($season_id);
  $hobjects = [];
  foreach($season['hobjects'] as $hobject) {
    $hobjects[] = $hobject['name'];
  }
  $output = 'Пример работы препаратов Кирово-Чепецкой Химической Компании. Система защиты от вредных объектов ' . implode(', ', $hobjects) . '. ' . $season['region'] . '.';
  $output .= $season["previous_culture"] ? ' Предшественник: ' . $season["previous_culture"] . '.' : '';
  $output .= $season["sub_culture"] ? ' Подсев: ' . $season["sub_culture"] . '.' : '';

  return $output;
}

/**
 * возвращает массив с информацией о Сезоне
 * @param $sid
 * @return mixed
 */
function before_after_get_atfield($sid)
{
  $output = &drupal_static(__FUNCTION__);
  if (empty($output)) {
    $season_wrapper = entity_metadata_wrapper('field_collection_item', $sid);
    if ($season = $season_wrapper->value()) {
      $fid = $season->hostEntityId();
      $field_wrapper = entity_metadata_wrapper('node', $fid);
      $field = $field_wrapper->value();

      $uid = $field->uid;
      $user_wrapper = entity_metadata_wrapper('user', $uid);
      $user = $user_wrapper->value();

      $output['farm'] = $field_wrapper->field_f_farm->value();
      $output['culture'] = $season_wrapper->field_f_s_culture->label();
      $output['culture_note'] = $season_wrapper->field_f_s_culture_note->value();
      $output['sub_culture'] = $season_wrapper->field_f_s_sub_culture->value() ? $season_wrapper->field_f_s_sub_culture->value() : '';
      $output['previous_culture'] = $season_wrapper->field_f_s_previous_culture->value() ? $season_wrapper->field_f_s_previous_culture->label() : '';
      $output['season'] = $season_wrapper->field_f_s_season->value();
      $output['year'] = $season_wrapper->field_f_s_year->value();
      $output['region'] = $field_wrapper->field_f_region->label();
      $output['author'] = person_get_user_array($user);

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
      foreach ($result_p as $key_p => $item_p) {
        $date = $item_p->field_f_s_p_date_value;
        if (!$date_first_processing) $date_first_processing = $date;
        $processes[$date . 'p' . $key_p]['type'] = 'processing';
        $processes[$date . 'p' . $key_p]['id'] = $item_p->field_f_s_processings_value;
        $processes[$date . 'p' . $key_p]['date'] = $item_p->field_f_s_p_date_value;
        $count_processings++;
      }
      $count_measurements = 0;
      foreach ($result_m as $key_m => $item_m) {
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
              'name' => $item_ho->field_f_s_h_hobject->label(),
              'link' => drupal_get_path_alias('node/' . $item_ho->field_f_s_h_hobject->nid->value()),
              'phase' => $item_ho->field_f_s_h_phase->value(),
              'dominant' => $item_ho->field_f_s_h_dominant->value() ? $item_ho->field_f_s_h_dominant->value() : 0,
              'type' => $item_ho->field_f_s_h_hobject->getBundle(),
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

        $output['hobjects'] = $arr_hobjects;
        $output['date_last'] = $processes[$count_processes - 1]['date'];


        $measurements = $processings = array();
        $key_m = $key_p = 0;
        foreach ($processes as $process) {
          if ($process['type'] == 'measurement') {
            $measurement_wrapper = entity_metadata_wrapper('field_collection_item', $process['id']);
            $image_f_fid = $measurement_wrapper->field_f_s_m_foto_field->value();
            $image_f = file_load($image_f_fid);
            $measurements[$key_m]['image_field_thumb'] = $image_f ? image_style_url('news_teaser', $image_f->uri) : '';
            $measurements[$key_m]['image_field_full'] = $image_f ? image_style_url('1200_1200', $image_f->uri) : '';
            $image_c_fid = $measurement_wrapper->field_f_s_m_foto_culture->value();
            $image_c = $image_c_fid ? file_load($image_c_fid) : false;
            $measurements[$key_m]['image_culture_thumb'] = $image_c ? image_style_url('news_teaser', $image_c->uri) : '';
            $measurements[$key_m]['image_culture_full'] = $image_c ? image_style_url('1200_1200', $image_c->uri) : '';
            $measurements[$key_m]['condition'] = drupal_strtolower($measurement_wrapper->field_f_s_m_condition_mc->value());
            $measurements[$key_m]['phase'] = $measurement_wrapper->field_f_s_m_phase_mc->value() ? drupal_strtolower($measurement_wrapper->field_f_s_m_phase_mc->name->value()) : '';
            $measurements[$key_m]['date'] = date('d.m.Y', $measurement_wrapper->field_f_s_m_date->value());
            $comment = trim($measurement_wrapper->field_f_s_m_comment->value());
            $comment_short = _cropStr($comment, 150, '... <span class="s1">развернуть</span>');
            $measurements[$key_m]['comment'] = $comment;
            $measurements[$key_m]['comment_short'] = $comment_short;

            if ($date_first_processing && $key_m) {
              $diff = _seconds2times($measurement_wrapper->field_f_s_m_date->value() - $date_first_processing);
              $measurements[$key_m]['days_after'] = empty($diff['3']) ? 'Контроль' : (' ' . format_plural($diff['3'], '@count day', '@count days'));
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
                    $arr_ho[$nid]['photo'] = $photo ? image_style_url('1200_1200', $photo->uri) : '';
                  }

                  $arr_ho[$nid]['percent'] = $item_ho->field_f_s_m_c_percent->value() ? $item_ho->field_f_s_m_c_percent->value() : '-';
                }
              }
            }

            $measurements[$key_m]['hobjects'] = $arr_ho;

            $key_m++;
          } else {
            $processing_wr = entity_metadata_wrapper('field_collection_item', $process['id']);
            $processings[$key_p]['date'] = date('d.m.Y', $processing_wr->field_f_s_p_date->value());
            $processings[$key_p]['time'] = $processing_wr->field_f_s_p_time->value();
            $processings[$key_p]['mechanism'] = $processing_wr->field_f_s_p_mechanism->value();

            // препараты
            $preparations = [];
            $titles = [];
            foreach ($processing_wr->field_f_s_p_preparations->getIterator() as $prep_wr) {
              $product_info = get_product_info($prep_wr->field_f_s_p_p_preparation->value());
              $prep_info = $product_info['items'][$prep_wr->field_f_s_p_p_preparation->getIdentifier()];

              $title = $prep_info['title'] . ($prep_info['form_short'] ? ', ' . $prep_info['form_short'] : '');
              $preparations[] = [
                'title' => $title,
                'ingredients' => implode(' + ', $prep_info['ingredients']),
                'description' => $prep_info['description'],
                'url' => $prep_info['url'],
                'nid' => $prep_info['id'],
                'image_thumb' => image_style_url('medium', $prep_info['photo_uri']),
                'image_full' => image_style_url('medium', $prep_info['photo_uri']),
                'rate' => $prep_wr->field_f_s_p_p_rate->value(),
                'unit_short' => $prep_info['unit_short'],
                'unit_field' => $prep_info['unit_field'][array_key_first($prep_info['categories'])],
                'consumption' => $prep_wr->field_f_s_p_p_consumption->value(),
              ];
              $titles[] = $title;
            }

            // для обратной совместимости со старыми полями препаратов
            // препарат
            if ($processing_wr->field_f_s_p_preparation->value()) {
              $prep_info = array_shift(get_product_info($processing_wr->field_f_s_p_preparation->value())['items']);
              $title = $prep_info['title'] . ($prep_info['form_short'] ? ', ' . $prep_info['form_short'] : '');
              $preparations[] = [
                'title' => $title,
                'ingredients' => implode(' + ', $prep_info['ingredients']),
                'description' => $prep_info['description'],
                'url' => $prep_info['url'],
                'nid' => $prep_info['id'],
                'image_thumb' => image_style_url('medium', $prep_info['photo_uri']),
                'image_full' => image_style_url('medium', $prep_info['photo_uri']),
                'consumption' => $processing_wr->field_f_s_p_consumption->value(),
              ];
              $titles[] = $title;
            }
            // препарат2
            if ($processing_wr->field_f_s_p_preparation2->value()) {
              $prep_info2 = array_shift(get_product_info($processing_wr->field_f_s_p_preparation2->value())['items']);
              $title2 = $prep_info2['title'] . ($prep_info2['form_short'] ? ', ' . $prep_info2['form_short'] : '');
              $preparations[] = [
                'title' => $title2,
                'ingredients' => implode(' + ', $prep_info2['ingredients']),
                'description' => $prep_info2['description'],
                'url' => $prep_info2['url'],
                'nid' => $prep_info2['id'],
                'image_thumb' => image_style_url('medium', $prep_info2['photo_uri']),
                'image_full' => image_style_url('medium', $prep_info2['photo_uri']),
                'consumption' => $processing_wr->field_f_s_p_consumption2->value(),
              ];
              $titles[] = $title2;
            }

            $processings[$key_p]['preparations'] = $preparations;
            $processings[$key_p]['title'] = implode('<br/>', $titles);

            $processings[$key_p]['precipitation'] = drupal_strtolower($processing_wr->field_f_s_p_precipitation->value());
            $processings[$key_p]['wind'] = $processing_wr->field_f_s_p_wind_speed->value();
            $processings[$key_p]['t_day'] = $processing_wr->field_f_s_p_temp_day->value();
            $processings[$key_p]['t_night'] = $processing_wr->field_f_s_p_temp_night->value();
            $processings[$key_p]['acidity'] = drupal_strtolower($processing_wr->field_f_s_p_acidity->name->value());
            $processings[$key_p]['humidity'] = $processing_wr->field_f_s_p_humidity->value();

            $image_fid = $processing_wr->field_f_s_p_photo->value();
            $image = file_load($image_fid);
            $processings[$key_p]['image_thumb'] = $image ? image_style_url('news_teaser', $image->uri) : '';
            $processings[$key_p]['image_full'] = $image ? image_style_url('1200_1200', $image->uri) : '';

            $key_p++;
          }
        }

        $output['measurements'] = $measurements;
        $output['processings'] = $processings;
      }
    }
  }

  return $output;
}
