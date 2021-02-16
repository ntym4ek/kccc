<div class="block-3 block<? print (isset($content[ROOM3]['events']) && count($content[ROOM3]['events'])) ? '' : ' disabled'; ?>">
    <div class="header">
        <div class="box1">3</div>
        <div class="box2">
            <div class="line1"><? print $content[ROOM3]['title']; ?></div>
            <div class="line2"><? print $content[ROOM3]['floor_text']; ?></div>
        </div>
    </div>
    <div class="content">
        <? if (!empty($content[ROOM3]['events'])): ?>
            <?php foreach($content[ROOM3]['events'] as $eid => $event): ?>
                <div class="event<? print $event['started']; ?>">
                    <div class="row1"><? print $event['start']; ?></div>
                    <div class="row2">
                        <div class="line1"><? print $event['title']; ?></div>
                        <div class="line2"><? print $event['description']; ?></div>
                    </div>
                    <? if ($content['admin']): ?><a href="/node/<? print $eid; ?>/edit" class="edit-link"><i class="fas fa-pen"></i></a><? endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>