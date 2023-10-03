<?php
?>
<div class="block-catalog">
  <div class="screen-width">
    <div class="section-title invert">
      <div>Вся продукция</div>
      <div class="underline"></div>
    </div>

    <div class="container">
      <div class="b1">
      <div class="row">

        <?php foreach ($categories as $category): ?>
        <div class="col-xs-6 col-md-4">
          <div class="category category-<?php print $category['id']; ?>">
            <a href="<?php print $category['path']; ?>">
<!--              <div class="icon"><img src="--><?php //print $category['image']; ?><!--" alt="--><?php //print $category['label']; ?><!--"></div>-->
              <div class="media hover-zoom"><i class="icon icon-rounded icon-<?php print $category['icon_num']; ?>"></i></div>
              <div class="title h4"><?php print $category['label']; ?></div>
            </a>
          </div>
        </div>
        <?php endforeach; ?>

      </div>
      </div>
    </div>

  </div>

</div>
