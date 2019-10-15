<?php
hide($form['intro']);
?>

<div class="row agro-expert">
    <div class="col-md-7">
        <fieldset class="panel panel-default">
            <legend class="panel-heading">
                <span class="panel-title fieldset-legend"><? print t('Need help'); ?></span>
            </legend>
            <div class="panel-body">
                <? print render($form['field_f_s_culture']); ?>
                <? print render($form['field_f_s_m_phase_mc']); ?>
            </div>
        </fieldset>

        <? print render($form['field_image']); ?>
        <? print render($form['field_ho_type']); ?>
        <? print render($form['field_pd_r_hobjects_comment']); ?>
        <? print render($form['actions']); ?>
    </div>
    <div class="col-md-5">
        <fieldset class="panel panel-default panel-shadow">
            <legend class="panel-heading">
                <span class="panel-title fieldset-legend"><? print t('My contacts'); ?></span>
            </legend>
            <div class="panel-body">
                <? print render($form['field_f_region']); ?>
                <? print render($form['field_company']); ?>
                <? print render($form['field_fullname']); ?>
                <? print render($form['field_phone']); ?>
                <? print render($form['field_email']); ?>
            </div>
        </fieldset>
    </div>

    <? print drupal_render_children($form); ?>

</div>

