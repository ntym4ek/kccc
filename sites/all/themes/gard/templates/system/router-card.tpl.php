<?php
?>
<div class="col-xs-12 col-sm-6 col-md-4">

    <div class="r-card field">

        <div class="r-card-image">
            <a href="<?php print $item['url']; ?>"<?php print empty($item['nofollow']) ? '' : ' rel="nofollow"';?>>
                <img src="/<?php print $item['image_url']; ?>" class="img-responsive" title="<?php print $item['title']; ?>">
            </a>
        </div>

        <div class="r-card-content">
            <h4 class="r-card-title"><a href="<?php print $item['url']; ?>"<?php print empty($item['nofollow']) ? '' : ' rel="nofollow"';?>><?php print $item['title']; ?></a></h4>
            <p class="r-card-description"><?php print $item['description']; ?></p>

            <a class="btn btn-danger r-card-more" href="<?php print $item['url']; ?>"<?php print empty($item['nofollow']) ? '' : ' rel="nofollow"';?>>
                <?php print empty($item['button_text']) ? t('More') : $item['button_text']; ?>
                <i class="fa fa-chevron-right pull-right"></i>
            </a>
        </div>

    </div>

</div>