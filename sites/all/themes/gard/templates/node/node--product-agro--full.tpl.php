<?php
/**
 * @file
 * Default theme implementation to display a node.
 *
 * Available variables:
 * - $title: the (sanitized) title of the node.
 * - $content: An array of node items. Use render($content) to print them all,
 *   or print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $user_picture: The node author's picture from user-picture.tpl.php.
 * - $date: Formatted creation date. Preprocess functions can reformat it by
 *   calling format_date() with the desired parameters on the $created variable.
 * - $name: Themed username of node author output from theme_username().
 * - $node_url: Direct URL of the current node.
 * - $display_submitted: Whether submission information should be displayed.
 * - $submitted: Submission information created from $name and $date during
 *   template_preprocess_node().
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the
 *   following:
 *   - node: The current template type; for example, "theming hook".
 *   - node-[type]: The current node type. For example, if the node is a
 *     "Blog entry" it would result in "node-blog". Note that the machine
 *     name will often be in a short form of the human readable label.
 *   - node-teaser: Nodes in teaser form.
 *   - node-preview: Nodes in preview mode.
 *   The following are controlled through the node publishing options.
 *   - node-promoted: Nodes promoted to the front page.
 *   - node-sticky: Nodes ordered above other non-sticky nodes in teaser
 *     listings.
 *   - node-unpublished: Unpublished nodes visible only to administrators.
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 *
 * Other variables:
 * - $node: Full node object. Contains data that may not be safe.
 * - $type: Node type; for example, story, page, blog, etc.
 * - $comment_count: Number of comments attached to the node.
 * - $uid: User ID of the node author.
 * - $created: Time the node was published formatted in Unix timestamp.
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the node. Increments each time it's output.
 *
 * Node status variables:
 * - $view_mode: View mode; for example, "full", "teaser".
 * - $teaser: Flag for the teaser state (shortcut for $view_mode == 'teaser').
 * - $page: Flag for the full page state.
 * - $promote: Flag for front page promotion state.
 * - $sticky: Flags for sticky post setting.
 * - $status: Flag for published status.
 * - $comment: State of comment settings for the node.
 * - $readmore: Flags true if the teaser content of the node cannot hold the
 *   main body content.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * Field variables: for each field instance attached to the node a corresponding
 * variable is defined; for example, $node->body becomes $body. When needing to
 * access a field's raw values, developers/themers are strongly encouraged to
 * use these variables. Otherwise they will have to explicitly specify the
 * desired field language; for example, $node->body['en'], thus overriding any
 * language negotiation rule that was previously applied.
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 * @see template_process()
 *
 * @ingroup templates
 */
?>

<div class="product-intro row">
  <div class="col-sm-5">
    <?php print render($content['product:field_p_images']); ?>
  </div>
  <div class="col-sm-7">
    <div class="product-brief">
      <!--            --><?php //if ($content['product:commerce_price']['#items'][0]['amount'] != 0) : ?>
      <!--                --><?php //print render($content['product:commerce_price']); ?>
      <!--            --><?php //else: ?>
      <!--                <div class="field field-commerce-price">-->
      <!--                    <div class="field-label">--><?php //print t('Price'); ?><!--</div>-->
      <!--                    <div class="field-items">--><?php //print '<a id="price-request" href="#" onclick="supportAPI.openTab(0); return false;">' . t('Check price') . '</a>'; ?><!--</div>-->
      <!--                </div>-->
      <!--            --><?php //endif; ?>

      <?php print render($content['product:field_p_tare']); ?>

      <?php print render($content['product:field_p_packaging']); ?>

        <div class="field field-name-commerce-price">
          <div class="field-label"><?php print t('Price'); ?></div>
          <div class="field-items">
            <div class="price">от <? print $price_start_formatted; ?></div>
            <div class="button-notice">Цена зависит от общей стоимости заказа</div>
          </div>
        </div>

      <?php print render($content['field_product']); ?>


<!--      --><?php //if (!empty($content['field_pd_consumption_rate'])) : ?>
<!--        <div class="field field-name-field-pd-consumption-rate">-->
<!--          <div class="field-label">--><?php //print $content['field_pd_consumption_rate']['#title']; ?><!--</div>-->
<!--          <div class="field-items">-->
<!--            --><?php //print (float) $content['field_pd_consumption_rate']['#items'][0]['from']
//              . ' - ' . (float) $content['field_pd_consumption_rate']['#items'][0]['to']
//              . ' ' . $unit; ?>
<!--          </div>-->
<!--        </div>-->
<!--      --><?php //endif; ?>

<!--      --><?php //if ($content['field_pd_price_per_unit']['#items'][0]['amount'] != 0 && !empty($content['field_pd_consumption_rate']['#items'][0]['from'])) : ?>
<!--        <div class="field field-name-field-pd-price-per-unit">-->
<!--          <div class="field-label">--><?php //print t('Processing cost'); ?><!--</div>-->
<!--          <div class="field-items">-->
<!--            --><?php //print (float) $content['field_pd_price_per_unit']['#items'][0]['amount'] / 100 * $content['field_pd_consumption_rate']['#items'][0]['from']
//              . ' - ' . (float) $content['field_pd_price_per_unit']['#items'][0]['amount'] / 100 * $content['field_pd_consumption_rate']['#items'][0]['to']
//              . ' ' . t('rub') . '/' . $unit; ?>
<!--          </div>-->
<!--        </div>-->
<!--      --><?php //endif; ?>

      <?php if(!empty($formulation_full)): ?>
        <div class="field field-name-field-pd-formulation">
          <div class="field-label fleft"><?php print t('Preparative form'); ?></div>
          <div class="field-items">
            <?php print $formulation_full; ?>
          </div>
        </div>
      <?php endif; ?>

      <?php if(!empty($ingredients_arr)): ?>
        <div class="field field-name-field-pd-active-ingredients">
          <div class="field-label fleft"><?php print t('Active ingredients'); ?></div>
          <div class="field-items">
            <?php foreach($ingredients_arr as $ingr): ?>
              <div class="field-item"><?php print $ingr; ?></div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>

<div class="product-description">
  <?php print render($content['group_description']); ?>
</div>
