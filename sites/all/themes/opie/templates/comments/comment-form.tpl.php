<?php
global $user;
// Disable author name.
unset($form['author']);

// If user have own avatar.
$user_pic = '';
$profile = profile2_load_by_user($user->uid, 'main');
if (isset($profile->field_profile_photo['und'][0]['uri'])) {
    $user_pic = theme('image_style', array(
      'style_name' => 'user_photo',
      'path' => $profile->field_profile_photo['und'][0]['uri'],
    ));
}
?>

<div class="img-wrap left">
  <?php print $user_pic; ?>
</div>
<div class="text-wrap">
  <?php print drupal_render_children($form); ?>
</div>