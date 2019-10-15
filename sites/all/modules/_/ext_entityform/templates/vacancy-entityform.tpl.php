<?php
hide($form['intro']);
?>

<div class="vacancy-form">
        <fieldset class="panel panel-default panel-shadow">
            <legend class="panel-heading">
                <span class="panel-title fieldset-legend"><? print t('My response'); ?></span>
            </legend>
            <div class="panel-body">
                <? print render($form['field_vacancy_surname']); ?>
                <? print render($form['field_vacancy_name_1']); ?>
                <? print render($form['field_vacancy_name_2']); ?>
                <? print render($form['field_vacancy_location']); ?>
                <? print render($form['field_contact_email']); ?>
                <? print render($form['field_vacancy_file']); ?>

                <? print drupal_render_children($form); ?>
            </div>
        </fieldset>
</div>

