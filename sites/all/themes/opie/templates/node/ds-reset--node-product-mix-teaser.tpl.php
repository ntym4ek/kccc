<?php

// We render it before drupal_render_children to avoid double printing photos.
$image_title = $node->field_promo_image['und'][0]['title'] ? $node->field_promo_image['und'][0]['title'] : $title;
$image_alt = $node->field_promo_image['und'][0]['alt'];
$image_url = image_style_url('product_medium', $node->field_promo_image['und'][0]['uri']);
$images = '<img src="' . $image_url . '" alt="' . $image_alt . '" />';

$node_wrapper = entity_metadata_wrapper('node', $node->nid);
$products = $preps_arr = $units_arr = $prices_arr = $ingr_arr = array();
foreach($node_wrapper->field_pd_mix_components->getIterator() as $key => $value) {
    // препараты
    $prep = get_product_agro_title($value->nid->value());
    $prep_url = drupal_get_path_alias('node/' . $value->nid->value());
    $preps_arr[] = '<a href="/' . $prep_url . '" target="_blank">' . $prep['title'] . ', ' . $prep['formulation'] . '</a><br /><span class="ingredients">' . $prep['ingredients'] . '</span>';
    $ingr_arr[] = $prep['ingredients'];
    $products[] = $value->field_product[0]->product_id->value();

    // единицы измерения
    $unit_arr = get_product_units($value->nid->value());
    $units_arr[$value->nid->value()] = $unit_arr['cons_unit'];
    // цены
    $prices_arr[] = number_format($value->field_product[0]->commerce_price->amount->value()/100, 0, ',', ' ');
}
$ingredients = implode(' + ', $ingr_arr);
$cons_units = implode('+', $units_arr);
$prices = implode(' + ', $prices_arr);

$summary = $node_wrapper->body->summary->value();


?>

<article class="product teaser" <?php print $attributes; ?>>
    <?php if ($_GET['q'] == 'search'): ?>
        <div class="type"><?php print t('Product') . ': ' . t('Agrochemicals'); ?></div>
    <?php endif; ?>

    <div class="grid-row">
        <div class="grid-1-4 left">
            <?php print $images; ?>
        </div>

        <div class="grid-2-4 left">
            <h2><a href="<?php print $node_url; ?>"><?php print $title; ?></a></h2>
            <?php print $ingredients ? '<h3>' . $ingredients . '</h3>' : ''; ?>
            <div class="summary"><? print $summary; ?></div>
        </div>

        <div class="grid-1-4 left">
            <div class="price">
                <? print $prices ?> РУБЛЕЙ
            </div>
            <div class="components">
                <? print t('Components') . ':'; ?>
                <? foreach($preps_arr as $prep_item): ?>
                    <div class="component">
                        <? print $prep_item; ?>
                    </div>
                <? endforeach; ?>
            </div>
            <div class="actions">
                <a href="<?php print $node_url; ?>" class="submit-button">Подробнее</a>
            </div>
        </div>
    </div>
</article>