<?php

/**
 * @file
 * Customize the display of a complete webform.
 *
 * This file may be renamed "webform-form-[nid].tpl.php" to target a specific
 * webform on your site. Or you can leave it "webform-form.tpl.php" to affect
 * all webforms on your site.
 *
 * Available variables:
 * - $form: The complete form array.
 * - $nid: The node ID of the Webform.
 *
 * The $form array contains two main pieces:
 * - $form['submitted']: The main content of the user-created form.
 * - $form['details']: Internal information stored by Webform.
 *
 * If a preview is enabled, these keys will be available on the preview page:
 * - $form['preview_message']: The preview message renderable.
 * - $form['preview']: A renderable representing the entire submission preview.
 */
?>
<div class="form-wrapper">
  <div class="row">
    <div class="col-sm-12">
      <div class="form-header">
        <?php
          // Print out the preview message if on the preview page.
          if (isset($form['preview_message'])) {
            print '<div class="messages warning">';
            print drupal_render($form['preview_message']);
            print '</div>';
          }
        ?>
      </div>
    </div>
    <div class="col-sm-12 col-md-4 col-md-offset-2 last-md">
      <div class="block-attention">
        <div class="h4"><?php print t('Attention'); ?>!</div>
        <p><?php print t('Perhaps we already have a vacancy in your specialty. You can check it in the «Vacancies» section'); ?></p>
        <div class="actions">
          <a href="<?php print url('vakansii'); ?>" class="btn btn-brand btn-wide btn-underline"><?php print t('Vacancies'); ?></a>
        </div>
      </div>
    </div>
    <div class="col-sm-12 col-md-6">
      <div class="form-content">
        <div class="intro"><?php print t('If you have not found the necessary vacancy on the website, send your resume to our personnel reserve.<p>We may need a specialist of your qualifications soon.</p>'); ?></div>
        <?php
          // Print out the main part of the form.
          // Feel free to break this up and move the pieces within the array.
          print drupal_render($form['submitted']);

          // Always print out the entire $form. This renders the remaining pieces of the
          // form that haven't yet been rendered above (buttons, hidden elements, etc).
          print drupal_render_children($form);
        ?>
      </div>
    </div>
  </div>
</div>

