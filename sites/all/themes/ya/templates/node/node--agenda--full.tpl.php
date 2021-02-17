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
      <? if (!empty($viewed)): ?>
          <span class="viewed"><i class="fas fa-eye"></i><? print $viewed; ?></span>
      <? endif; ?>
  </div>
  <? endif; ?>

  <h1 class="node-title">
    <? print $title; ?>
  </h1>

  <div class="divider"></div>

  <div class="row">
    <div class="col-md-8">

      <div class="summary">
        <?php print $summary; ?>
      </div>

      <? if (!empty($image)): ?>
        <div class="node-image"><?php print $image; ?></div>
      <? endif; ?>

      <? print render($content['body']); ?>
    </div>
    <div class="col-md-4">
      <? print empty($content['field_registration']) ? '' : render($content['field_registration']); ?>
    </div>
  </div>

</article>
