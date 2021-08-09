<?php
  $text = empty($row->field_field_text[0]["rendered"]["#markup"]) ? $row->field_name_field[0]["rendered"]["#markup"] : $row->field_field_text[0]["rendered"]["#markup"];
?>
<div class="block-content no-gutters">
    <div class="block-image image-valign-center">
      <a href="<?php print url($row->field_field_link[0]['rendered']['#markup']); ?>">
        <?php print $image ?>
      </a>
    </div>
    <?php if (!empty($row->field_description_field[0]["rendered"]["#markup"])): ?>
    <div class="block-body">
      <a href="<?php print url($row->field_field_link[0]['rendered']['#markup']); ?>"><h2><?php print $text; ?></h2></a>
      <?php print $row->field_description_field[0]["rendered"]["#markup"] ?>
      <a class="show-more" href="<?php print url($row->field_field_link[0]['rendered']['#markup']); ?>">
        <i class="fas fa-chevron-right"></i><?php print t('More'); ?>
      </a>
    </div>
    <?php endif; ?>
</div>

