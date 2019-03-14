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
function gard_menu_tree__primary(&$variables) {
  return '<ul class="menu nav navbar-nav primary">' . $variables['tree'] . '</ul>';
}

/**
 * Bootstrap theme wrapper function for the primary desctop menu links.
 */
function gard_menu_tree__menu_main_d(&$variables) {
  return '<ul class="menu nav navbar-nav level-1">' . $variables['tree'] . '</ul>';
}

/**
 * Bootstrap theme wrapper function for the user menu links.
 */
function gard_menu_tree__user_menu(&$variables) {
  return '<ul class="menu user-menu">' . $variables['tree'] . '</ul>';
}

/**
 * Bootstrap theme wrapper function for the navigation menu links.
 */
function gard_menu_tree__navigation(&$variables) {
  return '<ul class="menu nav navbar-nav navigation">' . $variables['tree'] . '</ul>';
}

