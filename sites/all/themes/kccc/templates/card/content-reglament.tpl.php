<?php
?>

<?php if (!empty($card['hobjects']['formatted'])): ?>
<div class="hobjects">
  <?php print $card['hobjects']['formatted']; ?>
</div>
<?php endif; ?>

<div class="row">
  <div class="col-xs-6">
    <div class="spec">
      <div class="label"><?php print t('preparation use rate'); ?></div>
      <div class="text"><?php print $card['prep_spends']['formatted']; ?></div>
    </div>
  </div>
  <div class="col-xs-6">
    <div class="spec">
      <div class="label"><?php print t('working fluid use rate'); ?></div>
      <div class="text"><?php print $card['mix_spend']['formatted']; ?></div>
    </div>
  </div>
</div>
<?php if (!empty($card['wait']['formatted'] || !empty($card['ratio']['formatted']))): ?>
<div class="row">
  <div class="col-xs-6">
    <div class="spec">
      <div class="label"><?php print t('waiting period'); ?></div>
      <div class="text"><?php print $card['wait']['formatted']; ?></div>
    </div>
  </div>
  <div class="col-xs-6">
    <div class="spec">
      <div class="label"><?php print t('treatments qty'); ?></div>
      <div class="text"><?php print $card['ratio']['formatted']; ?></div>
    </div>
  </div>
</div>
<?php endif; ?>
<?php if ($card['description']): ?>
<div class="row">
  <div class="col-xs-12">
    <div class="spec">
      <div class="label"><?php print t('method and time of treatment'); ?></div>
      <div class="description"><?php print $card['description']; ?></div>
    </div>
  </div>
</div>
<?php endif; ?>
