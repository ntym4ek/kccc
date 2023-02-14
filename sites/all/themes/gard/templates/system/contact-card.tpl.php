<div class="media contact-card<?php print isset($options['class']) ? ' ' . $options['class'] : ''; ?>">
    <div class="media-left">
      <?php if (isset($contact['id'])): ?><a href="/user/<?php print $contact['id']; ?>" target="_blank"><?php endif; ?>
        <img class="media-object" src="<?php print $contact['photo']; ?>" alt="<?php print str_replace('<br />', ' ', $contact['subtitle']); ?>" loading="lazy" />
      <?php if (isset($contact['id'])): ?></a><?php endif; ?>
    </div>
    <div class="media-body">
      <h4 class="media-heading">
        <?php if (isset($contact['id'])): ?><a href="/user/<?php print $contact['id']; ?>" target="_blank"><?php endif; ?>
          <?php print $contact['title1']; ?><br /><?php print $contact['title2'] ; ?>
        <?php if (isset($contact['id'])): ?></a><?php endif; ?>
      </h4>
        <div class="contact-dep"><?php print $contact['subtitle']; ?></div>
      <?php if (!empty($collapse['content'])): ?>
            <div class="contact-collapse">
                <div data-toggle="collapse" data-target="#collapse-<?php print $collapse['id']; ?>" aria-expanded="true" aria-controls="collapse-<?php print $collapse['id']; ?>" class="collapse-title collapsed"><?php print $collapse['title']; ?><i class="fa fa-caret-down" aria-hidden="true"></i></div>
                <div class="collapse" id="collapse-<?php print $collapse['id']; ?>"><?php print $collapse['content']; ?></div>
            </div>
      <?php endif; ?>

      <?php if (!empty($contact['messengers'])): ?>
            <div class="contact-expert">
              <?php foreach ($contact['messengers'] as $mess_type => $messenger): ?>
                <?php if ($mess_type == 'whatsapp'): ?>
                  <?php $mess_raw = str_replace(array('(', ')', '-', ' ', '+'), '', $messenger); ?>
                  <div class="contact-whatsapp"><a href="https://wa.me/<?php print $mess_raw; ?>" rel="nofollow">WhatsApp</a></div>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
      <?php endif; ?>

      <?php if (!empty($contact['phones'])): ?>
        <div class="contact-phones">
            <?php foreach ($contact['phones'] as $phone): ?>
              <?php $phone_raw = str_replace(array('(', ')', '-',' '), '', $phone)?>
                <div class="contact-phone"><a href="tel:<?php print $phone_raw; ?>" rel="nofollow"><?php print $phone; ?></a></div>
            <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($contact['emails'])): ?>
        <div class="contact-emails">
            <?php foreach ($contact['emails'] as $email): ?>
                <div class="contact-email"><a href="mailto:e(<?php print email_antibot_encode($email); ?>)" class="eAddr-encoded eAddr-html" rel="nofollow">e(<?php print email_antibot_encode($email); ?>)</a></div>
            <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
</div>
