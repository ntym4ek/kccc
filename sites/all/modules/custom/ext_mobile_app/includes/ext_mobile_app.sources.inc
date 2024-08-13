<?php


/**
 * ------------------ Каталог ------------------------------------------------------------------------------------------
 */

/**
 * Вернуть полный список препаратов для функции быстрого поиска в Приложении
 */
function ext_mobile_app_catalog_source($category_id = null)
{
  $catalog = [];

  $query = db_select('node', 'n');
  $query->condition('n.type', 'preparation');
  $query->condition('n.status', 1);
  $query->addField('n', 'nid');
  $query->leftJoin('field_data_title_field', 'tf', 'n.nid = tf.entity_id');
  $query->condition('tf.language', 'ru');
  $query->orderby('tf.title_field_value');

  if ($category_id) {
    $query->innerJoin('field_data_field_prep_category', 'pc', 'n.nid = pc.entity_id');
    $query->condition('pc.field_prep_category_tid', $category_id);
  }
  if ($result = $query->execute()->fetchAll()) {
    foreach($result as $prep) {
      $prep_info = sec_catalog_get_product_info($prep->nid, 'teaser');

      $product = [
        'id' => $prep_info['id'],
        'label' => $prep_info['label'],
        'summary' => $prep_info['summary'],
        'components' => $prep_info["components"],
        'image' => $prep_info["image"],
      ];

      $tid = $prep_info["category"]["id"];
      if (!$category_id || $tid == $category_id) {
        if (empty($catalog[$tid])) {
          $catalog[$tid] = [
            'id' => $tid,
            'name' => $prep_info["category"]['label'],
            'color' => '',
            'items' => []
          ];
        }
        // если задана категория, список только для неё
        $catalog[$tid]['products'][] = $product;
      }
    }
  }

  drupal_json_output(['catalog' => $catalog]);
  drupal_exit();
}

/**
 * Вернуть полный список препаратов для функции быстрого поиска в Приложении
 */
function ext_mobile_app_product_source($product_id = null)
{
  $prep_info = sec_catalog_get_product_info($product_id);
  $prep_info['reglaments'] = sec_catalog_get_product_reglaments_info($product_id);
  drupal_json_output(['product' => $prep_info]);
  drupal_exit();
}

/**
 * Вернуть ссылку на актуальный прайс-лист
 */
function ext_mobile_app_price_list_source()
{
  $price_list_url = '';

  if ($file = file_load(variable_get('price_list', 0))) {
    $price_list_url = file_create_url($file->uri);
  }

  drupal_json_output(['price_url' => $price_list_url]);
  drupal_exit();
}

/**
 * ------------------ Программы защиты -----------------------------------------
 */

/**
 * вернуть список ПЗ
 */
function ext_mobile_app_programs_source()
{
  $programs = [];

  $query = db_select('node', 'n');
  $query->condition('n.type', 'program');
  $query->condition('n.status', 1);
  $query->addField('n', 'nid');
  $query->leftJoin('field_data_title_field', 'tf', 'n.nid = tf.entity_id');
  $query->condition('tf.language', 'ru');
  $query->orderby('tf.title_field_value');

  if ($result = $query->execute()->fetchCol()) {
    foreach ($result as $nid) {
      $program_wr = entity_metadata_wrapper('node', $nid);
      $programs[$nid] = [
        'id' => $nid,
        'label' => $program_wr->label(),
        'icon' => file_create_url($program_wr->field_image_icon->value()['uri']),
      ];
    }
  }

  drupal_json_output(['programs' => $programs]);
  drupal_exit();
}

/**
 * вернуть данные по Программе защиты
 */
function ext_mobile_app_program_source($program_id)
{
  $program = _reglaments_get_protection_system([
    'program_id' => $program_id,
  ]);

  drupal_json_output(['program' => $program]);
  drupal_exit();
}

/**
 * ------------------ Слайдеры на главной --------------------------------------
 */
