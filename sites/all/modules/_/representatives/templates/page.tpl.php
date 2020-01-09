<?php
$rep00 = $representatives['head'][0]; unset($representatives['head']);
$rep01 = $representatives['head2']; unset($representatives['head2']);

?>

<div class="representatives">
    <div class="row">
        <? print theme('contact_card', array(
            'contact' => $rep00,
            'options' => ['class' => 'col-md-12'])); ?>

        <? $i = 0; ?>
        <? foreach ($rep01 as $key_c => $rep): ?>
        <? $collapse = [];
            if (isset($rep['regions'])) {
                $collapse['id'] = $key_c;
                $collapse['title'] = t('Regions list');
                $collapse['content'] = implode(', ', $rep['regions']);
            }
           print theme('contact_card', array(
                'contact' => $rep,
                'collapse' => $collapse,
                'options' => ['class' => 'col-md-6']));
           if (($i++) % 2) print '<div class="clearfix"></div>';
        ?>

        <? endforeach; ?>

        <div class="col-xs-12">
            <div class="map-title">
                <h3><? print t('Regional representatives map'); ?></h3>
                <p><? print t('Click at sprout in region to filter representatives below'); ?></p>
            </div>
        </div>

        <div class="map col-xs-12">
            <img src="/<? print $rep00['region_path'] . 'map.png'; ?>" />

            <?php foreach ($representatives as $key_rs => $reps): ?>
                <div class="rep <? print $key_rs; ?>">
                    <div class="popup-trigger-js" data-region="<? print $key_rs; ?>">
                        <img class="reg <? print $key_rs; ?>" src="/<? print $rep00['region_path'] . $key_rs . '.png'; ?>"/>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="clearfix"></div>

        <div class="rep-list">
            <div class="rep-title">
                <h3>Официальные представители</h3>
            </div>

            <?php $counter = 0; ?>
            <? foreach ($representatives as $key_rs => $reps): ?>
                <? foreach ($reps as $rep): ?>
                    <? if (isset ($rep['role']) && $rep['role'] == 'rep') {
                        $rep['office'] .= '<br />' . current($rep['regions']);
                        print theme('contact_card', array(
                            'contact' => $rep,
                            'options' => ['class' => 'rep-item col-sm-12 col-md-6 ' . $key_rs]));
                        if ($counter++ % 2) print '<div class="clearfix"></div>';
                    } ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>

            <div class="clearfix"></div>

            <div class="rep-box last col-sm-6 col-md-6">
                <? print t('If there is no representative in your region, contact our central office.'); ?>
                <? print t('<a href="/en/info/contacts" class="contacts">Contacts page <i class="icon-arrow_right"></i></a>'); ?>
            </div>
            <div class="rep-box last col-sm-6 col-md-6">
                <span style="display: inline-block; font-size: 14px; margin-bottom: 20px;">613048, <? print t('Kirov region'); ?>,<br /><? print t('Kirovo-Chepetsk'); ?>, <? print t('Proizvodstvennaya, 6'); ?></span>
                +7 (8332) 76-15-20
                <a href="e(<? print email_antibot_encode('td@kccc.ru'); ?>)" class="mail eAddr-encoded eAddr-html" rel="nofollow"></a>
            </div>


    </div>
</div>