<?php

/**
 * @file
 * Default simple view template to all the fields as a row.
 *
 * - $view: The view in use.
 * - $fields: an array of $field objects. Each one contains:
 *   - $field->content: The output of the field.
 *   - $field->raw: The raw data for the field, if it exists. This is NOT output safe.
 *   - $field->class: The safe class id to use.
 *   - $field->handler: The Views field handler object controlling this field. Do not use
 *     var_export to dump this object, as it can't handle the recursion.
 *   - $field->inline: Whether or not the field should be inline.
 *   - $field->inline_html: either div or span based on the above flag.
 *   - $field->wrapper_prefix: A complete wrapper containing the inline_html to use.
 *   - $field->wrapper_suffix: The closing tag for the wrapper.
 *   - $field->separator: an optional separator that may appear before a field.
 *   - $field->label: The wrap label text to use.
 *   - $field->label_html: The full HTML of the label to use including
 *     configured element type.
 * - $row: The raw result object from the query, with all data it fetched.
 *
 * @ingroup views_templates
 */

// дополнительная информация о line_item
$line_item_info = ext_checkout_get_line_item_info($row->_field_data['commerce_line_item_field_data_commerce_line_items_line_item_']['entity']);
$product_info = isset($line_item_info['product_info']['items'][$line_item_info['product_info']['nid']]) ? $line_item_info['product_info']['items'][$line_item_info['product_info']['nid']] : $line_item_info['product_info'];
$category = array_shift($product_info['categories']);
?>

<div class="ccf-item<?php print ($product_info['stock'] ? '' : ' out-of-stock'); ?>"  data-id="<?php print $product_info['id']; ?>" data-price="<?php print $line_item_info['price']; ?>" data-quantity="<?php print $line_item_info['qty']; ?>">
  <div class="ccf-content">
    <div class="ccf-image">
      <img src="<? print $product_info["photo_medium"]; ?>" alt="<? print $product_info["title"]?>">
    </div>
    <div class="ccf-info">
      <div class="ccf-title"><span class="category"><? print $category['name']; ?></span><span class="point"></span><a href="<? print $product_info['url']; ?>" target="_blank"><? print $product_info['title']; ?></a></div>
      <div class="ccf-descr">
        <div class="ccf-tare">
          <span>Упаковка</span><span><? print $product_info["tare"]; ?></span>
        </div>
        <div class="ccf-stock">
          <span>Наличие</span><span><?php print ($product_info['stock'] ? 'Готово к отгрузке' : 'В производстве'); ?></span>
        </div>
      </div>
    </div>
  </div>

  <div class="ccf-footer">
    <div class="ccf-qty">
      <?php print $fields['edit_quantity']->wrapper_prefix . $fields['edit_quantity']->label_html . $fields['edit_quantity']->content . $fields['edit_quantity']->wrapper_suffix; ?>
<!--      --><?php //print $fields['commerce_unit_price']->wrapper_prefix . $fields['commerce_unit_price']->label_html . $fields['commerce_unit_price']->content . $fields['commerce_unit_price']->wrapper_suffix; ?>
    </div>
    <div class="ccf-action">
      <?php print $fields['edit_delete']->wrapper_prefix . $fields['edit_delete']->label_html . $fields['edit_delete']->content . $fields['edit_delete']->wrapper_suffix; ?>
    </div>
    <div class="ccf-total">
      <?php print str_replace(',00', '', $fields['commerce_total']->content); ?>
    </div>
  </div>

</div>
