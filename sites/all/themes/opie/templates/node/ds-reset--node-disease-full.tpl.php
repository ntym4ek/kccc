<?php

$summary = !empty($node->body['ru'][0]['safe_summary']) ? $node->body['ru'][0]['safe_summary'] : $node->body['und'][0]['safe_summary'];

// список поражаемых культур
$c_arr = array();
foreach($content['field_hobject_cultures'] as $key => $culture) {
    if (is_numeric($key)) {
        $c_arr[] = $culture['#markup'];
    }
}
$cultures = implode($c_arr, ', ');

$p_arr = array();
foreach($field_fungi_preparations as $item) {
    $prep_wrapper = entity_metadata_wrapper('node', $item['entity']);
    $title = get_product_agro_title($item['entity']->nid);
    $formula = $prep_wrapper->title;
    $image = $prep_wrapper->field_product[0]->field_p_images[0]->value();
    $img = theme(
        'image_style',
        array(
            'path' => $image['uri'],
            'style_name' => 'thumbnail',
            'alt' => $image['alt'],
            'title' => $image['title'],
        )
    );
    $link = drupal_get_path_alias('node/' . $item['entity']->nid);
    $p_arr[] = array(
        'img' => $img,
        'title' => $title['title'],
        'link' => $link,
        'formulation' => $title['formulation'],
        'ingredients' => $title['ingredients'],
    );
}
?>

<article class="fungi full"<?php print $attributes; ?>>
    <div class="content" property="content:encoded">
        <div class="img-wrap">
            <?php print render($content['field_promo_image']); ?>
        </div>
        <div class="info-wrap">
            <?php print render($content['field_name_latin']); ?>
            <?php print render($content['field_hobject_synonyms']); ?>
            <div class="field field-name-field-fungi-cultures field-type-entityreference field-label-inlinec clearfix field-label-inline">
                <div class="field-label">Поражаемые культуры</div>
                <div class="field-items"><?php print $cultures; ?></div>
            </div>
            <div class="field field-name-field-fungi-preparations field-type-entityreference field-label-inlinec clearfix field-label-inline">
                <div class="field-label">Препараты для борьбы с заболеванием</div>
                <div class="field-items">
                    <?php foreach($p_arr as $prep): ?>
                        <div class="field-item">
                            <div class="p-img"><?php print $prep['img']; ?></div>
                            <div class="p-title"><a href="/<?php print $prep['link']; ?>" target="_blank"><?php print $prep['title'] . ', ' . $prep['formulation']; ?></a></div>
                            <div class="p-ingr"><?php print $prep['ingredients']; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="summary"><?php print $summary; ?></div>
        </div>

        <div class="text-wrap">
            <?php print render($content['body']); ?>
            <?php print render($content['field_lit_sources']); ?>
        </div>
    </div>
</article>
