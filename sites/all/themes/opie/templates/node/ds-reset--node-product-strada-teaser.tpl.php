<?php
// необходим для результатов поиска
?>

<article class="product teaser" <?php print $attributes; ?>>
    <!-- если тизер выводится в результатах поиска - добавить заголовок с названием типа-->
    <?php if ($_GET['q'] == 'search'): ?>
        <div class="type"><?php print t('Product') . ': ' . t('Private farm products'); ?></div>
    <?php endif; ?>

    <div class="grid-full">
        <div class="grid-1-4 left">
            <?php print render($content['product:field_p_images']); ?>
        </div>
        <div class="grid-3-4 left">
            <h2><a href="<?php print $node_url; ?>"><?php print $node->title; ?></a></h2>

            <div class="buy-table">
                <div class="grid-row">
                    <div class="grid-1-2 summary left">
                        <?php print render($content['body']); ?>
                        <div class="more">
                            <a href="<?php print $node_url; ?>"><?php print t('More...') ?></a>
                        </div>
                    </div>
                    <div class="grid-1-2 price left">
                        <?php if ($content['product:commerce_price']['#items'][0]['amount'] != 0) : ?>
                            <?php print render($content['product:commerce_price']); ?>
                        <?php else: ?>
                            <div class="field-label fleft"><?php print t('Price'); ?></div>
                            <div class="field-items"><?php print '<a href="#" onclick="supportAPI.openTab(0); return false;">' . t('Check price') . '</a>'; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="grid-row">
                    <div class="grid-1-2 info left">
                    </div>
                    <div class="grid-1-2 submit right">
                        <?php print render($content['field_product']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>