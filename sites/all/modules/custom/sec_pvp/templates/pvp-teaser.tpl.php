<?php
?>

<div class="pvp-teaser hover-raise">
  <div class="date"><?php print $item['date']; ?></div>
  <div class="text">
    <div class="region"><?php print $item['region']; ?></div>
    <div class="culture"><?php print $item['culture']; ?></div>
    <div class="comment"><?php print $item['comment']; ?></div>
  </div>
  <div class="image"><a href="/<?php print $item['path']; ?>"><img src="<?php print $item['photo']; ?>" alt="<?php print $item['culture']; ?>"></a></div>
  <div class="action"><a href="/<?php print $item['path']; ?>" class="btn btn-brand btn-large btn-full-wide">Подробнее</a></div>
</div>
