<?
$form['options']['catalog_announce']['#title'] = '';
$form['body']['und'][0]['summary']['#description'] = '';
?>

<div class="custom-form">
    <fieldset class="label-inline form-wrapper">
        <legend><span class="fieldset-legend">Оформите новость</span></legend>
        <div class="fieldset-wrapper">
            <? print render($form['field_news_category']); ?>
            <? print render($form['title']); ?>
            <? print render($form['body']); ?>
        </div>
    </fieldset>

    <fieldset class="label-inline form-wrapper">
        <legend><span class="fieldset-legend">Добавьте одно или несколько изображений<i>, иллюстрирующих новость</i></span></legend>
        <div class="fieldset-wrapper">
            <? print render($form['field_image_gallery']); ?>
        </div>
    </fieldset>

    <? hide($form['language']); ?>
    <? hide($form['field_promo_image']); ?>
    <? echo drupal_render_children($form); ?>
</div>