<?php
/**
 * @file
 * Stub file for "html" theme hook [pre]process functions.
 */

/**
 * Pre-processes variables for the "html" theme hook.
 *
 * See template for list of available variables.
 *
 * @see html.tpl.php
 *
 * @ingroup theme_preprocess
 */
function gard_preprocess_html(&$vars)
{
    /** -------------------------- тип ноды Страница -
        - подключение постраничных библиотек, стилей и скриптов (прописаны в пoле files страницы )
     */
    if (arg(0) == 'node' && is_numeric(arg(1)) && isset($vars['page']['content']['system_main']['nodes'][arg(1)])) {
        $node = $vars['page']['content']['system_main']['nodes'][arg(1)]['#node'];
        if ($node->type == 'page') {
            if (isset($node->field_page_files['und'])) {
                foreach ($node->field_page_files['und'] as $item) {
                    if ($item['filemime'] == 'text/css') { drupal_add_css($item['uri']); }
                    // js файлы для этого поля загружаются в обход стандартного core-смены расширения js в js.txt
                    // обход сделан в функции chibs_file_presave
                    if ($item['filemime'] == 'application/javascript') { drupal_add_js($item['uri']); }
                }
            }
            if (isset($node->field_page_lib['und'])) {
                drupal_add_library($node->field_page_lib['und']['value'], $node->field_page_lib['und']['value']);
            }
        }
    }

    /** -------------------------- добавление класса в зависимости от категории
     */
    $color = '';
    $path_array = explode('/', $_GET['q']);
    if ($path_array[0] == 'taxonomy' && is_numeric($path_array[2]) && $term = taxonomy_term_load($path_array[2])) {
        if (isset($term->name_field['en'][0]['value'])) {
            $color = drupal_strtolower($term->name_field['en'][0]['value']);
            $color = str_replace(' ', '-', $color);
        }
    }
    /** -------------------------- добавление класса в зависимости от категории выводимого препарата
     */
    if ($path_array[0] == 'node' && is_numeric($path_array[1]) && isset($vars['page']['content']['system_main']['nodes'][$path_array[1]])) {

        $node = $vars['page']['content']['system_main']['nodes'][$path_array[1]];
        // определить категорию
        $tid = empty($node['#node']->field_pd_category['und'][0]['tid']) ? 0 : $node['#node']->field_pd_category['und'][0]['tid'];
        if (!empty($_GET['cat'])) $tid = $_GET['cat'];

        if ($term = taxonomy_term_load($tid)) {
            if (isset($term->name_field['en'][0]['value'])) {
                $color = drupal_strtolower($term->name_field['en'][0]['value']);
                $color = str_replace(' ', '-', $color);
            }
        }
    }
    if ($color) $vars['classes_array'][] = 'page-'. $color;

    /** --------------------------  подключить FontAwesome 5 - */
    drupal_add_css('https://use.fontawesome.com/releases/v5.11.0/css/all.css', array('type' => 'external'));

    /** --------------------------  подключить Ubuntu - */
    drupal_add_css('https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500&display=swap', array('type' => 'external'));
    // <link rel="preconnect" href="https://fonts.googleapis.com">
    //<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    //<link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500&display=swap" rel="stylesheet">
}

