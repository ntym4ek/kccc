<?php
/**
 * @file
 * Stub file for "page" theme hook [pre]process functions.
 */

/**
 * Pre-processes variables for the "taxonomy_term" theme hook.
 *
 * See template for list of available variables.
 *
 * @see taxonomy_term.tpl.php
 *
 * @ingroup theme_preprocess
 */
function gard_preprocess_taxonomy_term(&$vars)
{
    // для словаря "Справочники" изменить вывод тизеров
    if ($vars['vocabulary_machine_name'] == 'handbooks' && !$vars['page']) {
        $image_style = 'news_teaser';
        $image_uri = 'public://default_images/no_image.jpg';

        if (isset($vars['content']['field_promo_image'][0])) {
            if (!empty($vars['content']['field_promo_image'][0]['#image_style'])) $image_style = $vars['content']['field_promo_image'][0]['#image_style'];
            $image_uri = $vars['content']['field_promo_image'][0]['#item']['uri'];
        }
        $vars['image'] = image_style_url($image_style, $image_uri);

        $vars['term_url'] = empty($vars['field_link'][0]['value']) ? '' : url($vars['field_link'][0]['value']);

        // количество записей в справочниках
        $vars['items_qty'] = empty($vars['field_textfield_1'][0]['value']) ? '' : t($vars['field_textfield_1'][0]['value']);
    }
}