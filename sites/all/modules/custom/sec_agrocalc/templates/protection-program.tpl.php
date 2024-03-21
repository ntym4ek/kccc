<?php

global $user;
$area = $program['header']['area'];
$phase = $program['header']['phase'];
$path = drupal_get_path('module', 'agrocalc');
?>


<div id ="protection-program" class="view-list">
    <div class="view-content">

        <div class="protection-program col-sm-offset-1 col-sm-10 col-md-offset-2 col-md-8">
            <?php if (isset($program['categories'])): ?>
                <?php if ($area): ?>
                <header>
                    <div class="switch-all hidden-print">
                        <div class="material-switch-title"><? print t('Include all preparations'); ?></div>
                        <div class="material-switch">
                            <input id="switch_all" name="switch_all" type="checkbox" />
                            <label for="switch_all" class="label-info"></label>
                        </div>
                    </div>
                </header>
                <?php endif; ?>

                <?php $cnt_cat = 1; ?>
                <?php foreach($program['categories'] as $key_cat => $category): ?>
                    <div class="row" style="page-break-before: always;">
                        <div id="category-<?php print $category['tid']; ?>" class="list-category-header col-xs-12 category-<?php print $category['tid']; ?><?php print empty($category['cnt']) ? "" : " is-active"; ?>" data-toggle="collapse" data-cnt="<?php print empty($category['cnt']) ? '0' : $category['cnt']; ?>" href="#collapse-<?php print $category['tid']; ?>" aria-expanded="true" aria-controls="collapse-<?php print $category['tid']; ?>">
                            <div class="box">
                                <div class="bkg"><img src="<?php print $category['bkg_desk']; ?>" alt="<?php print $category['name']; ?>"></div>

                                <?php if ($cnt_cat == 1): ?>
                                    <img class="help4 hidden-xs" data-onscreen="true" data-animate="true" data-a-delay=".5s" src="/<?php print $path; ?>/images/help/4.png" alt="<? print t('help'); ?>"  />
                                <?php endif; ?>
                                <?php if ($area): ?>
                                    <div class="amountByCat">
                                        <div><h5 class="clr-category"><? print t('Per hectare'); ?></h5><p class="amount"></p></div>
                                        <div><h5 class="clr-category"><? print t('Summary', [], ['context' => 'agrocalc']); ?></h5><p class="total"></p></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <h4 class="clr-category"><?php print $category['name']; ?></h4>
                        </div>
                    </div>

                    <div class="row collapse in category-<?php print $category['tid']; ?>" id="collapse-<?php print $category['tid']; ?>">
                    <?php $cnt_reg = 1; ?>
                    <?php if (isset($category['stages'])): ?>
                        <?php foreach($category['stages'] as $key_stage => $stage): ?>
                            <?php foreach($stage as $key_set => $set): ?>
                                <?php foreach($set as $key_reg => $reglament): ?>

                                    <?php
                                    $title = $reglament['preparations']['title'];
                                    $product_url = $reglament['preparations']['desktop_url'];
                                    $periods = $reglament['period']['start']['name'] . ($reglament['period']['start']['name'] != $reglament['period']['end']['name'] ? ' - ' . $reglament['period']['end']['name'] : '');

                                    $photos = $rates = $prices = $rates_units = [];
                                    foreach($reglament['preparations']['items'] as $key_prep => $preparation) {
                                        $pid = $preparation['id'];
                                        $photos[] = $preparation['photo_medium'];
                                        $prices[] = $preparation['price'];
                                        $rates_units[] = $preparation['rate']['unit'];

                                        $rate = $preparation['rate'];
                                        if ($area) {
                                            $min = (float)$rate['from'];
                                            $max = (float)$rate['to'];

                                            $min_count = empty(explode('.', $min)[1]) ? 1 : drupal_strlen(explode('.', $min)[1]);
                                            $max_count = empty(explode('.', $max)[1]) ? 1 : drupal_strlen(explode('.', $max)[1]);
                                            $mid_count = $min_count > $max_count ? $min_count : $max_count;
                                            $mid = round(($max + $min)/2, $mid_count+1);

                                            // start value
                                            $from = 1;
                                            if (isset($rate['default'])) {
                                                if ($rate['default'] == $min) $from = 0;
                                                if ($rate['default'] == $max) $from = 2;
                                            }

                                            $block = $min == $max ? 'true' : 'false';
                                            $rates[$pid] = '<input  type="text"
                                                                    id="range_' . $key_cat . '_' . $key_stage . '_' . $key_set . '_' . $key_reg . '_' . $key_prep . '"
                                                                    name="range_' . $key_cat . '_' . $key_stage . '_' . $key_set . '_' . $key_reg . '_' . $key_prep . '"
                                                                    data-values="' . $min . ', ' . $mid . ', ' . $max . '"
                                                                    data-from="' . $from . '"
                                                                    data-block="' . $block . '"
                                                                    >';
                                        } else {
                                          $rate_from = str_replace('.', ',', $preparation['rate']['from']);
                                          $rate_to = str_replace('.', ',', $preparation['rate']['to']);
//                                          $rates[$pid] = '<div>' . $rate_from . ($rate_from != $rate_to ? '-' . $rate_to : '') . ' ' . $preparation['rate']['unit'] . '</div>';
                                          $rates[$pid] = $rate_from . ($rate_from != $rate_to ? '-' . $rate_to : '') . ' ' . $preparation['rate']['unit'];
                                        }
                                    }
                                    ?>

