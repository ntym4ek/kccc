<?php  //kpr( $forecast );?>
<div class="weather stuff">
  <div class="bg">
  <div class="today">
    <div class="info">
      <span style="font-size:24px;"> <?php print $forecast['city'] . ', ' . $forecast['country'];?> </span><br />
      <?php print $forecast['date']; ?>
    </div>
    <div class="weather">
      <div class="temp">
        <!--img src="http://yandex.st/weather/1.2.61/i/icons/48x48/<?php print $forecast['fact']['imagev3']; ?>.png" title="<?php print $forecast['fact']['weather_type']; ?>"-->
        <!--img src="http://yastatic.net/weather/i/icons/svg/<?php print $forecast['fact']['imagev3']; ?>.svg" title="<?php print $forecast['fact']['weather_type']; ?>"-->
        <img src="http://img.yandex.net/i/wiz<?php print $forecast['fact']['image']; ?>.png" title="<?php print $forecast['fact']['weather_type']; ?>">
        <span> <?php print $forecast['fact']['temp']; ?>&deg;</span>
      </div>
      <div class="cloud">
        <span><?php print $forecast['fact']['weather_type']; ?></span>
      </div>
      <div class="detail">
        Атм. давление <?php print $forecast['fact']['pressure']; ?> мм. рт.ст.<br />
        Влажность <?php print $forecast['fact']['humidity']; ?>%<br />
        Скорость ветра <?php print $forecast['fact']['wind_speed']; ?> м/с<br />
        Рассвет <?php print $forecast['day'][0]['sunrise']; ?> / Закат <?php print $forecast['day'][0]['sunset']; ?><br />
      </div>
    </div>
  </div>
  <div class="forecast">
    <?php foreach ($forecast['day'] as $key => $value): ?>
      <?php if (( $key )&&( $key < 7 )): ?>
        <div class="item">
          <div><?php print $value['date'] . ', '. $value['weekday_short']; ?></div>
          <!--div class="icon"><img src="http://yandex.st/weather/1.2.61/i/icons/48x48/<?php print $value['imagev3']; ?>.png" /></div-->
          <div class="icon"><img src="http://img.yandex.net/i/wiz<?php print $value['image']; ?>.png" /></div>
          <div><?php print $value['temp_day']; ?> &deg; &nbsp; <?php print $value['temp_night']; ?> &deg;</div>
          <div class="weather_type"><span><?php print $value['weather_type']; ?></span></div>
          <div><?php print $value['pressure']; ?> мм </div>
          <div><?php print $value['humidity']; ?> % </div>
          <div><?php print $value['wind_speed']; ?> м/с </div>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
  </div>
</div>
