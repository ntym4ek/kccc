<div id="profile" class="profile-wrapper">
  <div class="profile">
    <div class="profile-content">

      <div class="row">
        <div class="profile-left">
          <div class="photo">
            <img src="<? print $user_profile['photo']; ?>" alt="Фото пользователя" />
          </div>
          <? print $user_profile['add_photo_link'] ? '<div class="profile-add-photo">' . $user_profile['add_photo_link'] . '</div>' : ''; ?>
        </div>
        <div class="profile-right">
          <div class="profile-header">
            <div class="profile-role">
              <? print $user_profile['label']; ?>
            </div>
            <div class="profile-status">
              <? if ($user_profile['is_online']): ?>
                <span class="profile-online">В сети</span>
              <? else: ?>
                <span class="profile-offline">Не в сети</span>
              <? endif; ?>
            </div>
          </div>

          <? if (!empty($user_profile['counts'])): ?>
          <div class="profile-counts">
            <? foreach($user_profile['counts'] as $count): ?>
              <a href="<? print $count['link']; ?>" class="profile-count <? print isset($count['class']) ? implode(' ', $count['class']) : ''; ?>" target="_blank">
                <div class="count-amount"><? print $count['amount']; ?></div>
                <div class="count-title"><? print $count['title']; ?></div>
              </a>
            <? endforeach; ?>
          </div>
          <? endif; ?>

          <? if ($user_profile['show_contacts']): ?>
          <div class="profile-info">
            <? if (!empty($user_profile['regions'])): ?>
            <div class="profile-row">
              <div class="profile-col-left">Регион</div>
              <div class="profile-col-right"><? print $user_profile['regions']; ?></div>
            </div>
            <? endif; ?>

            <? if (!empty($user_profile['staff']['phones'])): ?>
            <div class="profile-row">
              <div class="profile-col-left">Телефон</div>
              <div class="profile-col-right">
                <?php foreach ($user_profile['staff']['phones'] as $phone): ?>
                <div><a href="tel:<? print $phone; ?>" rel="nofollow"><? print ext_user_format_phone($phone); ?></a></div>
                <?php endforeach; ?>
              </div>
            </div>
            <? endif; ?>

            <? if (!empty($user_profile['email'])): ?>
            <div class="profile-row">
              <div class="profile-col-left">E-Mail</div>
              <div class="profile-col-right">
                <a href="mailto:e(<? print email_antibot_encode($user_profile['email']); ?>)" class="mail eAddr-encoded eAddr-html" rel="nofollow"></a>
              </div>
            </div>
            <? endif; ?>
          </div>
          <? endif; ?>
        </div>
      </div>

    </div>
  </div>
</div>
