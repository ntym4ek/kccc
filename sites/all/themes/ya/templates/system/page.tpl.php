<?

/**
 * @file
 * Default theme implementation to display a single Drupal page.
 *
 * The doctype, html, head and body tags are not in this template. Instead they
 * can be found in the html.tpl.php template in this directory.
 *
 * Available variables:
 *
 * General utility variables:
 * - $base_path: The base URL path of the Drupal installation. At the very
 *   least, this will always default to /.
 * - $directory: The directory the template is located in, e.g. modules/system
 *   or themes/bartik.
 * - $is_front: TRUE if the current page is the front page.
 * - $logged_in: TRUE if the user is registered and signed in.
 * - $is_admin: TRUE if the user has permission to access administration pages.
 *
 * Site identity:
 * - $front_page: The URL of the front page. Use this instead of $base_path,
 *   when linking to the front page. This includes the language domain or
 *   prefix.
 * - $logo: The path to the logo image, as defined in theme configuration.
 * - $site_name: The name of the site, empty when display has been disabled
 *   in theme settings.
 * - $site_slogan: The slogan of the site, empty when display has been disabled
 *   in theme settings.
 *
 * Navigation:
 * - $main_menu (array): An array containing the Main menu links for the
 *   site, if they have been configured.
 * - $secondary_menu (array): An array containing the Secondary menu links for
 *   the site, if they have been configured.
 * - $breadcrumb: The breadcrumb trail for the current page.
 *
 * Page content (in order of occurrence in the default page.tpl.php):
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title: The page title, for use in the actual HTML content.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 * - $messages: HTML for status and error messages. Should be displayed
 *   prominently.
 * - $tabs (array): Tabs linking to any sub-pages beneath the current page
 *   (e.g., the view and edit tabs when displaying a node).
 * - $action_links (array): Actions local to the page, such as 'Add menu' on the
 *   menu administration interface.
 * - $feed_icons: A string of all feed icons for the current page.
 * - $node: The node object, if there is an automatically-loaded node
 *   associated with the page, and the node ID is the second argument
 *   in the page's path (e.g. node/12345 and node/12345/revisions, but not
 *   comment/reply/12345).
 *
 * Regions:
 * - $page['help']: Dynamic help text, mostly for admin pages.
 * - $page['highlighted']: Items for the highlighted content region.
 * - $page['content']: The main content of the current page.
 * - $page['sidebar_first']: Items for the first sidebar.
 * - $page['sidebar_second']: Items for the second sidebar.
 * - $page['header']: Items for the header region.
 * - $page['footer']: Items for the footer region.
 *
 * @see bootstrap_preprocess_page()
 * @see template_preprocess()
 * @see template_preprocess_page()
 * @see bootstrap_process_page()
 * @see template_process()
 * @see html.tpl.php
 *
 * @ingroup templates
 */
?>
<div class="header">
  <div class="background" style="background-image: url(/sites/default/files/images/tmp_front/startpage.jpg);">
    <div class="header-gradient"></div>
  </div>

  <div class="header-logo">
    <a class="logo" href="<? print $front_page; ?>" title="<? print t('Home'); ?>">
      <img src="<? print $logo; ?>" alt="<? print t('Home'); ?>" />
    </a>
    <? if ($is_front): ?>
    <h1 class="site-slogan"><? print $site_slogan;?></h1>
    <? else:?>
    <div class="site-slogan"><? print $site_slogan;?></div>
    <? endif;?>
  </div>

  <div id="navbar" role="banner" class="<? print $navbar_classes; ?>">
    <div class="<? print $container_class; ?>">
      <? if (!empty($primary_nav_d) || !empty($secondary_nav) || !empty($page['navigation'])): ?>
        <div class="menu-line first" role="navigation">
          <? print render($top_nav); ?>

          <? print render($secondary_nav); ?>
        </div>
        <div class="menu-line second" role="navigation">
          <? print render($primary_nav_d); ?>
        </div>
      <? endif; ?>
    </div>
  </div>

  <? if (!empty($page['header'])): ?>
    <div class="header-spot" role="banner">
      <? print render($page['header']); ?>
    </div>
  <? endif; ?>

  <? if (!$is_front && !empty($header["category_title"])): ?>
    <div class="<? print $container_class; ?>">
      <div class="header-title">
        <div class="title-block">
          <div class="category-title"><? print $header["category_title"]; ?></div>
        </div>
      </div>
    </div>
  <? endif; ?>


  <? if (!empty($tabs) && !$is_front): ?>
  <div class="admin-tabs">
    <div class="container">
      <? print render($tabs); ?>
    </div>
  </div>
  <? endif; ?>
