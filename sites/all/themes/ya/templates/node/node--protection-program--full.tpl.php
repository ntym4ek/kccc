<?php
hide($content['links']);
?>

<article class="<?php print $classes; ?>"<?php print $attributes; ?>>

  <? if (!empty($backstep_url)): ?>
    <div class="backstep">
      <a href="<? print $backstep_url; ?>"><i class="fas fa-long-arrow-alt-left"></i>&nbsp;&nbsp;<? print t("Back to list");?></a>
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
    </div>
  <? endif; ?>

  <? if (empty($node_title_off)): ?>
    <h1 class="node-title">
      <? print $title; ?>
    </h1>

    <div class="divider"></div>
  <? endif; ?>

  <div class="summary">
    <?php print $summary; ?>
  </div>

  <? if (!empty($image)): ?>
    <div class="node-image"><?php print $image; ?></div>
  <? endif; ?>

  <div class="node-body<? print (empty($image) ? '': ' node-body-bordered'); ?>" property="content:encoded">
    <? print render($content); ?>
  </div>

  <? if (!empty($program)): ?>
    <div>
      <? print $program; ?>
    </div>
  <? endif; ?>

</article>

