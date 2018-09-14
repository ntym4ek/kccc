<?php

/**
 * Checkout pane callback: returns the pane's settings form.
 */
function checkout_cart_pane_settings_form($checkout_pane) {
    $form['checkout_cart_pane_field'] = array(
        '#type' => 'textfield',
        '#title' => t('TODO: checkout_cart settings form'),
    );

    return $form;
}

/**
 * Checkout pane callback: returns the contents View for inclusion in the checkout form.
 */
function checkout_cart_pane_checkout_form($form, &$form_state, $checkout_pane, $order)
{
    $order = commerce_order_load($order->order_id);
    $order_wrapper = entity_metadata_wrapper('commerce_order', $order);
    if (!empty($form_state['triggering_element'])
        && strpos($form_state['triggering_element']['#name'], 'delete-line-item') !== false) {
        $lid = str_replace('delete-line-item-', '', $form_state['triggering_element']['#name']);
        $line_item_wrapper = entity_metadata_wrapper('commerce_line_item', $lid);
        $line_item_wrapper->delete();
    }

    $checkout_form = array(
        '#type' => 'container',
        '#attributes' => array('id' => array('checkout-cart'), 'class' => array('checkout-cart')),
    );

    if ($order_wrapper->commerce_line_items->count()) {
        $checkout_form['list'] = array(
            '#type' => 'container',
            '#attributes' => array('class' => array('cc-list')),
        );

        $order_total = $weight = $volume = 0;
        foreach ($order_wrapper->commerce_line_items as $item_wrapper) {
            $lid = $item_wrapper->line_item_id->value();
            $qty = $item_wrapper->quantity->value();
            if (!empty($form_state['triggering_element']) && $form_state['triggering_element']['#name'] == 'update-cart') {
                $new_qty = $form_state['values']['checkout_cart']['list']['item-' . $lid]['qty'];
                if (!is_numeric($new_qty)) $new_qty = 1;

                if ($qty != $new_qty) {
                    $qty = $new_qty;
                    $item_wrapper->quantity = $qty;
                    $item_wrapper->save();
                    $order_wrapper->save();  // тоже сохраняем для обновления информации о сумме заказа
                }
            }

            $price = $item_wrapper->commerce_unit_price->amount->value() / 100;
            $total = $item_wrapper->commerce_total->amount->value() / 100;
            $balance = $item_wrapper->commerce_total->value();

            $commerce_product_wrapper = $item_wrapper->commerce_product;
            //$commerce_product = $item_wrapper->commerce_product->value();
            $pid = $commerce_product_wrapper->product_id->value();
            $tare = $commerce_product_wrapper->field_p_tare->name->value();
            $tare_qty = $commerce_product_wrapper->field_p_qty->value();

            $nid = _get_product_display_by_product_id($pid);
            $prep_info = array_shift(get_product_info($nid)['items']);
            $package = $tare ? drupal_strtolower($tare) : '';

            $checkout_form['list']['item-' . $lid] = array(
                '#type' => 'container',
                '#attributes' => array('class' => array('cc-item')),
            );

            // изображение
            $image_style = 'thumbnail';
            $image_uri = 'public://default_images/no_image.jpg';

            if ($commerce_product_wrapper->field_p_images[0]->value()) {
                $image_file = $commerce_product_wrapper->field_p_images[0]->file->value();
                $image_uri = $image_file->uri;
            }
            $image = image_style_url($image_style, $image_uri);

            $checkout_form['list']['item-' . $lid]['image'] = array(
                '#markup' => '<img src="' . $image . '">',
                '#prefix' => '<div class="cc-image">',
                '#suffix' => '</div>',
            );

            // характеристики препарата
            $properties = array();
            $properties['Quantity'] = $tare_qty . ' ' . $prep_info['unit_short'] . ' ' . t('of preparation');
            $properties['Preparation form'] = $prep_info['form_full'] ? $prep_info['form_full'] : '';
            $properties['Active ingredients'] = $prep_info['ingredients'] ? implode(' + ', $prep_info['ingredients']) : '';

            $prop_text = '';
            foreach ($properties as $label => $value) {
                if ($value) $prop_text .= '<div><span>' . t($label, array(), array('context' => 'Checkout')) . ': </span>' . $value . '</div>';
            }

            $checkout_form['list']['item-' . $lid]['product'] = array(
                '#markup' => '<h4><a href="' . url('/node/' . $nid) . '" target="_blank">' . $prep_info['title'] . '<span>, ' . $package . '</span></a></h4>'
                    . $prop_text,
                '#prefix' => '<div class="cc-product">',
                '#suffix' => '</div>',
            );

            $checkout_form['list']['item-' . $lid]['price'] = array(
                '#markup' => commerce_currency_format($price, $balance['currency_code'], NULL, FALSE),
                '#prefix' => '<div class="cc-price-group"><div class="cc-price">',
                '#suffix' => '</div>',
            );

            $checkout_form['list']['item-' . $lid]['qty'] = array(
                '#type' => 'textfield',
                '#default_value' => (integer)$qty,
                '#attributes' => array('class' => array('qty-input')),
                '#prefix' => '<div class="cc-qty"><div class="input-group"><div class="input-group-btn"><button class="btn btn-default qty-minus" type="button"><i class="fa fa-minus" aria-hidden="true"></i></button></div>',
                '#suffix' => '<div class="input-group-btn"><button class="btn btn-default qty-plus" type="button"><i class="fa fa-plus" aria-hidden="true"></i></button></div></div></div>',
            );

            $checkout_form['list']['item-' . $lid]['total'] = array(
                '#markup' => commerce_currency_format($total, $balance['currency_code'], NULL, FALSE),
                '#prefix' => '<div class="cc-total">',
                '#suffix' => '</div></div>',
            );

            $checkout_form['list']['item-' . $lid]['actions'] = array(
                '#type' => 'container',
                '#attributes' => array('class' => array('cc-action')),
            );
            $checkout_form['list']['item-' . $lid]['actions']['delete'] = array(
                '#type' => 'button',
                '#value' => '<i class="fa fa-trash" aria-hidden="true"></i>',
                '#name' => 'delete-line-item-' . $lid,
                '#attributes' => array('class' => array('btn-sm')),
//                '#limit_validation_errors' => array(array('checkout_cart', 'list')),
            );

            // считаем суммы веса, объёма, стоимости
            $order_total += $total;
            $weight += $commerce_product_wrapper->field_p_weight->weight->value() * $qty;
            $volume += $commerce_product_wrapper->field_p_dimentions->length->value()
                * $commerce_product_wrapper->field_p_dimentions->width->value()
                * $commerce_product_wrapper->field_p_dimentions->height->value()
                * $qty;
        }

        $checkout_form['list']['summary'] = array(
            '#type' => 'container',
            '#attributes' => array('class' => array('cc-summary')),
        );

        $checkout_form['list']['summary']['physical'] = array(
            '#markup' => '<div class="cc-physical"><span>' . t('Weight') . ': </span>' . $weight . ' ' . t('kg')
                . '&nbsp;&nbsp;&nbsp;<span>' . t('Volume') . ': </span>' . number_format($volume, 2) . ' ' . t('m<span class="sup">3</span>')
                . '</div>',
        );
        $checkout_form['list']['summary']['total'] = array(
            '#markup' => '<div class="cc-total">'
                . commerce_currency_format($order_total, $balance['currency_code'], NULL, FALSE)
                . '</div>',
        );

        $checkout_form['list']['summary']['refresh'] = array(
            '#type' => 'button',
            '#value' => '<i class="fa fa-refresh" aria-hidden="true"></i>',
            '#name' => 'update-cart',
            '#attributes' => array('class' => array('btn-sm')),
            '#prefix' => '<div class="cc-refresh">',
            '#suffix' => '</div>',
            '#ajax' => array(
                'callback' => 'checkout_cart_ajax_refresh',
                'event' => 'click',
            ),
//            '#limit_validation_errors' => array(array('checkout_cart', 'list')),
        );

        $checkout_form['#attached']['js'] = array(
            drupal_get_path('module', 'checkout') . '/js/checkout.js',
        );
    } else {
        $checkout_form['empty'] = array(
            '#markup' => '<div class="cc-empty">' . t('Your cart is empty') . '</div>',
        );
    }

    return $checkout_form;
}

/**
 * Checkout pane ajax callback: refresh form.
 */
function checkout_cart_ajax_refresh ($form, $form_state) {
    // вернуть части пересчитанной формы
    return array(
        '#type' => 'ajax',
        '#commands' => array(
            ajax_command_invoke('#checkout-cart', 'replaceWith', array(drupal_render($form['checkout_cart']))),
        ),
    );
}

/**
 * Checkout pane callback: validation callback.
 */
function checkout_cart_pane_checkout_form_validate($form, &$form_state, $checkout_pane, $order)
{
    return true;
}

/**
 * Checkout pane callback: submit callback.
 */
function checkout_cart_pane_checkout_form_submit($form, &$form_state, $checkout_pane, $order)
{
}