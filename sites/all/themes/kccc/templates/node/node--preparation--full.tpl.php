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

  <?php if (!$section): ?>
  <div class="product-intro">
    <div class="row">
      <div class="col-xs-12 col-md-4 col-lg-6">
        <div class="top show-xs hide-md">
          <div class="node-title"><?php print $product_info['label']; ?></div>
          <div class="summary"><?php print $product_info['summary']; ?></div>
        </div>
        <div class="image">
          <a href="<?php print $product_info['image']['full']; ?>" class="fancybox"><img src="<?php print $product_info['image']['full']; ?>" alt="<?php print $product_info['title']; ?>"></a>
        </div>
      </div>
      <div class="col-xs-12 col-md-8 col-lg-6">
        <div class="product-info">
          <div class="top hide-xs show-md">
            <div class="node-title"><?php print $product_info['label']; ?></div>
            <div class="summary"><?php print $product_info['summary']; ?></div>
          </div>
          <div class="bottom">
            <div class="specification">
              <?php if ($product_info['components']['formatted']): ?>
              <div class="components">
                <div class="media category-clr">
                  <i class="icon icon-052"></i>
                </div>
                <div><?php print $product_info['components']['formatted']; ?></div>
              </div>
              <?php endif; ?>
              <div class="formulation">
                <div class="media category-clr">
                  <i class="icon icon-053"></i>
                </div>
                <div><?php print $product_info['formulation']['full']; ?></div>
              </div>
            </div>
            <div class="actions">
              <a href="<?php print url('predstaviteli'); ?>" class="btn btn-brand btn-large btn-wide"><?php print t('Buy'); ?></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php if ($product_info['advantages']): ?>
  <div class="product-advantages">
    <div class="section-title">
      <div><?php print t('Main advantages'); ?></div>
      <div class="underline "></div>
    </div>

    <div class="row center-xs">
    <?php foreach($product_info['advantages'] as $adv): ?>
      <div class="col-xs-12 col-md-4">
        <div class="advantage">
          <div class="media"><i class="icon icon-<?php print $adv['icon_num']; ?>"></i></div>
          <div class="underline"></div>
          <div class="text"><?php print $adv['text']; ?></div>
        </div>
      </div>
    <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <div class="product-specs">
    <div class="screen-width">
      <div class="section-title invert category-bkg">
        <div><?php print t('Description', [], ['context' => 'product']); ?></div>
        <div class="underline"></div>
      </div>

      <div class="container">
        <div class="specs">
        <?php foreach($product_info['specs'] as $spec): ?>
          <div class="spec">
            <div class="media category-clr">
              <i class="icon icon-<?php print $spec['icon_num']; ?>"></i>
            </div>
            <div class="text"><?php print $spec['text']; ?></div>
          </div>
        <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <?php if (isset($product_info['reglaments_cards'])): ?>
  <div class="product-reglaments">
    <div class="section-title category-clr">
      <div><?php print t('Application regulations'); ?></div>
      <div class="underline category-bkg"></div>
    </div>

    <?php if (count($product_info['reglaments_cards']) > 1): ?>
    <div id="carousel-reglaments" class="carousel carousel-reglaments outer-pagination outer-navigation" data-autoheight="true" data-slidesperview-xs="1" data-slidesperview-md="1.5" data-slidesperview-lg="2">
      <div class="swiper">
        <div class="swiper-wrapper">

          <?php foreach($product_info['reglaments_cards'] as $card): ?>
          <div class="swiper-slide">
            <?php print $card; ?>
          </div>
          <?php endforeach; ?>

        </div>
      </div>
      <div class="swiper-pagination"></div>
      <div class="swiper-button-prev hide show-lg"></div>
      <div class="swiper-button-next hide show-lg"></div>
    </div>
    <?php else: ?>
    <div class="row center-xs">
      <?php foreach($product_info['reglaments_cards'] as $card): ?>
      <div class="col-xs-12 col-md-8">
        <?php print $card?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <?php if (isset($product_info['reglaments_table'])): ?>
  <div class="product-reglaments-table hide-xs show-lg">
    <?php print $product_info['reglaments_table']; ?>
  </div>
  <?php endif; ?>


    <!-- блок Калькулятор -->
  <?php print drupal_render($block_calc); ?>

  <?php if ($product_info['how_it_works_banner'] || $product_info['how_it_works']): ?>
  <div class="product-how-it-works">
    <div class="screen-width">
      <div class="section-title invert">
        <div><?php print t('How it works'); ?></div>
        <div class="underline"></div>
      </div>
    </div>

    <div class="row center-xs">
      <?php if ($product_info['how_it_works_banner']): ?>
      <div class="col-xs-12">
        <div class="banner">
          <img src="<?php print $product_info['how_it_works_banner']; ?>" alt="Механизм действия <?php print $product_info['title']; ?>">
        </div>
      </div>
      <?php endif; ?>
      <?php if ($product_info['how_it_works']): ?>
      <?php foreach($product_info['how_it_works'] as $how): ?>
        <div class="col-xs-12 col-md-4">
          <div class="how-it-works">
