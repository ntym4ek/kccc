<?php
  // $active - вся карточка является ссылкой на профиль пользователя
  if (!isset($active)) $active = false;
?>

<?php if ($active): ?>
  <a href="<?php print $url; ?>">
<?php endif; ?>

<div class="card card-contact">

    <div class="image">
      <?php if ($active): ?>
        <img src="<?php print $main['photo']['url']; ?>" alt="<?php print $label; ?>">
      <?php else: ?>
        <a href="<?php print $url; ?>"><img src="<?php print $main['photo']['url']; ?>" alt="<?php print $label; ?>"></a>
      <?php endif; ?>
    </div>
    <div class="contact-info">
      <div class="department"><?php print $staff['manage']; ?></div>
      <div class="name">
        <?php if ($active): ?>
          <?php if (!empty($main['name_full'])) : ?><?php print $main['name_full']; ?><?php endif; ?>
        <?php else: ?>
          <?php if (!empty($main['name_full'])) : ?><a href="<?php print $url; ?>"><?php print $main['name_full']; ?></a><?php endif; ?>
        <?php endif; ?>
      </div>
      <?php if (!empty($staff['office']['title'])) : ?><div class="office"><?php print $staff['office']['title']; ?></div><?php endif; ?>
      <?php if (empty($staff['hide_contact'])): ?>
      <div class="phones">
      <?php
      foreach($staff['phones'] as $i => $phone) {
        if ($active) print ($i ? ', ' : '') . '<span class="phone">' . $phone['formatted'] . '</span>';
          else print ($i ? ', ' : '') . '<a href="tel:+' . $phone['tel'] . '"><span class="phone">' . $phone['formatted'] . '</span></a>';
      }
      ?>
      </div>
        <div class="email">
        <?php if ($active): ?>
          <?php print $email; ?>
        <?php else: ?>
          <a href="mailto:<?php print $email; ?>"><?php print $email; ?></a>
        <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>

</div>
<?php if ($active): ?>
  </a>
<?php endif; ?>

