<?php
?>

<article class="product teaser tgrid recomendation text-slide" <?php print $attributes; ?>>
    <div class="grid-full">
        <div class="grid-row photo pos-rel">
            <?php print render($content['product:field_p_images']); ?>
            <?php if ($node->new_product): ?>
                <div class="new-product-<?php print $GLOBALS['language']->language; ?>"></div>
            <?php endif; ?>

            <div class="text-wrap no-date">
                <a href="<?php print $node_url; ?>">
                    <div class="text"><?php print $content['body'][0]['#markup']; ?></div>
                </a>
            </div>
        </div>

        <div class="grid-row title">
            <h2><a href="<?php print $node_url; ?>"><?php print $node->title; ?></a></h2>
        </div>
    </div>

    <div class="buy-table">
        <div class="grid-row price">
            <div class="field-commerce-price">
                <div class="field field-name-commerce-price field-type-commerce-price field-label-hidden">
                    <div class="field-item">
                        <?php print $content['product:commerce_price'][0]['#markup']; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>