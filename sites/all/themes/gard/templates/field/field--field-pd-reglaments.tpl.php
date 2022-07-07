<?php
/**
 * вывод регламентов
 */

$node_wrapper = entity_metadata_wrapper('node', $element['#object']);

$category = $node_wrapper->field_pd_category[0]->tid->value();
$prices = [];
$show_calculation = true;
// составляем строку единиц
$item = array_shift(ext_product_product_get_info($element['#object']->nid)['items']);
if (!$prices[] = $item['price']) $show_calculation = false;
$units = $item['unit_short'] . '/' . array_shift($item['unit_field']);

//  для Протравителей вывести 'Вес обрабатываемых семян', для остальных
if ($category == AGRO_CATEGORY_DISINFECTANTS_TID || $category == AGRO_CATEGORY_FUNGICIDES_TID) {
    $calc_volume_title = 'Вес обрабатываемых семян (т)';
    $volume = 'вес семян';
} else {
    $calc_volume_title = 'Посевная площадь ';
    $volume = 'посевную площадь';
}


?>
<?php if (!empty($element['#items'])): ?>
    <div class="reglaments col-sm-12">
      <h4>Таблица регламентов</h4>
      <table class="table table-bordered">

        <?php
        $table = '';
        $old_table = '-';  // $old_table не должен быть равен $table

        // определяем какие категории (Функции) есть в регламентах
        $categories = isset($element['#object']) ? $element['#object']->field_pd_category['und'] : array();
        $options = array();
        foreach ($element['#items'] as $fc_key => $fc_value) {
          $fc_item = $element[$fc_key]['entity']['field_collection_item'][$fc_value['value']];
          if (!empty($fc_item['field_pd_r_function'])) {
            $cat_tid = $fc_item['field_pd_r_function']['#items'][0]['tid'];
            $options[$cat_tid] = $cat_tid;
          }
        }
        asort($options);

        // выводим регламенты поФункционально (напр.: сначала Гербициды, потом Фунгициды и т.д.)
        if ($options) {
          $row_counter = 0;
          foreach($options as $o_key => $option) {

            $count = count($element['#items']);
            $count_f = $count_c = $count_h = $count_rp = $count_rm = $count_d = $count_p = 0;
            $txt_c = $txt_h = $txt_rp = $txt_rm = $txt_d = $txt_p = '';
            foreach ($element['#items'] as $fc_key => $fc_value) {
              $fc_item = $element[$fc_key]['entity']['field_collection_item'][$fc_value['value']];
              $fc_links = empty($element[$fc_key]['links']['#links']) ? array() : $element[$fc_key]['links']['#links'];
              $fid = $function = '';
              if (!empty($fc_item['field_pd_r_function'])) {
                if ($fc_item['field_pd_r_function']['#items'][0]['tid'] != $option) continue; // если Функция не совпадает, пропускаем
                $function = $fc_item['field_pd_r_function']['#items'][0]['taxonomy_term']->name;
                $fid = $fc_item['field_pd_r_function']['#items'][0]['taxonomy_term']->tid;
              }

              // список культур со ссылками (дополнение в скобках)
              $c_array = array();
              $cultures = '';
              $hobjects = '';
              if (!empty($fc_item['field_pd_r_cultures']['#items'])) {
                foreach ($fc_item['field_pd_r_cultures']['#items'] as $c_key => $c_value) {
                  $c_array[] = '<a class="ctext" href="/' . drupal_get_path_alias('node/' . $c_value['target_id']) . '" target="_blank">' . $c_value['entity']->title . '</a>';
                }
                if ($c_array) $cultures = implode(',<br />', $c_array);
              }
              if (!empty($fc_item['field_pd_r_cultures_comment'][0])) {
                if ($cultures) $cultures .= '<br />(' . $fc_item['field_pd_r_cultures_comment'][0]['#markup'] . ')';
                else $cultures .= $fc_item['field_pd_r_cultures_comment'][0]['#markup'];
              }
              if ($txt_c === $cultures) $count_c++; else $txt_c = '';

              // список вредных объектов (сначала группы, потом дополнение в скобках, потом Виды)
              $hog_array = array();
              $h_groups = array();
              // сначала проверить группу Сорняков
              if ($fid == AGRO_CATEGORY_HERBICIDES_TID) {
                if (!empty($fc_item['field_pd_a_weeds_groups']['#items'])) {
                  foreach ($fc_item['field_pd_a_weeds_groups']['#items'] as $key_g => $item_g) {
                    $array_lt = $array_bc = array();
                    $hos = '';
                    $group = $fc_item['field_pd_a_weeds_groups'][$key_g]['entity']['field_collection_item'][$item_g['value']];

                    if (!empty($group['field_pd_a_wg_life_time']['#items']) || !empty($group['field_pd_a_wg_class']['#items'])) {
                      if (!empty($group['field_pd_a_wg_life_time']['#items'])) {
                        foreach ($group['field_pd_a_wg_life_time']['#items'] as $key_lt => $item_lt) {
                          $array_lt[] = drupal_strtolower($group['field_pd_a_wg_life_time'][$key_lt]['#title']);
                        }
                      }
                      if (!empty($group['field_pd_a_wg_class']['#items'])) {
                        foreach ($group['field_pd_a_wg_class']['#items'] as $key_bc => $item_bc) {
                          $array_bc[] = drupal_strtolower($group['field_pd_a_wg_class'][$key_bc]['#title']);
                        }
                      }
                      if ($array_lt) $hos .= implode(', ', $array_lt);
                      if ($array_bc) {
                        if ($array_lt) $hos .= ' ' . implode(', ', $array_bc);
                        else $hos = implode(', ', $array_bc);
                      }
                      $hog_array[] = drupal_ucfirst($hos) . '.';
                    }
                  }
                }
                if ($hog_array) $h_groups[] = implode(' ', $hog_array);
              }

              // проверить группу Вредителей
              if (in_array($fid, array(AGRO_CATEGORY_INSECTICIDES_TID, AGRO_CATEGORY_DISINFECTANTS_TID))) {
                if (!empty($fc_item['field_pd_a_pests_groups']['#items'])) {
                  foreach ($fc_item['field_pd_a_pests_groups']['#items'] as $key_g => $item_g) $hog_array[] = $item_g['taxonomy_term']->name;
                }
                if ($hog_array) $h_groups[] = drupal_ucfirst(implode(', ', $hog_array)) . '.';
              }

              // иначе вывести "Десикация"
              if ($fid == AGRO_CATEGORY_DESICCANTS_TID) {
                $h_groups[] = 'Десикация';
              }

              // виды Вредителей
              $ho_array = array();
              $hovids = '';
              if (!empty($fc_item['field_pd_a_hobjects']['#items'])) {
                foreach ($fc_item['field_pd_a_hobjects']['#items'] as $ho_key => $ho_value) {
                  $ho_array[] = '<a class="ctext" href="/' . drupal_get_path_alias('node/' . $ho_value['target_id']) . '" target="_blank">' . $ho_value['entity']->title . '</a>';
                }
                if ($hovids) $hovids .= ', ';
                if ($ho_array) $hovids .= implode(', ', $ho_array);
              }

              $hobjects = ' ';
              if ($h_groups) $hobjects .= '<h5>Группы</h5>' . implode(' ', $h_groups);
              if ($hovids) $hobjects .= '<h5>Виды</h5>' . $hovids;


              if (!empty($fc_item['field_pd_r_hobjects_comment'][0]['#markup'])) {
                $hobjects =  ($hobjects) ? $hobjects . ' (' .$fc_item['field_pd_r_hobjects_comment'][0]['#markup'] . ')' : $fc_item['field_pd_r_hobjects_comment'][0]['#markup'];
              }
              if ($txt_h === $hobjects) $count_h++; else $txt_h = '';


              // норма расхода препарата
              $rate_p = '';
              $rates_p_arr = array();
              if (!empty($fc_item['field_pd_r_prep_rate']['#items'])) {
                foreach($fc_item['field_pd_r_prep_rate']['#items'] as $fk_key => $fk_value) {
                  if ($fk_value['from'] == $fk_value['to']) $rates_p_arr[] = (float)$fk_value['from'];
                  else $rates_p_arr[] = (float)$fk_value['from'] . '-' . (float)$fk_value['to'];
                }
              }
              $rate_p = str_replace('.', ',', implode('<br />+ ', $rates_p_arr));
              if ($txt_rp === $rate_p) $count_rp++; else $txt_rp = '';


              // норма расхода жидкости
              $rate_m = '';
              $rates_m_arr = array();
              if (!empty($fc_item['field_pd_r_mix_rate']['#items'])) {
                $mix_rate = $fc_item['field_pd_r_mix_rate']['#items'][0];
                if ($mix_rate['from'] == $mix_rate['to']) $rates_m_arr[] = (float)$mix_rate['from'];
                else $rates_m_arr[] = (float)$mix_rate['from'] . '-' . (float)$mix_rate['to'];
              }
              $rate_m = str_replace('.', ',', implode('<br />+ ', $rates_m_arr));
              if ($txt_rm === $rate_m) $count_rm++; else $txt_rm = '';


              // инструкция по обработке
              $descr = empty($fc_item['field_pd_r_processing'][0]['#markup']) ? '' : $fc_item['field_pd_r_processing'][0]['#markup'];
              if ($txt_d === $descr) $count_d++; else $txt_d = '';


              // срок ожидания и кратность
              $period = (empty($fc_item['field_pd_r_wait_period'][0]['#markup']) ? '-' : $fc_item['field_pd_r_wait_period'][0]['#markup']);
              $multi = '';
              if (!empty($fc_item['field_pd_r_multiplicator'][0]['#markup'])) {
                if ($fc_item['field_pd_r_multiplicator']['#items'][0]['from'] == $fc_item['field_pd_r_multiplicator']['#items'][0]['to'])
                  $multi = $fc_item['field_pd_r_multiplicator']['#items'][0]['from'];
                else $multi = $fc_item['field_pd_r_multiplicator'][0]['#markup'];
              }
              if ($multi) $multi = '(' . $multi . ')';
              if ($txt_p === trim($period . ' ' . $multi)) $count_p++; else $txt_p = '';

              // ссылки на Действия
              $links = array();
              foreach ($fc_links as $l_key => $link) {
                $links[] = '<a href="/' . $link['href'] . '?destination=' . url($link['query']['destination']) . '">' . $link['title'] . '</a>';
              }


              // установить rowspan для ячеек
              if ($cultures && !$txt_c) {
                $table = str_replace('rowspan_c=""', 'rowspan="' . ($count_c+1) . '"', $table);
                $count_c = 0; $txt_c = $cultures;
              }
              if ($hobjects && !$txt_h) {
                $table = str_replace('rowspan_h=""', 'rowspan="' . ($count_h+1) . '"', $table);
                $count_h = 0; $txt_h = $hobjects;
              }
              if ($rate_p && !$txt_rp) {
                $table = str_replace('rowspan_rp=""', 'rowspan="' . ($count_rp+1) . '"', $table);
                $count_rp = 0; $txt_rp = $rate_p;
              }
              if ($rate_m && !$txt_rm) {
                $table = str_replace('rowspan_rm=""', 'rowspan="' . ($count_rm+1) . '"', $table);
                $count_rm = 0; $txt_rm = $rate_m;
              }
              if ($descr && !$txt_d) {
                $table = str_replace('rowspan_d=""', 'rowspan="' . ($count_d+1) . '"', $table);
                $count_d = 0; $txt_d = $descr;
              }
              if ($period && $multi && !$txt_p) {
                $table = str_replace('rowspan_p=""', 'rowspan="' . ($count_p+1) . '"', $table);
                $count_p = 0; $txt_p = trim($period . ' ' . $multi);
              }

              $table .= '<tr>'
                . ($count_f ? '' : '<td rowspan_f="" class="reg-category"><div>' . $function . '</div></td>')
                . ($count_c ? '' : '<td rowspan_c="">' . $cultures . '</td>')
                . ($count_h ? '' : '<td rowspan_h="">' . $hobjects . '</td>')
                . ($count_rp ? '' : '<td rowspan_rp="" align="center" class="rate' . ($row_counter ? '' : ' active') .'">' . $rate_p . '</td>')
                . ($count_rm ? '' : '<td rowspan_rm="" align="center">' . $rate_m . '</td>')
                . ($count_d ? '' : '<td rowspan_d="" class="reg-text">' . $descr . '</td>')
                . (($count_p || ($option == AGRO_CATEGORY_FERT_TID)) ? '' : '<td rowspan_p="" align="center">' . trim($period . ' ' . $multi) . '</td>')
                . (user_access('access product_reglaments actions') ? '<td align="center" class="actions">' . implode(',<br />', $links) . '</td>' : '')
                . '</tr>';
              $count_f++;
              $row_counter++;
            }

            // установить последний rowspan для ячеек
            $table = str_replace('rowspan_f=""', 'rowspan="' . $count_f . '"', $table);
            $table = str_replace('rowspan_c=""', 'rowspan="' . ($count_c+1) . '"', $table);
            $table = str_replace('rowspan_h=""', 'rowspan="' . ($count_h+1) . '"', $table);
            $table = str_replace('rowspan_rp=""', 'rowspan="' . ($count_rp+1) . '"', $table);
            $table = str_replace('rowspan_rm=""', 'rowspan="' . ($count_rm+1) . '"', $table);
            $table = str_replace('rowspan_d=""', 'rowspan="' . ($count_d+1) . '"', $table);
            $table = str_replace('rowspan_p=""', 'rowspan="' . ($count_p+1) . '"', $table);

            $table = str_replace('rowspan="1"', '', $table);
          }
        }

        ?>

        <?php if (!empty($option)): ?>
          <tr>
            <th width="4%"></th>
            <th width="<? print ($option != AGRO_CATEGORY_FERT_TID ? '15' : '30'); ?>%"><?php print t('Cultures'); ?></th>
            <? if ($option != AGRO_CATEGORY_FERT_TID): ?><th width="28%"><?php print t('Harmful objects'); ?></th><? endif; ?>
            <th width="10%"><?php print t('Consumption rate') . ',<br />' . $units . ''; ?></th>
            <th width="10%"><?php print t('Mix consumption rate') . ',<br />л/га(т)'; ?></th>
            <th><?php print t('A method of processing, time, application features'); ?></th>
            <? if ($option != AGRO_CATEGORY_FERT_TID): ?><th width="8%"><?php print t('Waiting period (treatments multiplicity)'); ?></th><? endif; ?>
            <?php print user_access('access product_reglaments actions') ? ('<th width="5%"></th>') : ''; ?>
          </tr>

          <? print $table; ?>
        <?php endif; ?>
      </table>
    </div>

    <? if (!empty($option) && $show_calculation): ?>
    <div class="calculation col-sm-12">
      <h4>Калькулятор стоимости обработки</h4>
      <p class="text-muted">выберите ячейку с нормой расхода в таблице регламентов и задайте <?php print $volume; ?></p>

      <table class="table table-bordered">
        <tr>
          <td width="50%"><?php print $calc_volume_title; ?>
            <select name="units">
              <option value="ga">га
              <option value="sot">сотка
            </select>
          </td>
          <td width="50%" class="area">
            <input id="area" type="text" value="100"/>
            <? foreach($prices as $p_key => $price): ?>
              <input id="price_<? print $p_key; ?>" type="hidden" value="<?  print ($price); ?>">
            <? endforeach;?>
          </td>
        </tr>
        <tr>
          <td>Необходимый объём препарата (<span id="units" data-unit-volume="<? print $item['unit_short']; ?>"><? print $units; ?></span>)</td>
          <td class="calc-volume"></td>
        </tr>
        <tr>
          <td>Итоговая стоимость обработки</td>
          <td class="calc-cost"></td>
        </tr>
      </table>
    </div>
    <? endif; ?>

<?php else: ?>
    <?php print t('No reglaments yet.'); ?>
<?php endif; ?>
