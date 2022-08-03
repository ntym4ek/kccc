<div class="media contact-card<?php print isset($options['class']) ? ' ' . $options['class'] : ''; ?>">
    <div class="media-left">
        <img class="media-object" src="<? print $contact['photo']; ?>" alt="<? print str_replace('<br />', ' ', $contact['subtitle']); ?>" loading="lazy" />
    </div>
    <div class="media-body">
        <h4 class="media-heading"><? print $contact['title1']; ?><br /><? print $contact['title2'] ; ?></h4>
        <div class="contact-dep"><? print $contact['subtitle']; ?></div>
        <? if (!empty($collapse['content'])): ?>
            <div class="contact-collapse">
                <div data-toggle="collapse" data-target="#collapse-<? print $collapse['id']; ?>" aria-expanded="true" aria-controls="collapse-<? print $collapse['id']; ?>" class="collapse-title collapsed"><? print $collapse['title']; ?><i class="fa fa-caret-down" aria-hidden="true"></i></div>
                <div class="collapse" id="collapse-<? print $collapse['id']; ?>"><? print $collapse['content']; ?></div>
            </div>
        <? endif; ?>

        <? if (!empty($contact['messengers'])): ?>
            <div class="contact-expert">
              <?php foreach ($contact['messengers'] as $mess_type => $messenger): ?>
              <? if ($mess_type == 'whatsapp'): ?>
                  <? $mess_raw = str_replace(array('(', ')', '-', ' ', '+'), '', $messenger); ?>
                  <div class="contact-whatsapp"><a href="https://wa.me/<? print $mess_raw; ?>" rel="nofollow">WhatsApp</a></div>
                <? endif; ?>
              <?php endforeach; ?>
            </div>
        <? endif; ?>

        <? if (!empty($contact['phones'])): ?>
        <div class="contact-phones">
            <?php foreach ($contact['phones'] as $phone): ?>
                <? $phone_raw = str_replace(array('(', ')', '-',' '), '', $phone)?>
                <div class="contact-phone"><a href="tel:<? print $phone_raw; ?>" rel="nofollow"><? print $phone; ?></a></div>
            <?php endforeach; ?>
        </div>
        <? endif; ?>

        <? if (!empty($contact['emails'])): ?>
        <div class="contact-emails">
            <?php foreach ($contact['emails'] as $email): ?>
                <div class="contact-email"><a href="mailto:e(<? print email_antibot_encode($email); ?>)" class="eAddr-encoded eAddr-html" rel="nofollow">e(<? print email_antibot_encode($email); ?>)</a></div>
            <?php endforeach; ?>
        </div>
        <? endif; ?>
    </div>
</div>
