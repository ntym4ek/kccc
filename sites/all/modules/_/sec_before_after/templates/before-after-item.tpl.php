<?php
?>
<div class="view-item col-xs-12 col-sm-6 col-md-4">

    <div class="v-card field">
        <header>
            <?php print $content['date']; ?>
        </header>

        <div class="v-card-image">
            <a href="<?php print $content['link']; ?>">
                <img typeof="foaf:Image" src="<?php print $content['image_thumb']; ?>" class="img-responsive" title="Действие препарата в поле" alt="Защита культуры <?php print $content['culture'] . ' препаратами ООО ТД Кирово-Чепецкая Химическая Компания. ' . $content['region']; ?>" loading="lazy">
            </a>
        </div>

        <div class="v-card-content">
            <h4 class="v-card-title"><a href="<?php print $content['link']; ?>"><?php print $content['region']; ?></a></h4>
            <div class="v-card-summary">
                <div class="v-card-subtitle"><?php print $content['culture']; ?></div>
                <div>
                    <strong>Хозяйство:</strong> <?php print $content['farm']; ?><br />
                    <strong>Обработка:</strong> <?php print $content['preps_w_links']; ?>
                </div>
            </div>
            <a class="btn btn-danger v-card-more" href="<?php print $content['link']; ?>">
                <?php print t('More'); ?>
                <i class="fa fa-chevron-right pull-right"></i>
            </a>
        </div>

        <footer>
            <?php print $content['footer']; ?>
        </footer>
    </div>

</div>