function ext_mobile_app_block_banners_source()
{
  $items = [];
  if ($banners = variable_get('ema_front_banners', [])) {
    foreach($banners as $fid => $banner) {
      $file = file_load($fid);
      $file_full = $banner['bann_full_fid'] ? file_load($banner['bann_full_fid']) : null;

      $items[] = [
        'image' => file_create_url($file->uri),
        'image_full' => $file_full ? file_create_url($file_full->uri) : '',
        'type' => $banner['type'],
        'link' => [
          'external' => $banner['type'] == 'external',
          'url' => $banner['url']
        ],
      ];
    }
  }

  drupal_json_output(['block' => [
    'banners' => $items
  ]]);
  drupal_exit();
}

function ext_mobile_app_block_products_source()
{
  $products = [];
  foreach (variable_get('ema_ap_list', []) as $nid) {
    $products[] = sec_catalog_get_product_info($nid);
  }

  drupal_json_output(['block' => [
    'status' => true,
    'title' => variable_get('ema_ap_title', ''),
    'intro' => variable_get('ema_ap_intro', ''),
    'products' => $products,
  ]]);
  drupal_exit();
}

/**
 * ------------------ Представители---------------------------------------------
 */
/**
 * вернуть данные о представителях
 */
function ext_mobile_app_sellers_source($area_id = null)
{
  if ($area_id) {
    $_GET['area'] = $area_id;
  }
  $sellers = sec_sellers_get_sellers_by_region();
  $experts = sec_experts_get_experts();

  drupal_json_output(['sellers' => $sellers, 'experts' => $experts, 'filters' => sec_sellers_get_filters_set()]);
  drupal_exit();
}

/**
 * ------------------ Найти решение --------------------------------------------
 */

/**
 * вернуть Культуры, присутствующие в регламентах
 */
function ext_mobile_app_solution_cultures_source()
{
  drupal_json_output(['cultures' => agrocalc_get_cultures_sorted()]);
  drupal_exit();
}

/**
 * вернуть Этапы роста заданной культуры
 */
function ext_mobile_app_solution_stages_source($culture_id)
{
  drupal_json_output(['stages' => agrocalc_get_stages($culture_id)]);
  drupal_exit();
}

/**
 * вернуть список Сорняков
 */
function ext_mobile_app_solution_weeds_source($culture_id)
{
  drupal_json_output(['weeds' => agrocalc_get_weeds($culture_id)]);
  drupal_exit();
}

/**
 * вернуть список Вредителей культур
 */
function ext_mobile_app_solution_pests_source($culture_id)
{
  drupal_json_output(['pests' => agrocalc_get_pests($culture_id)]);
  drupal_exit();
}

/**
 * вернуть список Болезней культур
 */
function ext_mobile_app_solution_diseases_source($culture_id)
{
  drupal_json_output(['diseases' => agrocalc_get_diseases($culture_id)]);
  drupal_exit();
}


/**
 * вернуть рассчитанное решение по защите культуры
 */
function ext_mobile_app_solution_source($program_id)
{
  $program = _reglaments_get_protection_system([
    'program_id' => $program_id,
  ]);

  drupal_json_output(['program' => $program]);
  drupal_exit();
}

/**
 * ------------------ Препараты в поле -----------------------------------------
 */
/**
 * вернуть список ПвП
 */
function ext_mobile_app_pvps_source()
{
  // отправляем все записи
  // фильтровать будем локально, так как требуется работа вне сети
  $filters = sec_pvp_get_filters_set();
  $pvp_per_page = 1000;
//  $pvp_per_page = 10; тест
  $pvp_page_num = 0;
  $pvps = sec_pvp_get_pvp_filtered($filters, $pvp_per_page, $pvp_page_num*$pvp_per_page);

  drupal_json_output(['pvps' => $pvps, 'filters' => $filters]);
  drupal_exit();
}

function ext_mobile_app_pvp_source($pvp_id)
{
  $pvp = sec_pvp_get_pvp_info($pvp_id);
  foreach($pvp['events'] as &$event) {
    sec_pvp_prepare_event_view($event, $pvp);
  }
  $author = ext_user_get_author_info($pvp_id);

  drupal_json_output(['pvp' => $pvp, 'author' => $author]);
  drupal_exit();
}

/**
 * ------------------ Справочники ----------------------------------------------
 */
