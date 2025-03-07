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
 * @ingroup themeable
 */
?>
<div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?>"<?php print $attributes; ?>>

  <?php if ($title): ?>
  <div class="node-title">
    <?php print $title; ?>
  </div>
  <?php endif; ?>

  <?php if (!empty($date) || !empty($stats)): ?>
  <div class="node-info">
    <div class="date"><?php print $date; ?></div>
    <?php if (isset($stats['totalcount'])): ?>
    <div class="stats">
      <div class="media"><i class="icon icon-099"></i></div>
      <div class="text"><?php print $stats['totalcount']; ?></div>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <?php if ($display_submitted): ?>
    <div class="author">
      <?php print $author; ?>
    </div>
  <?php endif; ?>

  <?php if (count($images)): ?>
  <div class="node-images">
    <?php hide($content['field_image']); ?>
    <?php hide($content['field_images']); ?>
    <?php if (count($images) > 1): ?>
    <div id="slider-images" class="slider slider-images outer-pagination" data-slidesperview-xs="1" data-autoheight="true">
      <div class="swiper">
        <div class="swiper-wrapper">
          <?php foreach ($images as $key => $image) {
              print '<div class="swiper-slide">'  .
                      '<div class="image">' .
                        drupal_render($image) .
                      '</div>' .
                      '<div class="image-note"><span>' . t('Photo') . ' ' . ++$key . ' ' . t('of', [], ['context' => '1 of 8']) . ' ' . count($images) . '.</span> ' . $image['#item']['title'] . '</div>' .
                    '</div>';
          } ?>
        </div>
      </div>
      <div class="swiper-pagination"></div>
      <div class="swiper-button-prev hide show-md"></div>
      <div class="swiper-button-next hide show-md"></div>
    </div>
    <?php else: ?>
    <div class="image">
      <?php print drupal_render($images[0]); ?>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <?php if (!empty($body[0]["safe_summary"])): ?>
  <?php hide($content['body']); ?>
  <div class="node-summary">
    <?php print $body[0]["safe_summary"]; ?>
  </div>
  <?php endif; ?>

  <?php if (!empty($body[0]["safe_value"])): ?>
  <?php hide($content['body']); ?>
  <div class="node-text">
    <?php print $body[0]["safe_value"]; ?>
  </div>
  <?php endif; ?>

  <?php if (!empty($content['field_files'])): ?>
  <div class="files">
    <?php print drupal_render($content['field_files']); ?>
  </div>
  <?php endif; ?>

  <?php print drupal_render($content); ?>

</div>
