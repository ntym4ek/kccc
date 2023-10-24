<?php ?>

<div class="page-wrapper">

  <div class="nav-mobile">
    <div class="logo">
      <img src="<?php print $logo; ?>" />
    </div>
    <?php if (isset($search_form) && $is_mobile): ?>
      <div class="search hide-lg">
        <?php print render($search_form); ?>
      </div>
    <?php endif; ?>
    <div class="menu-mobile-wr">
      <?php if ($primary_nav): print $primary_nav; endif; ?>
      <?php if ($secondary_nav): print $secondary_nav; endif; ?>
    </div>
  </div>

  <div class="page">
    <?php if (empty($is_header_off)): ?>
    <header class="header">
      <div class="header-wr">
        <div class="container">
          <div class="row middle-xs full-height no-wrap">
            <div class="col col-1 full-height col-no-gutter">
              <div class="branding">
                <div class="brand">
                  <div class="ru"><a href="/">Кирово-Чепецкая Химическая&nbsp;Компания</a></div>
                  <div class="en"><a href="/">Kirovo-Chepetsk Chemical Company</a></div>
                </div>
                <div class="logo"><a href="/"><img src="/sites/default/files/images/logo/logo_t.png" alt="Кирово-Чепецкая Химическая&nbsp;Компания" /></a></div>
              </div>
            </div>
            <div class="col col-2 full-height col-no-gutter">
              <div class="menu-bkg">
                <div class="nav-mobile-label hide-lg"><div class="label"><div class="icon"></div></div></div>
                <div class="hide-xs show-lg">
                  <div class="menu-wr">
                    <div class="global">
                      <a href="https://kccc.group" target="_blank" title="KCCC GROUP">
                        <i class="icon icon-119"></i>
                      </a>
                    </div>
                    <div class="primary-menu">
                      <?php if ($primary_nav): print $primary_nav; endif; ?>
                    </div>
                    <div class="secondary-menu">
                      <?php if ($secondary_nav): print $secondary_nav; endif; ?>
                    </div>
                  </div>
                </div>
              </div>
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

          <?php if (isset($search_form) && !$is_mobile): ?>
            <div class="search hide show-lg">
              <?php print drupal_render($search_form); ?>
            </div>
          <?php endif; ?>

          <?php if ($page['highlighted']): ?>
            <?php print render($page['highlighted']); ?>
          <?php endif; ?>

          <?php if ($is_title_as_banner): ?>
          <div class="page-banner">
            <div class="screen-width"<?php print (!empty($title_background) ? ' style="background-image: url(' . $title_background . ');"' : ''); ?>>
              <div class="container full-height">
                <div id="page-title" class="page-title">
                  <?php print render($title_prefix); ?>
                  <?php if ($title): ?><h1 class="title"><?php print $title; ?></h1><?php endif; ?>
                  <?php print render($title_suffix); ?>
                </div>
              </div>
            </div>
          </div>

          <?php if ($page['header']): ?>
          <div class="page-context-menu">
            <div class="screen-width">
              <div class="container">
                <?php print render($page['header']); ?>
              </div>
            </div>
          </div>
          <?php endif; ?>
          <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="page-margin"></div>
        <?php endif; ?>

        <?php print $breadcrumb; ?>

        <?php
          $ls = !empty($page['sidebar_first']);
          $rs = !empty($page['sidebar_second']);
        ?>
        <?php if ($ls || $rs): ?>
        <div class="row">
        <?php endif; ?>

        <?php if ($ls): ?>
          <div class="col-xs-12 col-lg-3">
            <div class="page-left">
              <?php print render($page['sidebar_first']); ?>
            </div>
          </div>
        <?php endif; ?>

          <?php if ($ls || $rs): ?>
          <div class="col-xs-12 col-lg-9">
          <?php endif; ?>

            <div class="page-content">
              <?php if (isset($tabs)): ?><?php print render($tabs); ?><?php endif; ?>
              <?php print $messages; ?>
              <?php if ($action_links): ?><ul class="action-links"><?php print render($action_links); ?></ul><?php endif; ?>

              <?php if (empty($is_title_as_banner) && $title): ?>
                <div class="page-title">
                  <?php print render($title_prefix); ?>
                  <?php if ($title): ?><h1 class="title" id="page-title"><?php print $title; ?></h1><?php endif; ?>
                  <?php print render($title_suffix); ?>
                </div>
              <?php endif; ?>

              <?php print render($page['content']); ?>
            </div>

          <?php if ($ls || $rs): ?>
          </div>
          <?php endif; ?>

          <?php if ($rs): ?>
          <div class="col-xs-12 col-lg-3">
            <div class="page-right">
              <?php print render($page['sidebar_second']); ?>
            </div>
          </div>
          <?php endif; ?>

        <?php if ($ls || $rs): ?>
        </div>
        <?php endif; ?>

        <div class="page-bottom">
          <?php print render($page['page_bottom']); ?>
        </div>
      </div>
    </div>

    <div class="footer">
      <div class="container">
        <div class="row">
          <div class="col-xs-12 col-md-4 col-lg-3">
            <div class="b">
              <div class="branding">
                <div class="logo"><a href="/"><img src="<?php print $logo; ?>" alt="" /></a></div>
                <div class="brand">
                  <div class="ru"><a href="/">Кирово-Чепецкая Химическая&nbsp;Компания</a></div>
                  <div class="en"><a href="/">Kirovo-Chepetsk Chemical Company</a></div>
                </div>
              </div>
              <div class="legal-name">
                ООО&nbsp;Торговый&nbsp;Дом «Кирово-Чепецкая Химическая&nbsp;Компания»
              </div>
            </div>
          </div>

          <div class="col-xs-12 col-md-8 col-lg-6">
            <div class="row">
              <div class="col-xs-12 col-md-6">
                <div class="menu about">
                  <div class="title">О компании</div>
                  <ul>
                    <li><a href="/o-kompanii">Общая информация</a></li>
                    <li><a href="/otzyvy">Отзывы</a></li>
                  </ul>
                </div>
              </div>
              <div class="col-xs-12 col-md-6">
                <div class="menu contacts">
                  <div class="title">Контакты</div>
                  <ul>
                    <li><a href="/kontakty">Центральный офис</a></li>
                    <li><a href="/predstaviteli">Региональные представители</a></li>
                    <li><a href="/filialy">Как нас найти</a></li>
                    <li class="socials">
                      <a href="https://vk.com/public147827276" rel="nofollow" target="_blank" title="ВКонтакте"><i class="icon icon-rounded icon-068 hover-raise"></i></a>
                      <a href="https://ok.ru/group/54447113371728" rel="nofollow" target="_blank" title="Одноклассники"><i class="icon icon-rounded icon-090 hover-raise"></i></a>
                      <a href="https://youtube.com/@kccc_td" rel="nofollow" target="_blank" title="YouTube"><i class="icon icon-rounded icon-069 hover-raise"></i></a>
                      <a href="https://dzen.ru/td_kccc" rel="nofollow" target="_blank" title="Дзен"><i class="icon icon-rounded icon-070 hover-raise"></i></a>
                      <a href="https://t.me/tdkccc" rel="nofollow" target="_blank" title="Telegram"><i class="icon icon-rounded icon-091 hover-raise"></i></a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xs-12 col-md-6 hide-md show-lg col-md-offset-3 col-lg-3 col-lg-offset-0">
            <div class="subscribe">
              <div class="title">Подпишитесь на&nbsp;нашу&nbsp;рассылку</div>
              <p>Новинки, скидки, предложения!</p>
              <?php print render($subscribe_form); ?>
            </div>
          </div>

        </div>
      </div>
  </div>

</div>

