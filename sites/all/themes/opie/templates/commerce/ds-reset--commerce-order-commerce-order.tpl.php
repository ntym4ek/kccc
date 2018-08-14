<?php
/**
 * Created by PhpStorm.
 * User: ntym
 * Date: 16.07.2015
 * Time: 15:37
 */
$order_wrapper = entity_metadata_wrapper('commerce_order', $commerce_order);
$shipping_wrapper = $order_wrapper->commerce_customer_shipping;

$name = $address = $phone = $passport = '';
$mail = $commerce_order->mail;
if (isset($order_wrapper->commerce_customer_shipping->field_ship_address)) {
  $address_id = $order_wrapper->commerce_customer_shipping->field_ship_address->value();

    //$address_wrapper = entity_metadata_wrapper('node', $address_id);

    $addr_book = address_book_addr_to_string($address_id, 'full');
    if ($addr_book['email']) $mail = $addr_book['email'];
    $name = $addr_book['user'];
    $address = $addr_book['address'];
    $passport = $addr_book['passport'];
    $phone = $addr_book['phone'];
}


// информация о доставке
$shipping = array();
foreach ($order_wrapper->commerce_line_items as $delta => $line_item_wrapper) {
    if ($line_item_wrapper->type->value() == 'shipping') {
        $shipping['name'] = $line_item_wrapper->commerce_shipping_service->value();
        $shipping['label'] = $line_item_wrapper->line_item_label->value();
        $cost = $line_item_wrapper->commerce_total->value();
        $shipping['cost'] = $cost['amount']/100 . ' руб.';
        $info = $line_item_wrapper->commerce_total->data->value();
        if (isset($info['terminals'][0]['address'])) $shipping['place'] = $info['terminals'][0]['address'];
        if (isset($info['time'])) $shipping['time'] = $info['time'];
    }
}

// физические  данные
$arr_weight = commerce_physical_order_weight($commerce_order, 'kg');
$weight = empty($arr_weight['weight']) ? '' : $arr_weight['weight'];
$arr_volume = commerce_physical_order_volume($commerce_order, 'm');
$volume = empty($arr_volume['volume']) ? '' : $arr_volume['volume'];

    // опции
$field_opt_to_door = isset($order_wrapper->commerce_customer_shipping->field_ship_to_door) ? $shipping_wrapper->field_ship_to_door->value() : '';
$field_opt_insurance = isset($order_wrapper->commerce_customer_shipping->field_ship_insurance) ? $shipping_wrapper->field_ship_insurance->value() : '';
if ($shipping && $shipping['name'] == 'transport_shipping_service') {
    $shipping['deliver_to'] = (!$field_opt_to_door)? 'terminal':'door';
    $shipping['insurance'] = (!$field_opt_insurance)? 'no':'yes';
}

// информация об оплате
$payment_method = '';
$payment_balance = commerce_payment_order_balance($commerce_order);
$paid_amount = ($commerce_order_total[0]['amount']-$payment_balance['amount'])/100 . ' руб.';

?>

<div class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>

    <div class="content"<?php print $content_attributes; ?>>
        <?php print render($content['commerce_line_items']); ?>
        <?php print render($content['commerce_order_total']); ?>
        <div class="fieldset grid-full">
            <div class="head"><?php print t('Customer'); ?></div>
            <div class="item"><?php print '<div>' . t('Name')      . ':</div> <div>' . $name; ?></div></div>
            <div class="item"><?php print '<div>' . t('Address')   . ':</div> <div>' . $address; ?></div></div>
            <div class="item"><?php print '<div>' . t('Phone')     . ':</div> <div>' . $phone; ?></div></div>
            <div class="item"><?php print '<div>' . t('E-Mail')    . ':</div> <div>' . $mail; ?></div></div>
            <?php if ($passport): ?>
                <div class="item"><?php print '<div>' . t('Passport')  . ':</div> <div>' . $passport; ?></div></div>
            <?php endif; ?>
            <div class="head"><?php print t('Shipping information'); ?></div>
            <div class="item"><?php print '<div>' . t('Method')    . ':</div> <div>' . ($shipping ? $shipping['label'] : ''); ?></div></div>
            <div class="item"><?php print '<div>' . t('Cost')      . ':</div> <div>' . ($shipping ? $shipping['cost'] : ''); ?></div></div>
            <?php if (isset($shipping_service['insurance'])): ?>
            <div class="item"><?php print '<div>' . t('Insurance')  . ':</div> <div>' . ($shipping ? t($shipping['insurance']) : ''); ?></div></div>
            <?php endif; ?>
            <?php if (isset($shipping['deliver_to'])): ?>
            <div class="item"><?php print '<div>' . t('Deliver order to '). ':</div> <div>' . ($shipping ? t($shipping['deliver_to']) : ''); ?></div></div>
            <?php endif; ?>
            <?php if (isset($shipping['place'])&&$shipping['deliver_to'] != 'door'): ?>
            <div class="item"><?php print '<div>' . t('Terminal')       . ':</div> <div>' . ($shipping ? $shipping['place'] : ''); ?></div></div>
            <?php endif; ?>
            <?php if (isset($shipping['time'])): ?>
            <div class="item"><?php print '<div>' . t('Delivery time')  . ':</div> <div>' . ($shipping ? $shipping['time'] : ''); ?></div></div>
            <?php endif; ?>
            <div class="head"><?php print t('Payment information'); ?></div>
            <div class="item"><?php print '<div>' . t('Paid amount')   . ':</div> <div>' . $paid_amount; ?></div></div>
            <div class="head"><?php print 'Прочее'; ?></div>
            <div class="item"><?php print '<div>' . t('Order #')   . ':</div> <div>' . $commerce_order->order_number; ?></div></div>
            <div class="item"><?php print '<div>' . t('Created')   . ':</div> <div>' . format_date($commerce_order->created, 'short'); ?></div></div>
            <div class="item"><?php print '<div>' . t('Changed')   . ':</div> <div>' . format_date($commerce_order->changed, 'short'); ?></div></div>
            <div class="item"><?php print '<div>' . t('Status')    . ':</div> <div>' . t($commerce_order->status); ?></div></div>
            <div class="item"><?php print '<div>' . t('Weight')    . ':</div> <div>' . $weight . ' ' . t('kg'); ?></div></div>
            <div class="item"><?php print '<div>' . t('Volume')    . ':</div> <div>' .(float)number_format($volume, 4) . ' ' .t('m<span class="sup">3</span>'); ?></div></div>
        </div>
    </div>
</div>