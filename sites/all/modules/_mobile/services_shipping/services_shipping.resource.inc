<?php

/**
 * функция, собирающая способы доставки со стоимостью в зависимости от полученоого адреса
 *
 * @return mixed
 */
function services_shipping_get_list($order_id) {
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
function services_shipping_add_shipping_line_item($service_name, $order_id) {

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