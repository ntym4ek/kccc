<?php
?>
<div class="pvp-u-list-teaser">
    <div class="pvp-t-header">
      <div class="pvp-t-title">
        <a href="<?php print $item['link']; ?>">
          <div class="title-1"><?php print $item['title1']; ?></div>
          <div class="title-2"><?php print $item['title2']; ?></div>
        </a>
      </div>
      <div class="pvp-t-actions">
        <a href="<?php print $item['link_edit']; ?>" class="action-edit">
          <i class="fas fa-pencil-alt" aria-hidden="true"></i>
        </a>
        <a href="<?php print $item['link_del']; ?>" class="action-delete">
          <i class="fa fa-trash" aria-hidden="true"></i>
        </a>
      </div>
  </div>
</div>
