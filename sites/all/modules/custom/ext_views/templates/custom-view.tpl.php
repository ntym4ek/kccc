<?php
/**
 * @file
 * Custom view template.
 */
?>
<div id="<?php print $view['id']; ?>" class="<?php print $view['classes']; ?>">
  <?php if (!empty($view['exposed'])): ?>
    <div class="view-filters">
      <?php print $view['exposed']; ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($view['content'])): ?>
    <div class="view-content">
      <?php print $view['content']; ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($view['pager'])): ?>
    <?php print $view['pager']; ?>
  <?php endif; ?>
</div>

