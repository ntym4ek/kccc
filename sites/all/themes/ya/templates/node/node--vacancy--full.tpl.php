<?php
    hide($content['links']);
    hide($content['field_vacancy_form']);
    hide($content['body']);
    $st = (isset($field_vacancy_employer[0]) && $field_vacancy_employer[0]['value'] == 'st') ? true : false;
?>

<article class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>

  <? if (!empty($backstep_url)): ?>
    <div class="backstep">
      <a href="<? print $backstep_url; ?>"><i class="fas fa-long-arrow-alt-left"></i>&nbsp;&nbsp;<? print t("Back to list");?></a>
    </div>
  <? endif; ?>

  <? if (!empty($date) || !empty($period) || !empty($location) || !empty($viewed)): ?>
  <div class="node-stuff">
      <? if (!empty($date) || !empty($period)): ?>
          <?php print empty($period) ? $date : $period; ?>
      <? endif; ?>
      <? if (!empty($location)): ?>
          <span class="location"><i class="fas fa-location-arrow"></i><? print $location; ?></span>
      <? endif; ?>
      <? if (!empty($viewed)): ?>
          <span class="viewed"><i class="fas fa-eye"></i><? print $viewed; ?></span>
      <? endif; ?>
  </div>
  <? endif; ?>

  <h1 class="node-title">
    <? print $title; ?>
  </h1>

  <div class="divider"></div>

  <div class="row">
    <div class="col-md-8">
      <h3><? print t('Workplace'); ?></h3>
      <? print render($content['field_region']); ?>
      <? print render($content['field_vacancy_location']); ?>
      <? print render($content['field_vacancy_employer']); ?>


      <h3><? print t('About vacancy'); ?></h3>
      <? print render($content); ?>
    </div>
    <div class="col-md-4">
      <? print render($content['field_vacancy_form']); ?>
    </div>


    <div class="col-md-12">
      <h3><? print t('Your actions'); ?></h3>
      <ul>
        <li>Отправить отклик</li>
        <li>Дождаться звонка с приглашением на собеседование от сотрудников нашей кадровой службы</li>
      </ul>

      <? print render($content['body']); ?>

      <h3><? print t('Contacts'); ?></h3>

      <? print theme('contact_card', array(
        'contact' => [
          'office' => t('Personnel manager'),
          'surname' => 'Огородова',
          'name' => 'Мария',
          'name2' => 'Николаевна',
          'phones' => array('+7(8332) 76-15-22, доб. 1186'),
          'emails' => array('maria.ogorodova@kccc.ru'),
          'photo' => '/sites/all/modules/_/contacts/images/photo/ogorodova.png',
        ],
        'options' => ['class' => 'col-md-6'])); ?>
    </div>
  </div>

</article>
