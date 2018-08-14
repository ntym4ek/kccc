<?php
?>

<article class="product full" <?php print $attributes; ?>>
    <div class="grid-full joy">
        <div class="grid-1-3 left pt10">
            <?php print render($content['product:field_p_images']); ?>
        </div>
        <div class="grid-2-3 left pt10">
            <div class="rating">
                <div class="r-summary smooth">
                    Рейтинг:&nbsp;
                    <div class="r-replace"><? print theme('product_rating', array('rating' => $rating)) . '(' . $rating_count . ') '; ?></div>
                    <? if ($rating_link) print $rating_link; ?>
                </div>
                <div class="r-list"></div>
            </div>
            <div class="grid-row body">
                <?php print render($content['body']['#items'][0]['value']); ?>
            </div>
            <div class="grid-row price">
                <?php if ($content['product:commerce_price']['#items'][0]['amount'] != 0) : ?>
                    <?php print render($content['product:commerce_price']); ?>
                <?php else: ?>
                    <div class="field-label fleft"><?php print t('Price'); ?></div>
                    <div class="field-items fleft">
                        <?php print '<a href="#" onclick="supportAPI.openTab(0); return false;">' . t('Check price') . '</a>'; ?>
                    </div>
                <?php endif; ?>
                <div class="shipping">
                    <a href="/info/shipping" target="_blank"
                       alt="<?php print t('Shipping information'); ?>"
                       title="<?php print t('Shipping information'); ?>"><span> </span></a>
                </div>
            </div>
            <div class="grid-row submit">
                Заказать препарат можно в нашем новом интернет-магазине <a href="https://joy-magazin.ru" style="color: #2b8aff;">Joy-Magazin.ru</a>
<!--                --><?php //print render($content['field_product']); ?>
            </div>
        </div>
    </div>
</article>

<?php
// блок с рекоммендациями - views
if (!empty($content['field_pd_recommend']['#items'])) {
    $recommends = array();
    foreach ($content['field_pd_recommend']['#items'] as $key => $item) {
        $recommends[] = $item['target_id'];
    }
    print views_embed_view('shop_product_recommendations', $display_id = 'default', implode(',', $recommends));
}
?>
