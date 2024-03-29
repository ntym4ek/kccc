<?php
?>
<div class="view-item col-xs-12 col-sm-6 col-md-4">

    <div class="v-card field">
        <header>
            <?php print $content['date']; ?>
        </header>

        <div class="v-card-image">
            <a href="<?php print $content['link']; ?>">
                <img typeof="foaf:Image" src="<?php print $content['image_thumb']; ?>" class="img-responsive" title="Действие препарата в поле" alt="Защита культуры <?php print $content['culture']['title'] . $content['culture']['notes'] . ' препаратами ООО ТД Кирово-Чепецкая Химическая Компания. ' . $content['region']['name']; ?>" loading="lazy">
            </a>
        </div>

        <div class="v-card-content">
            <h4 class="v-card-title"><a href="<?php print $content['link']; ?>"><?php print $content['region']['name']; ?></a></h4>
            <div class="v-card-summary">
                <div class="v-card-subtitle"><?php print $content['culture']['title'] . $content['culture']['notes']; ?></div>
                <div>
                  <strong>Хозяйство:</strong> <?php print $content['farm']; ?><br />
                  <? if (!empty($content['preps_w_links'])): ?>
                  <strong><?php print $content['preps_title']; ?></strong> <?php print $content['preps_w_links']; ?>
                  <? endif; ?>
                </div>
            </div>
            <a class="btn btn-primary v-card-more" href="<?php print $content['link']; ?>">
                <?php print t('More'); ?>
                <i class="fas fa-chevron-right pull-right"></i>
            </a>
        </div>

        <footer>
            <?php print $content['footer']; ?>
        </footer>
    </div>

</div>
