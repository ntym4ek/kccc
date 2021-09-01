<?php
    $attributes = empty($item['url_attributes']) ? [] : $item['url_attributes'];
?>
<div class="col-xs-12 col-sm-6 col-md-4">

    <div class="r-card field">

        <div class="r-card-image">
            <a href="<?php print $item['url']; ?>"<?php print drupal_attributes($attributes);?>>
                <img src="/<?php print $item['image_url']; ?>" class="img-responsive" alt="<?php print $item['title']; ?>" title="<?php print $item['title']; ?>" loading="lazy" />
            </a>
        </div>

        <div class="r-card-content">
            <h4 class="r-card-title"><a href="<?php print $item['url']; ?>"<?php print drupal_attributes($attributes);?>><?php print $item['title']; ?></a></h4>
            <p class="r-card-description"><?php print $item['description']; ?></p>

            <a class="show-more" href="<?php print $item['url']; ?>"<?php print drupal_attributes($attributes);?>>
              <i class="fas fa-chevron-right"></i>
              <?php print empty($item['button_text']) ? t('More') : $item['button_text']; ?>
            </a>
        </div>

    </div>

</div>
