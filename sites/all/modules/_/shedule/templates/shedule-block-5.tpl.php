<div class="block-5 block<? print (isset($content[ROOM5]['events']) && count($content[ROOM5]['events'])) ? '' : ' disabled'; ?>">
    <div class="header">
        <div class="box1">5</div>
        <div class="box2">
            <div class="line1"><? print $content[ROOM5]['title']; ?>, <span><? print $content[ROOM5]['floor_text']; ?></span></div>
        </div>
    </div>
    <div class="content">
        <? if (!empty($content[ROOM5]['events'])): ?>
            <?php foreach($content[ROOM5]['events'] as $eid => $event): ?>
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