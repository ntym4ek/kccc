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

$category = empty($content['field_news_category'][0]['#markup']) ? '' : t('Category', array(), array('context' => 'Блоки на главной странице')) . ' "' . $content['field_news_category'][0]['#markup'] . '"';

$image = isset($content['field_promo_image'][0]['#item']) ? image_style_url('479_319', $content['field_promo_image'][0]['#item']['uri']) : '';
$image = (!$image && $content['field_image_gallery'][0]['#item']) ? image_style_url('479_319', $content['field_image_gallery'][0]['#item']['uri']) : '';
$image = !$image ? image_style_url('479_319', 'public://default_images/no_image.jpg') : $image;

$image_mobile = isset($content['field_promo_image'][0]['#item']) ? image_style_url('news_teaser', $content['field_promo_image'][0]['#item']['uri']) : '';
$image_mobile = (!$image_mobile && $content['field_image_gallery'][0]['#item']) ? image_style_url('news_teaser', $content['field_image_gallery'][0]['#item']['uri']) : '';
$image_mobile = !$image_mobile ? image_style_url('news_teaser', 'public://default_images/no_image.jpg') : $image_mobile;

$node_url = url('node/' . $node->nid);
?>

<div class="block-content no-gutters">
    <div class="block-image image-valign-center">
        <a href="<?php print $node_url; ?>">
          <picture>
            <source srcset="<?php print $image_mobile; ?>" media="(max-width: 767px)">
            <img src="<?php print $image; ?>" alt="<?php print $title; ?>" loading="lazy">
          </picture>
        </a>
    </div>
    <div class="block-body">
        <a href="<?php print $node_url; ?>"><h2><?php print $title; ?></h2></a>
        <p><?php print $category; ?></p>
        <a class="btn btn-danger" href="<?php print $node_url; ?>">
          <?php print t('More'); ?>
        </a>
    </div>
</div>

