<?php
// We render it before drupal_render_children to avoid double printing photos.
$photos = render($content['product:field_p_images']);

// единицы измерения
$units = get_product_units($node->nid);

// название и форма с ДВ
$title = get_product_agro_title($node->nid);

// обрабатываемые культуры
$cultures = '';
$c_arr = get_product_main_cultures($node->nid);
foreach ($c_arr as $c_key => $c_item) {
    $cultures .= '<a href="/' . drupal_get_path_alias("node/" . $c_key) . '" target="_blank">' . $c_item['title'] . '</a>; ';
}
?>

<article class="product full" <?php print $attributes; ?>>
    <div class="grid-full agro">
        <div class="grid-1-2 left">
            <?php print $photos; ?>
        </div>

        <div class="grid-1-2 left ">
            <div class="rating">
                <div class="r-summary smooth">
                    Рейтинг:&nbsp;
                    <div class="r-replace"><? print theme('product_rating', array('rating' => $rating)) . '(' . $rating_count . ') '; ?></div>
                    <? if ($rating_link) print $rating_link; ?>
                </div>
                <div class="r-list"></div>
            </div>
            <div class="buy-table">
                <div class="grid-row price">
                    <?php if ($content['product:commerce_price']['#items'][0]['amount'] != 0) : ?>
                        <?php print render($content['product:commerce_price']); ?>
                    <?php else: ?>
                        <div>
                            <div class="field-label fleft"><?php print t('Price'); ?></div>
                            <div class="field-items fleft"><?php print '<a id="price-request" href="#" onclick="supportAPI.openTab(0); return false;">' . t('Check price') . '</a>'; ?></div>
                        </div>
                    <?php endif; ?>

                    <?php print render($content['product:field_p_in_package']); ?>
                </div>

                <div class="grid-row submit">
                    <?php print render($content['field_product']); ?>
                </div>

                <div class="grid-row brief">
                    <div class="per-unit-price">
                        <div class="field-label fleft"><?php print t('Price per ') .'&nbsp;'. mb_strtolower($units['short_unit']) . '. <span style="white-space: nowrap;">' . t('(with NDS)') . '</span>'; ?></div>
                        <?php if ($content['field_pd_price_per_unit']['#items'][0]['amount'] != 0) : ?>
                            <div class="field-items fleft"><?php print $content['field_pd_price_per_unit'][0]['#markup']; ?></div>
                        <?php else: ?>
                            <div class="field-items fleft"><?php print '<a href="#" onclick="yaCounter11541151.reachGoal(\'agro_price_request\'); supportAPI.openTab(0); return true;">' . t('Check price') . '</a>'; ?></div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($content['field_pd_consumption_rate'])) : ?>
                    <div class="range">
                        <div class="field-label fleft"><?php print $content['field_pd_consumption_rate']['#title']; ?></div>
                        <div class="field-items fleft">
                            <?php print (float) $content['field_pd_consumption_rate']['#items'][0]['from']
                                . ' - ' . (float) $content['field_pd_consumption_rate']['#items'][0]['to']
                                . ' ' . $units['cons_unit']; ?>
                        </div>
                    </div>
                    <div class="cost">
                        <div class="field-label fleft"><?php print t('Processing cost'); ?></div>

                        <?php if ($content['field_pd_price_per_unit']['#items'][0]['amount'] != 0) : ?>
                            <div class="field-items">
                                <?php print (float) $content['field_pd_price_per_unit']['#items'][0]['amount'] / 100 * $content['field_pd_consumption_rate']['#items'][0]['from']
                                    . ' - ' . (float) $content['field_pd_price_per_unit']['#items'][0]['amount'] / 100 * $content['field_pd_consumption_rate']['#items'][0]['to']
                                    . ' ' . t('rub') . '/' . $units['field_unit']; ?>
                            </div>
                        <?php else: ?>
                            <div class="field-items fleft">-</div>
                        <?php endif; ?>
                    </div>
                        <?php if(!empty($title['form_full'])): ?>
                        <div class="preparate-form">
                            <div class="field-label fleft"><?php print t('Preparative form'); ?></div>
                            <div class="field-items">
                                <?php print $title['form_full']; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if(!empty($title['ingredients_arr'])): ?>
                        <div class="formulation">
                            <div class="field-label fleft"><?php print t('Active ingredients'); ?></div>
                            <div class="field-items">
                                <?php foreach($title['ingredients_arr'] as $ingr): ?>
                                    <div class="field-item"><?php print $ingr; ?></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>

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
        <?php print render($content['group_description']); ?>
    </div>

</article>