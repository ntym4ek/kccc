<div class="block-6 block<? print (isset($content[ROOM6]['events']) && count($content[ROOM6]['events'])) ? '' : ' disabled'; ?>">
    <div class="header">
        <div class="box1">6</div>
        <div class="box2">
          <div class="line1"><a href="/shedule/room/6"><? print $content[ROOM6]['title']; ?>, <span><? print $content[ROOM6]['floor_text']; ?></span></a></div>
        </div>
    </div>
    <div class="content">
        <? if (!empty($content[ROOM6]['events'])): ?>
            <?php foreach($content[ROOM6]['events'] as $eid => $event): ?>
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
