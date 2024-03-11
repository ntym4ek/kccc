<?php

/**
 * Подготовить текст материала перед сохранением
 */
function ext_node_prepare_content_to_save(&$node)
{
  foreach($node->body as $lang => $body) {
    $text = $body[0]['value'];

    // убираем кавычки, чтобы проще было обрабатывать
    // типограф позже вернёт
    $text = str_replace(['«', '»'], '"', $text);

    // декодируем спецсимволы &;
    $text = html_entity_decode($text);

    // убрать все ссылки (с исключениями)
    $text = ext_node_remove_all_links($text);
    // проставить внутренние ссылки
    $text = ext_node_place_internal_links($text);

    $node->body[$lang][0]['value'] = $text;

    // запрос к типографу, причёсываем текст
    if ($text = ext_node_text_typograph_request($text)) {
      $node->body[$lang][0]['value'] = $text;
    }
  }
}

/**
 * Подготовоить текст метериала перед редактированием
 */
function ext_node_prepare_content_to_edit(&$form)
{
  if (!empty($form["body"]["und"][0]["#default_value"])) {
    $text = $form["body"]["und"][0]["#default_value"];

    // замена &nbsp; на пробелы, тк html_entity_decode меняет на спецсимвол NBSP
    $text = str_replace('&nbsp;', ' ', $text);

    // декодируем спецсимволы &;
    $text = html_entity_decode($text);

    $form["body"]["und"][0]["#default_value"] = $text;
  }
}

/**
 * Вернуть текст без ссылок
 * (кроме исключений)
 */
function ext_node_remove_all_links($text)
{
  // составить паттерн исключений, содержащих текст
  $excludes_list = ['youtu.be', 'youtube.com'];
  $excludes = [];
  foreach ($excludes_list as $item) {
    $excludes[] = '<a[^>]href="[^"]*' . $item . '[^"]*".*?</a>';
  }
  $excludes_pattern = implode('|', $excludes);

  // составить полный паттерн
  $pattern = '#' . ($excludes_pattern ? '(?:' . $excludes_pattern . ')(*SKIP)(*F)|' : '') . '<a.*?>(.*?)</a>#is';

  return preg_replace($pattern, '$1', $text);
}

function ext_node_place_internal_links($text)
{
  // список искомых фраз и их ссылок
  foreach (ext_node_replacements_list() as $replacement_item) {
    $patterns = ext_node_convert_needles_to_preg_patterns($replacement_item);
    $id_attr = empty($replacement_item['id']) ? '' : 'data-node-id="' . $replacement_item['id'] . '" ';
    $replace = '\\1<a href="' . url($replacement_item['url']) . '" ' . $id_attr . 'target="_blank">\\2</a>\\3';
    foreach ($patterns as $pattern) {
      $text_old = $text;
      $text = preg_replace($pattern, $replace, $text, 1);
      if ($text != $text_old) break;
    }
  }

  return $text;
}

/**
 * Вернуть массив слов и фраз для которых нужно проставить ссылки
 *
 * $arr[] = [
 *   'needles' => [],   // список искомых слов и фраз
 *   'url' => '',       // ссылка, которую необходимо прикрепить
 *   'id' => '',        // id ноды для идентификации в приложении
 *   'excludes' => [],  // список слов, которые нужно исключить при поиске текста для ссылки
 *                      // например, чтобы в "Гранберг Про" не поставить ссылку только на "Гранберг"
 * ];
 */
function ext_node_replacements_list()
{
  $result = [];

  $result[] = [
    'needles' => [
      'KCCC Group', 'KCCC GROUP',
    ],
    'url' => 'https://kccc.group',
  ];
  $result[] = [
    'needles' => [
      'ООО ТД "Кирово-Чепецкая Химическая Компания"',
      'ООО Торговый Дом "Кирово-Чепецкая Химическая Компания"',
      'ТД "Кирово-Чепецкая Химическая Компания"',
      'Торговый Дом "Кирово-Чепецкая Химическая Компания"',
      'Торговый Дом "КЧХК"',
      'ООО ТД "КЧХК"', 'ТД "КЧХК"',
      'Кирово-Чепецкой Химической Компании',
      'KCCC', 'КССС',
    ],
    'url' => 'https://kccc.ru',
  ];
  $result[] = [
    'needles' => [
      'представител',
      'купить', 'приобрести'
    ],
    'url' => 'https://kccc.ru/predstaviteli',
  ];
  $result[] = [
    'needles' => [
      'испытани', 'опыты',
    ],
    'url' => 'https://kccc.ru/preparaty-v-pole',
  ];
  $result[] = [
    'needles' => [
      'специалист', 'экспертное мнение', 'эксперт'
    ],
    'url' => 'https://kccc.ru/eksperty',
  ];
  $result[] = [
    'needles' => [
      'программы защиты', 'программа защиты', 'программ защиты', 'системы защиты', 'система защиты', 'систем защиты'
    ],
    'url' => 'https://kccc.ru/programmy-zashchity',
  ];

  // препараты
  $query = db_select('node', 'n');
  $query->fields('n', ['nid', 'title']);
  $query->condition('n.type', 'preparation');
  $query->leftJoin('field_data_field_prep_formulation', 'pf', 'pf.entity_id = n.nid');
  $query->leftJoin('field_data_field_acronym', 'fa', 'fa.entity_id = pf.field_prep_formulation_tid');
  $query->condition('fa.bundle', 'preparation_formulations');
  $query->addField('fa', 'field_acronym_value', 'formulation');

  if ($products = $query->execute()->fetchAllAssoc('nid')) {
    // определить пересекающиеся названия и задать исключения (напр. Грандсил для Грандсил Ультра)
    foreach($products as $product_check) {
      foreach($products as $key => $product) {
        if (strpos($product_check->title, $product->title) === 0 && ($product->title != $product_check->title)) {
          $products[$key]->exclude[] = $product_check->title;
          break;
        }
      }
    }

    // подготовить список фраз для каждого препарата
    foreach($products as $product) {
      $title = trim($product->title);
      $result[$product->nid] = [
        'needles' => [$title . ', ' . trim($product->formulation), $title . ',' . trim($product->formulation), $title],
        'url' => 'node/' . $product->nid,
        'id' => $product->nid,
        'exclude' => $product->exclude ?? '',
      ];
    }
  }

  return $result;
}

