<?php
?>
<div class="pvp-user-list">
  <?php if (!empty($content['title1'])): ?>
    <header>
      <div class="cover"></div>
      <div class="title-1"><?php print $content['title1']; ?></div>
      <div class="title-2"><?php print ($content['title2'] ?? ''); ?></div>
      <div class="title-3"><?php print ($content['title3'] ?? ''); ?></div>
      <div class="title-4"><?php print ($content['title4'] ?? ''); ?></div>
    </header>
    <div class="subheader-menu">
      <?php print $content['menu']; ?>
    </div>
  <?php endif; ?>

    <div class="content">
      <?php if (!empty($content['message'])): ?>
        <div class="pvp-message"><?php print $content['message']; ?></div>
      <?php endif; ?>

      <?php if (!empty($content['items'])): ?>
        <?php foreach($content['items'] as $item): ?>
            <?php print $item; ?>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
</div>

