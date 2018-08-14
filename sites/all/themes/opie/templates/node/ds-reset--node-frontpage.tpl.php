<?
// раздел
$section = 'Не задано в шаблоне';
switch ($type) {
    case 'blog':
        $sec_text = t('Blogs');
        $sec_url = '/blogs';
        break;
    case 'news':
        $tid_values = field_get_items('node', $node, 'field_news_category');
        $tid = $tid_values[0]['tid'];
        $term = taxonomy_term_load($tid);
        $sec_text = t($term->name);
        $term_path = taxonomy_term_uri($term);
        $sec_url = drupal_get_path_alias($term_path['path']);
        break;
}
$section = '<a href="' . $sec_url . '">' . $sec_text . '</a>';

// заголовок
$node_url = drupal_get_path_alias('node/' . $node->nid);
$title_link = '<a href="' . $node_url . '">' . $title . '</a>';

// изображение
if (empty($field_promo_image))  $img_url = image_style_url('block_1_1', $field_image_gallery['und'][0]['uri']);
else                            $img_url = image_style_url('block_1_1', $field_promo_image['und'][0]['uri']);

?>
<article class="promo-block-article block-1-1"<? print $attributes; ?>>
    <a href="<? print $node_url; ?>"><img src="<? print $img_url; ?>" /></a>
    <div class="section"><? print $section; ?></div>
    <div class="title"><? print $title_link; ?></div>
</article>