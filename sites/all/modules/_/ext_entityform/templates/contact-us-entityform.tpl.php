<?php
?>

<div class="row contact-us">
    <div class="cu-info col-md-7">
        <h2><? print t('ООО Trade House') . '<br />' . t('Kirovo-Chepetsk Chemical Company');?></h2>
        <div class="cu-title"><? print 'Региональный филиал'; ?></div>
        <div class="cu-content"><? print $contact; ?></div>
        <div class="hr"></div>
        <div class="cu-link"><i class="fas fa-user"></i><a href="/info/contacts"><? print t('Central office departments contacts'); ?></a></div>
        <div class="cu-link"><i class="fas fa-download"></i><a href="/sites/default/files/etc/contact_us/card_th_kccc.pdf" target="_blank"><? print t('Download OOO TD "KCCC" requisites'); ?></a></div>
        <div class="cu-link"><i class="fas fa-map"></i><a href="/info/representatives"><? print t('Official representatives'); ?></a></div>
        <div class="cu-link"><i class="fas fa-user"></i><a href="/agro-expert/online"><? print t('Agronomic service'); ?></a></div>
    </div>
    <div class="col-md-5 do-not-print">
        <div class="cu-form">
            <fieldset class="panel panel-default panel-shadow">
                <legend class="panel-heading">
                    <span class="panel-title fieldset-legend"><? print t('Contact form', [], ['context' => 'Contact us']); ?></span>
                </legend>
                <div class="panel-body">
                    <? print render($form['intro']); ?>
                    <? print render($form['field_contact_name']); ?>
                    <? print render($form['field_phone']); ?>
                    <? print render($form['field_contact_email']); ?>
                    <? print render($form['field_contact_message']); ?>
                    <? print drupal_render_children($form); ?>
                </div>
            </fieldset>
        </div>
    </div>
    <div id="map" class="cu-map col-xs-12"></div>

  <div class="col-md-12">
    <div class="branches">
      <h3>Филиалы в других регионах</h3>
      <? print $branches; ?>
    </div>
  </div>
</div>

