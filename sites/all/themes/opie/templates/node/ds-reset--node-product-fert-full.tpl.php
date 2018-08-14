<?php
// We render it before drupal_render_children to avoid double printing photos.
$photos = render($content['product:field_p_images']);

// единицы измерения
$units = get_product_units($node->nid);


?>

<article class="product full" <?php print $attributes; ?>>
    <div class="grid-full fert">
        <div class="grid-1-2 left">
            <?php print $photos; ?>
        </div>

        <div class="grid-1-2 left ">
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
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="grid-full info">
        <?php print render($content['group_description']); ?>
    </div>

</article>