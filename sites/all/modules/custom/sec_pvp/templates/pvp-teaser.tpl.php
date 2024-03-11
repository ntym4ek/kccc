<?php
?>

<a href="<?php print $item['path']; ?>">
  <div class="pvp-teaser hover-raise">
    <div class="date"><?php print $item['date']; ?></div>
    <div class="text">
      <div class="region"><?php print $item['region']; ?></div>
      <div class="culture"><?php print $item['culture']; ?></div>
      <div class="comment"><?php print $item['comment']; ?></div>
    </div>
    <div class="image"><img src="<?php print $item['photo']; ?>" alt="<?php print $item['culture']; ?>"></div>
    <div class="action"><span class="btn btn-brand btn-full-wide"><?php print t('Read more'); ?></span></div>
  </div>
</a>
