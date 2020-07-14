<?php
    hide($content['links']);
    hide($content['field_vacancy_form']);
    hide($content['body']);
    $st = (isset($field_vacancy_employer[0]) && $field_vacancy_employer[0]['value'] == 'st') ? true : false;
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
                    <div class="media contact-card col-md-6">
                        <div class="media-left">
                            <img class="media-object" src="/sites/all/modules/_/contacts/images/photo/ogorodova.png" alt="Отдел кадров">
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">Огородова<br>Мария Николаевна</h4>
                            <div class="contact-dep">Менеджер по персоналу</div>


                            <div class="contact-phones">
                                <div class="contact-phone"><a href="tel:+78332761522" rel="nofollow">+7(8332) 76-15-22, доб. 1186</a></div>
                            </div>
                            <div class="contact-emails">
                                <div class="contact-email"><a href="mailto:maria.ogorodova@kccc.ru" class="" rel="nofollow">maria.ogorodova@kccc.ru</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>

</article>
