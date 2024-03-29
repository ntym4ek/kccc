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

  <a class="navbar-brand" href="<?php print $front_page; ?>" title="<? print $site_name; ?>">
    <img src="<?php print $logo; ?>" alt="<? print $site_name; ?>" />
  </a>

  <div class="primary-d-menu hidden-xs hidden-sm">
    <?php print render($primary_nav_d); ?>
  </div>

  <div class="right-menu">
      <div class="btn btn-header popup-trigger btn-s3 right-menu-links">
        <div><span class="link-header-white">Телефон</span></div>
        <div><a class="link-header-white" href="tel:<? print $reception_phone; ?>"><? print $reception_phone_formatted; ?></a></div>
      </div>
    <?php print render($secondary_nav); ?>
    <button type="button" class="btn btn-header popup-trigger btn-s2 call_back_post">
      <img src="/sites/all/themes/gard/images/icons/phone.png" alt="Позвонить <? print $site_name; ?>">
      <div class="popup popup-top-center"><div class="popup-content"><? print t('Callback request');?></div></div>
    </button>
    <a href="<?php print url('/checkout'); ?>" class="btn btn-header popup-trigger btn-s3" rel="nofollow">
      <img src="/sites/all/themes/gard/images/icons/cart.png" alt="Корзина"><?php print ext_checkout_get_cart_block(); ?></i>
      <div class="popup popup-top-right"><div class="popup-content"><? print t('Cart');?></div></div>
    </a>
  </div>
</header>


<div class="menu-container">
    <div class="side-menu">
<!--        --><?php //print render($secondary_nav); ?>
        <?php print render($primary_nav); ?>
        <?php print render($navigation_nav); ?>
    </div>
