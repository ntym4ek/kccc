<?php
?>
<div class="block-new-products">
  <div class="section-title">
    <div><?php print $title; ?></div>
    <div class="underline"></div>
  </div>

  <div class="row">
    <div class="col-xs-12">

      <div id="carousel-products" class="carousel carousel-products outer-pagination outer-navigation" data-slidesperview-xs="1.5" data-slidesperview-md="3.3" data-slidesperview-lg="4">
        <div class="swiper">
          <div class="swiper-wrapper">
            <?php foreach ($cards as $card) {
              print '<div class="swiper-slide">'  . $card . '</div>';
            } ?>
          </div>
        </div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev hide show-lg"></div>
        <div class="swiper-button-next hide show-lg"></div>
      </div>

    </div>
  </div>
</div>
