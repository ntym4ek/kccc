<?php


/**
 * Checkout pane callback: returns the contents View for inclusion in the checkout form.
 */
function checkout_contact_pane_checkout_form($form, &$form_state, $checkout_pane, $order)
{
    $checkout_form = array(
        '#type' => 'container',
        '#attributes' => array('id' => array('checkout-contact'), 'class' => array('checkout-contact')),
    );

    $checkout_form['request'] = array(
        '#type' => 'fieldset',
        '#title' => t('Contact our representative<i>, in your region</i>'),
        '#attributes' => array('class' => array('cc-request')),
    );
    $checkout_form['request']['text'] = array(
        '#markup' => '<p> If necessary, adjust the list and number of drugs. Then select your region, add contacts and click "Send request". Our representative for this region will contact you. </p> ' .
                    '<p> Our representative in the region: </p>' .
                    '<ul>' .
                        '<li> will advise on any of the drugs; </li>' .
                        '<li> will offer a mutually beneficial cooperation option; </li>' .
                        '<li> will demonstrate the results of the use of our preparations in the fields; </li>' .
                        '<li> will answer any of your other questions. </li>' .
                    '</ul>',
//        '#markup' =>  '<p>При необходимости скорректируйте список и количество препаратов. Затем выберите свой регион, добавьте контакты и нажмите "Отправить заявку". С Вами свяжется наш представитель по данному региону.</p>'
//                    . '<p>Наш представитель в регионе:</p>'
//                        . '<ul>'
//                            . '<li>проконсультирует по любому из препаратов;</li>'
//                            . '<li>предложит вариант взаимовыгодного сотрудничества;</li>'
//                            . '<li>продемонстрирует результаты применения наших препаратов в полях;</li>'
//                            . '<li>ответит на любые другие Ваши вопросы.</li>'
//                        . '</ul>',
        '#prefix' => '<div class="row"><div class="col-md-7">',
        '#suffix' => '</div>',
    );
    $checkout_form['request']['forma'] = array(
        '#type' => 'container',
        '#attributes' => array('class' => array('cc-forma')),
        '#prefix' => '<div class="col-md-5">',
        '#suffix' => '</div></div>',
    );

    // регионы
    $regions = taxonomy_get_tree(29, 0, 1);
    $options = array();
    foreach($regions as $region) {
        $options[$region->tid] = $region->name;
    }
    $options = array('All' => '- ' . t('Choose region') . ' -') + $options;
    $checkout_form['request']['forma']['region'] = array(
        '#type' => 'select',
        '#options' => $options,
        '#default_value' => 'All',
    );
    $checkout_form['request']['forma']['name'] = array(
        '#type' => 'textfield',
        '#attributes' => array('placeholder' => t('Your name or organization')),
    );
    $checkout_form['request']['forma']['email'] = array(
        '#type' => 'textfield',
        '#attributes' => array('placeholder' => t('Contact e-mail')),
    );
    $checkout_form['request']['forma']['phone'] = array(
        '#type' => 'textfield',
        '#attributes' => array('placeholder' => t('Contact phone')),
    );
    $checkout_form['request']['forma']['agree'] = array(
        '#type' => 'checkbox',
        '#title' => t('I do agree for my personal data <a href="/info/privacy-policy">processing</a>'),
    );

    return $checkout_form;
}

/**
 * Checkout pane callback: validation callback.
 */
function checkout_contact_pane_checkout_form_validate($form, &$form_state, $checkout_pane, $order)
{
    $invalid = array();
    if (!empty($form_state['values'][$checkout_pane['pane_id']])) {
        $pane_values = $form_state['values'][$checkout_pane['pane_id']];

        if ($pane_values['request']['forma']['region'] == 'All')    { $invalid[$checkout_pane['pane_id'] . '][request][forma][region'] = ''; }
        if (!$pane_values['request']['forma']['name'])              { $invalid[$checkout_pane['pane_id'] . '][request][forma][name'] = ''; }
        if (!valid_email_address($pane_values['request']['forma']['email']))
                                                                    { $invalid[$checkout_pane['pane_id'] . '][request][forma][email'] = t('Unable to recognize email address'); }
        // todo проверить валидность phone
        if (!$pane_values['request']['forma']['phone'])             { $invalid[$checkout_pane['pane_id'] . '][request][forma][phone'] = ''; }
        if (!$pane_values['request']['forma']['agree'])             { $invalid[$checkout_pane['pane_id'] . '][request][forma][agree'] = ''; }

        // выделить поля и вывести сообщение об ошибке
        if ($invalid) {
            $invalid[] = t('You need to pay attention to form below to continue.');
            foreach($invalid as $field => $message)
                form_set_error($field, t($message));
            return false;
        }
    }
    return true;
}

/**
 * Checkout pane callback: submit callback.
 */
function checkout_contact_pane_checkout_form_submit($form, &$form_state, $checkout_pane, $order)
{
    if (!empty($form_state['values'][$checkout_pane['pane_id']])) {
        $pane_values = $form_state['values'][$checkout_pane['pane_id']];

        $order_wrapper = entity_metadata_wrapper('commerce_order', $order);
        $order_wrapper->mail = $pane_values['request']['forma']['email'];
        $order_wrapper->field_email = $pane_values['request']['forma']['email'];
        $order_wrapper->field_phone = $pane_values['request']['forma']['phone'];
        $order_wrapper->field_username = $pane_values['request']['forma']['name'];
        $order_wrapper->field_f_region = $pane_values['request']['forma']['region'];
        $order_wrapper->save();
    }
}