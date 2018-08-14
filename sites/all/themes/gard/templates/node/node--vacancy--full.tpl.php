<?php
    hide($content['links']);
    hide($content['field_vacancy_form']);
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
    </div>

</article>