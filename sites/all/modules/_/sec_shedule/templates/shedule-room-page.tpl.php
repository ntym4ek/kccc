<?
$current = array_shift($room['events']);
$next = array_shift($room['events']);
?>
<div class="room<? print $current['started'] ?>">

  <div class="room-header">
    <a href="/shedule" class="back"><i class="fas fa-chevron-left"></i></a>
    <div class="block-wrap">
      <div class="block-i block">
        <div class="box">
          <div class="line1"><? print $room['title']; ?></div>
          <div><? print $room['floor_text']; ?></div>
        </div>
        <div class="live">ЗАНЯТА</div>
      </div>

      <div class="block-l block">
        <div class="content">
          <div class="logo">
            <img src="/sites/all/themes/gard/images/logo/logo_blue.png">
          </div>

          <div class="clock">
            <span class="hours">00</span><span class="minutes">:00</span><span class="seconds"> 00</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="room-event">
    <div class="line2"><? print empty($current['description']) ? 'Нет мероприятий' : $current['description']; ?></div>
    <? if (!empty($current['start'])): ?>
      <div class="line3"><? print $current['start'] . ' - ' . $current['finish']; ?></div>
    <? endif; ?>
  </div>

  <div class="room-participants">
    <div class="block">
      <div class="content">
        <? foreach($current['participants'] as $pp): ?>
          <div class="participant">
            <div class="line1"><? print $pp->field_company_value; ?></div>
            <div class=""><? print nl2br($pp->field_participants_list_value); ?></div>
          </div>
        <? endforeach; ?>
      </div>
    </div>
  </div>

  <div class="room-footer">
    <? if (!empty($current)): ?>
      <div class="line1">Следующая встреча</div>
      <div class="line2"><? print $current['description']; ?></div>
      <div class="block-wrap">
        <div class="block">
          <div class="line3"><? print $current['start'] . ' - ' . $current['finish']; ?></div>
        </div>
        <div class="block">
          <? foreach($current['participants'] as $pp): ?>
            <div class="line1"><? print $pp->field_company_value; ?></div>
          <? endforeach; ?>
        </div>
      </div>

    <? elseif (!empty($current)): ?>
      <div class="line1">На сегодня нет мероприятий</div>
    <? endif; ?>
  </div>

</div>
