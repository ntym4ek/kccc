<?php
?>

<div class="labor-protection">
    <div class="step">
        <div class="step1 <?php print ($form['#form_id']=='labor_page_1_form')?'active':''; ?>"><?php print t('Intro'); ?></div>
        <div class="step2 <?php print ($form['#form_id']=='labor_page_2_form')?'active':''; ?>"><?php print t('Video'); ?></div>
        <div class="step3 <?php print ($form['#form_id']=='labor_page_3_form')?'active':''; ?>"><?php print t('Questions'); ?></div>
        <div class="step4 <?php print ($form['#form_id']=='labor_page_good_form' || $form['#form_id']=='labor_page_bad_form')?'active':''; ?>"><?php print ($form['#form_id']=='labor_page_bad_form')?t('Fail!'):t('Done!'); ?></div>
    </div>

    <div class="lp-form">
        <?php print drupal_render($form); ?>
    </div>
</div>

