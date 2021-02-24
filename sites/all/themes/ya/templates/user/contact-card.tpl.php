<?if (isset($options['class'])): ?>
<div class="<? print $options['class']; ?>">
<? endif; ?>
<div class="card contact-card">
  <div class="card-content">
    <div class="card-image">
      <img src="<? print $contact['photo']; ?>" alt="<? print $contact['office']; ?>" class="img-circle">
    </div>
    <div class="card-title">
      <h3><? print $contact['surname']; ?><br /><? print $contact['name'] . ' ' . $contact['name2'] ; ?></h3>
    </div>
    <div class="card-subtitle"><? print $contact['office']; ?></div>

    <div class="divider"></div>

    <? if (!empty($contact['expert'])): ?>
      <div class="contact-expert">
        <? $phone_raw = str_replace(array('(', ')', '-', ' ', '+'), '', $contact['expert'])?>
        <div class="contact-whatsapp"><i class="fab fa-whatsapp"></i><a href="https://wa.me/<? print $phone_raw; ?>" rel="nofollow">WhatsApp</a></div>
      </div>
    <? endif; ?>

    <? if (!empty($contact['phones'])): ?>
      <div class="contact-phones">
        <?php foreach ($contact['phones'] as $phone): ?>
          <? $phone_raw = str_replace(array('(', ')', '-',' '), '', $phone)?>
          <div class="contact-phone"><i class="fas fa-phone"></i><a href="tel:<? print $phone_raw; ?>" rel="nofollow"><? print $phone; ?></a></div>
        <?php endforeach; ?>
      </div>
    <? endif; ?>

    <? if (!empty($contact['emails'])): ?>
      <div class="contact-emails">
        <?php foreach ($contact['emails'] as $email): ?>
          <div class="contact-email"><i class="far fa-envelope"></i><a href="mailto:e(<? print email_antibot_encode($email); ?>)" class="eAddr-encoded eAddr-html" rel="nofollow">e(<? print email_antibot_encode($email); ?>)</a></div>
        <?php endforeach; ?>
      </div>
    <? endif; ?>
    <div class="card-footer">
      <? if (!empty($collapse['content'])): ?>
        <div class="contact-collapse">
          <div data-toggle="collapse" data-target="#collapse-<? print $collapse['id']; ?>" aria-expanded="true" aria-controls="collapse-<? print $collapse['id']; ?>" class="collapse-title<? empty($collapse['collapsed']) ? '' : ' collapsed'; ?>"><? print $collapse['title']; ?><i class="fa fa-caret-down" aria-hidden="true"></i></div>
          <div class="collapse <? print empty($collapse['collapsed']) ? ' in' : ''; ?>" id="collapse-<? print $collapse['id']; ?>"><? print $collapse['content']; ?></div>
        </div>
      <? endif; ?>
    </div>
  </div>
</div>
<?if (isset($options['class'])): ?>
  </div>
<? endif; ?>
