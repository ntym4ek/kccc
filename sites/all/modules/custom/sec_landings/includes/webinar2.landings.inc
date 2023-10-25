<?php

function landings_webinar2_page()
{

  $output = '<picture>' .
              '<source srcset="750x100.png" media="(max-width: 1279px)">' .
              '<img src="/sites/all/modules/custom/sec_landings/images/webinar2/1920.png" alt="вебинар">' .
            '</picture>';


  return $output;
}
