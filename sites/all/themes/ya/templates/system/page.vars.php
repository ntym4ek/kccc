<?php

/**
 * @file
 * Stub file for "page" theme hook [pre]process functions.
 */

/**
 * Pre-processes variables for the "page" theme hook.
 *
 * See template for list of available variables.
 *
 * @param array $variables
 *   An associative array of variables, passed by reference.
 *
 * @see page.tpl.php
 *
 * @ingroup theme_preprocess
 */
function ya_preprocess_page(array &$vars)
{
  $vars['logo'] = file_create_url('public://images/logo/logo.svg');
  $vars['logo_bl'] = file_create_url('public://images/logo/logo_bl.svg');

  // Заменить стандартную запись из Bootstrap page.vars.php на названия слассов
  if (!empty($vars['page']['sidebar_first']) && !empty($vars['page']['sidebar_second'])) {
    $vars['content_column_class'] = 'col-sm-6';
  }
  elseif (!empty($vars['page']['sidebar_first']) || !empty($vars['page']['sidebar_second'])) {
    $vars['content_column_class'] = 'col-sm-8';
  }
  else {
    $vars['content_column_class'] = 'col-sm-12';
  }

  /** -------------------------------------------- Меню */
  // Primary desktop nav.
  $menu = menu_tree_all_data('menu-main-d');
  $vars['primary_nav_d'] = menu_tree_output($menu);

  // top desktop nav.
  $menu = menu_tree_all_data('menu-top');
  $vars['top_nav'] = menu_tree_output($menu);

  // Secondary nav.
  if ($vars['secondary_menu']) {
    $menu = menu_tree_all_data(variable_get('menu_secondary_links_source', 'user-menu'));
    $vars['secondary_nav'] = menu_tree_output($menu);
    $vars['secondary_nav']['#theme_wrappers'] = array('menu_tree__user_menu');
  }


  /** -------------------------------------- Категория страницы */
  if (strpos(url($_GET['q']), '/news') === 0) {
    $vars['category_title'] = t('News');
  }
  if (strpos(url($_GET['q']), '/info/job') === 0) {
    $vars['category_title'] = t('Careers');
  }
}

/**
 * Processes variables for the "page" theme hook.
 *
 * See template for list of available variables.
 *
 * @param array $variables
 *   An associative array of variables, passed by reference.
 *
 * @see page.tpl.php
 *
 * @ingroup theme_process
 */
function ya_process_page(array &$vars) {

}