function ext_node_convert_needles_to_preg_patterns($replacement)
{
  $needles = $replacement['needles'];
  if (empty($needles)) return false;

  // <a.*?<\/a>(*SKIP)(*F) - всё, что внутри ссылок в расчёт не берём
  // как исключать ненужное - https://stackoverflow.com/questions/23589174/regex-pattern-to-match-excluding-when-except-between
  $patterns = [];
  foreach ($needles as $needle) {
    $excludes = ['<a.*?<\/a>'];
    if (!empty($replacement['exclude'])) {
      foreach ($replacement['exclude'] as $exclude_needle) {
        $excludes[] = '[>\s\"]' . $exclude_needle . '.*?[<\.\!\?\,\s\"\;\:]';
      }
    }
    $exclude = implode('|', $excludes);

    // кавычки могут быть любыми
    $needle = str_replace('"', '["\']', $needle);
    $patterns[] = '/(?:' . $exclude . ')(*SKIP)(*F)|([>\s\"])(' . $needle . '.*?)([<\.\!\?\,\s\"\;\:])/si';
  }

  return $patterns;
}


/**
 * Возвращает типографированный на http://mdash.ru/ текст
 */
function ext_node_text_typograph_request1($text)
{
  $agent= 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
  $data = [
    'text' => $text,
    'OptAlign.oa_oquote'=>'off',
    'Text.paragraphs'=>'off',
  ];
  $headers = [
    'accept: application/json',
    'Content-Type: application/x-www-form-urlencoded',
  ];

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,'http://mdash.ru/api.v1.php');
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_USERAGENT, $agent);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  if ($response = curl_exec($ch)) {
    $result = drupal_json_decode($response);
    $result = isset($result['result']) ? urldecode($result['result']) : $result;
  }
  curl_close($ch);

  return $result ?? false;
}

/**
 * Возвращает типографированный на http://typograf.ru/ текст
 */
function ext_node_text_typograph_request($text)
{
  $xml =   '<?xml version="1.0" encoding="windows-1251" ?>
            <preferences>
              <!-- Теги -->
              <tags delete="0">1</tags>
              <!-- Абзацы -->
              <paragraph insert="1">
                <start><![CDATA[<p>]]></start>
                <end><![CDATA[</p>]]></end>
              </paragraph>
              <!-- Переводы строк -->
              <newline insert="1"><![CDATA[<br />]]></newline>
              <!-- Переводы строк <p>&nbsp;</p> -->
              <cmsNewLine valid="0" />
              <!-- DOS текст -->
              <dos-text delete="0" />
              <!-- Неразрывные конструкции -->
              <nowraped insert="1" nonbsp="0" length="0">
                <start><![CDATA[<nobr>]]></start>
                <end><![CDATA[</nobr>]]></end>
              </nowraped>
              <!-- Висячая пунктуация -->
              <hanging-punct insert="0" />
              <!-- Удалять висячие слова -->
              <hanging-line delete="0" />
              <!-- Символ минус -->
              <minus-sign><![CDATA[&ndash;]]></minus-sign>
              <!-- Переносы -->
              <hyphen insert="0" length="0" />
              <!-- Акронимы -->
              <acronym insert="1"></acronym>
              <!-- Вывод символов 0 - буквами 1 - числами -->
              <symbols type="0" />
              <!-- Параметры ссылок -->
              <link target="" class="" />
            </preferences> ';

  $data = [
    'text' => $text,
    'chr' => 'UTF-8',
//    'xml' => $xml,
  ];

  $ch = curl_init('https://typograf.ru/webservice/');
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  $response = curl_exec($ch);
  curl_close($ch);

  return $response ?? false;
}
