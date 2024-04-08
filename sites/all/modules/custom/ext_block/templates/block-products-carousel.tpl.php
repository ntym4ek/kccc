<?php
?>
<div class="block-new-products">
  <div class="section-title">
    <div><?php print $title; ?></div>
    <div class="underline"></div>
  </div>

  <div class="row">
    <div class="col-xs-12">

      <div id="carousel-products" class="carousel carousel-products outer-pagination outer-navigation" data-slidesperview-xs="1.5" data-slidesperview-md="2.8" data-slidesperview-lg="3.4" data-slidesperview-xl="4">
        <div class="swiper">
          <div class="swiper-wrapper">
            <?php foreach ($cards as $card) {
              print '<div class="swiper-slide">'  . $card . '</div>';
            } ?>
          </div>
        </div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev hide show-xl"></div>
        <div class="swiper-button-next hide show-xl"></div>
      </div>

    </div>
  </div>
</div>
