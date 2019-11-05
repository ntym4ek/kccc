<div class="block-2 block<? print count($content[ROOM2]['events']) ? '' : ' disabled'; ?>">
    <div class="header">
        <div class="box1">2</div>
        <div class="box2">
            <div class="line1"><? print $content[ROOM2]['title']; ?></div>
            <div class="line2"><? print $content[ROOM2]['floor_text']; ?></div>
        </div>
    </div>
    <div class="content">
        <?php foreach($content[ROOM2]['events'] as $eid => $event): ?>
            <div class="event<? print $event['started']; ?>">
                <div class="row1"><? print $event['start']; ?></div>
                <div class="row2">
                    <div class="line1"><? print $event['title']; ?></div>
                    <div class="line2"><? print $event['description']; ?></div>
                </div>
                <? if ($content['admin']): ?><a href="/node/<? print $eid; ?>/edit" class="edit-link"><i class="fas fa-pen"></i></a><? endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
