<?php
  $text = empty($row->field_field_text[0]["rendered"]["#markup"]) ? $row->field_name_field[0]["rendered"]["#markup"] : $row->field_field_text[0]["rendered"]["#markup"];
?>
<div class="block-content no-gutters">
    <div class="block-image image-valign-center">
        <?php print $image ?>
    </div>
    <?php if (!empty($row->field_description_field[0]["rendered"]["#markup"])): ?>
    <div class="block-body">
        <a href="<?php print $row->field_field_link[0]['rendered']['#markup']; ?>"><h2><?php print $text; ?></h2></a>
        <?php print $row->field_description_field[0]["rendered"]["#markup"] ?>
    </div>
    <?php endif; ?>
</div>

