<?php
// лого
$logo_url = '/sites/default/files/images/etc/logo_joy.png';

// категория
$category = 'Не задано в шаблоне';
$tid_values = field_get_items('node', $node, 'field_pd_category');
$tid = $tid_values[0]['tid'];
$term = taxonomy_term_load($tid);
$sec_text = t($term->name);
$term_path = taxonomy_term_uri($term);
$sec_url = drupal_get_path_alias($term_path['path']);
$category = '<a href="' . $sec_url . '">' . $sec_text . '</a>';

// заголовок
$node_url = drupal_get_path_alias('/node/' . $node->nid);
$title_link = '<a href="' . $node_url . '">' . $title . '</a>';

// изображение
$img_url = image_style_url('block_novelties', $content['product:field_p_images']['#items'][0]['uri']);

// цена
$amount = $content['product:commerce_price']['#items'][0]['amount']/100;

// текст кнопки
$buy_text = t('Read more');
if ($amount) $buy_text = t('Buy');
?>

<article class="promo-block-product joy block-1-1"<?php print $attributes; ?>>
    <img class="strada-logo" src="<?php print $logo_url; ?>" />
    <img class="product" src="<?php print $img_url; ?>" />
    <div class="category"><?php print $category; ?></div>
    <div class="title"><?php print $title_link; ?></div>
    <div class="price">
        <?php if ($amount): ?>
            <?php print $amount; ?><i class="fa fa-rub" aria-hidden="true"></i>
        <?php endif; ?>
    </div>
    <a href="<?php print $node_url; ?>" class="submit-button">
        <?php if ($amount): ?>
            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
        <?php endif; ?>
        <?php print $buy_text; ?>
    </a>
</article>