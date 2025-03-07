<?php
?>

<div class="pvp-event-check">
  <div class="event-title"><?php print t('Check', [], ['context' => 'pvp']); ?></div>
  <div class="date"><?php print $event['date_formatted']; ?></div>

  <div class="culture">
    <div class="images">
      <div id="slider-culture-images-<?php print $event['id']; ?>" class="slider slider-culture-images top-pagination">
        <div class="swiper">
          <div class="swiper-wrapper">
            <?php foreach ($event['culture']['photos'] as $i => $item) {
              print '<div class="swiper-slide">'  .
                      '<div class="image">' .
                        '<a href="' . $item['image'] . '" class="fancybox" rel="gallery-c-' . $event['id'] . '" data-fancybox="gallery-c-' . $event['id'] . '" title="' . $item['note'] . '">' .
                          '<img src="' . $item['image_teaser'] . '" alt="' . $item['image_alt'] . '">' .
                        '</a>' .
                      '</div>' .
                      '<div class="image-note"><span>' . t('Photo') . ' ' . ++$i . ' ' . t('of', [], ['context' => '1 of 8']) . ' ' . count($event['culture']['photos']) . '.</span> ' . $item['note'] . '</div>' .
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
    <div class="header"><?php print t('Harmful objects'); ?></div>
    <?php if (!empty($event['hobjects']['photos'])): ?>
    <div class="images">
      <div id="slider-hobjects-images-<?php print $event['id']; ?>" class="slider slider-hobjects-images top-pagination">
        <div class="swiper">
          <div class="swiper-wrapper">
            <?php foreach ($event['hobjects']['photos'] as $i => $item) {
              if (isset($item['image_teaser'])) {
                print '<div class="swiper-slide">'  .
                        '<div class="image">' .
                          '<a href="' . $item['image'] . '" class="fancybox" rel="gallery-h-' . $event['id'] . '" data-fancybox="gallery-h-' . $event['id'] . '" title="' . $item['note'] . '">' .
                            '<img src="' . $item['image_teaser'] . '" alt="' . $item['image_alt'] . '">' .
                          '</a>' .
                        '</div>' .
                        '<div class="image-note"><span>' . t('Photo') . ' ' . ++$i . ' ' . t('of', [], ['context' => '1 of 8']) . ' ' . count($event['hobjects']['photos']) . '.</span> ' . $item['note'] . '</div>' .
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
    <div class="list">
      <div class="label"><?php print t('List'); ?></div>
      <?php print $event['hobjects']['list_formatted']; ?>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

</div>
