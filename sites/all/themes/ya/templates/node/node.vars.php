<?php
/**
 * @file
 * Stub file for "page" theme hook [pre]process functions.
 */

/**
 * Pre-processes variables for the "node" theme hook.
 *
 * See template for list of available variables.
 *
 * @see node.tpl.php
 *
 * @ingroup theme_preprocess
 */
function ya_preprocess_node(&$vars)
{
  $path_alias = url($_GET['q']);

  /** ----- возможность создания отдельных шаблонов для разных view mode
   *  ----- http://xandeadx.ru/blog/drupal/576 -----
   */
  $node_type_suggestion_key = array_search('node__' . $vars['type'], $vars['theme_hook_suggestions']);
  if ($node_type_suggestion_key !== FALSE) {
    $i = 1;
    $node_view_mode_suggestion = 'node__' . $vars['view_mode'];
    array_splice($vars['theme_hook_suggestions'], $node_type_suggestion_key + $i++, 0, array($node_view_mode_suggestion));

    if (strpos($vars['type'], 'product') === 0) {
      array_splice($vars['theme_hook_suggestions'], $node_type_suggestion_key + $i++, 0, ['node__product' . '__' . $vars['view_mode']]);
    }

    $node_type_view_mode_suggestion = 'node__' . $vars['type'] . '__' . $vars['view_mode'];
    array_splice($vars['theme_hook_suggestions'], $node_type_suggestion_key + $i, 0, array($node_type_view_mode_suggestion));
  }

  $vars['title'] = drupal_ucfirst($vars['title']);
  // если выведено меню на странице (например в Вакансиях), не выводить заголовок
  // todo по уму нужно бы отключать по факту наличия этого меню
  if (url($_GET['q']) == '/info/karera-v-kccc') {
    $vars['node_title_off'] = true;
  }

  /** -------------------------------------- Изображения ------------------------------------------------------------ */
    /** ------------------------------------ Тизеры и Блок на главной */
    if(in_array($vars['view_mode'], ['teaser', 'front_block'])) {
        if (empty($vars['image'])) {
            $image_style = 'news_teaser';
            // для Вакансий своё дефолтное изображение
            $image_uri = $vars['type'] == 'vacancy' ? 'public://default_images/user_image.jpg' : 'public://default_images/no_image.jpg';
            if (isset($vars['content']['field_promo_image'][0])) {
                $image_uri = $vars['content']['field_promo_image'][0]['#item']['uri'];
            } elseif (isset($vars['content']['field_image_gallery'][0])) {
                $image_uri = $vars['content']['field_image_gallery'][0]['#item']['uri'];
            } elseif (isset($vars['content']['product:field_p_images'][0])) {
                $image_uri = $vars['content']['product:field_p_images'][0]['#item']['uri'];
            }
            $vars['image'] = image_style_url($image_style, $image_uri);
        }
    }

    /** ------------------------------------ Страницы */
    elseif($vars['view_mode'] == 'full') {
        if (empty($vars['image'])) {
            if (isset($vars['content']['field_image_gallery'][0])) {
                $vars['image'] = render($vars['content']['field_image_gallery']);
            } elseif (isset($vars['content']['field_promo_image'][0])) {
                $image_title = empty($vars['field_promo_image'][0]['title']) ? $vars['title'] : $vars['field_promo_image'][0]['title'];
                $image_alt = empty($vars['field_promo_image'][0]['alt']) ? $vars['title'] : $vars['field_promo_image'][0]['alt'];

                if ($vars['field_promo_image'][0]['height'] <= 430) {
                    $image_url = image_style_url('news_full_vertical', $vars['field_promo_image'][0]['uri']);
                } else {
                    $image_url = image_style_url('news_full_horizontal_hd', $vars['field_promo_image'][0]['uri']);
                }
                $vars['image'] = '<a href="' . file_create_url($vars['field_promo_image'][0]['uri']) . '" class="fancybox"><img src="' . $image_url . '" class="img-responsive" alt="' . $image_alt . '" /></a>';
                $vars['image'] .= '<div class="img-title"><span>' . t('Photo') . '. ' . $image_title . '</span></div>';

            }
            hide($vars['content']['field_promo_image']);
            hide($vars['content']['field_image_gallery']);
        }
    }

  if ($vars['view_mode'] == 'full') {
    $vars["classes_array"][] = 'full';

    /** -------------------------------------- Теги ----------------------------------------------------------------- */
    if (!empty($vars['field_tags'])) {
      foreach ($vars['field_tags'] as $item) {
        $term = taxonomy_term_load($item['tid']);
        $tag_url = '/blogs/tag/' . $item['tid'];
        $tags_arr[] = '<a href="' . $tag_url . '">' . $term->name . '</a>';
      }
      $vars['tags'] = implode(', ', $tags_arr);
      hide($vars['content']['field_tags']);
    }
  }




  /** -------------------------------------- Ссылка Назад к списку -------------------------------------------------- */
  $vars['backstep_url'] = '';
  if ($vars['view_mode'] == 'full') {
    if (strpos($path_alias, '/news/events') === 0) $vars['backstep_url'] = '/news/events';
    if (strpos($path_alias, '/news/interesting-facts') === 0) $vars['backstep_url'] = '/news/interesting-facts';
    if (strpos($path_alias, '/blogs') === 0) $vars['backstep_url'] = '/blogs';
    if (strpos($path_alias, '/agenda') === 0) $vars['backstep_url'] = '/agenda';
    if (strpos($path_alias, '/info/job/') === 0) $vars['backstep_url'] = '/info/job';
    if (strpos($path_alias, '/handbook/protection-programs/') === 0) $vars['backstep_url'] = '/handbook/protection-programs';
    if (in_array($vars['type'], ['product_agro', 'product_chem', 'product_fert', 'product_mix']) && !empty($vars["field_pd_category"]["und"][0]["tid"])) {
      $vars['backstep_url'] = url('taxonomy/term/' . $vars["field_pd_category"]["und"][0]["tid"]);
    }
  }

  /** -------------------------------------- Краткое содержание ----------------------------------------------------- */
  $vars['summary'] = '';
  if ($vars['view_mode'] == 'teaser') {
    if (!empty($vars["content"]["body"][0]["#markup"])) {
      $vars['summary'] = $vars["content"]["body"][0]["#markup"];
    }
  } elseif ($vars['view_mode'] == 'full') {
    if (!empty($vars["body"][0]["safe_summary"])) {
      $vars['summary'] = $vars["body"][0]["safe_summary"];
    }
  }

  /** ------------------------------------ Фронтальный блок ------------------------------------------------------- */
  /** ------------------------------------ Категория - */
    if ($vars['type'] == 'news') {
      $vars['category_url'] = url($vars['content']["field_news_category"][0]["#href"]);
      $vars['category_title'] = $vars['content']["field_news_category"][0]["#title"];
    } elseif ($vars['type'] == 'review') {
      $vars['category_url'] = '/reviews';
      $vars['category_title'] = t('Reviews');
    } elseif ($vars['type'] == 'agenda') {
      $vars['category_url'] = '/agenda';
      $vars['category_title'] = t('Agenda');
    }

    /** ------------------------------------ Дата - */
    if (in_array($vars['type'], array('news', 'blog', 'review', 'vacancy'))) {
        $vars['date'] = $vars['view_mode'] == 'full' ? format_date2($vars['node']->created, 'custom', 'j F Y') : format_date($vars['created'], 'date');
    } else { $vars['date'] = ''; }

    /** ------------------------------------ Период (Афиша) - */
    if (isset($vars['field_period'])) {
        $period_st = format_date($vars['field_period'][0]['value'], 'date');
        $period_end = format_date($vars['field_period'][0]['value2'], 'date');
        if ($period_st == $period_end) $vars['period'] = $period_st;
        else $vars['period'] = '<span>'. $period_st . ' - ' . $period_end . '</span>';

        hide($vars['content']['field_period']);
    }
  /** -------------------------------------- Место проведения (Афиша) */
  if (!empty($vars['field_location'])) {
    $vars['location'] = $vars['field_location'][0]['value'];
    hide($vars['content']['field_location']);
  }

    /** ------------------------------------ Убрать подпись к фото для Афиши - */
    if ($vars['type'] == 'agenda') {
      $vars['image_title'] = '';
    }

    /** ------------------------------------ Авторство - */
    if (in_array($vars['type'], array('blog', 'review'))) {
        $author = ext_user_get_user_array($vars['node']->uid);
        $vars['author'] = $author;
//        $vars['author'] = $author['name'] . ' ' . $author['surname'];
        //$vars['commented'] = $vars['node']->comment_count;
    }

    /** ------------------------------------ Вакансии --------------------------------------------------------------- */
    if ($vars['type'] == 'vacancy') {
        $vars['employer'] = $vars['content']['field_vacancy_employer'][0]['#markup'];
        $vars['place'] = empty($vars['content']['field_vacancy_location'][0]['#markup']) ? '' : $vars['content']['field_vacancy_location'][0]['#markup'];
        $vars['image'] = null;
    }



    /** ------------------------------------ Программы защиты-------------------------------------------------------- */
  if ($vars['type'] == 'protection_program') {
    if ($vars['view_mode'] == 'full') {
      if (isset($vars['content']['field_image'][0])) {
        $image_alt = empty($vars['field_image'][0]['alt']) ? $vars['title'] : $vars['field_image'][0]['alt'];

        $image_url = file_create_url($vars['field_image'][0]['uri']);

        $vars['image'] = '<a href="' . file_create_url($vars['field_image'][0]['uri']) . '" class="fancybox"><img src="' . $image_url . '" class="img-responsive" alt="' . $image_alt . '" /></a>';
        $vars['image'] .= '<div class="img-title"><span>' . t('Click on image to zoom') . '</span></div>';
        hide($vars['content']['field_image']);
      }
      $program = _reglaments_get_protection_system2(['program_id' => $vars['node']->nid]);
      $vars['program'] = theme('protection_program', ['program' => $program]);
    }
    if ($vars['view_mode'] == 'teaser') {
      $vars['image_url'] = file_create_url($vars['field_icon'][0]["uri"]);
    }
  }

    /** ------------------------------------ Продукция -------------------------------------------------------------- */
    if (in_array($vars['type'], array('product_agro', 'product_fert', 'product_mix', 'product_chem'))) {
        // локальная ссылка
        $vars['product_url'] = url('node/' . $vars['node']->nid);
        // добавить к ссылке на товар id каталога, откуда туда пойдём (для формирования Path Breadcrumbs)
        $tid = str_replace('taxonomy/term/', '', $_GET['q']);
        if (is_numeric($tid)) {
            $vars['product_url'] .= '?cat=' . $tid;
        }

        // перевести количество
        $translate_array = [
            '100 грамм' => '100 g',
            '10 банок' => '10 jars',
            '20 пакетиков х 30 гр' => '20 packets х 30 g',
            '0.5 килограмма' => '0.5 kg',
            '1 килограмм' => '1 kg',
            '4 пакета' => '4 packets',
            '10 пакетов' => '10 packets',
            '5 литров' => '5 liters',
            '20 литров' => '20 liters',
            '4 канистры' => '4 canisters',
            '32 канистры' => '32 canisters',
            '30 коробок' => '30 boxes',
            '40 коробок' => '40 boxes',
            '60 коробок' => '60 boxes',
            '100 коробок' => '100 boxes',
        ];
        if (isset($vars['content']['product:field_p_in_package'][0]['#markup'])
            && $GLOBALS['language']->language == 'en'
            && isset($translate_array[$vars['content']['product:field_p_in_package'][0]['#markup']])) {
            $vars['content']['product:field_p_in_package'][0]['#markup'] = $translate_array[$vars['content']['product:field_p_in_package'][0]['#markup']];
        }

        $vars['titles_arr'] = $vars['ingredients_arr'] = $vars['images'] = $vars['prices_arr'] = $vars['images'] = $preparations = [];
        if ($product = get_product_info($vars['node'])) {
            foreach ($product['items'] as $nid => $item) {
                if ($item['title']) $vars['titles_arr'][] = $item['title'] . ($item['form_short'] ? ', ' . $item['form_short'] : '');
                if ($item['ingredients']) $vars['ingredients_arr'][] = implode(' + ', $item['ingredients']);
                if ($item['price']) $vars['prices_arr'][] = $item['price'];
                if ($item['unit_short']) $vars['units'][] = $item['unit_short'];
                $preparations[] = $nid;
                $vars['images'][] = image_style_url('large', $item['photo_url']);

                if ($vars['type'] == 'product_mix') {
                    $vars['preps_arr'][] = '<a href="' . url('node/' . $item['id']) . '" target="_blank">' . $item['title'] . ', ' . $item['form_short'] . '</a><br /><span class="ingredients">' . drupal_strtolower(implode(' + ', $item['ingredients'])) . '</span>';
                }
            }
        }

      $vars['subtitle'] = '';
        switch ($vars['type']) {
          case 'product_agro':
            $vars['title'] = $vars['titles_arr'][0];
            $vars['subtitle'] = implode(' + ', $vars['ingredients_arr']);
            break;
          case 'product_fert':
            if ($vars['view_mode'] == 'teaser') {
              $vars['title'] = explode('|', $vars['titles_arr'][0])[0];
              $vars['subtitle'] = explode('|', $vars['titles_arr'][0])[1];
            }
            break;
        }

        if ($vars['type'] == 'product_mix') {
            $vars['addtocart_form'] = drupal_get_form('product_mix_add_to_cart_form', $preparations);
        }
    }

    /** ------------------------------------ Страница Информации ---------------------------------------------------- */
    if ($vars['type'] == 'information') {
        // подключить вложенные стили и JS
        if (!empty($vars['node']->field_page_files['und'])) {
            foreach ($vars['node']->field_page_files['und'] as $item) {
                if ($item['filemime'] == 'text/css') {
                    drupal_add_css($item['uri']);
                }
                // js файлы для этого поля загружаются в обход стандартного core-смены расширения js в js.txt
                // обход сделан в функции chibs_file_presave
                if ($item['filemime'] == 'application/javascript') {
                    drupal_add_js($item['uri']);
                }
            }
        }
        if (!empty($vars['field_page_lib'])) {
            drupal_add_library($vars['field_page_lib'][0]['#markup'], $vars['field_page_lib'][0]['#markup']);
        }
    }

}


