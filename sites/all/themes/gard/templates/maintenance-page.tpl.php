<?php

/**
 * @file
 * Default theme implementation to display a single Drupal page while offline.
 *
 * All the available variables are mirrored in html.tpl.php and page.tpl.php.
 * Some may be blank but they are provided for consistency.
 *
 * @see template_preprocess()
 * @see template_preprocess_maintenance_page()
 *
 * @ingroup themeable
 */
?><!DOCTYPE html>
<html<?php print $html_attributes;?><?php print $rdf_namespaces;?>>
<head>
  <link rel="profile" href="<?php print $grddl_profile; ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php print $head; ?>
  <title><?php print $head_title; ?></title>
  <?php print $styles; ?>
  <style>
    html, body { height: 100%; }
    body {
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .maintenance {
      display: flex;
      width: 1000px;
      margin-top: -60px;
    }
    .text {
      flex: 1 0 500px;
      padding: 40px 30px;
    }
    .logo {
      flex: 0 1 350px;
      text-align: center;
    }
    .maintenance img { width: 200px; }
    @media(max-width: 1023px) {
      .maintenance { flex-wrap: wrap; }
      .logo { flex: auto; }
    }
  </style>
  <!-- HTML5 element support for IE6-8 -->
  <!--[if lt IE 9]>
  <script src="https://cdn.jsdelivr.net/html5shiv/3.7.3/html5shiv-printshiv.min.js"></script>
  <![endif]-->
  <?php print $scripts; ?>
</head>
<body<?php print $body_attributes; ?>>
<div class="maintenance">
  <div class="text">
    <?php if (!empty($title)): ?>
      <h1 class="page-header"><?php print $title; ?></h1>
    <?php endif; ?>
    <?php if (!empty($content)): ?>
      <?php print $content; ?>
    <?php endif; ?>
  </div>
  <div class="logo">
    <img src="/sites/default/files/images/logo/logo.svg" alt="KCCC" />
  </div>
</div>
</div>
<?php print $page_bottom; ?>
</body>
</html>