<!-- печатный вариант препаратов    -->
                                    <div class="view-item col-xs-4 reglament-print hidden-print" data-print="<?php print $key_cat . '_' . $key_stage . '_' . $key_set . '_' . $key_reg; ?>">
                                        <div class="v-card">
                                            <div class="v-card-image">
                                                <img typeof="foaf:Image" src="<?php print $photos[0]; ?>" class="img-responsive" alt="<?php print $title; ?>">
                                                <?php if (isset($photos[1])): ?>
                                                    <img typeof="foaf:Image" src="<?php print $photos[1]; ?>" class="img-responsive" alt="<?php print $title; ?>">
                                                <?php endif; ?>
                                            </div>
                                            <div class="v-card-content">
                                                <p class="v-card-title"><?php print $title; ?></p>
                                            </div>
                                        </div>
                                    </div>
<!--                                -->

                                    <div class="view-item col-xs-12 col-sm-6 col-md-4 col-lg-4 hidden-print">
                                        <?php if ($area && $cnt_reg == 1 && $cnt_cat == 1): ?>
                                            <img class="help5 hidden-xs" data-onscreen="true" data-animate="true" data-a-delay=".5s" data-a-effect="slide-down" src="/<?php print $path; ?>/images/help/5.png" alt="<? print t('help'); ?>"  />
                                        <?php endif; ?>
                                        <div class="v-card reglament gray<?php print $reglament['state'] == 'on' ? " is-active" : ""; ?> hidden-print" data-cat="category-<?php print $category['tid']; ?>" id="<?php print $key_cat . '_' . $key_stage . '_' . $key_set . '_' . $key_reg; ?>">
                                            <?php if ($area): ?>
                                            <header>
                                                <div class="material-switch-title"><? print t('Include'); ?></div>
                                                <div class="material-switch">
                                                    <input id="switch_<?php print $key_cat . '_' . $key_stage . '_' . $key_set . '_' . $key_reg; ?>"
                                                           name="switch_<?php print $key_cat . '_' . $key_stage . '_' . $key_set . '_' . $key_reg; ?>"
                                                           type="checkbox"
                                                           data-tid="<?php print $category['tid']; ?>"
                                                           data-price0="<?php print $prices[0]; ?>"
                                                           data-price1="<?php print empty($prices[1]) ? 0 : $prices[1]; ?>"
                                                           <?php print $reglament['state'] == 'on' ? 'checked="checked"' : ''; ?>
                                                    />
                                                    <label for="switch_<?php print $key_cat . '_' . $key_stage . '_' . $key_set . '_' . $key_reg; ?>" class="label-info"></label>
                                                </div>
                                            </header>
                                            <?php endif; ?>

                                            <div class="v-card-image">
                                                <a href="<?php print $product_url; ?>" target="_blank">
                                                    <img typeof="foaf:Image" src="<?php print $photos[0]; ?>" class="img-responsive" alt="<?php print $title; ?>">
                                                    <?php if (isset($photos[1])): ?>
                                                    <img typeof="foaf:Image" src="<?php print $photos[1]; ?>" class="img-responsive" alt="<?php print $title; ?>">
                                                    <?php endif; ?>
                                                </a>
                                            </div>

                                            <div class="v-card-content">
                                                <h4 class="v-card-title"><a href="<?php print $product_url; ?>" target="_blank"><?php print $title; ?></a></h4>
                                                <?php if (!empty($reglament['preparations']['ingredients'])): ?>
                                                <div class="v-card-summary">
                                                    <div class="v-card-subtitle"><?php print $reglament['preparations']['ingredients']; ?></div>
                                                </div>
                                                <?php endif; ?>
                                            </div>

                                            <footer>
                                                <?php if (empty($phase)): ?>
                                                <div>
                                                    <?php print '<span>' . t('Growth stage') . '</span>'; ?>
                                                    <?php print $periods; ?>
                                                </div>
                                                <?php endif; ?>
                                                <?php if (!empty($reglament['hobjects'])): ?>
                                                <div>
                                                    <?php print '<span>' . t('Harmful objects') . '</span>'; ?>
                                                    <?php print $reglament['hobjects']; ?>
                                                </div>
                                                <?php endif; ?>
                                                <div>
                                                    <?php print '<span>' . t('Consuption rate') . ', ' . implode(' + ', $rates_units) . '</span>'; ?>
                                                    <?php print ($area ? '<div>' . implode(' + ', $rates) . '</div>' : implode('', $rates)); ?>
                                                </div>
                                                <div class="calculation">
                                                    <div class="amountByItem"></div>
                                                </div>
                                            </footer>
                                        </div>
                                        <?php if ($area && $cnt_reg == 2 && $cnt_cat == 1): ?>
                                            <img class="help6 hidden-xs" data-onscreen="true" data-animate="true" data-a-delay=".5s" src="/<?php print $path; ?>/images/help/6.png" alt="<? print t('help'); ?>" />
                                        <?php endif; ?>
                                    </div>
                                    <?php
                                    if ($cnt_reg % 2 == 0) {
                                        print '<div class="clearfix hidden-md hidden-lg hidden-print"></div>';
                                    }
                                    if ($cnt_reg % 3 == 0) {
                                        print '<div class="clearfix hidden-sm hidden-print"></div>';
                                    }
                                    $cnt_reg++
                                    ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (isset($category['hobjects'])):
                        $phase = $program['header']['phase'] ? ' ' . t('on stage') . ' ' . $program['header']['phase'] : '';  ?>
                        <div class="view-item col-xs-12">
                            <? print t('To get information about preparations against harmful objects: @hobjects @phase contact our <a href="@url" target="_blank">Representatives</a>', ['@url' => url('info/representatives'), '@hobjects' => $category['hobjects'], '@phase' => $phase]); ?>
                        </div>
                    <?php endif; ?>
                    </div>

                    <?php $cnt_cat++; ?>
                <?php endforeach; ?>

                <?php if ($area): ?>
                <div class="row">
                    <div class="list-category-header col-xs-12 calculation-total category-17">
                        <img class="help7 hidden-xs" data-onscreen="true" data-animate="true" data-a-delay=".5s" src="/<?php print $path; ?>/images/help/7.png" alt="<? print t('help'); ?>"  />
                        <div class="box">
                            <div class="bkg"></div>
                            <div class="amountByProgram">
                                <div><h5><? print t('Per hectare'); ?></h5><p class="amount">0 руб.</p></div>
                                <div><h5><? print t('Summary', [], ['context' => 'agrocalc']); ?></h5><p class="total">0 руб.</p></div>
                            </div>
                        </div>
                        <h4 class="clr-category"><? print t('Program summary'); ?></h4>
                        <p class="note"><? print t('Presented prices could be lower. Contact our representatives for discount.'); ?></p>
                        <p class="choose-one hidden-print text-danger"><? print t('You did not chose any preparation. Click on switch above image.'); ?></p>
                    </div>
                 </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="col-xs-12"><p class="text-danger"><? print t('We do not have preparations for culture in this growth stage.'); ?></p></div>
            <?php endif;?>
        </div>

    </div>
</div>


