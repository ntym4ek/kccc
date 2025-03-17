<?php

function landings_webinar5_page()
{
  $output = '<div class="webinar-page">' .
              '<picture>' .
                '<source srcset="/sites/all/modules/custom/sec_landings/images/webinar5/768.jpg" media="(max-width: 767px)">' .
                '<source srcset="/sites/all/modules/custom/sec_landings/images/webinar5/1192.jpg" media="(max-width: 1192px)">' .
                '<img src="/sites/all/modules/custom/sec_landings/images/webinar5/1920.jpg" alt="Вебинар">' .
              '</picture>' .
              '<div class="buttons-wrapper2">' .
                '<div class="buttons2">' .
                  '<a href="https://my.mts-link.ru/j/KCCC/soybean" class="btn btn-brand"><span>25.03.2025</span><br>Вебинар. Защита сои.</a>' .
                  '<a href="https://my.mts-link.ru/j/KCCC/oilseeds" class="btn btn-brand"><span>27.03.2025</span><br>Вебинар. Защита рапса и подсолнечника.</a>' .
                '</div>' .
              '</div>' .
              '<div class="buttons-wrapper">' .
                '<div class="buttons">' .
                  '<a href="https://my.mts-link.ru/j/KCCC/soybean" class="btn btn-brand">Регистрируйся!</a>' .
                  '<a href="https://my.mts-link.ru/j/KCCC/oilseeds" class="btn btn-brand">Регистрируйся!</a>' .
                '</div>' .
              '</div>' .
            '</div>';


  return $output;
}
