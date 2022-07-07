<?
/**
 * @file
 * Default theme implementation for field collection items.
 *
 * Available variables:
 * - $content: An array of comment items. Use render($content) to print them all, or
 *   print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $title: The (sanitized) field collection item label.
 * - $url: Direct url of the current entity if specified.
 * - $page: Flag for the full page state.
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. By default the following classes are available, where
 *   the parts enclosed by {} are replaced by the appropriate values:
 *   - entity-field-collection-item
 *   - field-collection-item-{field_name}
 *
 * Other variables:
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 *
 * @see template_preprocess()
 * @see template_preprocess_entity()
 * @see template_process()
 */

// определить функцию препарата
$fc_wrapper = entity_metadata_wrapper('field_collection_item', $field_collection_item);
$node = $field_collection_item->hostEntity();
$node_wrapper = entity_metadata_wrapper('node', $node);

$classes = array(
    'name' => $fc_wrapper->field_pd_a_level->name->value(),
    'tid' => $fc_wrapper->field_pd_a_level->tid->value(),
    'description' => $fc_wrapper->field_pd_a_description->value(),
);

// определить функцию препарата
$fids = _get_product_categories($node);



// если Гербицид - проверить группы сорняков
if (in_array(AGRO_CATEGORY_HERBICIDES_TID, $fids)) {
    // словарь Классификатор
    $vid = taxonomy_vocabulary_machine_name_load('plants_classification')->vid;

    // корневые классы (Двудольные, Злаки, Хвощевые)
    // проходим по основным классам
    foreach ([71738, 71756, 71732] as $r_tid) {

        $root_term = taxonomy_term_load($r_tid);
        // tid класса и дерево
        $root_class = $root_term->tid;
        $tree = taxonomy_get_tree($vid, $root_class);

        // извлекаем имя класса
        $classes_arr[$root_class]['name'] = $root_term->name;

        // формируем массив подлассов класса
        $childs_all = array();
        $max_depth = 0;
        foreach ($tree as $term) {
            $childs_all[] = $term->tid;
            $classes_arr[$root_class][$term->tid] = $term;
        }
        // посчитать число потомков и находим максимальную глубину
        foreach ($classes_arr[$root_class] as $tid => $term) {
            if (is_numeric($tid)) {
                if (!empty($term->parents[0]) && $term->parents[0] != $root_class) {
                    $classes_arr[$root_class][$term->parents[0]]->childs = empty($classes_arr[$root_class][$term->parents[0]]->childs) ? 1 : ($classes_arr[$root_class][$term->parents[0]]->childs + 1);
                }
                if ($max_depth < $term->depth) $max_depth = $term->depth;
            }
        }

        // пузырьковым методом собрать все листья для каждого родителя
        for ($i = $max_depth; $i >= 0; $i--) {
            foreach ($classes_arr[$root_class] as $tid => $term) {
                if (is_numeric($tid)) {
                    if (empty($term->childs)) {
                        $classes_arr[$root_class][$term->parents[0]]->leafs[$tid] = $tid;
                    }
                    if (!empty($term->leafs)) {
                        $parent_leafs = empty($classes_arr[$root_class][$term->parents[0]]->leafs[$tid]) ? array() : $classes_arr[$root_class][$term->parents[0]]->leafs[$tid];
                        if ($term->parents[0] != $root_class) $classes_arr[$root_class][$term->parents[0]]->leafs = $term->leafs + $parent_leafs;
                        else $classes_arr[$root_class]['leafs'] = $term->leafs + $parent_leafs;
                    }
                }
            }
        }
        $classes_arr[$root_class]['childs_all'] = $childs_all;
    }

    // пройти по группам Сорняков
    if ($fc_wrapper->field_pd_a_weeds_groups->value()) {
        foreach ($fc_wrapper->field_pd_a_weeds_groups->getIterator() as $group_wrapper) {
            // время жизни сорняка
            $ltid = 'all';
            $ltime = array();
            if ($group_wrapper->field_pd_a_wg_life_time->value()) {
                $ltid = $group_wrapper->field_pd_a_wg_life_time[0]->tid->value();
                foreach ($group_wrapper->field_pd_a_wg_life_time->getIterator() as $life_time_wrapper) {
                    $ltime[] = drupal_strtolower($life_time_wrapper->name->value());
                }
            }

            // цикл по всем классам сорняков
            foreach ($group_wrapper->field_pd_a_wg_class->getIterator() as $class_wrapper) {
                $cid = $class_wrapper->tid->value();

                // проверить, к какому классу относится текущий
                foreach ($classes_arr as $ca_key => $class_arr) {
                    // если найден, внести в итоговый массив данные в соответствующий раздел
                    if ($cid == $ca_key || in_array($cid, $class_arr['childs_all'])) {
                        $hobject = '';
                        if ($cid != $ca_key && empty($class_arr[$cid]->childs)) {
                            $hobject = '<span>' . $class_arr[$cid]->name . ', виды</span>';
                        }
                        $classes[$ca_key]['subclass'][$ltid]['species'][] = $hobject;

                        $classes[$ca_key]['name'] = $class_arr['name'];
                        // наименование времени жизни
                        $classes[$ca_key]['subclass'][$ltid]['name'] = drupal_ucfirst(implode(', ', $ltime));
                        break;
                    }
                }
            }
        }
    }
}

