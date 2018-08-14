<?php
// Содержимое.
$body_values = field_get_items('node', $node, 'body');
$body = $body_values[0]['value'];

// Промо-изображение.
$promo_img_values = field_get_items('node', $node, 'field_promo_image');
if ($promo_img_values) {
    $promo_img = file_create_url($promo_img_values[0]['uri']);
}
?>
<article class="full"<?php print $attributes; ?>>
    <div class="content" property="content:encoded">
        <?php if ($promo_img_values): ?>
            <div class="img-wrap">
                <img src="<?php print $promo_img; ?>"
                     alt="<?php print $node->title; ?>" class="promo-image" property="dc:image">
            </div>
        <?php endif; ?>

        <div class="text-wrap">
            <?php print $body; ?>
        </div>
    </div>
</article>
