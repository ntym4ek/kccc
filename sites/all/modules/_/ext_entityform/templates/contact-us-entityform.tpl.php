<?php
// содержимое этого шаблона рендерится в шаблонах entity "Entityform" - entityform_type*.tpl.php
?>

<div class="row contact-us">
    <div class="cu-info col-md-7">
        <h2><? print t('ООО Trade House') . '<br />' . t('"Kirovo-Chepetsk Chemical Company"');?></h2>
        <div class="cu-title"><? print t('Address'); ?></div>
        <div class="cu-content"><? print t('613048, Kirov region, Kirovo-Chepetsk, Proizvodstvennaya, 6'); ?></div>
        <div class="cu-title"><? print t('Phone'); ?></div>
        <div class="cu-content"><? print '+7 (8332) 76-15-20 доб. 1107'; ?><a href="tel:+78332761520"><i class="icon-phone"></i><? print t('Call');?></a></div>
        <div class="cu-title"><? print 'Email'; ?></div>
        <div class="cu-content eAddr-encoded">e(td[s1]kc/cc[s2]ru)<a href="e(td[s1]kc/cc[s2]ru)" class="eAddr-encoded"><i class="icon-mail"></i><? print t('Email us');?></a></div>
        <div class="hr"></div>
        <div class="cu-link"><i class="fas fa-user"></i><a href="/info/contacts"><? print t('Central office departments contacts'); ?></a></div>
        <div class="cu-link"><i class="fas fa-download"></i><a href="/sites/default/files/etc/contact_us/card_th_kccc.pdf" target="_blank"><? print t('Download OOO TD "KCCC" requisites'); ?></a></div>
        <div class="cu-link"><i class="fas fa-map"></i><a href="/info/representatives"><? print t('Official representatives'); ?></a></div>
    </div>
    <div class="col-md-5 do-not-print">
        <div class="cu-form ">
            <? print render($form['intro']); ?>
            <? print render($form['field_contact_name']); ?>
            <? print render($form['field_phone']); ?>
            <? print render($form['field_contact_email']); ?>
            <? print render($form['field_contact_message']); ?>
            <? print drupal_render_children($form); ?>
        </div>
    </div>
    <div class="cu-map col-xs-12">
        <script type="text/javascript" charset="utf-8" async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3AdHxIteY-9AGYj5ZaIUEB86qWoAvLQXwJ&amp;width=100%25&amp;height=450&amp;lang=ru_RU&amp;scroll=true"></script>
    </div>
</div>

