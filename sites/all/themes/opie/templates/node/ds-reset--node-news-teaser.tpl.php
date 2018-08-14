<?php
// Content.
$body_values = field_get_items('node', $node, 'body');
$body = $body_values[0]['safe_value'];

// Promo-image.
$promo_img_values = field_get_items('node', $node, 'field_promo_image');
if (!$promo_img_values) $promo_img_values = field_get_items('node', $node, 'field_image_gallery');
if ($promo_img_values) {
    $promo_img = image_style_url('news_teaser', $promo_img_values[0]['uri']);
}

// для страницы поиска определить тип тизера
$type = '';
if ($_GET['q'] == 'search') {
    switch ($field_news_category['und'][0]['tid']) {
        case 6: $type = t('Market reviews'); break;
        case 7: $type = t('Interesting facts'); break;
        case 40: $type = t('JOY Magazine'); break;
        default : $type = t('News');
    }
}
?>

<article class="news teaser contextual-links-region"<?php print $attributes; ?>>
    <?php if ($type): ?>
        <div class="type"><?php print $type; ?></div>
    <?php endif; ?>

    <div class="content" property="content:encoded">
        <div class="img-wrap">
            <?php if ($promo_img_values): ?>
            <a href="<?php print $node_url; ?>" class="promo-image">
                <img src="<?php print $promo_img; ?>" alt="<?php print $node->title; ?>" property="dc:image">
            </a>

            <div class="submit-info">
                <span class="date"><?php print format_date($node->created, 'custom', 'd M'); ?></span>
                <span class="year"><?php print format_date($node->created, 'custom', 'Y'); ?></span>
                <a href="<?php print $node_url; ?>#comments">
                    <span class="comments"><?php print $node->comment_count; ?></span>
                </a>
            </div>
            <?php endif; ?>
        </div>
        <header>
          <div>
            <h2 property="dc:title" class="title"><a href="<?php print $node_url; ?>"><?php print $title; ?></a></h2>
            <?php print $ds_content; ?>
          </div>
        </header>
    </div>
</article>
