<?
// todo перенести всё в template.php
// описание
$summary = $node->body['und'][0]['safe_summary'];

// раздел
$tags = '';

// автор
$author = array();
$user = user_load($node->uid);
$author = person_get_user_array($node->uid);
$author_name = $author['surname'] . '<br />' . $author['name'] . ' ' . $author['name2'];
$author_role = $author['role'];
$author_photo = $author['photo'];
$author_phone = $author['phone'];


// изображение или галерея, при наличии
if (isset($content['field_image_gallery'])) {
    $images = render($content['field_image_gallery']);
}

// контент
$text = $node->body['und'][0]['value'];

// дата
$date = format_date2($node->created, 'custom', 'Опубликовано j F Y в H:i');

// кнопки действия
$author_phone_link = '<a href="">' . $author_phone . '</a>';
$author_message_link = l('Отправить сообщение', "board/ad/$node->nid/message/nojs", array('attributes' => array('class' => array('ctools-modal-style', 'ctools-use-modal'))));

?>
<article class="full"<?php print $attributes; ?>>
    <div class="section"><? print t('Board'); ?></div>

    <? if ($author): ?>
    <div class="author">
        <img src="<? print $author_photo; ?>" alt="">
        <div class="author-body">
            <div class="author-title"><? print $author_name; ?></div>
            <div class="author-subtitle"><? print render($content['field_place']); ?></div>
            <div class="author-actions">
                <? print $author_phone_link; ?>
                <? print $author_message_link; ?>
            </div>
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