<?php
?>

<a href="<?php print $path; ?>">
  <div class="card card-product-h">
    <div class="image">
      <img src="<?php print $image['teaser']; ?>" alt="<?php print $title; ?>">
    </div>
    <div class="product-info">
      <div class="title"><?php print $label; ?></div>
      <div class="components"><?php print $components['formatted']; ?></div>
      <div class="summary"><?php print $summary; ?></div>
    </div>
  </div>
</a>
