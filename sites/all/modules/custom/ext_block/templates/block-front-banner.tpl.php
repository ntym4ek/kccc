<?php
?>
<div class="block-front-banner">
  <div class="screen-width">

    <div id="slider-front-banner" class="slider slider-front-banner">
      <div class="swiper">
        <div class="swiper-wrapper">
          <?php foreach ($slides as $slide): ?>
            <div class="swiper-slide">
              <div class="img" style="background-image: url(<?php print $slide['img']; ?>);"></div>
              <div class="text-wr">
                <div class="container">
                  <div class="row">
                    <div class="text">
                      <h2><?php print $slide['title']; ?></h2>
                      <?php if ($slide['description']): ?><div class="summary"><?php print $slide['description']; ?></div><?php endif; ?>
                      <div class="more"><a href="<?php print $slide['path']; ?>" class="btn btn-brand btn-large">Подробнее</a></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="swiper-pagination"></div>
      </div>
    </div>

  </div>
</div>
