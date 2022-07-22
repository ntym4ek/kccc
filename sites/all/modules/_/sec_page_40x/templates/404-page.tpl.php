
<div class="page40x">
  <div class="b1">
    <h1>404</h1>
    <h2><?php print t('Page not found'); ?></h2>
    <h4><?php print t('But still you could be interested in:'); ?></h4>
    <ul>
      <li><?php print l(t('Preparations catalog'), 'catalog/agrochemicals'); ?></li>
      <li><?php print l(t('Protection programs'), 'handbook/protection-programs'); ?></li>
      <li><?php print l(t('Agro Calculator'), 'agro-calculator'); ?></li>
    </ul>
    <a href="<?php print $GLOBALS['base_root']; ?>" class="btn btn-success btn-wide"><?php print t('Homepage'); ?></a>
  </div>
  <div class="b2">
    <img src="/sites/all/modules/_/sec_page_40x/images/404.png" class="img-responsive" alt="">
  </div>
</div>
