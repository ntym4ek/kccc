<?php
// карта России на базе SVG
// интерактив на карте на базе плагина https://mapsvg.com
?>

<div class="representatives">
  <div class="row">

    <div class="col-xs-12">
      <div class="section-title">
        <h3><? print t('The list of head managers'); ?></h3>
      </div>
      <div class="row">
        <? if (!empty($sales['director'])) print theme('contact_card', array(
          'contact' => $sales['director'],
          'collapse' => [],
          'options' => ['class' => 'col-md-6']));
          print '<div class="clearfix"></div>';
        ?>

        <? if (!empty($sales['heads'])): ?>
          <? $i = 0; ?>
          <? foreach ($sales['heads'] as $key_c => $rep): ?>
            <? $collapse = [];
            if (isset($rep['regions'])) {
              $collapse['id'] = 'head-' . $key_c;
              $collapse['title'] = t('Regions list');
              $regions = [];
              foreach ($rep['regions'] as $region) { $regions[] = $region['name']; }
              $collapse['content'] = implode(', ', $regions);
            }
            print theme('contact_card', array(
              'contact' => $rep,
              'collapse' => $collapse,
              'options' => ['class' => 'col-md-6']));
              if (($i++) % 2) print '<div class="clearfix"></div>';
            ?>
          <? endforeach; ?>
        <? endif; ?>
      </div>
    </div>

    <div class="col-xs-12">
      <div class="section-title">
        <h3><? print t('Regional representatives map'); ?></h3>
        <p><? print t('Click at region to filter representatives list below'); ?></p>
      </div>
    </div>

    <div id="mapsvg" class="map col-xs-12"></div>
    <div class="clearfix"></div>

    <div class="rep-list col-xs-12">
      <div class="section-title">
        <h3><? print t('Representatives list'); ?></h3>
      </div>

      <?php $counter = 0; ?>
      <? foreach ($sales['reps'] as $key_r => $rep): ?>
        <?
        $collapse = [];
        if (count($rep['regions']) > 1) {
          $collapse['id'] = 'rep-' . $key_r;
          $collapse['title'] = t('Regions list');
          $regions = [];
          foreach ($rep['regions'] as $region) { $regions[] = $region['name']; }
          $collapse['content'] = implode(', ', $regions);
        } else {
          $rep['subtitle'] .= '<br />' . current($rep['regions'])['name'];
        }
        $rep_iso = [];
        foreach($rep['regions'] as $reg) { $rep_iso[] = $reg['iso']; }
        print theme('contact_card', array(
          'contact' => $rep,
          'collapse' => $collapse,
          'options' => ['class' => 'rep-item col-sm-12 col-md-6 ' . implode(' ', $rep_iso)]));
          if ($counter++ % 2) print '<div class="clearfix"></div>';
        ?>
      <?php endforeach; ?>
    </div>

    <div class="clearfix"></div>

    <div class="rep-box last col-sm-6 col-md-6">
      <? print t('If there is no representative in your region, contact our central office.'); ?>
      <? print t('<a href="/en/info/contacts" class="contacts">Contacts page <i class="icon-arrow_right"></i></a>'); ?>
    </div>
    <?
    $reception_phone = ext_user_normalize_phone(variable_get('phone_reception', ''));
    $reception_phone_formatted = ext_user_format_phone($reception_phone);
    ?>
    <div class="rep-box last col-sm-6 col-md-6">
      <span style="display: inline-block; font-size: 14px; margin-bottom: 20px;">613048, <? print t('Kirov region'); ?>,<br /><? print t('Kirovo-Chepetsk'); ?>, <? print t('Proizvodstvennaya, 6'); ?></span>
      <a href="tel:<? print $reception_phone; ?>"><? print $reception_phone_formatted; ?></a>
      <a href="mailto:e(<? print email_antibot_encode('td@kccc.ru'); ?>)" class="mail eAddr-encoded eAddr-html" rel="nofollow"></a>
    </div>


  </div>
</div>
