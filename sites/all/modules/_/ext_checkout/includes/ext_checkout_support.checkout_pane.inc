<?php


/**
 * Checkout pane callback: returns the contents View for inclusion in the checkout form.
 */
function ext_checkout_support_pane_checkout_form($form, &$form_state, $checkout_pane, $order)
{
  // если выбрана услуга доставки
  if (!empty($form_state["values"]["checkout_services"]["services"][TID_SUPPORT_SERVICE])) {

    $terms = taxonomy_get_tree(taxonomy_vocabulary_machine_name_load('regions')->vid);
    $regions_w_support = [];
    foreach($terms as $term) {
      $regions_w_support[$term->tid] = $term->name;
    }

    $checkout_form = [
      '#type' => 'container',
      '#attributes' => ['id' => ['checkout-support'], 'class' => ['checkout-support']],
    ];
    $checkout_form['title'] = [
      '#markup' => '<h2>Регион сопровождения</h2>',
    ];
    $checkout_form['region'] = [
      '#type' => 'select',
      '#options' => $regions_w_support,
    ];

    return $checkout_form;
  }
}

/**
 * Checkout pane callback: validation callback.
 */
function ext_checkout_support_pane_checkout_form_validate($form, &$form_state, $checkout_pane, $order)
{
  if (!empty($form_state["values"]["checkout_support"]["region"][TID_SUPPORT_SERVICE]) && empty($form_state["values"]["checkout_support"]["region"])) {
    form_set_error('checkout_support][support', 'Необходимо выбрать регион сопровождения');
    return false;
  }

  return true;
}

/**
 * Checkout pane callback: submit callback.
 */
function ext_checkout_support_pane_checkout_form_submit($form, &$form_state, $checkout_pane, $order)
{
  if (!empty($form_state["values"]["checkout_support"]["region"])) {

    // сохранить введённый регион
    $order_wr = entity_metadata_wrapper('commerce_order', $order);
    $order_wr->field_support_region = $form_state["values"]["checkout_support"]["region"];
    $order_wr->save();
  }
}
