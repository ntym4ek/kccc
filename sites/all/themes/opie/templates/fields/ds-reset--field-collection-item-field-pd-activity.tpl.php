<?
// определить функцию препарата
$fc_wrapper = entity_metadata_wrapper('field_collection_item', $field_collection_item);
$node = $field_collection_item->hostEntity();
$node_wrapper = entity_metadata_wrapper('node', $node);

$classes = array(
    'name' => $fc_wrapper->field_pd_a_level->name->value(),
    'color' => ($fc_wrapper->field_pd_a_level->field_color->value()) ? $fc_wrapper->field_pd_a_level->field_color->value() : 'aaaaaa',
    'description' => $fc_wrapper->field_pd_a_description->value(),
);

// определить функцию препарата
$fid = '';
if ($node_wrapper->field_pd_category[0]->tid->value() == AGRO_CATEGORY_MIX_TID) {
    $fid = $node_wrapper->field_pd_mix_components[0]->field_pd_category[0]->tid->value();
} else {
    $fid = $node_wrapper->field_pd_category[0]->tid->value();
}

// если Гербицид - проверить группы сорняков
if ($fid == AGRO_CATEGORY_HERBICIDES_TID) {
    // словарь Классификатор
    $vid = taxonomy_vocabulary_machine_name_load('plants_classification')->vid;
    // Двудольные, Злаки, Хвощевые
    $output_classes = array(71738, 71756, 71732);

    // проходим по основным классам
    foreach ($output_classes as $root_class) {
        $tree = taxonomy_get_tree($vid, $root_class);

        // извлекаем имя класса
        $root_term = taxonomy_term_load($root_class);
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
                        $classes[$ca_key]['ltimes'][$ltid]['hobjects'][] = $hobject;

                        $classes[$ca_key]['name'] = $class_arr['name'];
                        // наименование времени жизни
                        $classes[$ca_key]['ltimes'][$ltid]['name'] = drupal_ucfirst(implode(', ', $ltime));
                        break;
                    }
                }
            }
        }
    }
}

// если Инсектицид - проверить группы Насекомых
if ($fid == AGRO_CATEGORY_INCECTICIDES_TID) {
    // словарь Классификатор насекомых
    $vid = taxonomy_vocabulary_machine_name_load('pests_classification')->vid;

    // tid корневого класса
    $root_class = 72471;

    // извлекаем имя класса
    $root_term = taxonomy_term_load($root_class);
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
                    if ($cid != $ca_key && empty($class_arr[$cid]->childs)) {
                        $hobject = '<span>' . $class_arr[$cid]->name . ', виды</span>';
                    }
                    $classes[$ca_key]['ltimes']['all']['hobjects'][] = $hobject;

                    $classes[$ca_key]['name'] = $class_arr['name'];
                    // наименование времени жизни
                    $classes[$ca_key]['ltimes']['all']['name'] = '';
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
        if ($fid == AGRO_CATEGORY_HERBICIDES_TID) {
            $cid = $ho_wrapper->field_weed_kind->tid->value();
            $ltid = $ho_wrapper->field_weed_life_time->tid->value();
            $ltime = $ho_wrapper->field_weed_life_time->name->value();

            foreach ($classes_arr as $ca_key => $class_arr) {
                // если найден, внести в итоговый массив данные в соответствующий раздел
                if ($cid == $ca_key || in_array($cid, $class_arr['childs_all'])) {
                    $hobject = $ho_wrapper->title->value();
                    $classes[$ca_key]['name'] = $class_arr['name'];
                    $classes[$ca_key]['ltimes'][$ltid]['name'] = $ltime;
                    $classes[$ca_key]['ltimes'][$ltid]['hobjects'][] = '<a href="/' . drupal_get_path_alias('node/' . $ho_wrapper->nid->value()) . '" target="_blank">' . $hobject . '</a>';;
                }
            }

        }
        // классификация по Насекомым
        if ($fid == AGRO_CATEGORY_INCECTICIDES_TID) {

        }

    }
}

?>
<div class="act-level" style="border-color: #<? print $classes['color'] ?>;">
    <div class="act-title" style="background-color: #<? print $classes['color'] ?>;">
        <? print $classes['name']; ?>
        <? if ($classes['description']) print '<span>' . $classes['description'] . '</span>'; ?>
    </div>
    <? foreach($classes as $class): ?>
    <? if (is_array($class)): ?>
        <div class="act-class">
        <h3><? print $class['name'];?></h3>
        <? foreach($class['ltimes'] as $ltime): ?>
            <div class="act-life-time">
            <? if ($ltime['name']) print '<h4>' . $ltime['name'] . '</h4>';?>
                <div class="act-list"><? print implode('; ', $ltime['hobjects']); ?></div>
            </div>
        <? endforeach; ?>
        </div>
        <? endif; ?>
    <? endforeach; ?>
</div>