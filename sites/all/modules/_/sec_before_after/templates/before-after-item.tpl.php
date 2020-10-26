<?php
?>
<div class="card">

  <div class="card-image">
    <a href="<?php print $content['link']; ?>">
      <img src="<?php print $content['image_thumb']; ?>" class="img-responsive" title="Действие препарата в поле" alt="Защита культуры <?php print $content['culture'] . ' препаратами ООО ТД Кирово-Чепецкая Химическая Компания. ' . $content['region']; ?>">
    </a>
  </div>

  <div class="card-title">
    <h3><a href="<?php print $content['link']; ?>"><?php print $content['region']; ?></a></h3>
  </div>
  <div class="card-subtitle"><?php print $content['culture']; ?></div>

  <div class="divider"></div>

  <div class="card-summary">
    <div><span class="text-muted">Хозяйство: </span><?php print $content['farm']; ?></div>
    <div><span class="text-muted">Обработка: </span><?php print $content['preps_w_links']; ?></div>
  </div>
  <div class="card-actions">
    <a class="btn btn-gray" href="<?php print $content['link']; ?>">
      <?php print t('More'); ?>
      <i class="fa fa-chevron-right pull-right"></i>
    </a>
  </div>

  <div class="card-footer">
    <?php print $content['footer']; ?>
  </div>
</div>

