<?php
if ($element['#field_name'] != 'commerce_order_total'):
    $commerce_price = field_get_items('commerce_product', $element['#object'], 'commerce_price');
    $price = number_format($commerce_price['0']['amount'] / 100, 2, ',', ' ');
    // с разделением тысяч пробелами склонение выполняется неправильно
    $currency = format_plural($commerce_price['0']['amount'] / 100, '1 rouble', '@count roubles');
    $cost = $price . ' ' . trim(str_replace($commerce_price['0']['amount'] / 100, '', $currency));

    if (isset($commerce_price['0']['data']['components']['0']['price']['amount'])) {
        $base_price = number_format($commerce_price['0']['data']['components']['0']['price']['amount'] / 100, 2, ',', ' ');
        $base_currency = format_plural($base_price, '1 rouble', '@count roubles');
        $base_currency = $base_price . ' ' . format_plural($commerce_price['0']['data']['components']['0']['price']['amount']  / 100, '1 rouble', '@count roubles');
    }
    ?>

    <div class="<?php print $classes; ?>"<?php print $attributes; ?>>
        <div class="field-item"<?php print $content_attributes; ?>>
            <?php print mb_strtoupper($cost); ?>
            <?php if (($element['#view_mode'] == 'node_full' || $element['#view_mode'] == 'default'|| $element['#view_mode'] == 'node_teaser') && ($price != $base_price)): ?>
                <div class="old-price"><?php print mb_strtoupper($base_currency); ?></div>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="<?php print $classes; ?>"<?php print $attributes; ?>>
        <div class="field-items"<?php print $content_attributes; ?>>
            <?php foreach ($items as $delta => $item): ?>
                <div
                    class="field-item <?php print $delta % 2 ? 'odd' : 'even'; ?>"<?php print $item_attributes[$delta]; ?>><?php print render($item); ?></div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>