<?php ?>

<div id="profile" class="profile">
    <div class="profile-content">

      <div class="row">
        <div class="col-xs-12">
          <div class="profile-name">
            <?php print $user_info["main"]["name_short"]; ?>
          </div>
          </div>
      </div>

      <div class="row">
        <div class="col-xs-12 col-md-3">
          <div class="profile-left">
            <div class="image">
              <img src="<?php print $user_info["main"]["photo"]["url"]; ?>" alt="<?php print t('User photo'); ?>" />
            </div>
          </div>
        </div>
        <div class="col-xs-12 col-md-9 col-lg-6">
          <div class="profile-right">
            <div class="profile-header">
              <?php if (!empty($user_info["staff"]["office"]["department"])): ?>
              <div class="profile-department">
                <?php print $user_info["staff"]["office"]["department"]; ?>
              </div>
              <?php endif; ?>
              <div class="profile-role">
                <?php print $user_info['label']; ?>
              </div>
            </div>

            <?php if (!empty($user_info['staff']['counts'])): ?>
            <div class="profile-counts">
              <?php foreach($user_info['staff']['counts'] as $count): ?>
                <div class="profile-count" >
                  <?php if ($count['url']): ?><a href="<?php print $count['url']; ?>" target="_blank"><?php endif; ?>
                    <div class="count-amount"><?php print $count['amount']; ?></div>
                    <div class="count-title"><?php print $count['title']; ?></div>
                    <?php if ($count['url']): ?></a><?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (empty($user_info['staff']['hide_contact'])): ?>
            <div class="profile-info">
              <?php if (!empty($user_info['staff']['regions']['formatted'])): ?>
              <div class="row">
                <div class="col-xs-12 col-md-3"><div class="label"><?php print t('Region', [], ['context' => 'representative']); ?></div></div>
                <div class="col-xs-12 col-md-9"><div class="field"><?php print $user_info['staff']['regions']['formatted']; ?></div></div>
              </div>
              <?php endif; ?>

              <?php if (!empty($user_info['staff']['phones'])): ?>
              <div class="row">
                <div class="col-xs-12 col-md-3"><div class="label"><?php print t('Phone'); ?></div></div>
                <div class="col-xs-12 col-md-9">
                  <div class="field">
                    <?php foreach ($user_info['staff']['phones'] as $phone): ?>
                    <div><a href="tel:<?php print $phone['tel']; ?>" rel="nofollow"><span class="phone"><?php print $phone['formatted']; ?></span></a></div>
                    <?php endforeach; ?>
                </div>
                </div>
              </div>
              <?php endif; ?>

              <div class="row">
                <div class="col-xs-12 col-md-3"><div class="label">E-Mail</div></div>
                <div class="col-xs-12 col-md-9">
                  <div class="field">
                    <a href="mailto:<?php print $user_info['email']; ?>" class="email" rel="nofollow"><?php print $user_info['email']; ?></a>
                  </div>
                </div>
              </div>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <?php if (!empty($user_info["staff"]["qr_url"])): ?>
        <div class="col-xs-12 col-md-6 col-md-offset-3 col-lg-3 col-lg-offset-0">
          <div class="profile-qr">
            <div class="image">
              <img src="<?php print $user_info["staff"]["qr_url"]; ?>" alt="<?php print t('KCCC employee'); ?>" />
            </div>
            <p class="text-muted text-center"><?php print t('Scan to add contact.'); ?></p>
          </div>
        </div>
        <?php endif; ?>

      </div>

    </div>
</div>