</div>
<div class="content-container">
    <div class="content container-fluid">
        <?php if (!empty($slider)): ?>
        <div class="region region-highlighted">
            <div class="row-1">
                <?php print render($slider); ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="region region-content">
            <?php if (!empty($row_1)): ?>
            <div class="row-1">
              <?php print render($row_1['block_17']); ?>
            </div>
            <?php endif; ?>
            <?php if (!empty($row_2)): ?>
            <div class="row-2">
                <?php print render($row_2['block_1']); ?>
                <?php print render($row_2['block_2']); ?>
            </div>
            <?php endif; ?>
            <?php if (!empty($row_3)): ?>
            <div class="row-3">
                <?php print render($row_3['block_1']); ?>
                <?php print render($row_3['block_2']); ?>
                <?php print render($row_3['block_3']); ?>
            </div>
            <?php endif; ?>
            <?php if (!empty($row_4)): ?>
            <div class="row-4">
                <?php print render($row_4['block_1']); ?>
            </div>
            <?php endif; ?>
        </div>

        <footer class="footer container-fluid">
            <div class="row">
                <div class="hidden-xs col-sm-4 col-lg-4 social">
                    <div class="social-title"><?php print t('Follow us');?></div>
                    <div class="social-links">
                        <a class="vk" href="https://vk.com/public147827276" rel="nofollow" target="_blank" title="ВКонтакте"><i class="fab fa-vk" aria-hidden="true"></i></a>
                        <a class="ok" href="https://ok.ru/group/54447113371728" rel="nofollow" target="_blank" title="Одноклассники"><i class="fab fa-odnoklassniki" aria-hidden="true"></i></a>
                        <a class="yt" href="https://www.youtube.com/channel/UCFenAWL6Wa0iJzpVpsvn64w" rel="nofollow" target="_blank" title="YouTube"><i class="fab fa-youtube" aria-hidden="true"></i></a>
                    </div>
                </div>
                <div class="col-sm-8 col-lg-8">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-12 col-md-6 hidden-xs"><h1><?php print $site_slogan; ?></h1></div>
                                <div class="col-xs-12 col-md-6"><?php print render($footer_nav); ?></div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-12 col-md-6 col-lg-6">
                                    <div id="footer_contact" class="contact"><?php print $site_contact; ?></div>
                                </div>
                                <div class="col-xs-12 hidden-sm col-md-4 col-md-offset-2 hidden-xs">
                                    <a class="navbar-brand" href="<?php print $front_page; ?>" title="<?php print t('Home'); ?>">
                                        <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" loading="lazy" />
                                    </a>
                                </div>
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
<script type="application/ld+json">
[ {
  "@context" : "http://schema.org",
  "@type" : "Organization",
  "name" : "ООО Торговый Дом Кирово-Чепецкая Химическая компания",
  "image" : "https://kccc.ru/sites/default/files/images/logo/logo.svg",
  "telephone" : "+7 (922) 966-64-34",
  "address" : {
    "@type" : "PostalAddress",
    "streetAddress" : "Толмачевская, д. 43/4",
    "addressLocality" : "Новосибирск",
    "postalCode" : "630052"
  }
} , {
  "@context" : "http://schema.org",
  "@type" : "Organization",
  "name" : "ООО Торговый Дом Кирово-Чепецкая Химическая компания",
  "image" : "https://kccc.ru/sites/default/files/images/logo/logo.svg",
  "telephone" : "+7 (922) 900-01-52",
  "address" : {
    "@type" : "PostalAddress",
    "streetAddress" : "Розы Люксембург, д. 180, пом. 12А",
    "addressLocality" : "Иркутск",
    "postalCode" : "664040"
  }
} , {
  "@context" : "http://schema.org",
  "@type" : "Organization",
  "name" : "ООО Торговый Дом Кирово-Чепецкая Химическая компания",
  "image" : "https://kccc.ru/sites/default/files/images/logo/logo.svg",
  "telephone" : "+7 (922) 969-47-05",
  "address" : {
    "@type" : "PostalAddress",
    "streetAddress" : "Северное шоссе, д. 17",
    "addressLocality" : "Красноярск",
    "postalCode" : "660020"
  }
} , {
  "@context" : "http://schema.org",
  "@type" : "Organization",
  "name" : "ООО Торговый Дом Кирово-Чепецкая Химическая компания",
  "image" : "https://kccc.ru/sites/default/files/images/logo/logo.svg",
  "telephone" : "+7 (922) 900-05-73",
  "address" : {
    "@type" : "PostalAddress",
    "streetAddress" : "Привокзальная, д. 21",
    "addressLocality" : "Белая Глина",
    "postalCode" : "353040"
  }
} , {
  "@context" : "http://schema.org",
  "@type" : "Organization",
  "name" : "ООО Торговый Дом Кирово-Чепецкая Химическая компания",
  "image" : "https://kccc.ru/sites/default/files/images/logo/logo.svg",
  "telephone" : "+7 (922) 922-76-22",
  "address" : {
    "@type" : "PostalAddress",
    "streetAddress" : "Московская, д. 11А, пом. 3",
    "addressLocality" : "Курск",
    "postalCode" : "305007"
  }
} , {
  "@context" : "http://schema.org",
  "@type" : "Organization",
  "name" : "ООО Торговый Дом Кирово-Чепецкая Химическая компания",
  "image" : "https://kccc.ru/sites/default/files/images/logo/logo.svg",
  "telephone" : "+7 (922) 900-74-73",
  "address" : {
    "@type" : "PostalAddress",
    "streetAddress" : "3-я Молодежная, д. 6",
    "addressLocality" : "Омск",
    "postalCode" : "644117"
  }
} , {
  "@context" : "http://schema.org",
  "@type" : "Organization",
  "name" : "ООО Торговый Дом Кирово-Чепецкая Химическая компания",
  "image" : "https://kccc.ru/sites/default/files/images/logo/logo.svg",
  "telephone" : "+7 (922) 970-60-55",
  "address" : {
    "@type" : "PostalAddress",
    "streetAddress" : "Шарлыкское шоссе, д. 4",
    "addressLocality" : "Оренбург",
    "postalCode" : "460019"
  }
} , {
  "@context" : "http://schema.org",
  "@type" : "Organization",
  "name" : "ООО Торговый Дом Кирово-Чепецкая Химическая компания",
  "image" : "https://kccc.ru/sites/default/files/images/logo/logo.svg",
  "telephone" : "+7 (929) 209-00-80",
  "address" : {
    "@type" : "PostalAddress",
    "streetAddress" : "Байдукова, д. 94",
    "addressLocality" : "Пенза",
    "postalCode" : "440015"
  }
} , {
  "@context" : "http://schema.org",
  "@type" : "Organization",
  "name" : "ООО Торговый Дом Кирово-Чепецкая Химическая компания",
  "image" : "https://kccc.ru/sites/default/files/images/logo/logo.svg",
  "telephone" : "+7 (922) 665-00-15",
  "address" : {
    "@type" : "PostalAddress",
    "streetAddress" : "Школьная, д. 6А",
    "addressLocality" : "Чесноковка",
    "postalCode" : "450591"
  }
} , {
  "@context" : "http://schema.org",
  "@type" : "Organization",
  "name" : "ООО Торговый Дом Кирово-Чепецкая Химическая компания",
  "image" : "https://kccc.ru/sites/default/files/images/logo/logo.svg",
  "telephone" : "+7 (922) 957-10-20",
  "address" : {
    "@type" : "PostalAddress",
    "streetAddress" : "Тенистая, д. 2",
    "addressLocality" : "Ленинское",
    "postalCode" : "346703"
  }
} , {
  "@context" : "http://schema.org",
  "@type" : "Organization",
  "name" : "ООО Торговый Дом Кирово-Чепецкая Химическая компания",
  "image" : "https://kccc.ru/sites/default/files/images/logo/logo.svg",
  "address" : {
    "@type" : "PostalAddress",
    "streetAddress" : "Толстого, д. 15",
    "addressLocality" : "Новоалександровск",
    "postalCode" : "356001"
  }
} , {
  "@context" : "http://schema.org",
  "@type" : "Organization",
  "name" : "ООО Торговый Дом Кирово-Чепецкая Химическая компания",
  "image" : "https://kccc.ru/sites/default/files/images/logo/logo.svg",
  "address" : {
    "@type" : "PostalAddress",
    "streetAddress" : "Свердлова, д. 1Б",
    "addressLocality" : "Ржакса",
    "postalCode" : "393520"
  }
} , {
  "@context" : "http://schema.org",
  "@type" : "Organization",
  "name" : "ООО Торговый Дом Кирово-Чепецкая Химическая компания",
  "image" : "https://kccc.ru/sites/default/files/images/logo/logo.svg",
  "address" : {
    "@type" : "PostalAddress",
    "streetAddress" : "Совхозная, д. 2Ж",
    "addressLocality" : "Новая Ляда",
    "postalCode" : "392515"
  }
} , {
  "@context" : "http://schema.org",
  "@type" : "Organization",
  "name" : "ООО Торговый Дом Кирово-Чепецкая Химическая компания",
  "image" : "https://kccc.ru/sites/default/files/images/logo/logo.svg",
  "telephone" : "+7 (922) 966-60-04",
  "address" : {
    "@type" : "PostalAddress",
    "streetAddress" : "50 лет Октября, д. 200А",
    "addressLocality" : "Тюмень",
    "postalCode" : "625048"
  }
} , {
  "@context" : "http://schema.org",
  "@type" : "Organization",
  "name" : "ООО Торговый Дом Кирово-Чепецкая Химическая компания",
  "image" : "https://kccc.ru/sites/default/files/images/logo/logo.svg",
  "telephone" : "+7 (922) 905-89-84",
  "address" : {
    "@type" : "PostalAddress",
    "streetAddress" : "Российская, д. 17",
    "addressLocality" : "Есаульский",
    "postalCode" : "456530"
  }
} , {
  "@context" : "http://schema.org",
  "@type" : "Organization",
  "name" : "ООО Торговый Дом Кирово-Чепецкая Химическая компания",
  "image" : "https://kccc.ru/sites/default/files/images/logo/logo.svg",
  "telephone" : "+7 (8332) 76-15-20",
  "address" : {
    "@type" : "PostalAddress",
    "streetAddress" : "Производственная, д. 6",
    "addressLocality" : "Кирово-Чепецк",
    "postalCode" : "613048"
  }
} , {
  "@context" : "http://schema.org",
  "@type" : "Organization",
  "name" : "ООО Торговый Дом Кирово-Чепецкая Химическая компания",
  "image" : "https://kccc.ru/sites/default/files/images/logo/logo.svg",
  "telephone" : "+7 (929) 209-92-21",
  "address" : {
    "@type" : "PostalAddress",
    "streetAddress" : "Индустриальная, д. 46",
    "addressLocality" : "Бабяково",
    "postalCode" : "396313"
  }
} ]
</script>

<?php render($page['content']['metatags']); ?>
