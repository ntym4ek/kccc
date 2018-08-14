<?php
// We render it before drupal_render_children to avoid double printing photos.
$image_title = $node->field_promo_image['und'][0]['title'] ? $node->field_promo_image['und'][0]['title'] : $title;
$image_alt = $node->field_promo_image['und'][0]['alt'];
$image_url = image_style_url('product_medium', $node->field_promo_image['und'][0]['uri']);
$images = '<img src="' . $image_url . '" alt="' . $image_alt . '" />';

$node_wrapper = entity_metadata_wrapper('node', $node->nid);
$products = $preps_arr = $units_arr = $prices_arr = array();
foreach($node_wrapper->field_pd_mix_components->getIterator() as $key => $value) {
    // препараты
    $prep = get_product_agro_title($value->nid->value());
    $prep_url = drupal_get_path_alias('node/' . $value->nid->value());
    $preps_arr[] = '<a href="/' . $prep_url . '" target="_blank">' . $prep['title'] . ', ' . $prep['formulation'] . '</a><br /><span class="ingredients">(' . drupal_strtolower($prep['ingredients']) . ')</span>';
    $products[] = $value->field_product[0]->product_id->value();

    // единицы измерения
    $unit_arr = get_product_units($value->nid->value());
    $units_arr[$value->nid->value()] = $unit_arr['cons_unit'];
    // цены
    $prices_arr[] = $value->field_product[0]->commerce_price->amount->value()/100;
}
$cons_units = implode('+', $units_arr);
$prices = implode(' + ', $prices_arr);

// обрабатываемые культуры
$cultures = '';
$c_arr = get_product_main_cultures($node->nid);
foreach ($c_arr as $c_key => $c_item) {
    $cultures .= '<a href="/' . drupal_get_path_alias("node/" . $c_key) . '" target="_blank">' . $c_item['title'] . '</a>; ';
}

// форма добавления в корзину
$form = drupal_get_form('product_mix_add_to_cart_form', $products);
?>

<article class="product full" <?php print $attributes; ?>>
    <div class="grid-full agro">
        <div class="grid-1-2 left">
            <?php print $images; ?>
        </div>

        <div class="grid-1-2 left ">
            <div class="buy-table">
                <div class="grid-row price">
                    <div class="field-commerce-price">
                        <div class="field field-name-commerce-price">
                            <div class="field-item"><? print $prices ?> РУБЛЕЙ</div>
                        </div>
                    </div>

                    <div class="field-components">
                        <div class="field-label left">Компоненты</div>
                        <div class="field-items left">
                            <? foreach($preps_arr as $prep_item): ?>
                            <div class="field-item">
                                <? print $prep_item; ?>
                            </div>
                            <? endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="grid-row submit">
                    <?php print render($form); ?>
                </div>

                <div class="grid-row brief">
                    <?php if (!empty($cultures)): ?>
                        <div class="growth">
                            <div class="field-label left">Культуры</div>
                            <div class="field-items">
                                <?php print $cultures; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($price_link != '/') : ?>
                <div class="grid-row tright mr15">
                    <a href="<?php print $price_link; ?>" class="cgreen mr30"><?php print t('Price list'); ?></a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="grid-full info">
        <?php print render($content['group_tabs']); ?>
    </div>

</article>