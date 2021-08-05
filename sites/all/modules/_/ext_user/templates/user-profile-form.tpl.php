<div id="profile" class="profile-wrapper">
  <div class="profile">
    <div class="profile-content">

      <div class="row">
        <div class="profile-left">
          <div class="photo">
            <? if (isset($form["profile_main"]["field_profile_photo"])): ?>
              <? echo render($form["profile_main"]["field_profile_photo"]); ?>
            <? endif; ?>

            <? if (isset($form['picture'])): ?>
              <? echo render($form['picture']); ?>
            <? endif; ?>
          </div>
        </div>

        <div class="profile-right">
          <? if (isset($form["group_g1"])): ?>
            <? echo render($form['group_g1']); ?>
          <? endif; ?>

          <? if (isset($form["profile_main"])): ?>
            <? echo render($form["profile_main"]); ?>
          <? endif; ?>

          <? if (isset($form["profile_staff"])): ?>
            <? echo render($form["profile_staff"]); ?>
          <? endif; ?>

          <? echo render($form["actions"]); ?>
        </div>
      </div>

      <?php echo drupal_render_children($form); ?>

    </div>
  </div>
</div>
