<?php
if (isset($content['field_pd_consumption_rate'])) {
    $range_title = $content['field_pd_consumption_rate']['#title'];
    $range_from = $content['field_pd_consumption_rate']['#items'][0]['from'];
    $range_to = $content['field_pd_consumption_rate']['#items'][0]['to'];
}

$units = get_product_units($node->nid);

// добавить к ссылке на товар id каталога, откуда туда пойдём (для формирования Path Breadcrumbs)
$tid = str_replace('taxonomy/term/', '', $_GET['q']);
if (is_numeric($tid)) {
    $node_url .= '?cat=' . $tid;
}


// формируем название препарата с формуляцией и компонентами
$title = get_product_agro_title($node->nid);
if (empty($title['ingredients'])) {
    if (!empty($node->field_pd_tu['und'][0]['value']))
        $title['ingredients'] = 'ТУ ' . $node->field_pd_tu['und'][0]['value'];
}
else {
    $title['ingredients'] = drupal_strtolower($title['ingredients']);
}
?>

<article class="product teaser" <?php print $attributes; ?>>
    <!-- если тизер выводится в результатах поиска - добавить заголовок с названием типа-->
    <?php if ($_GET['q'] == 'search'): ?>
        <div class="type"><?php print t('Product') . ': ' . t('Agrochemicals'); ?></div>
    <?php endif; ?>

    <div class="grid-full">
        <div class="grid-1-4 left">
            <?php print render($content['product:field_p_images']); ?>
            <div class="rating-short">
                <a href="<? print $node_url; ?>" title="Оценка препарата"><i class="fa fa-star" aria-hidden="true"></i> (<? print $rating_count; ?>)</a>
            </div>
        </div>
        <div class="grid-3-4 left">
            <h2><a href="<?php print $node_url; ?>"><?php print $title['title'] . ($title['formulation'] ? ', ' . $title['formulation'] : ''); ?></a></h2>
            <?php print $title['ingredients'] ? '<h3>' . $title['ingredients'] . '</h3>' : ''; ?>

            <div class="buy-table">
                <div class="grid-row">
                    <div class="grid-1-2 summary left">
                        <?php print render($content['body']); ?>
                        <div class="more">
                            <a href="<?php print $node_url; ?>"><?php print t('More...') ?></a>
                        </div>
                    </div>
                    <div class="grid-1-2 price left">
                        <?php if ($content['product:commerce_price']['#items'][0]['amount'] != 0) : ?>
                            <?php print render($content['product:commerce_price']); ?>
                        <?php else: ?>
                            <div class="field-label fleft"><?php print t('Price'); ?></div>
                            <div class="field-items"><?php print '<a href="#" onclick="supportAPI.openTab(0); return false;">' . t('Check price') . '</a>'; ?></div>
                        <?php endif; ?>

                        <?php print render($content['product:field_p_in_package']); ?>
                    </div>
                </div>
                <div class="grid-row">
                    <div class="grid-1-2 info left">
                        <?php if (!empty($range_title)) : ?>
                        <table>
                            <tr>
                                <td><?php print t('Price per ') .'&nbsp;'. mb_strtolower($units['unit']) . ' <span style="white-space: nowrap;">' . t('(with NDS)') . '</span>'; ?></td>
                                <td colspan=2 class="cblue tcenter"><?php print $content['field_pd_price_per_unit'][0]['#markup']; ?></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="tcenter">min</td>
                                <td class="tcenter">max</td>
                            </tr>
                            <tr>
                                <td><?php print $range_title; ?>
                                    (<?php print $units['cons_unit']; ?>)
                                </td>
                                <td class="cblue tcenter"><?php print (float) $range_from; ?></td>
                                <td class="cblue tcenter"><?php print (float) $range_to; ?></td>
                            </tr>
                            <tr>
                                <td><?php print t('Processing cost (rub/') . $units['field_unit'] . ')'; ?></td>
                                <td class="cblue tcenter"><?php print (float) $content['field_pd_price_per_unit']['#items'][0]['amount'] / 100 * $range_from; ?></td>
                                <td class="cblue tcenter"><?php print (float) $content['field_pd_price_per_unit']['#items'][0]['amount'] / 100 * $range_to; ?></td>
                            </tr>
                        </table>
                        <?php endif; ?>
                    </div>
                    <div class="grid-1-2 submit right">
                        <?php print render($content['field_product']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>