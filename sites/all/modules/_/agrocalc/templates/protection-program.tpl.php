<?php

global $user;
$area = $program['header']['area'];
$phase = $program['header']['phase'];
?>


<div id ="protection-program" class="view-list">
    <div class="view-content">

        <div class="protection-program col-sm-offset-1 col-sm-10 col-md-offset-2 col-md-8">
            <?php if (isset($program['categories'])): ?>
                <?php if ($area): ?>
                <header>
                    <div class="switch-all hidden-print">
                        <div class="material-switch-title">Включить в расчёт все препараты</div>
                        <div class="material-switch">
                            <input id="switch_all" name="switch_all" type="checkbox" />
                            <label for="switch_all" class="label-info"></label>
                        </div>
                    </div>
                </header>
                <?php endif; ?>

                <?php foreach($program['categories'] as $key_cat => $category): ?>
                    <div class="row" style="page-break-before: always;">
                        <div id="category-<?php print $category['tid']; ?>" class="header col-xs-12 category-<?php print $category['tid']; ?><?php print empty($category['cnt']) ? "" : " is-active"; ?>" data-toggle="collapse" data-cnt="<?php print empty($category['cnt']) ? '0' : $category['cnt']; ?>" href="#collapse-<?php print $category['tid']; ?>" aria-expanded="true" aria-controls="collapse-<?php print $category['tid']; ?>">
                            <div class="box">
                                <div class="bkg"><img src="<?php print $category['bkg']; ?>" alt="<?php print $category['name']; ?>"></div>
                                <img class="icon" src="<?php print $category['icon']; ?>">
                                <?php if ($area): ?>
                                    <div class="amountByCat">
                                        <div><h5 class="clr-category">НА ГЕКТАР</h5><p class="amount"></p></div>
                                        <div><h5 class="clr-category">ВСЕГО</h5><p class="total"></p></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <h4 class="clr-category"><?php print $category['name']; ?></h4>
                        </div>
                    </div>

                    <div class="row collapse in category-<?php print $category['tid']; ?>" id="collapse-<?php print $category['tid']; ?>">
                    <?php $counter = 1; ?>
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
                                            $rates[$pid] = '<div>' . $preparation['rate']['from'] . ($preparation['rate']['from'] != $preparation['rate']['to'] ? '-' . $preparation['rate']['to'] : '') . ' ' . $preparation['rate']['unit'] . '</div>';
                                        }
                                    }
                                    ?>

<!-- печатный вариант препаратов    -->
                                    <div class="view-item col-xs-4 reglament-print hidden-print" data-print="<?php print $key_cat . '_' . $key_stage . '_' . $key_set . '_' . $key_reg; ?>">
                                        <div class="v-card">
                                            <div class="v-card-image">
                                                <img src="<?php print $photos[0]; ?>" class="img-responsive" title="<?php print ''; ?>">
                                                <?php if (isset($photos[1])): ?>
                                                    <img src="<?php print $photos[1]; ?>" class="img-responsive" title="<?php print ''; ?>">
                                                <?php endif; ?>
                                            </div>
                                            <div class="v-card-content">
                                                <p class="v-card-title"><?php print $title; ?></p>
                                            </div>
                                        </div>
                                    </div>
<!--                                -->

                                    <div class="view-item col-xs-12 col-sm-6 col-md-4 col-lg-4 hidden-print">
                                        <div class="v-card reglament gray<?php print $reglament['state'] == 'on' ? " is-active" : ""; ?> hidden-print" data-cat="category-<?php print $category['tid']; ?>" id="<?php print $key_cat . '_' . $key_stage . '_' . $key_set . '_' . $key_reg; ?>">
                                            <?php if ($area): ?>
                                            <header>
                                                <div class="material-switch-title">Включить в расчёт</div>
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
                                                    <img src="<?php print $photos[0]; ?>" class="img-responsive" title="<?php print ''; ?>">
                                                    <?php if (isset($photos[1])): ?>
                                                    <img src="<?php print $photos[1]; ?>" class="img-responsive" title="<?php print ''; ?>">
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
                                                    <?php print '<span>Фаза культуры</span>'; ?>
                                                    <?php print $periods; ?>
                                                </div>
                                                <?php endif; ?>
                                                <?php if (!empty($reglament['hobjects'])): ?>
                                                <div>
                                                    <?php print '<span>Вредные объекты</span>'; ?>
                                                    <?php print $reglament['hobjects']; ?>
                                                </div>
                                                <?php endif; ?>
                                                <div>
                                                    <?php print '<span>Норма расхода, ' . implode(' + ', $rates_units) . '</span>'; ?>
                                                    <?php print implode('', $rates); ?>
                                                </div>
                                                <div class="calculation">
                                                    <div class="amountByItem"></div>
                                                </div>
                                            </footer>

                                        </div>
                                    </div>
                                    <?php
                                    if ($counter % 2 == 0) {
                                        print '<div class="clearfix hidden-md hidden-lg hidden-print"></div>';
                                    }
                                    if ($counter % 3 == 0) {
                                        print '<div class="clearfix hidden-sm hidden-print"></div>';
                                    }
                                    $counter++
                                    ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (isset($category['hobjects'])):
                        $phase = $program['header']['phase'] ? ' на этапе "' . $program['header']['phase'] . '"' : '';  ?>
                        <div class="view-item col-xs-12">
                            По препаратам против вредных объектов: "<?php print $category['hobjects'] . '"' . $phase; ?> свяжитесь с нашими специалистами на странице <?php print l('Представителей', 'info/representatives', ['attributes' => ['target' => '_blank']]); ?>
                        </div>
                    <?php endif; ?>
                    </div>

                <?php endforeach; ?>

                <?php if ($area): ?>
                <div class="row">
                    <div class="header col-xs-12 calculation-total category-17">
                        <div class="box">
                            <div class="bkg"></div>
                            <div class="amountByProgram">
                                <div><h5>НА ГЕКТАР</h5><p class="amount">0 руб.</p></div>
                                <div><h5>ВСЕГО</h5><p class="total">0 руб.</p></div>
                            </div>
                        </div>
                        <h4 class="clr-category">Итог по программе</h4>
                        <p class="note font-small">Указанные цены могут быть снижены. Для расчёта скидки свяжитесь с нашим представителем.</p>
                        <p class="choose-one font-small hidden-print text-danger">Вы не включили в расчёт ни одного препарата. Нажмите на переключатель над изображением.</p>
                    </div>
                 </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="col-xs-12">Для культуры на данном этапе роста у нашей компании нет препаратов.</div>
            <?php endif;?>
        </div>

    </div>
</div>