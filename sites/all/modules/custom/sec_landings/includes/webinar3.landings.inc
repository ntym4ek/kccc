<?php

function landings_webinar3_page()
{
  $output = '<a href="https://my.mts-link.ru/KCCC/vozdelyvanie-soi">' .
              '<picture>' .
                '<source srcset="/sites/all/modules/custom/sec_landings/images/webinar3/768.webp" media="(max-width: 767px)">' .
                '<source srcset="/sites/all/modules/custom/sec_landings/images/webinar3/1200.webp" media="(max-width: 1279px)">' .
                '<img src="/sites/all/modules/custom/sec_landings/images/webinar3/1920.webp" alt="Вебинар">' .
              '</picture>' .
              '</a>';


  return $output;
}
