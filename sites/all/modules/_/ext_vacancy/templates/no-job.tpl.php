
<div class="page40x">
    <div class="b1">
        <h2><?php print 'Вакансия была закрыта'; ?></h2>
        <h4><?php print 'Но возможно Вас заинтересуют другие' ; ?></h4>
        <ul>
            <li><?php print l('Открытые вакансии', 'info/job'); ?></li>
        </ul>
      <p><?php print 'Также возможно, что вскоре нам потребуется специалист вашего профиля.<br>Оставьте своё резюме в <a href="/info/job/reserve">Кадровом резерве</a> и будете первым, с кем мы свяжемся.'; ?></p>

      <a href="<?php print $GLOBALS['base_root']; ?>" class="btn btn-success btn-wide"><?php print t('Homepage'); ?></a>
    </div>
    <div class="b2">
        <img src="/sites/all/modules/_/ext_vacancy/images/404.png" class="img-responsive" alt="">
    </div>
</div>
