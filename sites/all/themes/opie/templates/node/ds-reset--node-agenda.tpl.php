<?php
// Содержимое.
$body_values = field_get_items('node', $node, 'body');
$body = $body_values[0]['safe_value'];
$summary = $body_values[0]['safe_summary'];

// Промо-изображение.
$category = field_get_items('node', $node, 'field_news_category');
$promo_img_values = field_get_items('node', $node, 'field_promo_image');
if ($promo_img_values) {
    $promo_img = image_style_url('news_full', $promo_img_values[0]['uri']);
}

// период проведения События
$period_values = field_get_items('node', $node, 'field_period');
$period_st = format_date($period_values[0]['value'], 'custom', 'd.m.Y');
$period_end = format_date($period_values[0]['value2'], 'custom', 'd.m.Y');
if ($period_st == $period_end) $period = $period_st;
else $period = $period_st . ' - ' . $period_end;

?>

<article class="agenda full"<?php print $attributes; ?>>
    <? if ($promo_img_values): ?>
    <section class="bg-image">
        <img src="<?php print $promo_img; ?>" alt="<?php print $node->title; ?>" class="promo-image" property="dc:image">
        <div class="section"><? print t('Agenda'); ?></div>
        <h1 class="title"><?php print $node->title; ?></h1>
        <img src="/sites/all/themes/opie/images/icons/icon_kccc.png" class="icon">
    </section>
    <? endif; ?>

    <section class="ag-descr">
        <div class="summary"><? print $summary; ?></div>
        <div class="perloc"><span class="period"><? print $period; ?></span><? print $node->field_vacancy_location['und']['0']['value']; ?></div>
    </section>

    <div class="body" property="content:encoded"><? print $body; ?></div>

    <section class="stuff">
        <div class="tags">
            <? if (!empty($tags)): ?>
                <div class="tags-text">Теги:</div>
                <div class="tags-links"><span><? print $tags; ?></span></div>
            <? endif; ?>
        </div>
        <div class="share">
            <div class="share-text">Рассказать:</div>
            <div class="ya-share2" data-services="vkontakte,facebook,twitter"></div>
        </div>
    </section>
</article>
