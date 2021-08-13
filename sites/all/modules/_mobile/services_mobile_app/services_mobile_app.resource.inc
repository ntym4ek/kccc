<?php

/**
 * функция, собирающая способы доставки со стоимостью в зависимости от полученоого адреса
 *
 * @return mixed
 */
function services_mobile_app_get_list($order_id) {
    // получить стоимость доставок для переданного заказа
    $methods = array();

    $order = commerce_order_load($order_id);
    commerce_shipping_collect_rates($order);

    if (!empty($order->shipping_rates)) {
        foreach($order->shipping_rates as $rate) {
            $data = $rate->commerce_unit_price['und'][0]['data'];

            $methods[$rate->data['shipping_service']['name']] = array(
                'id' => $rate->data['shipping_service']['name'],
                'amount' => $data['components'][0]['price']['amount'],
                'name' => $rate->line_item_label,
                'error' => $data['error'],
                'message' => $data['message'],
            );
        }
    }


    return $methods;
}

/**
 * функция, добавляющая line_item со способом доставки в заказ
 *
 * @return mixed
 */
function services_mobile_app_add_shipping_line_item($service_name, $order_id) {

    $order = commerce_order_load($order_id);
    commerce_shipping_collect_rates($order);

    // Delete any existing shipping line items from the order.
    commerce_shipping_delete_shipping_line_items($order, TRUE);

    // Extract the unit price from the calculated rate.
    $rate_line_item = $order->shipping_rates[$service_name];
    $rate_line_item_wrapper = entity_metadata_wrapper('commerce_line_item', $rate_line_item);
    $unit_price = $rate_line_item_wrapper->commerce_unit_price->value();

    // Create a new shipping line item with the calculated rate from the form.
    $line_item = commerce_shipping_line_item_new($service_name, $unit_price, $order->order_id, $rate_line_item->data, $rate_line_item->type);

    // Save and add the line item to the order.
    commerce_shipping_add_shipping_line_item($line_item, $order, FALSE);
}

/**
 * функция, создающая временную YM транзакцию
 *
 * @return mixed
 */
function services_mobile_app_create_ym_transaction($order_id) {
    global $user;

    $order_wrapper = entity_metadata_wrapper('commerce_order', $order_id);
    $currency_code = $order_wrapper->commerce_order_total->currency_code->value();
    $amount = $order_wrapper->commerce_order_total->amount->value();
    $amount = round(commerce_currency_amount_to_decimal($amount, $currency_code), 2);

    $transaction = new YAMoneyTransaction();
    $transaction->uid = isset($user->uid) ? $user->uid : 0;
    $transaction->uid = isset($user->uid) ? $user->uid : '';
    $transaction->amount = $amount;
    $transaction->mail = isset($user->mail) ? $user->mail : '';
    $transaction->order_id = $order_id;
    if (yamoney_transaction_save($transaction)) $transaction_id = $transaction->ymid;
    else $transaction_id = 0;

    return array(
        'transaction_id' => $transaction_id,
    );
}

/**
 * функция, устанавливающая статус оплаты для заказа
 *
 * @return mixed
 */
function services_mobile_app_set_payment_status($instance_id, $order_id, $status) {

    $order_wrapper = entity_metadata_wrapper('commerce_order', $order_id);
    $currency_code = $order_wrapper->commerce_order_total->currency_code->value();
    $amount = $order_wrapper->commerce_order_total->amount->value();

    list($service_name) = explode('|', $instance_id);

    // Создаём транзакцию
    $transaction = commerce_payment_transaction_new($service_name, $order_id);
    $transaction->instance_id = $instance_id;
    $transaction->amount = $amount;
    $transaction->currency_code = $currency_code;
    $transaction->status = $status;
    $transaction->message = t('The payment is pending.');
    commerce_payment_transaction_save($transaction);

//    watchdog('services_mobile_app', '<pre>' . print_r($transaction, true) . '</pre>', array());

    return array(
        'transaction_id' => $transaction->transaction_id
    );
}

/**
 * функция, очищающая корзину пользователя
 *
 * @return mixed
 */
function services_mobile_app_clear_cart() {
    global $user;
    // Load the order and empty the cart

    $order = commerce_cart_order_load($user->uid);
    commerce_cart_order_empty($order);
}