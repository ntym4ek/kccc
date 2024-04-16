<?php
?>

<div id="agrocalc" class="agrocalc">
  <div class="row">
    <div class="col-xs-12 col-xl-8">
      <div class="intro">
        <p><?php print t('With the help of an «Agrocalculator», you can choose products to solve the problems of your field and culture.'); ?></p>
        <p><?php print t('Specify the culture, characteristics of the field, harmful objects and click «Find a solution».'); ?></p>
        <p><?php print t('An automatic algorithm will calculate the protection system and the consumption of products in accordance with the specified parameters.'); ?></p>
        <p><?php print t('To clarify the compiled program and calculate the cost of products complex, send a request to our experts.'); ?></p>
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
    <h3><?php print t('We can offer next preparations'); ?></h3>
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


