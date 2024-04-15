<?php
?>

<div class="block-contacts">

  <div class="screen-width">
    <div class="section-title invert">
      <div><?php print t('Contacts'); ?></div>
      <div class="underline"></div>
    </div>
  </div>

  <div class="content">
    <div class="row">
      <div class="col-xs-12 col-md-6 col-lg-4">
        <div class="row">
          <div class="col-xs-6">
            <div class="b">
            <div class="contact hover-raise">
              <a href="<?php print url('kontakty'); ?>">
                <div class="media"><i class="icon icon-109 icon-rounded"></i></div>
                <div class="title"><?php print t('Contacts of the central office'); ?></div>
              </a>
            </div>
            </div>
          </div>
          <div class="col-xs-6">
            <div class="b">
            <div class="contact hover-raise">
              <a href="<?php print url('predstaviteli'); ?>">
                <div class="media"><i class="icon icon-111 icon-rounded"></i></div>
                <div class="title"><?php print t('Official representatives'); ?></div>
              </a>
            </div>
            </div>
          </div>
          <div class="col-xs-6">
            <div class="b">
            <div class="contact hover-raise">
              <a href="<?php print url('eksperty'); ?>">
                <div class="media"><i class="icon icon-108 icon-rounded"></i></div>
                <div class="title"><?php print t('Agronomic service'); ?></div>
              </a>
            </div>
            </div>
          </div>
          <div class="col-xs-6">
            <div class="b">
            <div class="contact hover-raise">
              <a href="<?php print url('filialy'); ?>">
                <div class="media"><i class="icon icon-107 icon-rounded"></i></div>
                <div class="title"><?php print t('Details of TH «KCCC» LLC'); ?></div>
              </a>
            </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xs-12 col-md-6 col-lg-8">
        <div class="map">
          <img src="/sites/all/modules/custom/ext_block/images/map.jpg" alt="<?php print t('Representative offices of TH «KCCC» LLC'); ?>">
        </div>
      </div>
    </div>
  </div>

</div>
