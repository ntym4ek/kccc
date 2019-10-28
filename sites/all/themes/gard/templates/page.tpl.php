<?php
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

<header id="navbar" role="banner" class="navbar hidden-print">
    <button type="button" class="btn btn-header btn-s4 hidden-md hidden-lg">
        <i class="fa fa-bars" aria-hidden="true"></i>
        <i class="fa fa-times" aria-hidden="true"></i>
        <span class="btn-mark hidden-xs"><?php print t('Menu');?></span>
    </button>

    <a class="navbar-brand" href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>">
        <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" />
    </a>

    <div class="primary-d-menu hidden-xs hidden-sm">
        <?php print render($primary_nav_d); ?>
    </div>

    <div class="right-menu">
        <?php print render($secondary_nav); ?>
        <button type="button" class="btn btn-header popup-trigger btn-s2 call_back_post">
            <i class="fa fa-phone" aria-hidden="true"></i>
            <div class="popup popup-top-center"><div class="popup-content"><? print t('Callback request');?></div></div>
        </button>
        <a href="/checkout" class="btn btn-header popup-trigger btn-s3">
            <i class="fa fa-shopping-cart" aria-hidden="true"><?php print checkout_get_cart_block(); ?></i>
            <div class="popup popup-top-right"><div class="popup-content"><? print t('Preparations wishlist');?></div></div>
        </a>
    </div>

</header>


<div class="menu-container hidden-print">
    <div class="side-menu">
<!--        --><?php //print render($secondary_nav); ?>
        <?php print render($primary_nav); ?>
        <?php print render($navigation_nav); ?>
    </div>
    <div class="side-menu language-selector">
        <ul class="menu nav navbar-nav foot">
            <li class="with-icon"><?php print $language_link; ?></li>
        </ul>
    </div>
</div>
<div class="content-container">
    <div class="content container-fluid">

        <?php if (!empty($page['highlighted'])): ?>
            <div class="row highlighted jumbotron"><?php print render($page['highlighted']); ?></div>
        <?php endif; ?>

        <?php print $messages; ?>

        <?php if (!empty($header)): ?>
            <header class="row content-header" role="banner" >
                <div class="category-header hidden-print">
                    <?php if (!empty($header['image'])):?>
                    <img src="<?php print $header['image']; ?>">
                    <? endif; ?>
                    <?php if (!empty($header['category_title'])):?>
                    <div class="category-title">
                        <h3 class="col-sm-8 col-sm-offset-2"><?php print $header['category_title']; ?></h3>
                    </div>
                    <? endif; ?>
                </div>

                <?php if (!empty($tabs)): ?>
                    <?php print render($tabs); ?>
                <?php endif; ?>

                <?php if (!empty($action_links) || !empty($page['help'])): ?>
                    <div class="col-sm-offset-1 col-sm-10 col-md-offset-2 col-md-8">
                    <?php if (!empty($action_links)): ?>
                        <?php print render($action_links); ?>
                    <?php endif; ?>

                    <?php if (!empty($page['help'])): ?>
                        <?php print render($page['help']); ?>
                    <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (!$header['title_off']): ?>
                <div class="col-sm-offset-1 col-sm-10 col-md-offset-2 col-md-8">
                    <div class="article-header">
                        <h1><?php print $header['title']; ?></h1><? if (!empty($header['title_suffix'])) print '<h3>' . $header['title_suffix'] . '</h3>'; ?>
                        <?php if (!empty($header['subtitle'])): ?>
                            <div class="header-category"><h2><?php print $header['subtitle']; ?></h2></div>
                        <? endif; ?>

                        <?php if (!empty($header['print']) || !empty($header['url'])): ?>
                            <div class="header-social hidden-print">
                                <?php if (!empty($header['print'])): ?>
                                    <button class="btn-link btn-print hidden-xs"><?php print t('Print');?> <span class="glyphicon glyphicon-print" aria-hidden="true"></span></button>
                                <? endif; ?>
                                <?php if (!empty($header['url'])): ?>
                                    <button class="btn-link btn-share"><?php print t('Share');?> <span class="glyphicon glyphicon-share" aria-hidden="true"></span></button>
                                <? endif; ?>
                            </div>
                        <? endif; ?>

                        <?php if (!empty($header['url'])): ?>
                            <div class="header-share closed">
                                <div class="social-links">
                                    <a class="vk" href="http://vkontakte.ru/share.php?url=<?php print $header['url']; ?>" target="_blank" title="вконтакте"><i class="fa fa-vk" aria-hidden="true"></i></a>
                                    <a class="fb" href="https://www.facebook.com/sharer/sharer.php?u=<?php print $header['url']; ?>" target="_blank" title="facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                                    <a class="tw" href="https://twitter.com/intent/tweet?text=<?php print $header['url']; ?>" target="_blank" title="twitter"><i class="fa fa-twitter" aria-hidden="true"></i></a>
                                    <a class="em" href="mailto:?to=&subject=Subject&body=Link:%20<?php print $header['url']; ?>%0D%0D" target="_blank" title="email"><i class="fa fa-envelope" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        <? endif; ?>
                    </div>
                </div>
                <? endif; ?>

            </header>
        <?php endif; ?>

        <div class="row content-body">
            <?php if (!$wrapper_off): ?>
            <div class="wrapper col-sm-offset-1 col-sm-10 col-md-offset-2 col-md-8">
            <? endif; ?>

            <?php print render($page['content']); ?>

            <?php if (!$wrapper_off): ?>
            </div>
            <? endif; ?>
        </div>


        <footer class="footer hidden-print">
                <div class="hidden-xs col-sm-5 col-md-4 social">
                    <div class="social-title"><?php print t('Follow us');?></div>
                    <div class="social-links">
                        <a class="vk" href="http://vk.com/public147827276" rel="nofollow" target="_blank" title="ВКонтакте"><i class="fa fa-vk" aria-hidden="true"></i></a>
                        <a class="ok" href="https://ok.ru/group/54447113371728" rel="nofollow" target="_blank" title="Одноклассники"><i class="fa fa-odnoklassniki" aria-hidden="true"></i></a>
                        <a class="fb" href="http://www.facebook.com/kccc.ru" rel="nofollow" target="_blank" title="Facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                        <a class="tw" href="https://twitter.com/kccc_ru" rel="nofollow" target="_blank" title="Twitter"><i class="fa fa-twitter" aria-hidden="true"></i></a>
                        <a class="in" href="https://www.instagram.com/td_kccc/" rel="nofollow" target="_blank" title="Instagram"><i class="fa fa-instagram" aria-hidden="true"></i></a>
                        <a class="yt" href="http://www.youtube.com/channel/UCFenAWL6Wa0iJzpVpsvn64w" rel="nofollow" target="_blank" title="YouTube"><i class="fa fa-youtube-play" aria-hidden="true"></i></a>
                    </div>
                </div>
                <div class="col-sm-7 col-md-8">
                    <div class="row">
                        <div class="col-xs-12"><?php print render($footer_nav); ?></div>
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-12 col-md-5 col-md-offset-1">
                                    <div class="contact"><?php print t('Central office') . '<br />+7 (8332) 76-15-20 доб. 1107'; ?></div>
                                </div>
                                <div class="col-xs-12 hidden-sm col-md-4 col-md-offset-2">
                                    <a class="navbar-brand" href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>">
                                        <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php print render($page['footer']); ?>
        </footer>
    </div>
</div>

<div id="modalBackdrop"></div>
