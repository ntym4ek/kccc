<div id="profile" class="profile-wrapper">
    <header>
        <h1><?php print $content['account']['short_name']; ?></h1>
        <img src="<?php print $content['account']['photo']; ?>" alt="" class="img-circle">
        <?php if ($content['edit_link']): ?>
            <div class="profile-edit"><?php print $content['edit_link']; ?></div>
        <?php endif; ?>
    </header>
    <div class="subheader-menu">
        <? print $content['menu']; ?>
    </div>
    <div class="profile">
        <?php if ($content['message']): ?>
            <div id="messages-wrap"><? print $content['message']; ?></div>
        <?php endif;?>
        <div class="profile-content <? print $content['class']; ?>">
            <? print $content['body']; ?>
        </div>
    </div>
</div>