<?php
/**
 * @file
 * Theme implementation to display single product.
 *
 * Available variables:
 * - $product: custom product object. Contain model, url, price, category,
 *               image, title, description properties
 * - $currency:
 */

 ?>
  <offer id="<?php echo $product->model ?>" type="vendor.model" available="true">
      <url><?php echo $product->url ?></url>
      <vendor> <? echo $product->vendor; ?></vendor>
      <model><?php echo $product->title ?></model>
      <picture><?php echo $product->picture ?></picture>
      <price><?php echo $product->price ?></price>
      <currencyId><?php echo $currency; ?></currencyId>
      <categoryId><?php echo $product->category ?></categoryId>
      <delivery><?php echo variable_get('yml_export_delivery', 'true'); ?></delivery>
      <description><?php echo $product->description; ?></description>
  </offer>
