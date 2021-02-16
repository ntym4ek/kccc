<?php
?>
<div class="fields">
    <header>
        <div class="cover"></div>
        <div class="title-1"><?php print $content['title1']; ?></div>
        <div class="title-2"><?php print isset($content['title2']) ? $content['title2'] : ''; ?></div>
        <div class="title-3"><?php print isset($content['title3']) ? $content['title3'] : ''; ?></div>
        <div class="title-4"><?php print isset($content['title4']) ? $content['title4'] : ''; ?></div>
    </header>
    <div class="subheader-menu">
        <? print $content['menu']; ?>
    </div>

    <div class="content">
        <?php if (!empty($content['items'])): ?>
        <ul class="list">
            <?php foreach($content['items'] as $item): ?>
            <li>
                <a href="<?php print $item['link']; ?>">
                    <div class="image"></div>
                    <div class="title-1"><?php print $item['title1']; ?></div>
                    <div class="title-2"><?php print $item['title2']; ?></div>
                </a>
                <? if (isset($item['link_edit'])): ?>
                    <a href="<? print $item['link_edit']; ?>" class="action-edit">
                        <i class="fa fa-pencil" aria-hidden="true"></i>
                    </a>
                <? endif; ?>
                <? if (isset($item['link_delete'])): ?>
                    <a href="<? print $item['link_delete']; ?>" class="action-delete">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </a>
                <? endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>
</div>

