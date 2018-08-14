<?
// todo перенести всё в template.php
// описание
if (isset($node->body['und'][0]['safe_summary'])) { $summary = $node->body['und'][0]['safe_summary']; }
elseif (isset($node->body['ru'][0]['safe_summary'])) { $summary = $node->body['und'][0]['safe_summary']; }
    else $summary = 'Содержимое не определено. Сообщите админимтратору.';

// раздел
$section = 'Не задано в шаблоне';
$show_author = false;
$tags = '';
switch ($type) {
    case 'blog':
        $show_author = true;
        $section = t('Blogs');

        // теги
        if (!empty($node->field_tags['und'])) {
            foreach($node->field_tags['und'] as $item) {
                $term = taxonomy_term_load($item['tid']);
                $tag_url = '/blogs/tag/' . $item['tid'];
                $tags_arr[] = '<a href="' . $tag_url . '">' . $term->name . '</a>';
            }
            $tags = implode(', ', $tags_arr);
        }

        break;
    case 'news':
        $tid_values = field_get_items('node', $node, 'field_news_category');
        $tid = $tid_values[0]['tid'];
        $term = taxonomy_term_load($tid);
        $section = t($term->name);
        break;
    case 'review':
        $show_author = true;
        $section = t('Reviews');
        if (!empty($content['field_review_intro']['#items'][0]['safe_value'])) {
            $summary = $content['field_review_intro']['#items'][0]['safe_value'];
        }
        break;
    case 'main_cultures':
        $section = t('Main cultures');
        break;
    case 'harmful_objects':
        $section = t('Harmful objects');
        break;
    case 'weed':
        $section = t('Weeds');
        break;
    case 'pest':
        $section = t('Pests');
        break;
}


// автор
$author = array();
if ($show_author) {
    $user = user_load($node->uid);
    $author = person_get_user_array($node->uid);
    $author_name = $author['surname'] . '<br />' . $author['name'] . ' ' . $author['name2'];
    $author_role = $author['role'];
    $author_photo = $author['photo'];
}


// изображение или галерея, при наличии
if (isset($content['field_image_gallery'])) {
    $images = render($content['field_image_gallery']);
} else {
    $image_title = empty($node->field_promo_image['und'][0]['title']) ? $title : $node->field_promo_image['und'][0]['title'];
    $image_alt = empty($node->field_promo_image['und'][0]['alt']) ? $title : $node->field_promo_image['und'][0]['alt'];

    if ($node->field_promo_image['und'][0]['height'] <= 430) {
        $image_url = image_style_url('news_full_vertical', $node->field_promo_image['und'][0]['uri']);
    } else {
//        $image_url = image_style_url('news_full', $node->field_promo_image['und'][0]['uri']);
        $image_url = image_style_url('news_full_horizontal_hd', $node->field_promo_image['und'][0]['uri']);
    }
    $images = '<a href="' . file_create_url($node->field_promo_image['und'][0]['uri']) . '" class="fancybox"><img src="' . $image_url . '" alt="' . $image_alt . '" /></a><div class="image-title">' . $image_title . '</div>';
}

// контент
if (isset($node->body['und'][0]['value'])) { $text = $node->body['und'][0]['value']; }
elseif (isset($node->body['ru'][0]['value'])) { $text = $node->body['ru'][0]['value']; }
    else $text = 'Содержимое не определено. Сообщите админимтратору.';

// дата
$date = '';
if (!in_array($node->type, array('main_cultures', 'harmful_objects', 'weed', 'pest'))) {
    $date = format_date2($node->created, 'custom', 'j F Y');
}

?>
<article class="full"<?php print $attributes; ?>>
    <div class="section"><? print $section; ?></div>

    <? if ($author): ?>
    <div class="author">
        <img src="<? print $author_photo; ?>" alt="">
        <div class="author-body">
            <div class="author-title"><? print $author_name; ?></div>
            <div class="author-subtitle"><? print $author_role; ?></div>
        </div>
    </div>
    <? endif; ?>

    <h1 class="title"><? print $title; ?></h1>

    <? if ($summary): ?>
    <h2 class="summary"><? print $summary; ?></h2>
    <? endif; ?>

    <? if ($date): ?>
    <time><? print $date; ?></time>
    <? endif; ?>

    <div class="images"><?php print $images; ?></div>

    <div class="body" property="content:encoded"><? print $text; ?></div>

    <section class="stuff">
        <div class="tags">
            <? if ($tags): ?>
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

<section class="comments">
    <? print render($content['comments']); ?>
</section>