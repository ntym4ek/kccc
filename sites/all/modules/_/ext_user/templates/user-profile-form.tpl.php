<div id="profile" class="profile-wrapper">
  <div class="profile">
    <div class="profile-content">

      <div class="row">
        <div class="profile-left">
          <div class="photo">

            <? if (isset($form["profile_main"]["field_profile_photo"])): ?>
              <? echo render($form["profile_main"]["field_profile_photo"]); ?>
            <? endif; ?>

          </div>
        </div>

        <div class="profile-right">

          <? hide($form["actions"]); ?>
          <? echo drupal_render_children($form); ?>

          <? echo render($form["actions"]); ?>

        </div>
      </div>
    </div>
  </div>
</div>
