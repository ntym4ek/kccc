
<? 
  //print $ds_content; dpm($ds_content);
?>

<div class="contact-us">
    <div class="cu-info">
        <h2><? print t('ООО Trade House "Kirovo-Chepetsk Chemical Company"');?></h2>
        <div class="cu-title"><? print t('Address'); ?></div>
        <div class="cu-content"><? print t('613048, Kirov region, Kirovo-Chepetsk, Proizvodstvennaya, 6'); ?></div>
        <div class="cu-title"><? print t('Phone'); ?></div>
        <div class="cu-content"><? print '+7 (83361) 5-20-67, 5-40-60, 9-28-73'; ?><a href="tel:+78336152067"><i class="icon-phone"></i><? print t('Call');?></a></div>
        <div class="cu-title"><? print 'Email'; ?></div>
        <div class="cu-content eAddr-encoded">e(td[s1]kc/cc[s2]ru)<a href="e(td[s1]kc/cc[s2]ru)" class="eAddr-encoded"><i class="icon-mail"></i><? print t('Email us');?></a></div>
        <div class="hr"></div>
        <div class="cu-link"><img src="/sites/all/themes/opie/images/icons/icon_user.png"><a href="/info/contacts"><? print t('Central office departments contacts'); ?></a></div>
        <div class="cu-link"><img src="/sites/all/themes/opie/images/icons/icon_dnld.png"><a href="/sites/default/files/downloads/contact_us/card_th_kccc.pdf" target="_blank"><? print t('Download OOO TD "KCCC" requisites'); ?></a></div>
        <div class="cu-link"><img src="/sites/all/themes/opie/images/icons/icon_map.png"><a href="/info/representatives"><? print t('Official representatives'); ?></a></div>
    </div>
    <div class="cu-form">
        <? print render($form['intro']); ?>
        <? print render($form['field_contact_name']); ?>
        <? print render($form['field_phone']); ?>
        <? print render($form['field_contact_email']); ?>
        <? print render($form['field_contact_message']); ?>
        <? print drupal_render_children($form); ?>
    </div>
    <div class="cu-map">
        <script type="text/javascript" charset="utf-8" async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?sid=LoVw978Gj7HfJScsrZyXc9d_TkfGUdNc&amp;width=1004&amp;height=450&amp;lang=ru_RU&amp;sourceType=constructor&amp;scroll=true"></script>
    </div>
</div>
