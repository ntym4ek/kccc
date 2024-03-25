<?php
?>

<a href="<?php print $product['path']; ?>">
  <div class="card card-product-v">
    <div class="img">
      <img src="<?php print $product['image']['teaser']; ?>" alt="<?php print $product['title']; ?>">
    </div>
    <div class="text">
      <div class="title"><?php print $product['label']; ?></div>
      <div class="components"><?php print $product['components']['formatted']; ?></div>
      <div class="more btn btn-brand btn-full-wide"><?php print t('Read more'); ?></div>
    </div>
  </div>
</a>


