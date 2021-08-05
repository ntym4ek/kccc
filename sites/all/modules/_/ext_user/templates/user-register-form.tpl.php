<div class="user-auth-wr">
  <div class="user-auth-logo"></div>
  <div class="user-auth-header">
    <div class="user-auth-title">
      <? echo render($form['title']); ?>
    </div>
    <div class="user-auth-links">
      <? echo render($form['links']); ?>
    </div>
  </div>
  <div class="user-auth">
    <? echo drupal_render_children($form); ?>
  </div>
</div>
