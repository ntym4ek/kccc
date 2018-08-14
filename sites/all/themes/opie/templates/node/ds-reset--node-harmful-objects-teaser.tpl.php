<?php
$promo_img_values = field_get_items('node', $node, 'field_promo_image');
if ($promo_img_values) {
    $promo_img = image_style_url('medium_xm', $promo_img_values[0]['uri']);
}
?>

<article class="handbook teaser"<?php print $attributes; ?>>
    <!-- если тизер выводится в результатах поиска - добавить заголовок с названием типа-->
    <?php if ($_GET['q'] == 'search'): ?>
        <div class="type"><?php print t('Handbook "Harmful objects"'); ?></div>
    <?php endif; ?>

    <div class="content" property="content:encoded">
        <?php if ($promo_img_values): ?>
        <div class="img-wrap">
            <a href="<?php print $node_url; ?>" class="promo-image">
                <img src="<?php print $promo_img; ?>" alt="<?php print $title; ?>" property="dc:image">
            </a>
        </div>
        <?php endif; ?>
        <header>
            <h2 property="dc:title" class="title"><a href="<?php print $node_url; ?>"><?php print $title; ?></a></h2>
            <?php print render($content['field_name_latin']); ?>
        </header>
    </div>
</article>