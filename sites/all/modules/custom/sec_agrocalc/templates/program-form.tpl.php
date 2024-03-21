<?php
?>

<div id="agrocalc" class="agrocalc">
  <div class="row">
    <div class="col-xs-12 col-md-8">
      <div class="intro">
        <?php print drupal_render($form['intro']); ?>
      </div>
    </div>
  </div>

  <div class="params">
    <?php print drupal_render($form['params']); ?>
  </div>

  <?php print drupal_render($form['params']); ?>

  <div class="program">
    <?php print drupal_render($form['program']); ?>
  </div>

  <div class="request">
    <?php print drupal_render($form['request']); ?>
  </div>

  <?php echo drupal_render_children($form); ?>
</div>