// если Инсектицид - проверить группы Насекомых
if (in_array(AGRO_CATEGORY_INSECTICIDES_TID, $fids)) {
    // словарь Классификатор насекомых
    $vid = taxonomy_vocabulary_machine_name_load('pests_classification')->vid;

    $root_classes = taxonomy_get_tree($vid, 0, 1);

    // проходим по основным классам
    foreach ($root_classes as $root_term) {

        // tid класса и дерево
        $root_class = $root_term->tid;
        $tree = taxonomy_get_tree($vid, $root_class);

        // извлекаем имя класса
        $classes_arr[$root_class]['name'] = $root_term->name;

        $tree = taxonomy_get_tree($vid, $root_class);

        // формируем массив подклассов класса
        $childs_all = array();
        $max_depth = 0;
        foreach ($tree as $term) {
            $childs_all[] = $term->tid;
            $classes_arr[$root_class][$term->tid] = $term;
        }
        // посчитать число потомков и находим максимальную глубину
        foreach ($classes_arr[$root_class] as $tid => $term) {
            if (is_numeric($tid)) {
                if (!empty($term->parents[0]) && $term->parents[0] != $root_class) {
                    $classes_arr[$root_class][$term->parents[0]]->childs = empty($classes_arr[$root_class][$term->parents[0]]->childs) ? 1 : ($classes_arr[$root_class][$term->parents[0]]->childs + 1);
                }
                if ($max_depth < $term->depth) $max_depth = $term->depth;
            }
        }

        // пузырьковым методом собрать все листья для каждого родителя
        for ($i = $max_depth; $i >= 0; $i--) {
            foreach ($classes_arr[$root_class] as $tid => $term) {
                if (is_numeric($tid)) {
                    if (empty($term->childs)) {
                        $classes_arr[$root_class][$term->parents[0]]->leafs[$tid] = $tid;
                    }
                    if (!empty($term->leafs)) {
                        $parent_leafs = empty($classes_arr[$root_class][$term->parents[0]]->leafs[$tid]) ? array() : $classes_arr[$root_class][$term->parents[0]]->leafs[$tid];
                        if ($term->parents[0] != $root_class) $classes_arr[$root_class][$term->parents[0]]->leafs = $term->leafs + $parent_leafs;
                        else $classes_arr[$root_class]['leafs'] = $term->leafs + $parent_leafs;
                    }
                }
            }
        }
        $classes_arr[$root_class]['childs_all'] = $childs_all;
    }

    // пройти по группам Насекомых
    if ($fc_wrapper->field_pd_a_pests_groups->value()) {
        // цикл по всем классам Насекомых
        $ltime = array();
        foreach ($fc_wrapper->field_pd_a_pests_groups->getIterator() as $class_wrapper) {

            $cid = $class_wrapper->tid->value();

            // проверить, к какому классу относится текущий
            foreach ($classes_arr as $ca_key => $class_arr) {
                // если найден, внести в итоговый массив данные в соответствующий раздел
                if ($cid == $ca_key || in_array($cid, $class_arr['childs_all'])) {
                    $hobject = '';
//                    if ($cid != $ca_key && empty($class_arr[$cid]->childs)) { // выводились пустые значения, если указана классификация, не являющаяся конечной в иерархии
                    if ($cid != $ca_key) {
                        $hobject = '<span>' . $class_arr[$cid]->name . ', виды</span>';
                    }
                    $classes[$ca_key]['subclass']['all']['species'][] = $hobject;

                    $classes[$ca_key]['name'] = $class_arr['name'];
                    // наименование времени жизни
                    $classes[$ca_key]['subclass']['all']['name'] = '';
                    break;
                }
            }
        }
    }
}



