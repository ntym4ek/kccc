<?php
?>

<a href="/<?php print $path; ?>">
  <div class="card card-product-h">
    <div class="row">
      <div class="col-xs-12 col-md-3 col-lg-5">
        <div class="image">
          <img src="<?php print $image['teaser']; ?>" alt="<?php print $title; ?>">
        </div>
      </div>
      <div class="col-xs-12 col-md-9 col-lg-7">
        <div class="product-info">
          <div class="title"><?php print $label; ?></div>
          <div class="components">
            <?php print $components['formatted']; ?>
          </div>
          <div class="summary hide-md-only"><?php print $summary; ?></div>
        </div>
      </div>
      <div class="col-xs-12 hide-xs show-md-only">
        <p class="summary"><?php print $summary; ?></p>
      </div>
    </div>
  </div>
</a>
