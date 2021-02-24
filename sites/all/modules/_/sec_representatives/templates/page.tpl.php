<?php
// карта России на базе SVG
// интерактив на карте на базе плагина https://mapsvg.com

$director = array_shift($sales['heads']);
?>

<div class="representatives">
  <div class="row">

    <div class="col-xs-12">
      <div class="section-title">
        <h3><? print t('The list of head managers'); ?></h3>
      </div>
      <div class="row">
          <? print theme('contact_card', array(
          'contact' => $director,
          'collapse' => [],
          'options' => ['class' => 'col-sm-6 col-md-4']));
          print '<div class="clearfix"></div>';
        ?>
      </div>
      <div class="row">
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
          $collapse['collapsed'] = true;
          print theme('contact_card', array(
            'contact' => $rep,
            'collapse' => $collapse,
            'options' => ['class' => 'col-sm-6 col-md-4']));
//          if (($i++) % 2) print '<div class="clearfix"></div>';
          ?>
        <? endforeach; ?>
      </div>
    </div>

    <div class="col-xs-12">
      <div class="section-title">
        <h3><? print t('Regional representatives map'); ?></h3>
        <p><? print t('Click at region to filter representatives list below'); ?></p>
      </div>
    </div>

    <div class="col-xs-12">
      <div id="mapsvg" class="map"></div>
    </div>
    <div class="clearfix"></div>

    <div class="rep-list col-xs-12">
      <div class="section-title">
        <h3><? print t('Representatives list'); ?></h3>
      </div>
      <div class="row">
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
            $rep['office'] .= '<br />' . current($rep['regions'])['name'];
          }
          $rep_iso = [];
          foreach($rep['regions'] as $reg) { $rep_iso[] = $reg['iso']; }
          print theme('contact_card', array(
            'contact' => $rep,
            'collapse' => $collapse,
            'options' => ['class' => 'rep-item col-sm-6 col-md-4 ' . implode(' ', $rep_iso)]));
  //        if ($counter++ % 2) print '<div class="clearfix"></div>';
          ?>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="clearfix"></div>

    <div class="col-sm-12">
      <div class="last-box">
        <div class="row">
          <div class="col-sm-6">
            <div class="last-item">
              <? print t('If there is no representative in your region, contact our central office.'); ?>
              <? print t('<a href="/en/info/contacts" class="contacts">Contacts page <i class="icon-arrow_right"></i></a>'); ?>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="last-item">
              613048, <? print t('Kirov region'); ?>,<br /><? print t('Kirovo-Chepetsk'); ?>, <? print t('Proizvodstvennaya, 6'); ?>
              +7 (8332) 76-15-20
              <a href="mailto:e(<? print email_antibot_encode('td@kccc.ru'); ?>)" class="mail eAddr-encoded eAddr-html" rel="nofollow"></a>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
