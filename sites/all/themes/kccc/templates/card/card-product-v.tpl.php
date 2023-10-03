<?php
?>

<div class="card card-product-v">
  <a href="/<?php print $path; ?>">
    <div class="img">
      <img src="<?php print $image['teaser']; ?>" alt="<?php print $title; ?>">
    </div>
    <div class="category-icon category-clr-<?php print $category['id']; ?>">
      <i class="icon icon-<?php print $category['icon_num']; ?>"></i>
    </div>
    <div class="text">
      <div class="title"><?php print $label; ?></div>
      <div class="components"><?php print $components['formatted']; ?></div>
      <div class="more btn btn-brand btn-full-wide">Подробнее</div>
    </div>
  </a>
</div>

