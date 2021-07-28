<?php


/**
 * Checkout pane callback: returns the contents View for inclusion in the checkout form.
 */
function ext_checkout_address_pane_checkout_form($form, &$form_state, $checkout_pane, $order)
{
  // если выбрана услуга доставки
  if (!empty($form_state["values"]["checkout_services"]["services"][TID_DELIVERY_SERVICE])) {

    $terms = taxonomy_get_tree(taxonomy_vocabulary_machine_name_load('warehouses')->vid);
    $warehouses = [];
    foreach($terms as $term) {
      $warehouses[$term->tid] = $term->name;
    }

    $checkout_form = [
      '#type' => 'container',
      '#attributes' => ['id' => ['checkout-address'], 'class' => ['checkout-address']],
    ];
    $checkout_form['title'] = [
      '#markup' => '<h2>Адрес доставки</h2>',
    ];
    $checkout_form['warehouse'] = [
      '#type' => 'select',
      '#options' => $warehouses,
    ];

    return $checkout_form;
  }
}

/**
 * Checkout pane callback: validation callback.
 */
function ext_checkout_address_pane_checkout_form_validate($form, &$form_state, $checkout_pane, $order)
{
  if (!empty($form_state["values"]["checkout_services"]["services"][TID_DELIVERY_SERVICE]) && empty($form_state["values"]["checkout_address"]["warehouse"])) {
    form_set_error('checkout_address][warehouse', 'Необходимо выбрать склад доставки');
    return false;
  }

  return true;
}

/**
 * Checkout pane callback: submit callback.
 */
function ext_checkout_address_pane_checkout_form_submit($form, &$form_state, $checkout_pane, $order)
{
  if (!empty($form_state["values"]["checkout_address"]["warehouse"])) {

    // сохранить введённый адрес
    $order_wr = entity_metadata_wrapper('commerce_order', $order);
    $order_wr->field_warehouse = $form_state["values"]["checkout_address"]["warehouse"];
    $order_wr->save();
  }
}
