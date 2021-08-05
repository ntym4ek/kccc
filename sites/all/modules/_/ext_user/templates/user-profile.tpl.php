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
          <div class="profile-info">
            <div class="profile-info-top">
              <div class="profile-role">
                <? print $user_profile['role']; ?>
              </div>
              <div class="profile-status">
                <? if ($user_profile['is_online']): ?>
                  <span class="profile-online">В сети</span>
                <? else: ?>
                  <span class="profile-offline">Не в сети</span>
                <? endif; ?>
              </div>
            </div>
          </div>
          <div class="profile-counts">
            <? foreach($user_profile['counts'] as $count): ?>
              <a href="<? print $count['link']; ?>" class="profile-count <? print isset($count['class']) ? implode(' ', $count['class']) : ''; ?>" target="_blank">
                <div class="count-amount"><? print $count['amount']; ?></div>
                <div class="count-title"><? print $count['title']; ?></div>
              </a>
            <? endforeach; ?>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
