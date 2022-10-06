<?php

function ext_mobile_app_get_news_for_slider()
{
  $items = [
    [
      'path' => 'http://kccc.local/sites/default/files/images/tmp_front/slide_1.jpg',
      'link' => [
        'external' => true,
        'url' => 'https://kccc.ru/news/interesting-facts/pamyatka-rosselhozcentra-ob-osennem-vnesenii-glifosatov'
      ],
    ],
    ['path' => 'http://kccc.local/sites/default/files/images/tmp_front/slide_2.jpg', 'link' => ['url' => 'catalog']],
  ];

  sleep(2);

  return $items;
}
