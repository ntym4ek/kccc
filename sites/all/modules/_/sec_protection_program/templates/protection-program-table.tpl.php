<?php
/**
* темизации программы защиты
* принимает предварительно подготовленный массив
* выдаёт html таблицы
*/

// доступ к редактированию только для владельцев программы и на странице редактирования
$update_access = (node_access('update', node_load($pp['id'])) && arg(0) == 'protection-program') || (!empty($op) && $op == 'edit');

?>

<div id="pp_table">
    <?php if (empty($op) || $op != 'calculation'): ?>
    <div class="culture">
        <table class="noborder">
            <tr>
                <td class="img" rowspan="2">
                    <a class="fancybox" href="<?php print $pp['culture_image_uri']; ?>">
                        <img typeof="foaf:Image" src="<?php print $pp['culture_image_uri']; ?>">
                    </a>
                </td>
                <td class="name">
                    <?php
                        print '<span style="font-size: 20px;">' . $pp['culture_names'] . '</span><br />' . $pp['culture_names_latin'];
                    ?>
                </td>
            </tr>
            <tr>
                <td class="links">
                    <?php
                    if ($update_access) {
                        $l_descr = l('Изменить название и описание программы', 'protection-program/' . $pp['id'] . '/description/nojs', array('attributes' => array('class' => array('ctools-modal-style', 'ctools-use-modal'))));
                        print $l_descr . '<br />';
                        $l_bg = l('Изменить фоновое изображение в таблице', 'protection-program/' . $pp['id'] . '/bg/nojs', array('attributes' => array('class' => array('ctools-modal-style', 'ctools-use-modal'))));
                        print $l_bg . '<br />';
                        print '<a href="/agro-recipe">' . t('Calculate individual program') . '</a>';
                    }
                    ?>
                </td>
            </tr>
        </table>
    </div>
    <?php endif; ?>
    <table class="table" style="background-image: url('<?php print $pp['culture_image_bg_uri']; ?>');">
        <tr>
            <th width="4%"></th>
            <?php
            foreach ($pp['periods'] as $p_name) {
                $double = (strpos($p_name, '<br') !== false) ? true : false;
                print '<th class="text90' . ($double ? ' double' : '') . '"><div>' . $p_name . '</div></th>';
            }
            $count_p = count($pp['periods']);
            ?>
        </tr>

        <?php
        $table = '';
        if (!empty($pp['stages'])) {

            // вывести этапы пофункционально
            $cost_min = $cost_max = 0;
            foreach($pp['functions'] as $fid => $function) {

                $count_r = 0;                                                                                           // счётчик для rowspan
                // цикл по этапам ПЗ
                foreach ($pp['stages'] as $sid => $stage) {
                    // если функция этапа соответствует выводимому разделу
                    if ($stage['function_tid'] == $fid) {

                        $table .= '<tr>'
                            . ($count_r ? '' : '<td rowspan="" class="x-fade" style="background-color: #' . $function['color'] . ';">'
                                . '<div class="prep">' . drupal_substr($function['name'], 0, 1) . '</div>'
                                . '<div class="proc"><span style="background-color: #' . $function['color'] . '; ">' . drupal_substr($function['name'], 1) . '</span></div>'
                                . '</td>');

                        // цикл по периодам ПЗ
                        $count_c = 0;                                                                                   // счётчик для colspan
                        foreach ($pp['periods'] as $p_tid => $p_name) {
                            if ($stage['p_start_tid'] == $p_tid) $count_c = 1;
                            if ($stage['p_stop_tid'] == $p_tid) {
                                $preps_arr = array();
                                foreach($stage['pid'] as $sp_key => $sp_item) {
                                    $preps_arr[] = $stage['title'][$sp_key] . ' (' . $stage['rate'][$sp_key] . ')';
                                }
                                $preps = '<a href="/' . drupal_get_path_alias('node/' . $stage['nid']) . '">' . implode(' + ', $preps_arr) . '</a>';


                                $table .= '<td class="y-fade" colspan="' . $count_c . '"  style="background-color: #' . $function['color'] . ';">'
                                    . '<div class="prep">' . $preps;
                                if ($update_access) {
                                    $l_stage_edit   = l('#', 'protection-program/' . $pp['id'] . '/stage/' . $sid . '/edit/nojs', array('attributes' => array('class' => array('ctools-modal-style', 'ctools-use-modal'), 'title' => 'Редактировать этап')));
                                    $l_stage_del    = l('x', 'protection-program/' . $pp['id'] . '/stage/' . $sid . '/del/nojs', array('attributes' => array('class' => array('ctools-modal-style', 'ctools-use-modal'), 'title' => 'Удалить этап')));
                                    $table .= '<span class="edit-btn">' . $l_stage_edit . '</span>';
                                    $table .= '<span class="del-btn">' . $l_stage_del . '</span>';
                                }
                                $table .= '</div>'
                                    . '<div class="proc"><span style="background-color: #' . $function['color'] . ';">' . $stage['processing'] . '</span></div>'
                                    . '</td>';
                                $count_c = 0;
                            } else if ($count_c) $count_c++; else $table .= '<td></td>';
                        }
                        $table .= '</tr>';
                        $count_r++;
                    }
                }
                $table = str_replace('rowspan=""', 'rowspan="' . $count_r . '"', $table);
            }
        }
        // если этапов нет, добавить строку с сообщением
        if (empty($pp['stages']))
            print '<tr><td></td><td colspan="' . ($count_p+1). '" style="background-color:#ddd;"><span style="color:#fff;">Этапы обработки не добавлены</span></td></tr>';

        // сформировать и добавить ссылку на Добавление периода, если позволяет доступ
        if ($update_access && $count_p) {
            $l_stage_add = l('Добавить этап', 'protection-program/' . $pp['id'] . '/stage/add/nojs', array('attributes' => array('class' => array('ctools-modal-style', 'ctools-use-modal'))));
            $table .= '<tr><td colspan="' . ($count_p+3). '" class="add">' . $l_stage_add . '</td></tr>';
        }
        else $table .= '<tr><td colspan="' . ($count_p+2). '"></td></tr>';

        print $table;
        ?>
    </table>
</div>
