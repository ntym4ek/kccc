<?php
    hide($content['links']);
?>

<article class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>

    <? if (!empty($author)): ?>
    <div class="node-author">
        <img src="<? print $author['photo']; ?>" alt="" class="img-circle">
        <div class="author-body">
            <div class="author-title"><? print $author['surname'] . ' ' . $author['name'] . ' ' . $author['name2']; ?></div>
            <div class="author-subtitle"><? print $author['role']; ?></div>
        </div>
    </div>
    <? endif; ?>

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

    <? if (!empty($image)): ?>
        <div class="node-image"><?php print $image; ?></div>
    <? endif; ?>

    <div class="node-body<? print (empty($image) ? '': ' node-body-bordered'); ?>" property="content:encoded">
        <? print (empty($content['field_registration']) ? '': '<div class="row">'); ?>
            <?php if (empty($content['field_registration'])): ?>
                <? print render($content); ?>
            <?php else: ?>
                <div class="col-md-7"><? print render($content['body']); ?></div>
                <div class="col-md-5 do-not-print"><? print render($content['field_registration']); ?></div>
            <? endif; ?>
        <? print (empty($content['field_registration']) ? '': '</div>'); ?>
    </div>


    <? if (!empty($tags)): ?>
    <div class="node-tags">
        <? print $tags; ?>
    </div>
    <? endif; ?>
</article>