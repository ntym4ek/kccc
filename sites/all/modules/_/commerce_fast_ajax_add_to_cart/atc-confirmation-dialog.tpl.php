<?php
    $product_wrapper = entity_metadata_wrapper('commerce_product', $line_item->commerce_product['und'][0]['product_id']);
    $name = $product_wrapper->title->value();
    $qty = $line_item->quantity;
?>
<div class="atc-confirmation-dialog">
    <div class="text">
        <?php echo  '<span>' . $name . ' x ' . $qty . ' ' . t('pcs') . '.</span>'
                    . t('Product added to cart') . ', ' . t('now you can:'); ?>
    </div>
    <div class="links">
        <a href="<?php echo url('checkout'); ?>" class="submit-button"><?php echo t('Go to checkout'); ?></a>
        <a href="#" class="submit-button" onclick="jQuery('#atc-confirmation-dialog').dialog('close'); return false;"><?php echo t('Continue shopping'); ?></a>
    </div>
</div>