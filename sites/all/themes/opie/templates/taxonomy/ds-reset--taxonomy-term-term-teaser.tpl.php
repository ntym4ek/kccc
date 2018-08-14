<?php
// id родительской категории
$pid = &drupal_static(__FUNCTION__);
if (!isset($pid)) {
    $arr_pid = taxonomy_get_parents($tid);
    $pid = key($arr_pid);
}

$promo_img = '';
if ($pid == 1 || $pid == 2) {
    if ($promo_img_values = field_get_items('taxonomy_term', $term, 'field_shop_category_image'))
        $promo_img = image_style_url('category_list', $promo_img_values[0]['uri']);
} else {
    if ($promo_img_values = field_get_items('taxonomy_term', $term, 'field_promo_image'))
        $promo_img = image_style_url('medium_xm', $promo_img_values[0]['uri']);
}

$url = url('taxonomy/term/' . $term->tid);
$url = empty($field_link['und'][0]['value']) ? $url : $field_link['und'][0]['value'];

$color = $term->field_color['und'][0]['value'];

?>

<? if ($pid == 1 || $pid == 2): ?>
    <article class="category teaser tid_<?php print $term->tid; ?>" >
        <a href="<?php print $url; ?>" class="link">
            <? if ($promo_img): ?><img src="<?php print $promo_img; ?>" alt="<?php print $term_name; ?>" property="dc:image"><? endif; ?>
            <h2 style="background-color: #<? print $color; ?>;"><? print $term->name; ?></h2>
        </a>
    </article>
<? else: ?>
    <article class="taxonomy teaser"<?php print $attributes; ?>>
        <div class="content" property="content:encoded">
            <?php if ($promo_img): ?>
                <div class="img-wrap">
                    <a href="<?php print $term_url; ?>" class="promo-image">
                        <img src="<?php print $promo_img; ?>" alt="<?php print $term_name; ?>" property="dc:image">
                    </a>
                </div>
            <?php endif; ?>
            <header>
                <h2 property="dc:title" class="title"><a href="<?php print $term_url; ?>"><?php print $term_name; ?></a></h2>
                <?php print $ds_content; ?>
            </header>
        </div>
    </article>
<? endif; ?>