<!--            <div class="media category-clr"><i class="icon icon---><?php //print $how['icon_num']; ?><!--"></i></div>-->
            <div class="text"><?php print $how['text']; ?></div>
          </div>
        </div>
      <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>

  <div class="product-instructions">
    <div class="screen-width">
      <div class="section-title invert category-bkg">
        <div><?php print t('Application features'); ?></div>
        <div class="underline"></div>
      </div>
    </div>

    <div class="instructions">
      <?php foreach($product_info['instructions'] as $instr): ?>
      <div class="instruction">
        <div class="media category-clr"><i class="icon icon-<?php print $instr['icon_num']; ?>"></i></div>
        <div class="text">
          <div class="title category-clr">
            <?php print $instr['title']; ?>
          </div>
          <p><?php print $instr['text']; ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- блок Связаться с нами -->
  <?php print drupal_render($block_contact_us); ?>

  <!-- блок Рекомендуемые -->
  <?php print drupal_render($block_recommended); ?>

  <!-- блок Приложение -->
  <?php print drupal_render($block_app); ?>

  <?php elseif ($section == 'primenenie'): ?>
    <!-- блок Опыт применения -->
    <?php print drupal_render($block_prep_pvp); ?>

    <!-- блок Связаться с нами -->
    <?php print drupal_render($block_contact_us); ?>

    <!-- блок Рекомендуемые -->
    <?php print drupal_render($block_recommended); ?>

  <?php elseif ($section == 'media'): ?>

    <?php if (empty($media_video_block) && empty($media_blog_block)): ?>
      <div class="message">
        <?php print t('There are no associated media materials for this product.'); ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($media_video_block)): ?>
      <div class="media">
        <div class="section-title">
          <div><?php print t('Latest video'); ?></div>
          <div class="underline"></div>
          <div class="more-all"><?php print $media_video_more_link; ?></div>
        </div>

        <?php print $media_video_block; ?>
      </div>
    <?php endif; ?>


    <?php if (!empty($media_blog_block)): ?>
      <div class="media">
        <div class="section-title">
          <div><?php print t('Latest blog records'); ?></div>
          <div class="underline"></div>
          <div class="more-all"><?php print $media_blog_more_link; ?></div>
        </div>

        <?php print $media_blog_block; ?>
      </div>
    <?php endif; ?>

  <?php elseif ($section == 'faily'): ?>

    <?php if (empty($content['field_files'])): ?>
      <div class="message">
        <?php print t('There are no files for this product.'); ?>
      </div>
    <?php else: ?>
      <div class="files">
        <div class="section-title">
          <div><?php print t('Files for download'); ?></div>
          <div class="underline"></div>
        </div>

        <?php print drupal_render($content['field_files']); ?>
      </div>
    <?php endif; ?>

  <?php endif; ?>

</div>
