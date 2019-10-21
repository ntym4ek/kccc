<div class="shedule">
    <div class="column-f">
        <div class="group">
            <div class="block">
                <div class="content">
                    <?php foreach($content['footer']['events'] as $event): ?>
                        <div class="event<? print $event['started']; ?>">
                            <div class="row1">
                                <div class="line1"><? print $event['start']; ?></div>
                                <div class="line2"><? print $event['title']; ?></div>
                            </div>
                            <div class="row2">
                                <? if ($event['floor'] == 1): ?>
                                    <i class="fas fa-long-arrow-alt-left"></i>
                                    <i class="fas fa-<? print $event['hurry'] ? 'running' : 'walking'; ?> fa-flip-horizontal"></i>
                                <? endif; ?>
                                <? if ($event['floor'] == 2): ?>
                                    <i class="fas fa-<? print $event['hurry'] ? 'running' : 'walking'; ?>"></i>
                                    <i class="fas fa-long-arrow-alt-up"></i>
                                <? endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="block logo">
                <div class="content">
                    <img src="/sites/all/themes/gard/images/logo/logo_blue.png">
                </div>
            </div>
        </div>
    </div>

    <div class="column-a">
        <div class="group group-a">
            <div class="block<? print count($content[ROOM1]['events']) ? '' : ' disabled'; ?>">
                <div class="header">
                    <div class="box1">1</div>
                    <div class="box2">
                        <div class="line1"><? print $content[ROOM1]['title']; ?></div>
                        <div class="line2"><? print $content[ROOM1]['floor_text']; ?></div>
                    </div>
                    <? if ($content['admin']): ?><a href="/node/add/room-event" class="add-link"><i class="fas fa-plus"></i></a><? endif; ?>
                </div>
                <div class="content">
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
                </div>
            </div>
            <div class="block<? print count($content[ROOM2]['events']) ? '' : ' disabled'; ?>">
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
        </div>
        <div class="group group-b">
            <div class="block<? print count($content[ROOM4]['events']) ? '' : ' disabled'; ?>">
                <div class="header">
                    <div class="box1">4</div>
                    <div class="box2">
                        <div class="line1"><? print $content[ROOM4]['title']; ?>, <span><? print $content[ROOM4]['floor_text']; ?></span></div>
                    </div>
                </div>
                <div class="content">
                    <?php foreach($content[ROOM4]['events'] as $eid => $event): ?>
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
            <div class="block<? print count($content[ROOM5]['events']) ? '' : ' disabled'; ?>">
                <div class="header">
                    <div class="box1">5</div>
                    <div class="box2">
                        <div class="line1"><? print $content[ROOM5]['title']; ?>, <span><? print $content[ROOM5]['floor_text']; ?></span></div>
                    </div>
                </div>
                <div class="content">
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
                </div>
            </div>
            <div class="block<? print count($content[ROOM6]['events']) ? '' : ' disabled'; ?>">
                <div class="header">
                    <div class="box1">6</div>
                    <div class="box2">
                        <div class="line1"><? print $content[ROOM6]['title']; ?>, <span><? print $content[ROOM6]['floor_text']; ?></span></div>
                    </div>
                </div>
                <div class="content">
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
                </div>
            </div>
        </div>
    </div>
    <div class="column-b">
        <div class="group">
            <div class="block<? print count($content[ROOM3]['events']) ? '' : ' disabled'; ?>">
                <div class="header">
                    <div class="box1">3</div>
                    <div class="box2">
                        <div class="line1"><? print $content[ROOM3]['title']; ?></div>
                        <div class="line2"><? print $content[ROOM3]['floor_text']; ?></div>
                    </div>
                </div>
                <div class="content">
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
                </div>
            </div>
        </div>
    </div>

<!--    --><?php //print $content; ?>
</div>