function ext_mobile_app_handbook_source($type)
{
  $nodes = [];

  $query = db_select('node', 'n');
  $query->condition('n.type', $type);
  $query->condition('n.status', 1);
  $query->addField('n', 'nid');
  $query->orderby('n.title');

  if ($result = $query->execute()->fetchCol()) {
    $entities = entity_load('node', $result);

    foreach ($entities as $nid => $node) {
      $node_wr = entity_metadata_wrapper('node', $nid);
      $nodes[$nid] = [
        'id' => $nid,
        'label' => $node->title,
        'latin' => $node_wr->field_title_latin->value(),
        'image' => [
          'teaser' => image_style_url('teaser', $node_wr->field_image->file->value()->uri),
          'full' => file_create_url($node_wr->field_image->file->value()->uri),
        ],
        'summary' => strip_tags($node_wr->body->summary->value()),
        'body' => $node_wr->body->value->value(),
        'synonyms' => $node_wr->field_text->value(),
      ];

      if (in_array($type, ['weed', 'pest', 'culture']) && $node_wr->field_hb_classification->value()) {
        $nodes[$nid]['classification'] = $node_wr->field_hb_classification->value() ? strip_tags($node_wr->field_hb_classification->value->value()) : '';
      }
      if (in_array($type, ['weed', 'pest', 'disease']) && ($node_wr->field_hb_measure_chem->value() || $node_wr->field_hb_measure_agro->value())) {
        if ($node_wr->field_hb_measure_chem->value()) $nodes[$nid]['measures']['chem'] = $node_wr->field_hb_measure_chem->value->value();
        if ($node_wr->field_hb_measure_agro->value()) $nodes[$nid]['measures']['agro'] = $node_wr->field_hb_measure_agro->value->value();
      }
    }
  }

  drupal_json_output(['nodes' => $nodes, 'sorted' => $result]);
  drupal_exit();
}

/**
 * ------------------ Афиша ----------------------------------------------------
 */
function ext_mobile_app_agenda_source()
{
  $nodes = $sorted = [];

  $query = db_select('node', 'n');
  $query->condition('n.type', 'agenda');
  $query->condition('n.status', 1);
  $query->addField('n', 'nid');

  if ($result = $query->execute()->fetchCol()) {
    $entities = entity_load('node', $result);

    foreach ($entities as $nid => $node) {
      $node_wr = entity_metadata_wrapper('node', $nid);
      $sorted[$node_wr->field_period->value->value()] = $nid;
      $nodes[$nid] = [
        'id' => $nid,
        'label' => $node_wr->label(),
        'image' => [
          'teaser' => image_style_url('teaser', $node_wr->field_image->file->value()->uri),
          'full' => file_create_url($node_wr->field_image->file->value()->uri),
        ],
        'body' => $node_wr->body->value->value(),
        'period' => [
          'start' => date('d.m.Y', $node_wr->field_period->value->value()),
          'finish' => $node_wr->field_period->value2->value() == $node_wr->field_period->value->value() ? '' : date('d.m.Y', $node_wr->field_period->value2->value()),
        ],
      ];

    }
    ksort($sorted);
  }

  drupal_json_output(['nodes' => $nodes, 'sorted' => array_values($sorted)]);
  drupal_exit();
}

function ext_mobile_app_version_source($version)
{
  $update = 0;
  $message = '';
  $link = [];
  $latest_version = variable_get('ext_mobile_update_version', '0.0.0');

  if (version_compare($latest_version, $version) > 0) {
    $update = 1;
    $message = variable_get('ext_mobile_update_message', '');
    $link = [
      'android' => variable_get('ext_mobile_update_link_android', ''),
      'ios' => variable_get('ext_mobile_update_link_ios', ''),
    ];
  }

  drupal_json_output(['update' => $update, 'message' => $message, 'link' => $link]);
  drupal_exit();
}

/**
 * вернуть запрошенные изображения в формате base64
 */
function ext_mobile_app_preloader_source()
{
  // получаем запрос
  $image_urls = json_decode(file_get_contents('php://input'), true);

  // запрошенные изображения переводим в base64
  $images_base64 = [];
  if (!empty($image_urls)) {
    foreach ($image_urls as $url) {
      $base64 = helper_image_style_base64('', $url);
      if (strlen($base64) > 100) {
        $images_base64[] = ['u' => $url, 'b' => $base64];
      }
    }
  }

  // отправляем ответ
  $size = strlen(drupal_json_encode($images_base64));
  header("Content-length: $size");
  drupal_json_output($images_base64);
  drupal_exit();
}
