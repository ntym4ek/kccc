<?php
?>

<article class="product teaser tgrid" <?php print $attributes; ?>>
    <div class="grid-full">

        <div class="grid-row photo">
            <?php print render($content['product:field_p_images']); ?>
            <?php if ($node->new_product): ?>
                <div class="new-product-<?php print $GLOBALS['language']->language; ?>"></div>
            <?php endif; ?>
        </div>

        <div class="rating-short">
            <a href="<? print $node_url; ?>" title="Оценка препарата"><i class="fa fa-star" aria-hidden="true"></i> (<? print $rating_count; ?>)</a>
        </div>

        <div class="grid-row title">
            <h2><a href="<?php print $node_url; ?>"><?php print $node->title; ?></a></h2>
        </div>

        <div class="grid-row summary">
            <?php print render($content['body']); ?>
        </div>

        <div class="buy-table">
            <div class="grid-row price">
                <?php print render($content['product:commerce_price']); ?>
            </div>
            <div class="grid-row submit">
<!--                --><?php //print render($content['field_product']); ?>
            </div>
        </div>
    </div>
</article>