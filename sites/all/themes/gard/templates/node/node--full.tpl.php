<?php
    hide($content['links']);
    hide($content['comments']);
?>

<article class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>

    <? if (!empty($author) && empty($author['anonimous'])): ?>
    <div class="node-author">
        <img src="<? print $author['photo']; ?>" alt="<? print $author['staff']['role_full']; ?>" class="img-circle">
        <div class="author-body">
          <div class="author-title"><a href="/user/<? print $author['id']; ?>"><? print $author['full_name']; ?></a></div>
            <div class="author-subtitle"><? print $author['label']; ?></div>
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
    <? if (isset($content["field_review_intro"]) && !empty($content["body"][0]["#markup"])):?>
      <? print render($content['field_review_intro']); ?>
      <blockquote>
        <? print render($content['body']); ?>
      </blockquote>
      <? endif; ?>

      <? print render($content); ?>
    </div>

</article>
