<?php
/**
 * Created by PhpStorm.
 * User: ntym
 * Date: 15.07.2015
 * Time: 17:00
 */

$shipping_wrapper = entity_metadata_wrapper('commerce_customer_profile', $commerce_customer_profile);

$address_id = $shipping_wrapper->field_ship_address->value();
$phone = $passport = $comment = 0;
$arr_name = $arr_addr = array();
if ($address_id) {
    $address_wrapper = entity_metadata_wrapper('node', $address_id);

    if ($address_wrapper->value()) {
        // сформировать строку ФИО

        if ($address_wrapper->field_ab_surname->value()) $arr_name[] = $address_wrapper->field_ab_surname->value();
        if ($address_wrapper->field_ab_name->value()) $arr_name[] = $address_wrapper->field_ab_name->value();
        if ($address_wrapper->field_ab_name2->value()) $arr_name[] = $address_wrapper->field_ab_name2->value();

        // сформировать строку алреса
        if ($address_wrapper->field_ab_zipcode->value()) $arr_addr[] = $address_wrapper->field_ab_zipcode->value();
        if ($address_wrapper->field_ab_region->value()) $arr_addr[] = $address_wrapper->field_ab_region->value();
        if ($address_wrapper->field_ab_area->value()) $arr_addr[] = $address_wrapper->field_ab_area->value();
        if ($address_wrapper->field_ab_city->value()) $arr_addr[] = $address_wrapper->field_ab_city->value();
        if ($address_wrapper->field_ab_street->value()) $arr_addr[] = $address_wrapper->field_ab_street->value();
        if ($address_wrapper->field_ab_house->value()) $arr_addr[] = $address_wrapper->field_ab_house->value()
            . ($address_wrapper->field_ab_app->value() ? ' - ' . $address_wrapper->field_ab_app->value() : '');

        if ($address_wrapper->field_ab_phone->value()) $phone = $address_wrapper->field_ab_phone->value();
        if ($address_wrapper->field_ab_passport->value()) $passport = $address_wrapper->field_ab_passport->value();
        if ($shipping_wrapper->field_ship_comment->value()) $comment = $shipping_wrapper->field_ship_comment->value();
    }
}

?>

<div class="fieldset">
    <div class="head"><?php print t('Customer'); ?></div>
    <div class="item"><?php print '<span>' . t('Name')      . ':</span> ' . implode(' ', $arr_name); ?></div>
    <div class="item"><?php print '<span>' . t('Address')   . ':</span> ' . implode(', ', $arr_addr); ?></div>
    <div class="item"><?php print '<span>' . t('Passport')  . ':</span> ' . $passport; ?></div>
    <div class="item"><?php print '<span>' . t('Comment')   . ':</span> ' . $comment; ?></div>
</div>