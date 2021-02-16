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
        '#markup' => t('<p> If necessary, adjust the list and number of preparations. Then select your region, add contacts and click "Send request". Our representative for this region will contact you. </p> ' .
                    '<p> Our representative in the region: </p>' .
                    '<ul>' .
                        '<li> will advise on any of the preparations; </li>' .
                        '<li> will offer a mutually beneficial cooperation option; </li>' .
                        '<li> will demonstrate the results of the use of our preparations at the fields; </li>' .
                        '<li> will answer any of your other questions. </li>' .
                    '</ul>'),
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
        '#type' => 'fieldset',
        '#attributes' => array('class' => ['cc-forma', 'panel-shadow']),
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
        '#ajax' => array(
            'callback' => 'checkout_contact_pane_checkout_form_ajax_refresh',
            'wrapper' => 'checkout-contact',
            'effect' => 'fade',
            'method' => 'replace',
        ),
    );
    if (!empty($form_state['values']['checkout_contact']['request']['forma']['region'])) {
        $options_rep = [];
        if ($sales = representatives_get_representatives($form_state['values']['checkout_contact']['request']['forma']['region'])) {
            foreach ($sales['reps'] as $rep) {
                if (!empty($rep['emails'][0])) {
                    $options_rep[$rep['emails'][0]] = $rep['surname'] . ' ' . $rep['name'] . ' ' . $rep['name2'];
                }
            }
            if ($options_rep) {
                $checkout_form['request']['forma']['rep'] = array(
                    '#type' => 'select',
                    '#title' => t('Representative'),
                    '#options' => $options_rep,
                );
            }
        }
    }
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
        '#markup' => '<div class="agree">' . t('Submitting this form you agree with <a href="/info/privacy-policy">personal data processing</a>') . '</div>',
    );

    return $checkout_form;
}

function checkout_contact_pane_checkout_form_ajax_refresh($form, $form_values)
{
    return $form['checkout_contact'];
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

        if (!valid_email_address($pane_values['request']['forma']['email'])) {
            $invalid[$checkout_pane['pane_id'] . '][request][forma][email'] = t('Unable to recognize email address');
        }

        if (drupal_strlen(preg_replace('/[^\d]/', '', $pane_values['request']['forma']['phone'])) < 10) {
            $invalid[$checkout_pane['pane_id'] . '][request][forma][phone'] = t('Provided phone number is not correct');
        }

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
        $order_wrapper->language('ru')->field_email = $pane_values['request']['forma']['email'];
        $order_wrapper->language('ru')->field_phone = $pane_values['request']['forma']['phone'];
        $order_wrapper->field_username = $pane_values['request']['forma']['name'];
        $order_wrapper->field_f_region = $pane_values['request']['forma']['region'];
        $order_wrapper->field_representative_mail = empty($pane_values['request']['forma']['rep']) ? '' : $pane_values['request']['forma']['rep'];
        $order_wrapper->save();
    }
}
