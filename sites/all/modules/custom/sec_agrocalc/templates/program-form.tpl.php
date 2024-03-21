<?php
?>

<div id="agrocalc" class="agrocalc">
  <div class="row">
    <div class="col-xs-12 col-md-8">
      <div class="intro">
        <p><?php print t('You need to enter the parameters of your field, then specify the problems that are present on it and click the "Calculate" button.'); ?></p>
        <p><?php print t('The protection program we have proposed can be adjusted with switches and sent to a representative for the region.'); ?></p>
        <p><?php print t('You will be contacted with an offer of an individual program and prices.'); ?></p>
      </div>
    </div>
  </div>

  <div class="params-wrapper">
    <h3><?php print t('Culture and field parameters'); ?></h3>

    <?php print drupal_render($form['params']['field']); ?>

    <?php if (!empty($form['params']['problem'])): ?>
      <h3><?php print t('Culture and field problems'); ?></h3>
      <?php print drupal_render($form['params']['problem']); ?>
      <h3><?php print t('You can add'); ?></h3>
      <?php print drupal_render($form['params']['addon']); ?>
    <?php endif; ?>
  </div>

  <?php print drupal_render($form['actions']); ?>

  <?php if (!empty($form['program'])): ?>
  <div class="program-wrapper">
    <h3>Препараты для защиты культуры</h3>
    <?php print drupal_render($form['program']); ?>
  </div>
  <?php endif; ?>

  <?php if (!empty($form['request'])): ?>
  <div class="request-wrapper">
    <h3><?php print t('Get detailed calculation'); ?></h3>
    <p><?php print t('Choose your region, fill phone or E-Mail fields. Our representative will make individual program for you and send it to E-Mail or contact you by phone.'); ?></p>
    <?php print drupal_render($form['request']); ?>
  </div>
  <?php endif; ?>

  <?php echo drupal_render_children($form); ?>
</div>


