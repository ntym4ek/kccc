<?php
$rep00 = $representatives['head'][0]; unset($representatives['head']);
$rep01 = $representatives['head2']; unset($representatives['head2']);

?>

<div class="representatives">
    <div class="row">
        <div class="media user-card col-sm-6">
            <div class="media-left">
                <img class="media-object" src="<? print $rep00['photo']; ?>" alt="">
            </div>
            <div class="media-body">
                <h4 class="user-name"><? print $rep00['surname']; ?> <span><? print $rep00['name'] . ' ' . $rep00['name2'] ; ?></span></h4>
                <div class="dep"><? print $rep00['office']; ?></div>
                <? if (!empty($rep00['phones'][0])): ?>
                    <div class="phones">Телефон</div>
                    <? foreach ($rep00['phones'] as $phone): ?>
                        <? $phone_raw = str_replace(array('(', ')', '-',' '), '', $phone)?>
                        <div class="phone"><? print $phone; ?><a href="tel:<? print $phone_raw; ?>" rel="nofollow"><i class="icon-phone"></i>Позвонить</a></div>
                    <? endforeach; ?>
                <? endif; ?>
            </div>
        </div>
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
           if ((++$i) % 2) print '<div class="clearfix"></div>';
        ?>

        <? endforeach; ?>

        <div class="map col-xs-12">
            <img src="/<? print $rep00['region_path'] . 'map.png'; ?>" />

            <?php foreach ($representatives as $key_rs => $reps): ?>
                <div class="rep <? print $key_rs; ?>">
                    <div class="popup-trigger-js">
                        <img class="reg <? print $key_rs; ?>" src="/<? print $rep00['region_path'] . $key_rs . '.png'; ?>"/>

                        <div class="popup popup-bottom-left popup-<? print count($reps); ?>x">
                            <div class="header">
                                <img class="icon" src="/<? print $rep00['icon_l_path'] . $key_rs . '.png'; ?>"/>
                                <? print array_shift($reps[0]['regions']); ?>
                            </div>
                            <?php foreach ($reps as $key_r => $rep): ?>
                                <?php print theme('contact_card', array('contact' => $rep)); ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="clearfix"></div>
<!--        <h2 class="r-separator col-xs-12">-->
<!--            Официальные представители-->
<!--        </h2>-->
<!---->
<!--        --><?php //$counter = 0; ?>
<!--        --><?// foreach ($representatives as $key_rs => $reps): ?>
<!--            --><?// foreach ($reps as $rep): ?>
<!--                --><?// if ($rep['role'] == 'rep'): ?>
<!--                --><?// $phones_arr = array(); ?>
<!---->
<!--                    <div class="media user-card rep-box --><?// print $key_rs; ?><!-- col-sm-6 col-md-4">-->
<!--                        <div class="media-heading">-->
<!--                            <img class="icon" src="/--><?// print $rep00['icon_d_path'] . $key_rs . '.png'; ?><!--"/>-->
<!--                            --><?// print key_to_region($key_rs); ?>
<!--                        </div>-->
<!--                        <div class="media-body">-->
<!--                            <h4 class="user-name">--><?// print $rep['surname']; ?><!--<br /><span>--><?// print $rep['name'] . ' ' . $rep['name2'] ; ?><!--</span></h4>-->
<!--                            <div class="dep">--><?// print $rep['office']; ?><!--</div>-->
<!--                        </div>-->
<!--                        <div class="media-bottom">-->
<!--                            --><?// if (!empty($rep['phones'][0])): ?>
<!--                                --><?php //foreach ($rep['phones'] as $phone): ?>
<!--                                    --><?// $phone_raw = str_replace(array('(', ')', '-',' '), '', $phone)?>
<!--                                    <div class="phone"><a href="tel:--><?// print $phone_raw; ?><!--" rel="nofollow">--><?// print $phone; ?><!--</a></div>-->
<!--                                --><?php //endforeach; ?>
<!--                            --><?// endif; ?>
<!--                            --><?// if (!empty($rep['emails'])): ?>
<!--                                --><?// foreach ($rep['emails'] as $email): ?>
<!--                                    <div class="email"><a href="e(--><?// print $email; ?><!--)" class="eAddr-encoded eAddr-html" rel="nofollow"></a></div>-->
<!--                                --><?// endforeach; ?>
<!--                            --><?// endif; ?>
<!--                        </div>-->
<!--                    </div>-->
<!--                --><?php //endif; ?>
<!--                --><?php //$counter++; ?>
<!--            --><?php //endforeach; ?>
<!--        --><?php //endforeach; ?>
<!---->
<!--        --><?php //$counter = 3 - ($counter % 3); ?>
<!--        --><?php //$counter = $counter > 2 ? 0 : $counter; ?>
<!--        --><?php //for($i=0; $i < $counter; $i++): ?>
<!--            <div class="media user-card rep-box --><?// print $key_rs; ?><!-- hidden-sm col-md-4">-->
<!--            </div>-->
<!--        --><?php //endfor; ?>

        <div class="rep-box last col-sm-6 col-md-4">
            Если в Вашем регионе нет официального представителя, свяжитесь с нашим центральным офисом.
        </div>
        <div class="rep-box last col-sm-6 col-md-4">
            <span style="display: inline-block; font-size: 14px; margin-bottom: 20px;">613048, Кировская область,<br />Кирово-Чепецк, Производственная, 6</span>
            +7(83361) 5-20-67, 5-40-60, 9-28-73
            <a href="e(<? print email_antibot_encode('td@kccc.ru'); ?>)" class="mail eAddr-encoded eAddr-html" rel="nofollow"></a>
        </div>
        <div class="rep-box last col-sm-6 col-md-4">
            <a href="/info/contacts" class="contacts">Перейти в контакты <i class="icon-arrow_right"></i></a>
        </div>

    </div>
</div>