</div>

<div class="main">
  <div class="container">


    <? print $messages; ?>

    <? if (!empty($page['banner_top'])): ?>
    <div class="row row-screen-wide">
      <div class="banner-top col-screen-wide" role="banner"><? print render($page['banner_top']); ?></div>
    </div>
    <? endif; ?>

    <div class="row<? print $wide_content ? ' row-screen-wide' : ''; ?>">

      <? if (!empty($page['sidebar_first'])): ?>
        <aside class="col-sm-4" role="complementary">
          <? print render($page['sidebar_first']); ?>
        </aside>
      <? endif; ?>

      <section class="<? print $wide_content ? ' col-screen-wide' : $content_column_class; ?> content">
  <div class="row">
<!--          --><? //if (!empty($breadcrumb) && !$is_front): ?>
<!--            --><? //print $breadcrumb; ?>
<!--          --><? //endif;?>

        <? if (!$header['title_off']): ?>
        <div class="page-header">
          <? if (!empty($header['title'])): ?>
          <h1 class="page-title"><? print $header['title']; ?></h1>
        <? endif; ?>
        <? if (!empty($header['subtitle'])): ?>
          <h2 class="page-subtitle"><? print $header['subtitle']; ?></h2>
        <? endif; ?>
        </div>
        <? endif; ?>


        <? if (!empty($page['help'])): ?>
          <? print render($page['help']); ?>
        <? endif; ?>

        <? if (!empty($action_links)): ?>
          <ul class="action-links"><? print render($action_links); ?></ul>
        <? endif; ?>

        <? print render($page['content']); ?>
  </div>
      </section>

      <? if (!empty($page['sidebar_second'])): ?>
        <aside class="col-sm-4" role="complementary">
          <? print render($page['sidebar_second']); ?>
        </aside>
      <? endif; ?>

    </div>

    <? if (!empty($page['banner_bottom'])): ?>
    <div class="row row-screen-wide">
      <div class="banner-bottom col-screen-wide"><? print render($page['banner_bottom']); ?></div>
    </div>
    <? endif; ?>

  </div>
</div>

<? if (!empty($page['footer'])): ?>
  <footer class="footer">
    <div class="container">
      <div class="row">

        <div class="col-sm-12">
          <div class="col-md-4 social">
            <h3><? print t('Follow us'); ?></h3>
            <div class="social-links">
              <a class="vk" href="http://vk.com/public147827276" rel="nofollow" target="_blank" title="ВКонтакте"><i class="fab fa-vk"></i></a>
              <a class="ok" href="https://ok.ru/group/54447113371728" rel="nofollow" target="_blank" title="Одноклассники"><i class="fab fa-odnoklassniki"></i></a>
              <a class="fb" href="http://www.facebook.com/kccc.ru" rel="nofollow" target="_blank" title="Facebook"><i class="fab fa-facebook"></i></a>
              <a class="tw" href="https://twitter.com/kccc_ru" rel="nofollow" target="_blank" title="Twitter"><i class="fab fa-twitter"></i></a>
              <a class="in" href="https://www.instagram.com/td_kccc/" rel="nofollow" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
              <a class="yt" href="http://www.youtube.com/channel/UCFenAWL6Wa0iJzpVpsvn64w" rel="nofollow" target="_blank" title="YouTube"><i class="fab fa-youtube"></i></a>
            </div>
          </div>
          <div class="col-md-3"><h3><a href="/"><? print t('Ask a question'); ?></a></h3></div>
          <div class="col-md-3">
            <h3><? print t('Contacts'); ?></h3>
            <div class="contact"><? print t('Central office') . '<br />+7 (8332) 76-15-20 ' . t('add.') . ' 1107'; ?></div>
          </div>
          <div class="col-md-2 brand">
            <a href="<? print $front_page; ?>" title="<? print t('Home'); ?>">
              <img src="<? print $logo; ?>" alt="<? print t('KCCC GROUP logo'); ?>" />
            </a>
          </div>
        </div>


        <? print render($page['footer']); ?>
        </div>
      </div>
    </div>
  </footer>
<? endif; ?>

<div id="modalBackdrop"></div>
