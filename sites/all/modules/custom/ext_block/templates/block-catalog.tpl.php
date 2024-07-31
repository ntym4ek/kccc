<?php
?>
<div class="block-catalog">
  <div class="screen-width">
    <div class="section-title invert">
      <div><?php print t('All products'); ?></div>
      <div class="underline"></div>
    </div>

    <div class="container">
      <div class="b1">
        <div class="row">
          <div class="col-xs-12 col-md-10 col-md-offset-1">
            <div class="row">

              <?php $index = 1; ?>
              <?php foreach ($categories as $category): ?>
              <div class="col-xs-12 col-md-6">
                <div class="category">
                  <a href="<?php print $category['path']; ?>">
                  <div class="text">
                    <div class="index"><i class="icon icon-<?php print $category["field_icon"]; ?>"></i></div>
                    <div class="title"><?php print $category['label']; ?></div>
                  </div>
                  </a>
                </div>
              </div>
              <?php endforeach; ?>

            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

</div>
