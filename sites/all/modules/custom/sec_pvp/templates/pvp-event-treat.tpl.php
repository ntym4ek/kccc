<?php
?>

<div class="pvp-event-treat">
  <div class="event-title">Обработка</div>
  <div class="date"><?php print $event['date_formatted']; ?></div>

  <div class="process">
    <div class="images">
      <div id="slider-process-images-<?php print $event['id']; ?>" class="slider slider-process-images top-pagination">
        <div class="swiper">
          <div class="swiper-wrapper">
            <?php foreach ($event['photos'] as $i => $item) {
              print '<div class="swiper-slide">'  .
                      '<div class="image">' .
                        '<a href="' . $item['photo_url'] . '" class="fancybox" rel="gallery-t-' . $event['id'] . '" data-fancybox="gallery-t-' . $event['id'] . '"' . $item['note'] . '>' .
                          '<img src="' . $item['photo_teaser'] . '" alt="' . $item['photo_alt'] . '">' .
                        '</a>' .
                      '</div>' .
                      '<div class="note"><span>Фото ' . ++$i . ' из ' . count($event['photos']) . '.</span> ' . $item['note'] . '</div>' .
                    '</div>';
            } ?>
          </div>
        </div>
        <div class="swiper-pagination"></div>
      </div>
    </div>
    <div class="comment">
      <div class="readmore" data-closed-height="150">
        <?php print $event['comment']; ?>
      </div>
    </div>
  </div>

  <?php if (count($event['preparations'])): ?>
  <div class="preparations">
    <div class="header">Препараты</div>
    <?php foreach ($event['preparations'] as $item): ?>
    <div class="preparation hover-raise">
      <?php print $item['preparation']['rendered']; ?>
    </div>
    <?php endforeach; ?>
    <?php if (!empty($event['mix_spend'])): ?>
    <div class="mix-spend">
      <?php print $event['mix_spend_formatted']; ?>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

</div>
