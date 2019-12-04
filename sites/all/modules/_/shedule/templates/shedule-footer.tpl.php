<div class="block-f block">
    <div class="content">
        <? if (!empty($content['footer']['events'])): ?>
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
        <?php endif; ?>
    </div>
</div>