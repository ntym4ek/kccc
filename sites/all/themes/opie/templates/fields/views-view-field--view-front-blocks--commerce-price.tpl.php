<?php

/**
 * @file
 * This template is used to print a single field in a view.
 *
 * It is not actually used in default Views, as this is registered as a theme
 * function which has better performance. For single overrides, the template is
 * perfectly okay.
 *
 * Variables available:
 * - $view: The view object
 * - $field: The field handler object that can process the input
 * - $row: The raw SQL result that can be used
 * - $output: The processed output that will normally be used.
 *
 * When fetching output from the $row, this construct should be used:
 * $data = $row->{$field->field_alias}
 *
 * The above will guarantee that you'll always get the correct data,
 * regardless of any changes in the aliasing that might happen if
 * the view is modified.
 */
?>
<?php
    $raw_price = $row->field_commerce_price[0]['raw'];
    $price = $raw_price['amount'];
    $price_rub = substr( $price, 0, -2);
    $price_kop = substr( $price, -2);
    $price_rub = ( $price_rub ) ? $price_rub : '0';
    $price_kop = ( $price_kop ) ? $price_kop : '00';
    $currency = format_plural($price_rub . ',' . $price_kop, '1 rouble', '@count roubles');
    if ( isset( $raw_price['data']['components'] )) {
        $base_price = $raw_price['data']['components'][0]['price']['amount'];
        $base_price_rub = substr( $base_price, 0, -2);
        $base_price_kop = substr( $base_price, -2);
        $base_price_rub = ( $base_price_rub ) ? $base_price_rub : '0';
        $base_price_kop = ( $base_price_kop ) ? $base_price_kop : '00';
        $base_currency = format_plural($base_price_rub . ',' . $base_price_kop, '1 rouble', '@count roubles');
    }
?>
  <div class="">
    <div class="field-item">
        <?php if ( $price != $base_price ): ?>
            <div class="old-price"><?php print mb_strtoupper( $base_currency ); ?></div>
        <?php endif; ?>
        <?php print mb_strtoupper( $currency ); ?>
    </div>
  </div>
