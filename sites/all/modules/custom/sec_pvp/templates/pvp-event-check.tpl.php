<?php
?>

<div class="pvp-event-check">
  <div class="event-title">Контроль</div>
  <div class="date"><?php print $event['date_formatted']; ?></div>

  <div class="culture">
    <div class="images">
      <div id="slider-culture-images-<?php print $event['id']; ?>" class="slider slider-culture-images top-pagination" data-slidesperview-xs="1">
        <div class="swiper">
          <div class="swiper-wrapper">
            <?php foreach ($event['culture']['photos'] as $i => $item) {
              print '<div class="swiper-slide">'  .
                      '<div class="image">' .
                        '<a href="' . $item['photo_url'] . '" class="fancybox" rel="gallery-c-' . $event['id'] . '" data-fancybox="gallery-c-' . $event['id'] . '" title="' . $item['note'] . '">' .
                          '<img src="' . $item['photo_teaser'] . '" alt="' . $item['photo_alt'] . '">' .
                        '</a>' .
                      '</div>' .
                      '<div class="note"><span>Фото ' . ++$i . ' из ' . count($event['culture']['photos']) . '.</span> ' . $item['note'] . '</div>' .
                    '</div>';
            } ?>
          </div>
        </div>
        <div class="swiper-pagination"></div>
      </div>
    </div>
    <div class="comment">
      <div class="readmore" data-closed-height="150">
        <?php print $event['culture']['comment']; ?>
      </div>
    </div>
  </div>

  <?php if (!empty($event['hobjects']['photos']) || !empty($event['hobjects']['comment']) || !empty($event['hobjects']['list_formatted'])): ?>
  <div class="hobjects">
    <div class="header">Вредные объекты</div>
    <?php if (!empty($event['hobjects']['photos'])): ?>
    <div class="images">
      <div id="slider-hobjects-images-<?php print $event['id']; ?>" class="slider slider-hobjects-images top-pagination" data-slidesperview-xs="1">
        <div class="swiper">
          <div class="swiper-wrapper">
            <?php foreach ($event['hobjects']['photos'] as $i => $item) {
              if (isset($item['photo_teaser'])) {
                print '<div class="swiper-slide">'  .
                        '<div class="image">' .
                          '<a href="' . $item['photo_url'] . '" class="fancybox" rel="gallery-h-' . $event['id'] . '" data-fancybox="gallery-h-' . $event['id'] . '" title="' . $item['label'] . '. ' . $item['note'] . '">' .
                            '<img src="' . $item['photo_teaser'] . '" alt="' . $item['photo_alt'] . '">' .
                          '</a>' .
                        '</div>' .
                        '<div class="note"><span>Фото ' . ++$i . ' из ' . count($event['hobjects']['photos']) . '.</span> ' . $item['label'] . '. ' . $item['note'] . '</div>' .
                      '</div>';
              }
            } ?>
          </div>
        </div>
        <div class="swiper-pagination"></div>
      </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($event['hobjects']['comment'])): ?>
    <div class="comment">
      <div class="readmore" data-closed-height="150"><?php print $event['hobjects']['comment']; ?></div>
    </div>
    <?php endif; ?>

    <?php if (!empty($event['hobjects']['list_formatted'])): ?>
    <div class="list"><?php print $event['hobjects']['list_formatted']; ?></div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

</div>
