<?php

?>
<div class="block-prep-pvp">
  <div class="row">
    <div class="col-xs-12">
      <div class="section-title">
        <div>Последние опыты</div>
        <div class="underline"></div>
        <div class="more-all"><a href="<?php print $more_url; ?>"><?php print t('Show all') .' >'; ?></a></div>
      </div>
    </div>
  </div>

  <div class="view view-pvp-block">
    <div class="view-content row center-xs">
      <?php print $content; ?>
    </div>
  </div>
</div>
