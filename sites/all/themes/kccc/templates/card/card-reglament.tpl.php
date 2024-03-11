<?php
?>

<div class="card card-reglament">
  <div class="content">
    <div class="decor-line"></div>
    <div class="decor-circle">
      <?php if ($card['icon_num']): ?>
      <i class="icon icon-<?php print $card['icon_num']; ?>"></i>
      <?php endif; ?>
    </div>

    <div class="cultures">
      <?php print $card['cultures']['formatted']; ?>
    </div>

    <div class="hobjects">
      <?php print $card['hobjects']['formatted']; ?>
    </div>

    <div class="row">
      <div class="col-xs-6">
        <div class="b">
          <div class="title"><?php print t('preparation use rate'); ?></div>
          <div class="text"><?php print $card['prep_spends']['formatted']; ?></div>
        </div>
      </div>
      <div class="col-xs-6">
        <div class="b">
          <div class="title"><?php print t('working fluid use rate'); ?></div>
          <div class="text"><?php print $card['mix_spend']['formatted']; ?></div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-6">
        <div class="b">
          <div class="title"><?php print t('waiting period'); ?></div>
          <div class="text"><?php print $card['wait']['formatted']; ?></div>
        </div>
      </div>
      <div class="col-xs-6">
        <div class="b">
          <div class="title"><?php print t('treatments qty'); ?></div>
          <div class="text"><?php print $card['ratio']['formatted']; ?></div>
        </div>
      </div>
    </div>
    <?php if ($card['description']): ?>
    <div class="row">
      <div class="col-xs-12">
        <div class="b">
          <div class="title"><?php print t('method and time of treatment'); ?></div>
          <div class="description"><?php print $card['description']; ?></div>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>

