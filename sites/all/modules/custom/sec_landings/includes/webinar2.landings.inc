<?php

function landings_webinar2_page()
{
  $output = '<a href="https://events.webinar.ru/KCCC/raps-noviy-sezon">' .
              '<picture>' .
                '<source srcset="/sites/all/modules/custom/sec_landings/images/webinar2/768.webp" media="(max-width: 767px)">' .
                '<source srcset="/sites/all/modules/custom/sec_landings/images/webinar2/1200.webp" media="(max-width: 1279px)">' .
                '<img src="/sites/all/modules/custom/sec_landings/images/webinar2/1920.webp" alt="Вебинар">' .
              '</picture>' .
              '</a>';


  return $output;
}
