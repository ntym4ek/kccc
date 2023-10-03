<div class="page-wrapper">

  <div class="nav-mobile">
    <div class="logo">
      <img src="<?php print $logo; ?>" />
      <span><?php print $site_name; ?></span>
    </div>
    <div class="menu-mobile-wr">
      <?php if ($primary_nav): print $primary_nav; endif; ?>
      <?php if ($secondary_nav): print $secondary_nav; endif; ?>
    </div>
  </div>

  <div class="page">
    <?php if (empty($is_header_off)): ?>
    <header class="header">
      <div class="container">
        <div class="row middle-xs">
          <div class="col-xs-12 col-md-2">
            <div class="branding">
              <a href="<?php print $front_page ?>">
                <img src="<?php print $logo ?>" />
              </a>
            </div>
          </div>

          <div class="nav-mobile-label hide-md"><div class="label"><div class="icon"></div></div></div>

          <div class="col-xs-12 col-md-10 hide-xs show-md">
            <div class="menu-wr">
              <?php if ($primary_nav): print $primary_nav; endif; ?>
              <?php if ($secondary_nav): print $secondary_nav; endif; ?>
            </div>
          </div>
        </div>
      </div>
    </header>
    <?php endif; ?>

    <div class="main">
      <div class="container">

        <?php if ($page['highlighted'] || $is_title_as_banner): ?>
        <div class="page-highlighted">

          <?php if ($page['highlighted']): ?>
            <?php print render($page['highlighted']); ?>
          <?php endif; ?>

          <?php if ($is_title_as_banner): ?>
          <div class="page-banner">
            <div class="screen-width"<?php print (!empty($title_background) ? ' style="background-image: url(' . $title_background . ')"' : ''); ?>>
              <div class="container full-height">
                <div class="page-title">
                  <?php print render($title_prefix); ?>
                  <?php if ($title): ?><h1 class="title" id="page-title"><?php print $title; ?></h1><?php endif; ?>
                  <?php print render($title_suffix); ?>
                </div>
              </div>
            </div>
          </div>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php print $breadcrumb; ?>

        <?php if (empty($is_title_as_banner) && $title): ?>
          <div class="page-title">
            <?php print render($title_prefix); ?>
            <?php if ($title): ?><h1 class="title" id="page-title"><?php print $title; ?></h1><?php endif; ?>
            <?php print render($title_suffix); ?>
          </div>
        <?php endif; ?>

        <div class="page-content">
          <?php if (isset($tabs)): ?><?php print render($tabs); ?><?php endif; ?>
          <?php print $messages; ?>
          <?php if ($action_links): ?><ul class="action-links"><?php print render($action_links); ?></ul><?php endif; ?>

          <?php print render($page['content']); ?>
        </div>

        <div class="page-bottom">
          <?php print render($page['page_bottom']); ?>
        </div>
      </div>
    </div>

    <div class="footer">
      <div class="container">
        <div class="row middle-xs">
          <div class="col-xs-4 branding"><img class="logo" src="<?php print $logo; ?>" /></div>
          <div class="col-xs-8 rights">© <?php print date('Y', time()); ?> KCCC GROUP</div>
        </div>
      </div>
    </div>
  </div>

</div>

