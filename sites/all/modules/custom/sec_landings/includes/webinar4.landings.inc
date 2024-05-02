<?php

function landings_webinar4_page()
{
  $output = '<a href="https://my.mts-link.ru/j/KCCC/sunflower-protection">' .
              '<picture>' .
                '<source srcset="/sites/all/modules/custom/sec_landings/images/webinar4/768.webp" media="(max-width: 767px)">' .
                '<source srcset="/sites/all/modules/custom/sec_landings/images/webinar4/1200.webp" media="(max-width: 1279px)">' .
                '<img src="/sites/all/modules/custom/sec_landings/images/webinar4/1920.webp" alt="Вебинар">' .
              '</picture>' .
              '</a>';


  return $output;
}
