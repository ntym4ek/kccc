<?php
    hide($content['links']);
    hide($content['field_vacancy_form']);
    hide($content['body']);
?>

<article class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>

    <? if (!empty($date) || !empty($period) || !empty($location) || !empty($viewed)): ?>
    <div class="node-stuff">
        <? if (!empty($date) || !empty($period)): ?>
            <?php print empty($period) ? $date : $period; ?>
        <? endif; ?>
        <? if (!empty($location)): ?>
            <span class="location"><i class="fas fa-location-arrow"></i><? print $location; ?></span>
        <? endif; ?>
        <? if (!empty($viewed)): ?>
            <span class="viewed"><i class="fas fa-eye"></i><? print $viewed; ?></span>
        <? endif; ?>
    </div>
    <? endif; ?>

    <div class="row">
        <div class="col-md-7">
            <fieldset class="panel panel-default">
                <legend class="panel-heading">
                    <span class="panel-title fieldset-legend"><? print t('Workplace'); ?></span>
                </legend>
                <div class="panel-body">
                    <? print render($content['field_region']); ?>
                    <? print render($content['field_vacancy_location']); ?>
                    <? print render($content['field_vacancy_employer']); ?>
                </div>
            </fieldset>

            <fieldset class="panel panel-default">
                <legend class="panel-heading">
                    <span class="panel-title fieldset-legend"><? print t('About vacancy'); ?></span>
                </legend>
                <div class="panel-body">
                    <? print render($content); ?>
                </div>
            </fieldset>
        </div>

        <div class="col-md-5">
            <? print render($content['field_vacancy_form']); ?>
        </div>

        <div class="col-md-12">
            <fieldset class="panel panel-default">
                <legend class="panel-heading">
                    <span class="panel-title fieldset-legend"><? print t('Your actions'); ?></span>
                </legend>
                <div class="panel-body">
                    <ul>
                        <li>Отправить отклик</li>
                        <li>Дождаться звонка с приглашением на собеседование от сотрудников нашей кадровой службы</li>
                    </ul>

                    <? print render($content['body']); ?>
                </div>
            </fieldset>

            <fieldset class="panel panel-default">
                <legend class="panel-heading">
                    <span class="panel-title fieldset-legend"><? print t('Contacts'); ?></span>
                </legend>
                <div class="panel-body">
                  <? print $contact; ?>
                </div>
            </fieldset>
        </div>
    </div>

</article>
