<?php
    hide($content['links']);
    hide($content['comments']);
?>

<article class="<?php print $classes; ?> container"<?php print $attributes; ?>>

  <? if (!empty($backstep_url)): ?>
  <div class="backstep">
    <a href="<? print $backstep_url; ?>"><i class="fas fa-long-arrow-alt-left"></i>&nbsp;&nbsp;<? print t("Back to list");?></a>
  </div>
  <? endif; ?>

  <? if (!empty($author)): ?>
    <div class="node-author">
      <img src="<? print $author['photo']; ?>" alt="<? print $author['role']; ?>" class="img-circle">
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

  <h1 class="node-title">
    <? print $title; ?>
  </h1>

  <div class="divider"></div>

  <div class="summary">
    <?php print $summary; ?>
  </div>

  <? if (!empty($image)): ?>
    <div class="node-image"><?php print $image; ?></div>
  <? endif; ?>

  <div class="node-body<? print (empty($image) ? '': ' node-body-bordered'); ?>" property="content:encoded">
    <? print render($content); ?>
  </div>

  <? if (!empty($tags)): ?>
    <div class="node-tags">
      <? print $tags; ?>
    </div>
  <? endif; ?>

</article>


