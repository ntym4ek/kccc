<?
$form['options']['catalog_announce']['#title'] = '';
$form['body']['und'][0]['summary']['#description'] = '';
?>

    <fieldset class="label-inline form-wrapper">
        <legend><span class="fieldset-legend">Внесите текст</span></legend>
        <div class="fieldset-wrapper">
            <? print render($form['title']); ?>
            <? print render($form['body']); ?>
        </div>
    </fieldset>

    <fieldset class="label-inline form-wrapper">
        <legend><span class="fieldset-legend">Прикрепите теги<i>, 3-4 слова или фразы, характеризующие содержание записи блога</i></span></legend>
        <div class="fieldset-wrapper">
            <? print render($form['field_tags']); ?>
        </div>
    </fieldset>

    <fieldset class="label-inline form-wrapper">
        <legend><span class="fieldset-legend">Добавьте одно или несколько изображений<i>, относящихся к теме записи</i></span></legend>
        <div class="fieldset-wrapper">
            <? print render($form['field_image_gallery']); ?>
        </div>
    </fieldset>

    <? hide($form['language']); ?>
    <? hide($form['field_promo_image']); ?>
    <? echo drupal_render_children($form); ?>