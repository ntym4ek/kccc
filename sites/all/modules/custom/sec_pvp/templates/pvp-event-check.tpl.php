<?php
?>

<div class="pvp-event-check">
  <div class="event-title">Контроль</div>
  <div class="date"><?php print $event['date_formatted']; ?></div>

  <div class="culture">
    <div class="title">Культура</div>
    <div class="images">
      <div id="slider-culture-images-<?php print $event['id']; ?>" class="slider slider-culture-images" data-slidesperview-xs="1" data-autoheight="true">
        <div class="swiper">
          <div class="swiper-wrapper">
            <?php foreach ($event['culture']['photos'] as $image) {
              print '<div class="swiper-slide">'  .
                      '<div class="image">' .
                        '<img src="' . $image['photo_teaser'] . '" alt="' . $image['photo_alt'] . '">' .
                      '</div>' .
                    '</div>';
            } ?>
          </div>
        </div>
        <div class="swiper-pagination"></div>
      </div>
    </div>
    <div class="comment"><?php print $event['culture']['comment']; ?></div>
  </div>

  <div class="hobjects">
    <div class="title">Вредные объекты</div>
    <?php if ($event['hobjects']['has_photo']): ?>
    <div class="images">
      <div id="slider-hobjects-images-<?php print $event['id']; ?>" class="slider slider-hobjects-images" data-slidesperview-xs="1" data-autoheight="true">
        <div class="swiper">
          <div class="swiper-wrapper">
            <?php foreach ($event['hobjects']['list'] as $item) {
              if (isset($item['photo_teaser'])) {
                print '<div class="swiper-slide">'  .
                        '<div class="image">' .
                          '<img src="' . $item['photo_teaser'] . '" alt="' . $item['photo_alt'] . '">' .
                        '</div>' .
                      '</div>';
              }
            } ?>
          </div>
        </div>
        <div class="swiper-pagination"></div>
      </div>
    </div>
    <?php endif; ?>
    <?php if (!empty($event['hobjects']['list_formatted'])): ?>
      <div class="list"><?php print $event['hobjects']['list_formatted']; ?></div>
    <?php endif; ?>
    <div class="comment"><?php print $event['culture']['comment']; ?></div>
  </div>

</div>
