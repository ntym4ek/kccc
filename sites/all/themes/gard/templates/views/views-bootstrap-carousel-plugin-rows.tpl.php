<div class="block-content no-gutters">
    <div class="block-image image-valign-center">
        <?php print $image ?>
    </div>
    <div class="block-body">
        <a href="<?php print $row->field_field_link[0]['rendered']['#markup']; ?>"><h2><?php print $row->field_name_field_et[0]['rendered']['#markup'] ?></h2></a>
        <?php print $row->field_description_field_et[0]['rendered']['#markup'] ?>
    </div>
</div>

