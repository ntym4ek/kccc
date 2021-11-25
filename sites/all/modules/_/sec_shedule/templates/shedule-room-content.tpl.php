<?
if (!empty($room['events'])) {
  $current = array_shift($room['events']);
  if (time() < $current['start_unix'] - 15*60) {
    $next = $current;
    $current = null;
  } else {
    $next = array_shift($room['events']);
  }
}
?>
<div class="room-content">
  <div class="room-event<? print isset($current['started']) ? $current['started'] : ''; ?>">
    <? if (!empty($current['start'])): ?>
      <div class="block">
        <div class="line2"><? print empty($current['description']) ? $current['title'] : $current['description']; ?></div>
        <div class="line3"><? print $current['start'] . ' - ' . $current['finish']; ?></div>
      </div>
    <div class="block">
      <div class="live">ИДЁТ</div>
    </div>
    <? else: ?>
    <div class="block">
      <div class="line2">Нет мероприятий</div>
    </div>
    <div class="block block">
      <div class="free">СВОБОДНО</div>
    </div>
    <? endif; ?>
  </div>

  <div class="room-participants">
    <div class="block">
      <div class="content">
        <? if (!empty($current['participants'])): ?>
        <? foreach($current['participants'] as $pp): ?>
          <div class="participant">
            <div class="line1"><? print $pp->field_company_value; ?></div>
            <div class=""><? print nl2br($pp->field_participants_list_value); ?></div>
          </div>
        <? endforeach; ?>
        <? endif; ?>
      </div>
    </div>
  </div>

  <div class="room-footer">
    <? if (!empty($next)): ?>
      <div class="line1">Следующая встреча</div>
      <div class="line2"><? print empty($next['description']) ? $next['title'] : $next['description']; ?></div>
      <div class="block-wrap">
        <div class="block">
          <div class="line3"><? print $next['start'] . ' - ' . $next['finish']; ?></div>
        </div>
        <div class="block">
          <? foreach($next['participants'] as $pp): ?>
            <div class="line1"><? print $pp->field_company_value; ?></div>
          <? endforeach; ?>
        </div>
      </div>

    <? elseif (!empty($current)): ?>
      <div class="line1">Далее на сегодня нет мероприятий</div>
    <? endif; ?>
  </div>
</div>
