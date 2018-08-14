<?php // dsm( $forecast );?>
<div class="weather page">

    <div class="today">
        <div class="info">
            <span><?php print $forecast['city']; ?></span>, <?php print t('now'); ?><br />
            <?php print $forecast['date']; ?>
        </div>
        <div class="fact">
            <img src="http://yastatic.net/weather/i/icons/svg/<?php print $forecast['fact']['imagev3']; ?>.svg" title="<?php print $forecast['fact']['weather_type']; ?>">
            <?php print $forecast['fact']['temp']; ?>&deg;
        </div>
        <div class="common-info">
            <div class="cell"><?php print t('Humidity'); ?>: <span><?php print $forecast['fact']['humidity']; ?> %</span></div>
            <div class="cell"><?php print t('Pressure'); ?>: <span><?php print $forecast['fact']['pressure']; ?> <?php print t('mm'); ?></span></div>
            <div class="cell"><?php print t('Wind speed'); ?>: <span><?php print $forecast['fact']['wind_speed']; ?> <?php print t('m/s'); ?></span></div>
            <div class="cell"><?php print t('Wind direction'); ?>: <span><div class="wind drctn_<?php print $forecast['fact']['wind_drctn']; ?>"><img src="//yastatic.net/weather-frontend/_/N3n-oDD3MaaMLxl5pQnzNDkEryw.svg">&nbsp;<?php print $forecast['fact']['wind_info']; ?></div></span></div>
        </div>
    </div>
    <div class="forecast">
        <div class="days">
            <?php foreach ($forecast['day'] as $key1 => $day): ?>
                <?php if($key1 > 6) break; ?>
                <div class="item <?php print ($key1)?'':'active'; ?>">
                    <div class="weekday"><?php print $day['weekday_short']; ?></div>
                    <div class="icon"><img src="http://yastatic.net/weather/i/icons/svg/<?php print $day['imagev3']; ?>.svg" title="<?php print $day['weather_type']; ?>" /></div>
                    <div class="temp"><?php print $day['temp_day']; ?>&deg;<?php print '/' . $day['temp_night']; ?> &deg;</div>

                    <div class="day">
                        <div class="name">
                            <div class="common-info">
                                <div class="date"><?php print $day['weekday_full']; ?>, <?php print $day['date']; ?></div>
                                <div class="cell"><?php print t('Sunrise'); ?>: <span><?php print $day['sunrise']; ?></span></div>
                                <div class="cell"><?php print t('Sunset'); ?>: <span><?php print $day['sunset']; ?></span></div>
                            </div>
                            <div class="cell"><?php print t('Humidity'); ?></div>
                            <div class="cell"><?php print t('Pressure'); ?></div>
                            <div class="cell"><?php print t('Wind speed'); ?></div>
                            <div class="cell"><?php print t('Wind direction'); ?></div>
                        </div>
                        <?php foreach ($day['day_part'] as $key2 => $day_part): ?>
                            <?php
                                if($key2 > 3) break;
                                if ($day_part['temp_avg']<0) $color = 'rgba(9,148,220,0.5)';
                                elseif ($day_part['temp_avg']==0) $color = 'rgba(0,153,255,0.2)';
                                else $color = 'rgba(255,102,0,0.5)';
                            ?>
                            <div class="day_part">
                                <div class="part"><?php print t($day_part['part']); ?></div>
                                <div class="icon"><img src="http://yastatic.net/weather/i/icons/svg/<?php print $day_part['imagev3']; ?>.svg" title="<?php print $day_part['weather_type']; ?>"/></div>
                                <div class="temp">
                                    <div style="background-color:<?php print $color; ?>;height:<?php print $day_part['height']; ?>px;"><?php print $day_part['temp_avg'] . '&deg;'; ?></div>
                                </div>
                                <div class="cell"><?php print $day_part['humidity'] . ' %'; ?></div>
                                <div class="cell"><?php print $day_part['pressure'] . ' ' . t('mm'); ?></div>
                                <div class="cell"><?php print $day_part['wind_speed'] . ' ' . t('m/s'); ?></div>
                                <div class="wind drctn_<?php print $day_part['wind_drctn']; ?>"><img src="//yastatic.net/weather-frontend/_/N3n-oDD3MaaMLxl5pQnzNDkEryw.svg">&nbsp;<?php print $day_part['wind_info']; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="detail"></div>


</div>
