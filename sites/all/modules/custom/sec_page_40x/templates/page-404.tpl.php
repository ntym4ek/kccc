
<div class="page40x">
  <div class="b1">
    <h1>404</h1>
    <h2><?php print t('Page not found'); ?></h2>
    <h4><?php print t('But still you could be interested in:'); ?></h4>
    <ul>
      <li><?php print l(t('Preparations catalog'), 'katalog'); ?></li>
      <li><?php print l(t('Protection programs'), 'programmy-zashchity'); ?></li>
    </ul>
    <a href="<?php print $GLOBALS['base_root']; ?>" class="btn btn-brand btn-wide"><?php print t('Homepage'); ?></a>
  </div>
  <div class="b2">
    <img src="/<?php print drupal_get_path('module', 'page_40x')?>/images/404.png" class="img-responsive" alt="">
  </div>
</div>