// цикл по ВО уровня
if ($fc_wrapper->field_pd_a_hobjects->value()) {
    foreach ($fc_wrapper->field_pd_a_hobjects->getIterator() as $ho_wrapper) {
        // если Гербицид проверить, к какому классу относится ВО
        if (in_array(AGRO_CATEGORY_HERBICIDES_TID,$fids) && $ho_wrapper->type->value() == 'weed') {
            $cid = $ho_wrapper->field_weed_kind->tid->value();
            $ltid = $ho_wrapper->field_weed_life_time->tid->value();
            $ltime = $ho_wrapper->field_weed_life_time->name->value();

            foreach ($classes_arr as $ca_key => $class_arr) {
                // если найден, внести в соответствующий раздел итогового массива
                if ($cid == $ca_key || in_array($cid, $class_arr['childs_all'])) {
                    $hobject = $ho_wrapper->title->value();
                    $classes[$ca_key]['name'] = $class_arr['name'];
                    $classes[$ca_key]['subclass'][$ltid]['name'] = $ltime;
                    $classes[$ca_key]['subclass'][$ltid]['hobjects'][] = '<a href="' . url('node/' . $ho_wrapper->nid->value()) . '" target="_blank">' . $hobject . '</a>';
                }
            }
        }
        // классификация по Насекомым
        if (in_array(AGRO_CATEGORY_INSECTICIDES_TID,$fids) && $ho_wrapper->type->value() == 'pest') {
            $cid = $ho_wrapper->field_pest_classificator->tid->value();

            foreach ($classes_arr as $ca_key => $class_arr) {
                // если найден, внести в соответствующий раздел итогового массива
                if ($cid == $ca_key || in_array($cid, $class_arr['childs_all'])) {
                    $hobject = $ho_wrapper->title->value();
                    $classes[$ca_key]['name'] = $class_arr['name'];
                    $classes[$ca_key]['subclass']['all']['hobjects'][] = '<a href="' . url('node/' . $ho_wrapper->nid->value()) . '" target="_blank">' . $hobject . '</a>';
                }
            }
        }
        // Болезни всем скопом
        if ((in_array(AGRO_CATEGORY_FUNGICIDES_TID,$fids) || in_array(AGRO_CATEGORY_DISINFECTANTS_TID,$fids)) && $ho_wrapper->type->value() == 'disease') {
            $classes[$classes['tid']]['subclass']['all']['hobjects'][] = '<a href="' . url('node/' . $ho_wrapper->nid->value()) . '" target="_blank">' . $ho_wrapper->title->value() . '</a>';
        }
    }
}

?>
<div class="panel panel-default panel-<? print $classes['tid'] ?>">
    <div class="panel-heading">
        <h4><? print $classes['name']; ?></h4>
        <? if ($classes['description']) print '<span>' . $classes['description'] . '</span>'; ?>
    </div>
    <div class="panel-body">
        <? foreach($classes as $class): ?>
            <? if (is_array($class)): ?>
                <div class="act-class">
                    <? if (!empty($class['name'])) print '<h4>' . $class['name'] . '</h4>'; ?>
                    <? foreach($class['subclass'] as $ltime): ?>
                        <div class="act-life-time">
                            <? if (!empty($ltime['name'])) print '<h5>' . $ltime['name'] . '</h5>';?>
                            <div class="act-list">
                                <? if (!empty($ltime['species'])) print implode('; ', $ltime['species']) . '<br />'; ?>
                                <? if (!empty($ltime['hobjects'])) print implode(', ', $ltime['hobjects']); ?>
                            </div>
                        </div>
                    <? endforeach; ?>
                </div>
            <? endif; ?>
        <? endforeach; ?>
    </div>
</div>
