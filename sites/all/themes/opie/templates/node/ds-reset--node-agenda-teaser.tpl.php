<?php
// Content.
$body_values = field_get_items('node', $node, 'body');
$body = $body_values[0]['safe_value'];
// period
$period_values = field_get_items('node', $node, 'field_period');
$from = $period_values[0]['value'];
$to = $period_values[0]['value2'];
if ($from == $to) {
    if (format_date($from, 'custom', 'd') == '01') $period = format_date($from, 'custom', 'F Y');
    else $period = format_date($from, 'custom', 'd.m.Y');
}
else {
    $period = format_date($from, 'custom', 'd.m.y') . ' - ' . format_date($to, 'custom', 'd.m.y');
}
// Promo-image.
$promo_img_values = field_get_items('node', $node, 'field_preview_image') ? field_get_items('node', $node, 'field_preview_image') : field_get_items('node', $node, 'field_promo_image');;
if ($promo_img_values) {
    $promo_img = image_style_url('news_teaser', $promo_img_values[0]['uri']);
}
?>

<article class="agenda teaser contextual-links-region"<?php print $attributes; ?>>
    <?php if ($_GET['q'] == 'search'): ?>
        <div class="type"><?php print t('Agenda'); ?></div>
    <?php endif; ?>

    <div class="content" property="content:encoded">
        <?php if ($promo_img_values): ?>
            <div class="img-wrap">
                <a href="<?php print $node_url; ?>" class="promo-image">
                    <img src="<?php print $promo_img; ?>"
                         alt="<?php print $node->title; ?>" property="dc:image">
                </a>
            </div>
        <?php endif; ?>
        <header>
          <div>
            <h2 property="dc:title" class="title"><a href="<?php print $node_url; ?>"><?php print $title; ?></a></h2>
            <div class="date-wrap"><?php print $period; ?></div>
            <?php print $ds_content; ?>
          </div>
        </header>
    </div>
</article>
