<div class="media contact-card col-md-6">
    <div class="media-left">
        <img class="media-object" src="<? print $contact['photo']; ?>" alt="">
    </div>
    <div class="media-body">
        <h4 class="media-heading"><? print $contact['surname']; ?><br /><? print $contact['name'] . ' ' . $contact['name2'] ; ?></h4>
        <div class="contact-dep"><? print $contact['office']; ?></div>
        <? if (!empty($collapse)): ?>
            <div class="contact-collapse">
                <div data-toggle="collapse" data-target="#collapse-<? print $collapse['id']; ?>" aria-expanded="true" aria-controls="collapse-<? print $collapse['id']; ?>" class="collapse-title collapsed"><? print $collapse['title']; ?><i class="fa fa-caret-down" aria-hidden="true"></i></div>
                <div class="collapse" id="collapse-<? print $collapse['id']; ?>"><? print $collapse['content']; ?></div>
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
                <div class="contact-email eAddr-encoded"><a href="e(<? print $email; ?>)" class="eAddr-encoded" rel="nofollow">e(<? print $email; ?>)</a></div>
            <?php endforeach; ?>
        </div>
        <? endif; ?>
    </div>
</div>