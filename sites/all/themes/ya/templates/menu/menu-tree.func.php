<?php
/**
 * @file
 * Stub file for bootstrap_menu_tree() and suggestion(s).
 */

/**
 * Returns HTML for a wrapper for a menu sub-tree.
 *
 * @param array $variables
 *   An associative array containing:
 *   - tree: An HTML string containing the tree's items.
 *
 * @return string
 *   The constructed HTML.
 *
 * @see template_preprocess_menu_tree()
 * @see theme_menu_tree()
 *
 * @ingroup theme_functions
 */

/**
 * Bootstrap theme wrapper function for the primary menu links.
 */
function ya_menu_tree__menu_top(&$vars) {
  return '<ul class="menu top-menu">' . $vars['tree'] . '</ul>';
}

/**
 * Bootstrap theme wrapper function for the primary desktop menu links.
 */
function ya_menu_tree__menu_main_d(&$vars) {
  return '<ul class="menu main-menu level-1">' . $vars['tree'] . '</ul>';
}

/**
 * Bootstrap theme wrapper function for the user menu links.
 */
function ya_menu_tree__user_menu(&$vars) {
  return '<ul class="menu user-menu">' . $vars['tree'] . '</ul>';
}

///**
// * Bootstrap theme wrapper function for the user menu links.
// */
//function ya_menu_tree__menu_news(&$vars) {
//  return '<ul class="menu news-menu">' . $vars['tree'] . '</ul>';
//}

/**
 * Bootstrap theme wrapper function for the navigation menu links.
 */
function ya_menu_tree__navigation(&$vars) {
  return '<ul class="menu navigation">' . $vars['tree'] . '</ul>';
}


