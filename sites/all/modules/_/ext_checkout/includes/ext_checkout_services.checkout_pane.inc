<?php

/**
 * Checkout pane callback: returns the contents View for inclusion in the checkout form.
 */
function ext_checkout_services_pane_checkout_form($form, &$form_state, $checkout_pane, $order)
{
  $terms = taxonomy_get_tree(taxonomy_vocabulary_machine_name_load('services')->vid);
  foreach($terms as $term) {
    $services[$term->tid] = $term->name;
  }

  $checkout_form = [
    '#type' => 'container',
    '#attributes' => ['id' => ['checkout-services'], 'class' => ['checkout-services']],
  ];
  $checkout_form['title'] = [
    '#markup' => '<h2>Услуги</h2>',
  ];
  $checkout_form['services'] = [
    '#type' => 'container',
    '#attributes' => ['class' => ['services-list']],
  ];

  foreach($services as $key => $service) {
    $checkout_form['services'][$key] = [
      '#type' => 'checkbox',
      '#title' => '<div class="box"></div><div>' . $service . '</div>',
      '#ajax' => [
        'progress' => 'none',
        'callback' => 'ext_checkout_commerce_checkout_form_refresh',
        'wrapper' => 'commerce-checkout-ajax-wrapper',
      ],
    ];
  }

  return $checkout_form;
}

/**
 * Checkout pane callback: validation callback.
 */
function ext_checkout_services_pane_checkout_form_validate($form, &$form_state, $checkout_pane, $order)
{
  return true;
}

/**
 * Checkout pane callback: submit callback.
 */
function ext_checkout_services_pane_checkout_form_submit($form, &$form_state, $checkout_pane, $order)
{
  if (isset($form_state["values"]["checkout_services"])) {
    $services = [];
    foreach($form_state["values"]["checkout_services"]["services"] as $tid => $value) {
      if ($value) $services[] = $tid;
    }

    $order_wr = entity_metadata_wrapper('commerce_order', $order);
    $order_wr->field_services = $services;
    $order_wr->save();
  }
}
