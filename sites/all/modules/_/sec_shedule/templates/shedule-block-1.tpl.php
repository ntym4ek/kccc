<div class="block-1 block<? print (isset($content[ROOM1]['events']) && count($content[ROOM1]['events'])) ? '' : ' disabled'; ?>">
    <div class="header">
        <div class="box1">1</div>
        <div class="box2">
          <div class="line1"><a href="/shedule/room/1"><? print $content[ROOM1]['title']; ?></a></div>
            <div class="line2"><? print $content[ROOM1]['floor_text']; ?></div>
        </div>
        <? if ($content['admin']): ?><a href="/node/add/room-event" class="add-link"><i class="fas fa-plus"></i></a><? endif; ?>
    </div>
    <div class="content">
        <? if (!empty($content[ROOM1]['events'])): ?>
            <?php foreach($content[ROOM1]['events'] as $eid => $event): ?>
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

