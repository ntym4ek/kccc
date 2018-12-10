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
            <div class="node-body" property="content:encoded">
                <? print render($content); ?>
            </div>
        </div>
        <div class="col-md-5">
            <? print render($content['field_vacancy_form']); ?>
        </div>
        <div class="col-md-12">
            <div class="field field-label-above">
                <div class="field-label">Ваши действия</div>
                <div class="field-item">
                    <ul>
                        <li>Отправить отклик</li>
                        <li>Дождаться звонка с приглашением на собеседование от сотрудников нашей кадровой службы</li>
                    </ul>
                </div>
            </div>
            <? print render($content['body']); ?>
            <div class="contacts">
                Контактное лицо: <?php print $st ? 'Шиврина Дарья Сергеевна' : 'Жуйкова Елена Анатольевна'; ?><br />
                Телефон: <?php print $st ? '+7 (83361) 5-20-62' : '+7 (83361) 9-28-24'; ?><br />
                E-mail: <?php print $st ? 'darya.shivrina@kccc.ru' : 'elena.zhuykova@kccc.ru'; ?><br />
            </div>
        </div>
    </div>

</article>