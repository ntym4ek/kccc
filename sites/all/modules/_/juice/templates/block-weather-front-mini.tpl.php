<div class="weather front mini">
  <div class="bg">
    <?php if ( isset( $forecast['fact'] )) :?>
    <div class="today">
      <div class="info">
        <span><a href="info/agrocast" target="_blank">Агропрогноз</a></span><br />
        <?php print $forecast['city'] . ', ' . $forecast['date_short']; ?>
      </div>
      <div class="fact">
        <div class="icon">
          <img src="http://img.yandex.net/i/wiz<?php print $forecast['fact']['image']; ?>.png" title="<?php print $forecast['fact']['weather_type']; ?>">
          <!--img src="<?php print drupal_get_path('module', 'juice') . '/images/wiz' . $forecast['fact']['image']; ?>.gif" title="<?php print $forecast['fact']['weather_type']; ?>"-->
        </div>
        <div class="temp">
          <?php print $forecast['fact']['temp']; ?>&deg;
        </div>
      </div>
    </div>
    <div class="forecast">
      <div class="days">
        <?php foreach ($forecast['day'] as $key1 => $day): ?>
          <?php if( $key1 < 4 ):?>
            <div class="item">
              <div><?php print ($key1)?$day['weekday_short']:t('today'); ?></div>
              <div class="icon"><img src="http://img.yandex.net/i/wiz<?php print $day['image']; ?>.png" title="<?php print $day['weather_type']; ?>" /></div>
              <div><?php print $day['temp_day']; ?>&deg;<?php print '/' . $day['temp_night']; ?> &deg;</div>
              <div class="pointer <?php print ($key1)?'':'active'; ?>"></div>

              <div class="day">
                <?php foreach ($day['day_part'] as $key2 => $day_part): ?>
                  <?php if( $key2 < 4 ):?>
                    <div class="day_part">
                      <div class="icon"><img src="http://img.yandex.net/i/wiz<?php print $day_part['image']; ?>.png" title="<?php print $day_part['weather_type']; ?>"/></div>
                      <div class="temp"><?php print $day_part['temp_avg'] . '&deg;'; ?></div>
                      <div class="part"><?php print t($day_part['part']); ?></div>
                    </div>
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>            
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
      <div class="detail">
        
      </div>
    </div>
    <div class="disclaimer">
      Прогноз не может использоваться в процессах планирования
    </div>  
  <?php else: ?>
    Обновите, пожалуйста, страницу для получения прогноза погоды.
  <?php endif; ?>
  </div>
</div>

