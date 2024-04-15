<?php
?>
<div class="block-news">

  <div class="screen-width">
    <div class="container">

      <div class="row">
        <div class="col-xs-12">
          <div class="section-title">
            <div><?php print t('Latest news'); ?></div>
            <div class="underline"></div>
            <div class="more-all"><a href="<?php print url('novosti'); ?>"><?php print t('Show all') .' >'; ?></a></div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-xs-12">
          <div id="carousel-news" class="carousel carousel-news outer-pagination" data-slidesperview-xs="1" data-slidesperview-md="2">
            <div class="swiper">
              <div class="swiper-wrapper">
                <?php foreach ($cards as $card): ?>
                  <div class="swiper-slide">
                    <div class="news">
                      <a href="<?php print $card['path']; ?>">
                        <div class="img"><img src="<?php print $card['img']; ?>" alt=""></div>
                        <div class="text">
                          <div class="title"><?php print $card['title']; ?></div>
                          <p><?php print $card['text']; ?></p>
                          <div class="more"><?php print t('Read article') . ' >'; ?></div>
                        </div>
                      </a>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="swiper-pagination"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
