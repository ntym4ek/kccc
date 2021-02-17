<?php
$url_attributes = empty($item['url_attributes']) ? [] : $item['url_attributes'];
$card_attributes = empty($item['card_attributes']) ? [] : $item['card_attributes'];
array_unshift($card_attributes['class'], 'card');
?>

<div<?php print drupal_attributes($card_attributes);?>>
  <div class="card-content">
    <div class="card-image">
      <a href="<? print $item['url']; ?>"<?php print drupal_attributes($url_attributes);?>>
        <img src="<? print $item['image_url']; ?>" alt="<? print $item['title']; ?>" class="img-responsive" title="<?php print $item['title']; ?>" />
      </a>
    </div>
    <div class="card-title">
      <h3><a href="<? print $item['url']; ?>"<?php print drupal_attributes($url_attributes);?>><? print $item['title']; ?></a></h3>
    </div>

    <? if(!empty($item['description'])): ?>
      <div class="divider"></div>
      <div class="card-summary">
        <? print $item['description']; ?>
      </div>
    <? endif; ?>
  </div>
</div>

